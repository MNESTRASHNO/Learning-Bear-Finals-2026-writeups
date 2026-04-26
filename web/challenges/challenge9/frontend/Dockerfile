FROM node:18-alpine AS frontend-builder

WORKDIR /app/frontend

COPY frontend/package*.json ./

RUN npm install

COPY frontend/ .

RUN npm run build

FROM node:23-alpine AS backend-builder

WORKDIR /app

COPY package*.json ./

RUN npm install
COPY --from=frontend-builder /app/frontend/dist ./frontend/dist
COPY ./api.js .
COPY ./routes ./routes
COPY ./index.js .
COPY ./helpers.js .

EXPOSE 5002
CMD ["node", "index.js"]
