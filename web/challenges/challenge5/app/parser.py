from bs4 import BeautifulSoup


def parse_preview(html: bytes, url: str) -> dict:
    try:
        soup = BeautifulSoup(html, "html.parser")
    except Exception:
        return {
            "url": url,
            "title": None,
            "description": None,
            "image": None,
            "site_name": None,
        }

    title = None
    og_title = soup.find("meta", property="og:title")
    if og_title and og_title.get("content"):
        title = og_title["content"]
    elif soup.title and soup.title.string:
        title = soup.title.string.strip()

    description = None
    og_desc = soup.find("meta", property="og:description")
    if og_desc and og_desc.get("content"):
        description = og_desc["content"]
    else:
        meta_desc = soup.find("meta", attrs={"name": "description"})
        if meta_desc and meta_desc.get("content"):
            description = meta_desc["content"]

    image = None
    og_image = soup.find("meta", property="og:image")
    if og_image and og_image.get("content"):
        image = og_image["content"]

    site_name = None
    og_site = soup.find("meta", property="og:site_name")
    if og_site and og_site.get("content"):
        site_name = og_site["content"]

    favicon = None
    link_icon = soup.find("link", rel=lambda x: x and "icon" in x)
    if link_icon and link_icon.get("href"):
        favicon = link_icon["href"]

    raw_body = html.decode("utf-8", errors="ignore")

    return {
        "url": url,
        "title": title,
        "description": description,
        "image": image,
        "site_name": site_name,
        "favicon": favicon,
        "raw_body": raw_body,
    }
