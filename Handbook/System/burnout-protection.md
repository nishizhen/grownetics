# Burnout Protection

Some lights can burn out if they are turned on and off too quickly.

So, if a control device reboots, we disable all the outputs on that
device for 10 minutes.

The `burnout_protection_time` value on each Device is populated with
the timestamp at time of device boot. If that timestamp
is older than the `BURNOUT_PROTECTION_DELAY` environment variable
(default of 30 seconds in development, 10 minutes in production),
then no outputs are returned.

If a device is outside of the burnout protection time, a
`control_device_returned_outputs` event is recorded.

If a device is in the burnout protection time, a
`control_device_burnout_protected` device is recorded.
