from flask import Blueprint, request
from models import get_password, set_pasword
from hashlib import sha1

auth_routes = Blueprint("auth", __name__, template_folder="templates", url_prefix="/user")

@auth_routes.route("/register", methods=["POST"])
def regiser():
    username = request.form.get("username")
    password = request.form.get("password")
    if not username or not password:
        return { "status": "error", "message": "invalid username or password" }
    
    if get_password(username):
        return { "status": "error", "message": "account already exists" }


    enc = sha1(password.encode()).hexdigest()
    set_pasword(username, enc)
    return { "status": "success", "data": None }


@auth_routes.route("/login", methods=["POST"])
def login():
    username = request.form.get("username")
    password = request.form.get("password")
    if not username or not password:
        return { "status": "error", "message": "invalid username or password" }
    
    res = get_password(username)
    if not res:
        return { "status": "error", "message": "account doesnt exists" }

    enc = sha1(password.encode()).hexdigest()
    if res != enc:
        return { "status": "error", "message": "incorrect password" }

    return { "status": "success", "data": None }


