FROM golang:alpine AS build
WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download
COPY . .
RUN CGO_ENABLED=0 go build -o /notekeeper .

FROM alpine:3.20
RUN apk add --no-cache ca-certificates
COPY --from=build /notekeeper /notekeeper
EXPOSE 8080
CMD ["/notekeeper"]
