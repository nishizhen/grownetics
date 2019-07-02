# Hardware Burnout Protection

Some lights can burn out if they are turned on and off too quickly.

So, if a control device reboots, we disable all the outputs on that
device for 10 minutes. Once the device has been running steadily for 10
minutes, outputs will be re-enabled on the device, and the lights will
come back on.