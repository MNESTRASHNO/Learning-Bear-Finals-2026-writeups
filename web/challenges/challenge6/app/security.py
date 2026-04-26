import socket
import ipaddress
from urllib.parse import urlparse


def is_private_ip(ip_str: str) -> bool:
    try:
        ip = ipaddress.ip_address(ip_str)
        return (
            ip.is_private
            or ip.is_loopback
            or ip.is_link_local
            or ip.is_multicast
            or ip.is_reserved
            or ip.is_unspecified
        )
    except ValueError:
        return True


def is_alternative_ip_format(hostname: str) -> bool:
    try:
        num = int(hostname)
        if 0 <= num <= 0xFFFFFFFF:
            return True
    except ValueError:
        pass

    try:
        if hostname.lower().startswith("0x"):
            num = int(hostname, 16)
            if 0 <= num <= 0xFFFFFFFF:
                return True
    except ValueError:
        pass

    parts = hostname.split(".")
    if len(parts) == 4:
        for part in parts:
            if part.startswith("0") and len(part) > 1 and part.isdigit():
                return True

    if hostname.startswith("[") or "::" in hostname or ":" in hostname:
        return True

    return False


def validate_url(url: str) -> str:
    parsed = urlparse(url)

    if parsed.scheme not in ("http", "https"):
        raise ValueError("Only HTTP and HTTPS schemes are allowed")

    hostname = parsed.hostname
    if not hostname:
        raise ValueError("Invalid URL: missing hostname")

    port = parsed.port
    if port and port not in (80, 443, 8000, 8080):
        raise ValueError("Port not allowed")

    if is_alternative_ip_format(hostname):
        raise ValueError("IP address format not allowed")

    try:
        ipaddress.ip_address(hostname)
        if is_private_ip(str(hostname)):
            raise ValueError("Access to private/internal IPs is blocked")
    except ValueError as e:
        if "Access to" in str(e):
            raise

    try:
        results = socket.getaddrinfo(hostname, None, socket.AF_UNSPEC, socket.SOCK_STREAM)
    except socket.gaierror:
        raise ValueError("Could not resolve hostname")

    for _, _, _, _, sockaddr in results:
        resolved_ip = sockaddr[0]
        if is_private_ip(resolved_ip):
            raise ValueError("Hostname resolves to a blocked IP address")

    return url
