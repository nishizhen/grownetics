# Support

## Common Problems and Solutions

### Lighting/Co2 schedule adjustment

`sshuttle -r  root@159.65.180.181 0/0` to access Tinc server.
In a new terminal, `ssh grownetics@10.10.4.7/6` to access DR 1/2 server.
Once in, `sudo su` and `docker exec -it onsite_appdb_1 bash` to access the container.
`mysql -pgrownetics` and `use grownetics;` to get started changing the database.

#### Disable a rule
Simply set the Rule's status = 0;

#### Change a Timed Rule's thresholds
Timed Rules are based on UTC and thresholds are counted in seconds. MST/MDT is +6/5 hours from midnight UTC, EST/EDT is +8/7 hours. Check the 'operator' column and Rule Action 'type' column to figure out if the lights will trigger on/off at the trigger threshold.
Operator = '>', Rule Action type = 'Turn Off', then once the time is > trigger threshold the light will 'Turn Off'.

E.g. All Flower Rooms Zone W Rule: 'Turn Off' lights at 60 seconds after UTC midnight (6:01pm MST) and reset at 50400 seconds after UTC midnight (8:00am MST) so lights will Turn On then.

#### Turn off half the lights in a Room, keep the other half on schedule
First check the client's Hardware page (www.growserver.co/outputs) and toggle the outputs so that 1 zone is On and the other is Off, be consitent with other Zones and make note of the output's ID  (NOT the 'Output Target', that's the pin for the Arduino to toggle). Find the associated rule action target by querying `select * from rule_action_targets where target_id = <output_id>;`. From the results, make sure you find the rule action target associated with the Timed Rule Condition for that Zone, (there may be other rule action targets associated with alerts which we don't want to change here). Once found, set the rule action target's status = 0 to disable it.

#### Pre/post checks when changing Rules that will toggle Outputs.
Always check the Hardware page before and after to understand what is currently toggled On/Off.
Make sure the Device controlling the Output is actively sending messages to the server in < 20 second intervals.
Check at a later time that the Rule you changed behaves correctly when it's about to be triggered or reset.

### Too many notifications going out from a Facility

Login, click `Settings` -> `System Log`. Then click `Disable` and
accept the confirmation when prompted. Now the flow of notifications
going out is stopped, and you can also `Clear Queue` now if desired.

### Server out of disk space

`df -h` is a nice way to get a listing of the various disks on the system and how full they are.
You will generally only be concerned with the `/` mount however. This should ideally
be below 50%. If it's higher, you need to start clearing out some space.

`du -sh /*` will show you disk usage, pass it to grep to look for directories larger
than a gig `du -sh /* | grep G`. There are 2 'usual suspects' though, easy places to start.

/var/log/syslog* can fill up fast with a bad config setting. You can safely remove
any of the `.` files with `rm /var/log/syslog.*`, but you do NOT want to remove
the main syslog file itself. If you do, syslogd is still writing to it, but it
no longer appears to you as a file, so it will fill up in the background with no
clean way to stop it. Instead you want to truncate the file, which is as easy
as `> /var/log/syslog`.

The next place is the docker container logs themselves, found in
`/var/lib/docker/containers`. `du -sh /var/lib/docker/containers/* | grep G` will
show you which folders are larger than a gig. Then you want to truncate the log
files in the the same as with the syslog. If the output of the previous command
looks like `9.1G	/var/lib/docker/containers/e95a76e32a87d13ce5e34308a72934df9ee366a9d24d63bd2171907b7644ad59`
then all you need to do is
`> /var/lib/docker/containers/e95a76e32a87d13ce5e34308a72934df9ee366a9d24d63bd2171907b7644ad59/e95a76e32a87d13ce5e34308a72934df9ee366a9d24d63bd2171907b7644ad59-json.log`

Now a look at `df -h` again should show a much lowered disk use rate. If not,
`du -sh /*` will lead you in the right direction.