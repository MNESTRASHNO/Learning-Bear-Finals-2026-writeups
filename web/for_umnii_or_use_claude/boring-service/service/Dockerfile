FROM node:20-slim

WORKDIR /app

COPY package*.json ./

RUN npm install

RUN apt-get update && apt-get install -y nginx supervisor curl

COPY . .

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY nginx/default.conf /etc/nginx/sites-enabled/default

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]