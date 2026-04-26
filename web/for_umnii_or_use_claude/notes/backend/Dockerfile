FROM alpine:latest

# Install build dependencies and supervisord
RUN apk add --no-cache wget supervisor curl

COPY ./zig-linux-x86_64-0.13.0.tar.xz .
RUN tar -xf zig-linux-x86_64-0.13.0.tar.xz \
    && mv zig-linux-x86_64-0.13.0 /usr/local/zig \
    && rm zig-linux-x86_64-0.13.0.tar.xz

# Add Zig to PATH
ENV PATH="/usr/local/zig:${PATH}"

WORKDIR /app

# Copy source code and supervisord config
COPY . .

# Build the application
RUN zig build

# Run supervisord
CMD ["/usr/bin/supervisord", "-c", "/app/supervisord.conf"]
