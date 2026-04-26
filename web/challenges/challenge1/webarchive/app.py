from flask import Flask, request, render_template
import requests

app = Flask(__name__)


@app.route("/")
def index():
    return render_template("index.html")


@app.route("/fetch", methods=["POST"])
def fetch_url():
    url = request.form.get("url", "")
    if not url:
        return render_template("index.html", error="Please provide a URL.")

    try:
        resp = requests.get(url, timeout=5)
        return render_template(
            "index.html",
            result=resp.text,
            fetched_url=url,
            status_code=resp.status_code,
        )
    except requests.exceptions.RequestException as e:
        return render_template("index.html", error=f"Failed to fetch URL: {e}")


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
