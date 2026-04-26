import pycurl
from io import BytesIO

from app.config import FETCH_TIMEOUT, MAX_RESPONSE_SIZE


def fetch_url(url: str) -> bytes:
    buf = BytesIO()
    header_buf = BytesIO()

    c = pycurl.Curl()
    c.setopt(c.URL, url)
    c.setopt(c.WRITEDATA, buf)
    c.setopt(c.HEADERFUNCTION, header_buf.write)
    c.setopt(c.FOLLOWLOCATION, True)
    c.setopt(c.MAXREDIRS, 5)
    c.setopt(c.TIMEOUT, FETCH_TIMEOUT)
    c.setopt(c.CONNECTTIMEOUT, 3)
    c.setopt(c.NOSIGNAL, 1)
    c.setopt(c.USERAGENT, "LinkLens/1.0 Preview Bot")
    c.setopt(c.MAX_RECV_SPEED_LARGE, MAX_RESPONSE_SIZE)

    blocked = ["file"]
    scheme = url.split("://")[0].lower() if "://" in url else ""
    if scheme in blocked:
        raise ValueError("This protocol is not allowed")

    try:
        c.perform()
    except pycurl.error as e:
        if e.args[0] == pycurl.E_OPERATION_TIMEDOUT and buf.getvalue():
            pass
        else:
            c.close()
            raise

    c.close()
    return buf.getvalue()
