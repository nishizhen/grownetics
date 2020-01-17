var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var port = process.env.PORT || 8989;
var amqp = require('amqplib/callback_api');
var request = require('request');

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

io.on('connection', function(socket){
  socket.on('chat', function(msg){
    console.log("Got chat: " + msg);

    // Save the post to appdb for persistence, and get the user avatar
    request.post(
      'http://nginx/chats/chat',
      { json: msg },
      function (error, response, body) {
          if (!error && response.statusCode == 200) {
              console.log("Success:")
              console.log(body)
              message = JSON.parse(msg)
              message.avatar = body.avatar
              console.log("Message:")
              console.log(msg)
              io.emit('chat', JSON.stringify(message));
          }
      }
    );
  });

  socket.on('join', function(room) {
    console.log("Join: ",room)
    socket.join(room);
    let data_type = room.split('.')[2]
    // Send the latest data points for that data type when joining a room
    request.post(
      'http://nginx/DataPoints/map/'+data_type+'.json',
      { },
      function (error, response, body) {
          if (!error && response.statusCode == 200) {
            console.log("Send on join to map: "+body)
            io.to(room).emit('data.sensor', body)
          } else {
            console.log("Error from map: "+error)
          }
      }
    );
  });
  socket.on('leave', function(room) {
    socket.leave(room)
  })
});

http.listen(port, function(){
  // Serve the socket.io libraries
  console.log('listening on *:' + port);
});

amqp.connect('amqp://rabbit:rabbit@rabbitmq', function(err, conn) {
  console.log(err,conn)
  conn.createChannel(function(err, ch) {
    var q = 'data.sensor';

    ch.assertQueue(q, {durable: false});
    console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
    ch.consume(q, function(msg) {
      var message = JSON.parse(msg.content)
      io.to('data.sensor.'+message[0].type).emit('data.sensor', msg.content.toString());
    }, {noAck: true});
  });
});
