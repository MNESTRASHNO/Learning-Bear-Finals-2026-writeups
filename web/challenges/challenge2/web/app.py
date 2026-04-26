import sqlite3
import os
import base64
import socket
import ipaddress
from functools import wraps
from urllib.parse import urlparse
from datetime import datetime

import requests as http_client
from flask import (
    Flask, request, render_template, jsonify,
    session, redirect, url_for, g, flash
)
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.secret_key = os.environ.get("SECRET_KEY", os.urandom(32))

DATABASE = "/app/data/pixelgram.db"

BLOCKED_NETWORKS = [
    ipaddress.ip_network("0.0.0.0/8"),
    ipaddress.ip_network("10.0.0.0/8"),
    ipaddress.ip_network("100.64.0.0/10"),
    ipaddress.ip_network("127.0.0.0/8"),
    ipaddress.ip_network("169.254.0.0/16"),
    ipaddress.ip_network("172.16.0.0/12"),
    ipaddress.ip_network("192.0.0.0/24"),
    ipaddress.ip_network("192.0.2.0/24"),
    ipaddress.ip_network("192.88.99.0/24"),
    ipaddress.ip_network("192.168.0.0/16"),
    ipaddress.ip_network("198.18.0.0/15"),
    ipaddress.ip_network("198.51.100.0/24"),
    ipaddress.ip_network("203.0.113.0/24"),
    ipaddress.ip_network("224.0.0.0/4"),
    ipaddress.ip_network("240.0.0.0/4"),
    ipaddress.ip_network("255.255.255.255/32"),
    ipaddress.ip_network("::1/128"),
    ipaddress.ip_network("fc00::/7"),
    ipaddress.ip_network("fe80::/10"),
]

BLOCKED_HOSTNAMES = {
    "localhost",
    "localhost.localdomain",
    "ip6-localhost",
    "ip6-loopback",
    "internal-api",
    "web",
    "metadata.google.internal",
    "metadata.internal",
}


def get_db():
    if "db" not in g:
        g.db = sqlite3.connect(DATABASE)
        g.db.row_factory = sqlite3.Row
        g.db.execute("PRAGMA foreign_keys = ON")
    return g.db


@app.teardown_appcontext
def close_db(exception):
    db = g.pop("db", None)
    if db is not None:
        db.close()


def init_db():
    os.makedirs(os.path.dirname(DATABASE), exist_ok=True)
    db = sqlite3.connect(DATABASE)
    db.executescript("""
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            display_name TEXT NOT NULL,
            bio TEXT DEFAULT '',
            avatar TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
        CREATE TABLE IF NOT EXISTS likes (
            user_id INTEGER NOT NULL,
            post_id INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, post_id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (post_id) REFERENCES posts(id)
        );
        CREATE TABLE IF NOT EXISTS follows (
            follower_id INTEGER NOT NULL,
            following_id INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (follower_id, following_id),
            FOREIGN KEY (follower_id) REFERENCES users(id),
            FOREIGN KEY (following_id) REFERENCES users(id)
        );
    """)
    seed_data(db)
    db.commit()
    db.close()


def seed_data(db):
    existing = db.execute("SELECT COUNT(*) FROM users").fetchone()[0]
    if existing > 0:
        return

    users = [
        ("alice_wonder", "alice@pixelgram.local", "Alice Wonderland",
         "Photographer & traveler. Capturing moments one pixel at a time."),
        ("bob_pixel", "bob@pixelgram.local", "Bob Pixel",
         "Digital artist. Coffee enthusiast. Building cool stuff."),
        ("carol_shots", "carol@pixelgram.local", "Carol Shots",
         "Mountain lover. Weekend hiker. Dog person."),
        ("dave_frames", "dave@pixelgram.local", "Dave Frames",
         "Filmmaker. Storyteller. Always on the move."),
        ("eve_lens", "eve@pixelgram.local", "Eve Lens",
         "Street photography. Urban exploration. Night owl."),
    ]

    for username, email, display_name, bio in users:
        db.execute(
            "INSERT INTO users (username, email, password_hash, display_name, bio) VALUES (?, ?, ?, ?, ?)",
            (username, email, generate_password_hash("pixelgram123"), display_name, bio),
        )

    posts = [
        (1, "Beautiful sunset at the beach today! The colors were absolutely unreal."),
        (2, "New studio setup is coming together nicely. Can't wait to start creating!"),
        (3, "Mountain hike was worth every step. 3000m elevation and zero regrets."),
        (1, "Just discovered this hidden cafe downtown. The latte art is incredible."),
        (4, "Wrapped up editing on the new short film. Release coming soon!"),
        (5, "Night walk through the city. Love how the neon signs reflect off wet pavement."),
        (2, "Experimenting with generative art today. The results are wild."),
        (3, "Trail running season is here! Who's joining me this weekend?"),
        (4, "Behind the scenes from yesterday's shoot. The team killed it."),
        (5, "Found an abandoned warehouse with the most amazing light. Photo dump incoming."),
        (1, "Morning coffee and golden hour light. Perfect start to the day."),
        (3, "Summit selfie! Made it to the top before sunrise."),
    ]

    for user_id, content in posts:
        db.execute(
            "INSERT INTO posts (user_id, content) VALUES (?, ?)",
            (user_id, content),
        )

    follows = [
        (1, 2), (1, 3), (2, 1), (2, 3), (2, 4),
        (3, 1), (3, 5), (4, 1), (4, 2), (4, 5),
        (5, 1), (5, 3), (5, 4),
    ]
    for follower, following in follows:
        db.execute(
            "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)",
            (follower, following),
        )

    likes = [
        (1, 3), (1, 6), (2, 1), (2, 3), (2, 5),
        (3, 1), (3, 2), (3, 7), (4, 1), (4, 6),
        (4, 8), (5, 1), (5, 2), (5, 4), (5, 9),
    ]
    for user_id, post_id in likes:
        db.execute(
            "INSERT INTO likes (user_id, post_id) VALUES (?, ?)",
            (user_id, post_id),
        )


