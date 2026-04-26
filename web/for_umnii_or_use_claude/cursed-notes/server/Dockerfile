FROM fedora:latest

WORKDIR /app

RUN dnf -y update && dnf install -y \
    python3 \
    python3-pip \
    python3-devel \
    glib2 \
    pango \
    libpq-devel \
    gcc \
    gcc-c++ \
    zlib-devel \
    libjpeg-devel \
    libffi-devel \
    nc \
    postgresql \
    util-linux \
    && dnf clean all

ENV PATH=$PATH:/usr/pgsql-13/bin

COPY requirements.txt .
RUN pip3 install --no-cache-dir -r requirements.txt

COPY . .

RUN useradd user

RUN chmod +x entrypoint.sh

EXPOSE 8000

CMD ["./entrypoint.sh"]
