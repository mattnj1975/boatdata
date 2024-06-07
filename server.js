const express = require("express");

const app = express();
const fs = require("fs");

const server = require("http").createServer(app);

const io = require("socket.io")(server, {
    cors: { origin: "*" },
});
require("dotenv").config();

var redisPort = process.env.REDIS_PORT;

var redisHost = process.env.REDIS_HOST;

var ioRedis = require("ioredis");
var redis = new ioRedis(redisPort, redisHost);

redis.subscribe("link", "read");

io.on("connection", function (socket) {
    console.log("Socket Connected ");
});

redis.on("message", function (channel, message) {
    message = JSON.parse(message);
    io.emit(channel + ":" + message.event, message.data);
});

server.listen(6002, () => {
    console.log("Server is running");
});
