var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
const redis = require('redis');
const jwt = require("jsonwebtoken");
require('dotenv').config();

const PORT = process.env.PORT || 3000;
const REDIS_PORT = process.env.REDIS_PORT || 6667;
const REDIS_HOST = process.env.REDIS_HOST || 'localhost';
const SECRET = process.env.JWT_SECRET;
const prefix = "laravel_database_";

server.listen(PORT);

console.log("App run on port " + PORT);

io.sockets.use(function(socket, next) {
  if (socket.handshake.query && socket.handshake.query.token){
    jwt.verify(socket.handshake.query.token, SECRET, function(err, decoded) {
      if (err) return next(new Error('Authentication error'));
      socket.decoded = decoded;
      next();
    });
  } else {
    next(new Error('Authentication error'));
  }
})
.on('connection', function(socket) {

  console.log("New client connected");

  var redisClient = redis.createClient({port: REDIS_PORT, host: REDIS_HOST});

  redisClient.subscribe(`${prefix}user_notification_${socket.decoded.id}`);

  redisClient.subscribe(`${prefix}common`);

  redisClient.on("message", function(channel, message) {
    socket.emit("notification", message);
  });

  socket.on('disconnect', function() {
    redisClient.quit();
  });

});
