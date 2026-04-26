import socket
import struct
import threading

UPSTREAMS = [
    "8.8.8.8",
    "1.1.1.1",
    "8.8.4.4",
    "1.0.0.1",
    "9.9.9.9",
    "208.67.222.222",
    "208.67.220.220",
    "76.76.2.0",
]

counter = 0
lock = threading.Lock()


def next_upstream():
    global counter
    with lock:
        upstream = UPSTREAMS[counter % len(UPSTREAMS)]
        counter += 1
    return upstream


def forward_query(data):
    upstream = next_upstream()
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.settimeout(5)
    try:
        sock.sendto(data, (upstream, 53))
        response, _ = sock.recvfrom(4096)
        return response
    except Exception:
        return None
    finally:
        sock.close()


def zero_ttl(response):
    if not response or len(response) < 12:
        return response

    header = struct.unpack("!HHHHHH", response[:12])
    qdcount = header[2]
    ancount = header[3]
    nscount = header[4]
    arcount = header[5]

    offset = 12
    for _ in range(qdcount):
        while offset < len(response):
            length = response[offset]
            if length == 0:
                offset += 1
                break
            if (length & 0xC0) == 0xC0:
                offset += 2
                break
            offset += 1 + length
        offset += 4

    result = bytearray(response)
    for _ in range(ancount + nscount + arcount):
        if offset >= len(result):
            break
        while offset < len(result):
            length = result[offset]
            if length == 0:
                offset += 1
                break
            if (length & 0xC0) == 0xC0:
                offset += 2
                break
            offset += 1 + length
        if offset + 10 > len(result):
            break
        struct.pack_into("!I", result, offset + 4, 0)
        rdlength = struct.unpack("!H", result[offset + 8:offset + 10])[0]
        offset += 10 + rdlength

    return bytes(result)


def handle_client(server_sock, data, addr):
    response = forward_query(data)
    if response:
        response = zero_ttl(response)
        server_sock.sendto(response, addr)


def main():
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sock.bind(("127.0.0.1", 53))
    print("DNS forwarder started on 127.0.0.1:53 (round-robin, TTL=0)", flush=True)
    while True:
        data, addr = sock.recvfrom(4096)
        threading.Thread(target=handle_client, args=(sock, data, addr), daemon=True).start()


if __name__ == "__main__":
    main()
