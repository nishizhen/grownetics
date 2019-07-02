#!/usr/bin/env node

var amqp = require('amqplib/callback_api');

amqp.connect('amqp://rabbit:rabbit@localhost', function(err, conn) {
  conn.createChannel(function(err, ch) {
    var q = 'data.sensor';
    var date = new Date();
    var msg = [{"source_id":559,"value":Math.round(Math.random()*100),"created":date}];

    ch.assertQueue(q, {durable: false});
    ch.sendToQueue(q, Buffer.from(JSON.stringify(msg)));
    console.log(" [x] Sent %s", JSON.stringify(msg));
  });
  setTimeout(function() { conn.close(); process.exit(0) }, 500);
});

