import os
import socket

from flask import Flask, request, Blueprint, redirect
from blueprints.note import note_routes
from blueprints.auth import auth_routes
from blueprints.user import user_routes

TOKEN = os.environ.get("TOKEN", "TOKEN")
FRONTEND_HOST = socket.gethostbyname("frontend")

app = Flask(__name__)

def check_ip():
    ip = request.access_route[-1]
    if not (FRONTEND_HOST in ip):
        return { "status": "error", "message": "invalid ip" }
    

def verify_token():
    token = request.headers.get("Token", "")
    if token != TOKEN:
        return { "status": "error", "message": "Invalid Token" }


app.register_blueprint(note_routes)
app.register_blueprint(auth_routes)
app.register_blueprint(user_routes)
app.before_request_funcs = {
    "note": [ check_ip, verify_token ],
    "auth": [ check_ip, verify_token ],
    "user": [ check_ip, verify_token ]
}

@app.before_request
def hook():
    if request.path.startswith("/api"):
        return redirect(request.path[4:])

@app.route("/ping")
def ping():
    return "pong!"


if __name__ == "__main__":
    app.run("0.0.0.0", port=5001, debug=False)