def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if "user_id" not in session:
            return redirect(url_for("login"))
        return f(*args, **kwargs)
    return decorated


def get_current_user():
    if "user_id" in session:
        db = get_db()
        return db.execute("SELECT * FROM users WHERE id = ?", (session["user_id"],)).fetchone()
    return None


@app.context_processor
def inject_user():
    return {"current_user": get_current_user()}


def is_blocked_ip(ip_str):
    try:
        ip = ipaddress.ip_address(ip_str)
        for network in BLOCKED_NETWORKS:
            if ip in network:
                return True
        return False
    except ValueError:
        return True


def validate_url(url):
    try:
        parsed = urlparse(url)
    except Exception:
        return False, "Invalid URL"

    if parsed.scheme not in ("http", "https"):
        return False, "Only HTTP and HTTPS protocols are allowed"

    hostname = parsed.hostname
    if not hostname:
        return False, "No hostname provided"

    if "@" in parsed.netloc:
        return False, "Credentials in URL are not allowed"

    hostname_lower = hostname.lower().rstrip(".")

    if hostname_lower in BLOCKED_HOSTNAMES:
        return False, "This hostname is not allowed"

    for blocked in BLOCKED_HOSTNAMES:
        if hostname_lower.endswith("." + blocked):
            return False, "This hostname is not allowed"

    try:
        addrinfo = socket.getaddrinfo(hostname, parsed.port or 80)
        for _, _, _, _, sockaddr in addrinfo:
            ip = sockaddr[0]
            if is_blocked_ip(ip):
                return False, "Resolved IP address is in a restricted range"
    except socket.gaierror:
        return False, "Could not resolve hostname"

    return True, None


@app.route("/")
def index():
    if "user_id" in session:
        return redirect(url_for("feed"))
    return redirect(url_for("login"))


@app.route("/register", methods=["GET", "POST"])
def register():
    if request.method == "GET":
        return render_template("register.html")

    username = request.form.get("username", "").strip().lower()
    email = request.form.get("email", "").strip().lower()
    display_name = request.form.get("display_name", "").strip()
    password = request.form.get("password", "")
    confirm = request.form.get("confirm_password", "")

    if not all([username, email, display_name, password]):
        flash("All fields are required.", "error")
        return render_template("register.html")

    if len(username) < 3 or len(username) > 20:
        flash("Username must be 3-20 characters.", "error")
        return render_template("register.html")

    if not username.replace("_", "").isalnum():
        flash("Username can only contain letters, numbers, and underscores.", "error")
        return render_template("register.html")

    if len(password) < 6:
        flash("Password must be at least 6 characters.", "error")
        return render_template("register.html")

    if password != confirm:
        flash("Passwords do not match.", "error")
        return render_template("register.html")

    db = get_db()
    if db.execute("SELECT id FROM users WHERE username = ?", (username,)).fetchone():
        flash("Username already taken.", "error")
        return render_template("register.html")

    if db.execute("SELECT id FROM users WHERE email = ?", (email,)).fetchone():
        flash("Email already registered.", "error")
        return render_template("register.html")

    db.execute(
        "INSERT INTO users (username, email, password_hash, display_name) VALUES (?, ?, ?, ?)",
        (username, email, generate_password_hash(password), display_name),
    )
    db.commit()

    user = db.execute("SELECT id FROM users WHERE username = ?", (username,)).fetchone()
    session["user_id"] = user["id"]
    return redirect(url_for("feed"))


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "GET":
        return render_template("login.html")

    username = request.form.get("username", "").strip().lower()
    password = request.form.get("password", "")

    db = get_db()
    user = db.execute("SELECT * FROM users WHERE username = ?", (username,)).fetchone()

    if not user or not check_password_hash(user["password_hash"], password):
        flash("Invalid username or password.", "error")
        return render_template("login.html")

    session["user_id"] = user["id"]
    return redirect(url_for("feed"))


