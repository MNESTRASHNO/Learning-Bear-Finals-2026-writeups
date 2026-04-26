from flask import Blueprint, request
from models import Note, get_usernames

user_routes = Blueprint("user", __name__, template_folder="templates", url_prefix="/user")

@user_routes.route("/")
def get_all_users():
    return { "status": "success", "data": get_usernames() }

@user_routes.route("/<username>")
def get_user_info(username):
    notes = Note.get_all(username) or []

    notes = [ { "id": note.id, "private": note.private, "owner": note.owner } for note in notes ]
    return { "status": "success", "data": { "username": username, "notes": notes } }

