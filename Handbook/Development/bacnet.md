# BACnet Integration

BACnet integration is a critical part of our automation and integration infrastructure.

### Set Points

Currently we communicate with the BACnet device setting a series of Set Points and Read Points. It is up to the HVAC system to get these two points to match.

If we detect that a Read Point is more than a certain percentage (SET_POINT_ALARM_TOLERANCE_PERCENTAGE) away from the Set Point vaule, we send an alarm.

### Bacnet Stack

https://github.com/bacnet-stack/bacnet-stack
Docs: http://bacnet.sourceforge.net/

download zip https://github.com/bacnet-stack/bacnet-stack/archive/master.zip
unzip, in folder `make clean all`
`./apps/readprop/bacrp `

WhoIs All BacNet: `./whois/bacwi -1`

DR:

BACNET_URL=http://10.5.0.3/bacrest/bacnet_device_3011000/analog-value_