@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))


@app.route("/feed")
@login_required
def feed():
    db = get_db()
    posts = db.execute("""
        SELECT p.*, u.username, u.display_name, u.avatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    """, (session["user_id"],)).fetchall()
    return render_template("feed.html", posts=posts)


@app.route("/following")
@login_required
def following_feed():
    db = get_db()
    posts = db.execute("""
        SELECT p.*, u.username, u.display_name, u.avatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id IN (SELECT following_id FROM follows WHERE follower_id = ?)
           OR p.user_id = ?
        ORDER BY p.created_at DESC
    """, (session["user_id"], session["user_id"], session["user_id"])).fetchall()
    return render_template("feed.html", posts=posts, tab="following")


@app.route("/explore")
@login_required
def explore():
    db = get_db()
    users = db.execute("""
        SELECT u.*,
            (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count,
            (SELECT COUNT(*) FROM follows WHERE following_id = u.id) as follower_count,
            (SELECT COUNT(*) FROM follows WHERE follower_id = ? AND following_id = u.id) as is_following
        FROM users u
        WHERE u.id != ?
        ORDER BY follower_count DESC
    """, (session["user_id"], session["user_id"])).fetchall()
    return render_template("explore.html", users=users)


@app.route("/u/<username>")
@login_required
def profile(username):
    db = get_db()
    user = db.execute("SELECT * FROM users WHERE username = ?", (username,)).fetchone()
    if not user:
        flash("User not found.", "error")
        return redirect(url_for("feed"))

    posts = db.execute("""
        SELECT p.*,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as liked
        FROM posts p
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    """, (session["user_id"], user["id"])).fetchall()

    stats = {
        "posts": db.execute("SELECT COUNT(*) FROM posts WHERE user_id = ?", (user["id"],)).fetchone()[0],
        "followers": db.execute("SELECT COUNT(*) FROM follows WHERE following_id = ?", (user["id"],)).fetchone()[0],
        "following": db.execute("SELECT COUNT(*) FROM follows WHERE follower_id = ?", (user["id"],)).fetchone()[0],
    }

    is_following = db.execute(
        "SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?",
        (session["user_id"], user["id"]),
    ).fetchone() is not None

    return render_template("profile.html", user=user, posts=posts, stats=stats, is_following=is_following)


@app.route("/settings")
@login_required
def settings():
    return render_template("settings.html")


@app.route("/settings/profile", methods=["POST"])
@login_required
def update_profile():
    display_name = request.form.get("display_name", "").strip()
    bio = request.form.get("bio", "").strip()
    email = request.form.get("email", "").strip().lower()

    if not display_name:
        flash("Display name is required.", "error")
        return redirect(url_for("settings"))

    db = get_db()
    existing = db.execute(
        "SELECT id FROM users WHERE email = ? AND id != ?",
        (email, session["user_id"]),
    ).fetchone()
    if existing:
        flash("Email already in use.", "error")
        return redirect(url_for("settings"))

    db.execute(
        "UPDATE users SET display_name = ?, bio = ?, email = ? WHERE id = ?",
        (display_name, bio[:200], email, session["user_id"]),
    )
    db.commit()
    flash("Profile updated.", "success")
    return redirect(url_for("settings"))


@app.route("/settings/password", methods=["POST"])
@login_required
def change_password():
    current = request.form.get("current_password", "")
    new_pass = request.form.get("new_password", "")
    confirm = request.form.get("confirm_password", "")

    db = get_db()
    user = db.execute("SELECT * FROM users WHERE id = ?", (session["user_id"],)).fetchone()

    if not check_password_hash(user["password_hash"], current):
        flash("Current password is incorrect.", "error")
        return redirect(url_for("settings"))

    if len(new_pass) < 6:
        flash("New password must be at least 6 characters.", "error")
        return redirect(url_for("settings"))

    if new_pass != confirm:
        flash("Passwords do not match.", "error")
        return redirect(url_for("settings"))

    db.execute(
        "UPDATE users SET password_hash = ? WHERE id = ?",
        (generate_password_hash(new_pass), session["user_id"]),
    )
    db.commit()
    flash("Password changed.", "success")
    return redirect(url_for("settings"))


@app.route("/api/post", methods=["POST"])
@login_required
def create_post():
    content = request.form.get("content", "").strip()
    if not content:
        flash("Post cannot be empty.", "error")
        return redirect(url_for("feed"))

    if len(content) > 500:
        flash("Post cannot exceed 500 characters.", "error")
        return redirect(url_for("feed"))

    db = get_db()
    db.execute(
        "INSERT INTO posts (user_id, content) VALUES (?, ?)",
        (session["user_id"], content),
    )
    db.commit()
    return redirect(url_for("feed"))


