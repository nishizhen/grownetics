const bacnet = require('bacstack');

// Initialize BACStack
const client = new bacnet({adpuTimeout: 6000});

// Discover Devices
client.on('iAm', (device) => {
    console.log(device);
  console.log('address: ', device.address);
  console.log('deviceId: ', device.deviceId);
  console.log('maxAdpu: ', device.maxApdu);
  console.log('segmentation: ', device.segmentation);
  console.log('vendorId: ', device.vendorId);
});
client.whoIs();

const requestArray = [
  {objectId: {type: 1, instance: 1}, properties: [{id: 85}]}
];
const ip = '172.17.0.12';
console.log('Read from ',ip,' with: ',requestArray);
client.readPropertyMultiple(ip, requestArray, (err, value) => {
  if (err) {
    console.log('error: ', err);   
  } else {
    console.log('value: ', value);
  }
  return;
});

client.readProperty(ip, {type: 8, instance: 1234}, 28, (err, value) => {
  console.log('value: ', value);
  console.log('error: ', err);  
});
