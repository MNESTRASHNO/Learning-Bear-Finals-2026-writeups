from flask import Blueprint, request
from models import Note

note_routes = Blueprint("note", __name__, template_folder="templates", url_prefix="/note")

@note_routes.route("/create", methods=["POST"])
def create_note():
    username = request.headers.get("Username")
    if not username:
        return { "status": "error", "message": "Invalid username" }

    language = request.form.get("language", "plain")
    content = request.form.get("content", "")
    private = True if request.form.get("private", "").lower() == "true" else False

    note = Note.create(language, username, content, private)

    return { "status": "success", "data": note.to_dict() }


@note_routes.route("/my")
def list_notes():
    username = request.headers.get("Username")
    if not username:
        return { "status": "error", "message": "Invalid username" }


    notes = Note.get_all(username)
    return { "status": "success", "data": [ note.to_dict() for note in notes ] }


@note_routes.route("/get", methods=["POST"])
def get_note():
    username = request.headers.get("Username")
    if not username:
        return { "status": "error", "message": "Invalid username" }

    note_id = request.form.get("note_id", "")

    note = Note.get_by_id(note_id)
    if not note:
        return { "status": "error", "message": "Not Found" }

    if note.owner != username and note.private:
        return { "status": "error", "message": "Permission Denied" }

    return { "status": "success", "data": note.to_dict() }


@note_routes.route("/delete", methods=["POST"])
def delete_note():
    username = request.headers.get("Username")
    if not username:
        return { "status": "error", "message": "Invalid username" }

    note_id = request.form.get("note_id", "")

    note = Note.get_by_id(note_id)
    if not note:
        return { "status": "error", "message": "Not Found" }

    if note.owner != username:
        return { "status": "error", "message": "Permission Denied" }

    note.delete()
    return { "status": "success", "data": None }

