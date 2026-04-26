import requests
from flask import Flask, request, Response

session = requests.Session()

app = Flask(__name__)

@app.route('/ping', methods=['GET'])
def ping():
    return 'pong'

@app.route('/download', methods=['POST', 'GET'])
def download():
    data = request.json
    url = data.get('url', "https://penzanews.ru/images/stories/img2018/blinnikov06122018.jpg")
    method = data.get('method', 'get')

    params = data.copy()
    if 'url' in params:
        del params['url']
    if 'method' in params:
        del params['method']

    makeReponse = getattr(session, method)
    img_res = makeReponse(url, **params)

    res = Response(img_res.content, mimetype='image/jpeg')
    res.headers['Content-Disposition'] = 'inline; filename="image.jpg"'
    return res


if __name__ == '__main__':
    app.run("0.0.0.0", port=3940)