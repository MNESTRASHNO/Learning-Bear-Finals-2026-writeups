import base64
import uuid

from db import db

class Note():
    def __init__(self, id, language, owner, content, private=True):
        self.id = id
        self.language = language
        self.owner = owner
        self.content = content
        self.private = private


    def to_dict(self):
        return {
            "id": self.id,
            "language": self.language,
            "owner": self.owner,
            "content": base64.b64decode(self.content).decode(),
            "private": self.private
        }


    def __str__(self):
        return f"{self.language}:{self.owner}:{self.content}:{self.private}"


    @classmethod
    def create(cls, language, owner, content, private):
        content = base64.b64encode(content.encode()).decode()
        id = str(uuid.uuid4())
        note = cls(id, language, owner, content, private)

        db.sadd(f"user/{owner}/notes", id)
        db.set(f"note/{id}", str(note))
        return note


    @classmethod
    def get_by_id(cls, id):
        res = db.get(f"note/{id}")
        if not res:
            return

        res = res.split(":")

        language = res[0]
        owner = res[1]
        content = res[2]
        private = True if res[3].lower() == "true" else False
        return cls(id, language, owner, content, private)

    @classmethod
    def get_all(cls, owner):
        res = db.smembers(f"user/{owner}/notes")
        notes = [cls.get_by_id(i) for i in res]
        return notes

    def delete(self):
        db.srem(f"user/{self.owner}/notes", self.id)
        db.delete(f"note/{self.id}")



def get_usernames():
    res = db.keys("user/*/password")
    return [ u.split("/")[1] for u in res ]


def get_password(username):
    return db.get(f"user/{username}/password")

def set_pasword(username, password):
    return db.set(f"user/{username}/password", password)
