from flask import Flask

app = Flask(__name__)


@app.route("/")
def index():
    return "OK"


@app.route("/flag")
def flag():
    return "flag{redacted}"


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8080)
