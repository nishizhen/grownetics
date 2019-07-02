// controller.js
const mqtt = require('mqtt')
const client = mqtt.connect('mqtt://3.122.45.86')

var garageState = ''
var connected = false

client.on('connect', () => {
  console.log("connected")
  var num = Math.random()*10
  client.publish('sensor/value', num.toString())
  
})