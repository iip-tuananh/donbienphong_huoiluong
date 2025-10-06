FROM node:13-alpine

WORKDIR /app

COPY ./package.json .
COPY ./server_node .

RUN npm install

CMD ["node", "server.js"]