@app.route("/api/like/<int:post_id>", methods=["POST"])
@login_required
def toggle_like(post_id):
    db = get_db()
    existing = db.execute(
        "SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?",
        (session["user_id"], post_id),
    ).fetchone()

    if existing:
        db.execute(
            "DELETE FROM likes WHERE user_id = ? AND post_id = ?",
            (session["user_id"], post_id),
        )
    else:
        db.execute(
            "INSERT INTO likes (user_id, post_id) VALUES (?, ?)",
            (session["user_id"], post_id),
        )
    db.commit()

    count = db.execute("SELECT COUNT(*) FROM likes WHERE post_id = ?", (post_id,)).fetchone()[0]
    return jsonify({"liked": not existing, "count": count})


@app.route("/api/follow/<int:user_id>", methods=["POST"])
@login_required
def toggle_follow(user_id):
    if user_id == session["user_id"]:
        return jsonify({"error": "Cannot follow yourself"}), 400

    db = get_db()
    existing = db.execute(
        "SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?",
        (session["user_id"], user_id),
    ).fetchone()

    if existing:
        db.execute(
            "DELETE FROM follows WHERE follower_id = ? AND following_id = ?",
            (session["user_id"], user_id),
        )
    else:
        db.execute(
            "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)",
            (session["user_id"], user_id),
        )
    db.commit()

    count = db.execute("SELECT COUNT(*) FROM follows WHERE following_id = ?", (user_id,)).fetchone()[0]
    return jsonify({"following": not existing, "count": count})


@app.route("/api/post/<int:post_id>", methods=["DELETE"])
@login_required
def delete_post(post_id):
    db = get_db()
    post = db.execute("SELECT * FROM posts WHERE id = ? AND user_id = ?", (post_id, session["user_id"])).fetchone()
    if not post:
        return jsonify({"error": "Post not found"}), 404
    db.execute("DELETE FROM likes WHERE post_id = ?", (post_id,))
    db.execute("DELETE FROM posts WHERE id = ?", (post_id,))
    db.commit()
    return jsonify({"success": True})


@app.route("/api/avatar/fetch", methods=["POST"])
@login_required
def fetch_avatar():
    data = request.get_json()
    if not data or "url" not in data:
        return jsonify({"error": "URL is required"}), 400

    url = data["url"].strip()
    valid, error = validate_url(url)
    if not valid:
        return jsonify({"error": error}), 403

    try:
        resp = http_client.get(
            url,
            timeout=5,
            headers={"User-Agent": "Pixelgram/1.0 AvatarFetcher"},
        )
        content_type = resp.headers.get("Content-Type", "application/octet-stream")
        encoded = base64.b64encode(resp.content).decode()
        avatar_data = f"data:{content_type};base64,{encoded}"

        db = get_db()
        db.execute("UPDATE users SET avatar = ? WHERE id = ?", (avatar_data, session["user_id"]))
        db.commit()

        return jsonify({
            "success": True,
            "avatar": avatar_data,
            "size": len(resp.content),
        })
    except http_client.exceptions.Timeout:
        return jsonify({"error": "Request timed out"}), 408
    except http_client.exceptions.ConnectionError:
        return jsonify({"error": "Could not connect to the provided URL"}), 502
    except Exception:
        return jsonify({"error": "Failed to fetch the resource"}), 500


@app.route("/api/avatar/remove", methods=["POST"])
@login_required
def remove_avatar():
    db = get_db()
    db.execute("UPDATE users SET avatar = NULL WHERE id = ?", (session["user_id"],))
    db.commit()
    return jsonify({"success": True})


@app.route("/api/health")
def health():
    services = {"web": "ok"}
    try:
        http_client.get("http://internal-api:8080/", timeout=2)
        services["internal-api"] = "ok"
    except Exception:
        services["internal-api"] = "unavailable"
    return jsonify(services)


@app.template_filter("timeago")
def timeago_filter(dt_str):
    if not dt_str:
        return ""
    try:
        dt = datetime.strptime(dt_str, "%Y-%m-%d %H:%M:%S")
    except (ValueError, TypeError):
        return dt_str
    diff = datetime.utcnow() - dt
    seconds = int(diff.total_seconds())
    if seconds < 60:
        return "just now"
    if seconds < 3600:
        m = seconds // 60
        return f"{m}m ago"
    if seconds < 86400:
        h = seconds // 3600
        return f"{h}h ago"
    d = seconds // 86400
    if d < 30:
        return f"{d}d ago"
    return dt.strftime("%b %d, %Y")


if __name__ == "__main__":
    init_db()
    app.run(host="0.0.0.0", port=11002)
