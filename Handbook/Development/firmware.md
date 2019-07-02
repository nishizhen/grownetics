# Firmware

These folders contain the code that goes on our various microcontroller devices.

## AnalogLightDimmerController

Arduino code that talks to Consul to set various analog pins at desired levels, for interfacing with light dimming hardware.

This code is for lights like the Fluence LED light that are dimmed using a 0-10V analog control.

It reads values from the server and applies those values to the corresponding pins.

### [Prototype Wiring](firmware-aldc-prototype.jpg)

Connect GND on the Arduino to GND next to the PWM port on the ACB (Analog Control Board), and port 9 of the ARDUINO to the PWM port on the ACB. This is for testing. For production, there may be more than one ACB so there may be multiple ports needed.

Connect the GND and ANG

`dimmer_test.ino` is meant to test the physical wiring of a setup, to make sure everything is connected properly. It cycles between 0 and 255 on analog output pin 9.

`dimmer.ino` is the functional production code. It reads Consul to see what pins it should set to what level.

### Data Flow

Each ALDC device will have it's own ID, referred to on the GrowDash as an Analog Light Dimmer. The GrowPulse will set consul values for each pin it wants to control.

First the ALDC reads the JSON object stored in Consul at devices/ID/analogPins. This JSON object has the pin values we need to set.

#### Example JSON response
```
{"pins":[8,9],"values":[0,255]}
```

## CropSensor3D

Arduino code for our 3D Crop Sensor, contains only Temp, Humidity, Co2 and PAR. Hits the DeviceApi.

## Middleware

Arduino code for our all purpose Middleware devices, contains code for every sensor we currently support. Hits the DeviceApi.

This is our original work horse code, it lives on Arduino devices, asks the server what sensors it should read, and returning the data from those sensors.

This code currently supports every sensor type that we support in the system.

## Working with Atlas Scientific pH, EC, DO, RTD Sensors

We use the [Atlas Scientific Tentacle Shield](https://www.whiteboxes.ch/shop/tentacle/?v=7516fd43adaa) for our fertigation sensors.

### Debugging Sensors

To avoid our code altogether, you can use the [Tentacle Shield Circuit Setup code](https://raw.githubusercontent.com/whitebox-labs/tentacle-examples/master/arduino/tentacle-setup/tentacle_setup/tentacle_setup.ino) provided by Atlas Scientific.

### Switching Sensors to I2C

Our code setup uses the I2C setup of these sensors. The [directions for chaning to I2C mode](https://www.whiteboxes.ch/tentacle/?v=7516fd43adaa#switch-i2c) are pasted here for posterity:

```
This procedure switches the circuit between UART/serial and I2C. If the device is in UART mode, this procedure will switch it to I2C. If the device is in I2C mode, it will switch it to UART/serial.

When switched to I2C, this will reset the circuit ID to factory default:

    DO: 97 (0x60)
    ORP: 98 (0x61)
    pH: 99 (0x63)
    EC: 100 (0x64)
    RTD: 102 (0x66)

When switching to UART, this will reset the circuit baudrate to factory default of 9600 baud.

    Remove circuit from Tentacle shield
    Put the circuit into a breadboard
    For pH, DO, ORP and EC: Short the PGND pin to the TX pin using a jumper wire
    For RTD (temperature): Short the PRB pin to the TX pin using a jumper wire
    Power the device (GND, +5V)
    Wait for LED to change from green to blue (UART->I2C) or from blue to green (I2C->UART).
    Remove the jumper wire from the PGND (or PRB respectively) pin to the TX pin (Do this before removing power!)
    Remove power (5V)
    Apply power (5V)
    The device is now in the new mode (repeat 1-8 to switch back to the other mode)
```

The numbers listed above next to each sensor type should match the numbers for the `channel` value in the code for each sensor type.

## WifiReset

ESP8266 code which hits Consul to see if a specific device needs rebooted. Last line of defence against devices that become unresponsive for whatever reason.

## Flashing Devices

### Flashing the ESP8266 Wifi Reset

When flashing using platform.io on linux make sure to follow these instructions. [PlatformIO udev rules documentation](http://docs.platformio.org/en/latest/faq.html#platformio-udev-rules)