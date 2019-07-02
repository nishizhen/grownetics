# GrowServer

## API Structure

Device Request to Server

```
/api/raw?q={\"id\":4,\"v\":\"1.0.13\",\"st\":1,\"m\":3374,\"d\":\"[M1:34.56-91.76],[M2:33.21-91.76],[A3:451],[A4:311.0]\"}
```

```
*id - device id
*v - version of middleware software running on device
*st - status
*m - free memory on arduino
*d - data from sensors
*[M1:34.56-91.76],[M2:33.21-91.76],[A3:451],[A4:311.0]
*M = Multiplexer. So this is the data response from a multiplexed sensor. The 2 shown in the example happen to be Humidity/Temp data from our HIH-3160 Sensor.
*A = Analog. This is an analog sensor.
```

To get the data result above, a device would need 2 Humidity Sensors on M1 and M2, and 2 analog sensors like CTs on A3 and A4.

Response from Server

`{"outs":["2","3"]}` - Turn on pins 2 and 3, and turn off all other pins.

`{}` - Do nothing. (Turn everything off if anything was on)


## Manual database updates

These are the steps required to perform a database update to a live production server.

* [ ] Create fresh backup of target host DB

```
docker exec onsite_appdb_1 /usr/bin/mysqldump -u grownetics --password=aa2ca391ae63867511905c2edd839 grownetics > /var/data/backups/appdb-$(date +\%F).sql
```

* [ ] Download backup
* [ ] Import backup to local setup
* [ ] Make needed changes, verify everything works as expected locally
* [ ] Export local database to new .sql file
* [ ] Upload new .sql file
* [ ] Have the restore command ready to go before importing on the new server

### Note on bugs that come up in staging
If there is a bug that a customer will see if we push from staging this bug should be tagged a blocker. Top priority prior to public roll out is to test staging to see if we can find blockers. Blockers should be all dev's top priority for a timely public release.

## Testing Environments

### [Rancher](http://rancher.cropcircle.io:8080/)

Review apps are deployed here. Links to them appear on the relevant Merge Request in GitLab.

### [QA](http://qa.cropcircle.io)

Always has the latest master, fresh DB with each deploy. This is the server we (will) run continuous load tests against.

### [Staging](http://staging.cropcircle.io)

Also always latest master, no new fresh DB with each deploy. DB gets migrated like on-site and web installs.

### [Demo](http://demo.cropcircle.io)

Gets releases pushed to it before production, for final testing and early feature previews for clients.

### Cleaning local branches

`git branch --merged master --no-color | grep -v master | grep -v stable | xargs git branch -d` will remove all local branches that have been merged into master.

### Ignoring permission changes

`git config core.filemode false`

## ACLs

https://docs.google.com/spreadsheets/d/1PpH7cpXojQVgYYv0iSVKGa6PlODaxfzZvVaWAAOwNck/edit#gid=0

This sheet shows which controller / actions different roles are allowed to access. Changes to this sheet should be carried out as changes to the AclsSeed, RolesSeed, and AclRolesSeed files.

## Working with CakePHP

### Automated Testing with Selenium

To run our tests simply run `growctl test`. This will spin up the docker stack, and execute the tests necessary.

You can connect to the Chrome instance by connecting to VNC at localhost:5900 with password `secret`. More information on this is available in the [Selenium Documentation on Debugging](https://github.com/SeleniumHQ/docker-selenium#debugging)

On macOS, the pre-installed Screen Sharing app is a fine VNC client.

## Admin Access

### Impersonating Users

When logged in, click Settings -> Users, then next to any User you can click 'Impersonate'.

This will log you in as them and let you see their dashboard, tasks, etc.

### Adding Sensors Manually

If you need to add a sensor manually, navigate to `/sensors/add` and select the correct sensor type and Device.

This will ensure that the sensor gets a `MapItem` associated with it in the same position as the chosen device.
 
Directly adding Sensors to the database without an associated map item means it WILL NOT be displayed on the map.


## Working with CakePHP

### Style Guide

#### Model Names

 * *Label* - Use label rather than 'name' or 'title' or anything else. Whenever you want to display a label (name) for any object, you know you can access the `object.label` field.

### Action links

When a link performs an action, such as a delete, always use `$this->Form->postLink(__('Delete Recipe'), ['action' => 'delete', $recipe->id], ['confirm' => __('Are you sure you want to delete # {0}?', $recipe->id)]),` or similar to create action buttons, rather than html->links which can be triggered inadvertantly.

### Displaying Dates

`$entity->created->i18nFormat(null, env('TIMEZONE'))`

### SystemEventRecorder

This library is used to record various events to our time-series database. We log this data and use it for debugging and analytics purposes. Anything the system does should be logged as a `system_event` anything the user does should be logged as a `user_action`.

As a general rule, the more information the better. It's better to log things we end up not needing, than to not log enough and need it after the fact.

#### Example Use:
```
        $recorder = new SystemEventRecorder();
        $recorder->recordEvent(
            'system_events', # Either 'system_events' or 'user_actions'.
            'toggle_output', # Event name. This should be unique for each event type.
            1, # Value. If this is for event logging, use 1 as the value, as 1 event happened. This way we can then chart event counts with a simple count() query.
            [
                # This section is the various field_keys.
                # Here we should log pretty much every related system ID or status related to the event.

                'output_id' => $output->id,
                'pre_toggle_status' => $output->status,
                'post_toggle_status' => $this->enumValueToKey('status','Enabled'),
                'notification_level' => $ruleAction->notification_level,
                'rule_id' => $ruleAction->rule_id,
                'rule_action_id' => $ruleAction->id
            ]
        );
```


### NotifierBehavior

Every Table that uses the Notifier behavior can set the following entity values before save to have those values carry over to the notification.

`$entity->notifier_source_type = $this->Notifications->enumValueToKey('source_type','HarvestBatch');`

To have the save call not create a notification:

`$entity->dontNotify = true`

### DataConverter

Use this tool to convert data to different measurements e.g. convert humidity and temperature data to vapor pressure deficit. For future conversions, add a method to this tool.

Include the tool

`use App\Lib\DataConverter;`

To convert data:

`$converter = new DataConverter();
 $vaporPressureDeficit = $converter->convertToVaporPressureDeficit($humAverage, $airTempAverage);`

### Generating Code Coverage

`~/Code/Grownetics/Server $ ./coverage.sh` then navigate here: http://growserver.dev/coverage/index.html

### Database Reset
The following command will reset your database to a fresh state `growctl reset`

### Database Export

Sometimes you need to migrate some data by hand from one place to another. When that need arises you can make a dump with `docker exec onsite_appdb_1 /usr/bin/mysqldump -u grownetics --password grownetics > /var/data/backups/appdb-$(date +\%F).sql`

To export an InfluxDB databse:
  1. `docker exec -it growserver_influxdb_1 bash` then run `influxd backup -database <mydatabase> /influx_backups`

  2. To move the folder to your local machine, exit the container and run `docker cp growserver_influxdb_1:/influx_backups /influx_backups`

### Database Restore

To import an sql file like the one generated above, just run `docker exec -i onsite_appdb_1 mysql -ugrownetics --password grownetics < /var/data/backups/appdb-2018-04-13.sql`

To restore from a backup in InfluxDB:
  1. Copy the backup folder from your machine into the InfluxDB container:
  `docker cp my_backup_folder growserver_influxdb_1:/my_backup_folder`

  2. `docker exec -it growserver_influxdb_1 bash` and then run `influxd restore -metadir /var/lib/influxdb/meta /my_backup_folder`

  3. Now the metastore is restored, time to restore the data `influxd restore -database my_database -datadir /var/lib/influxdb/data /my_backup_folder`

  4. The new shards might not have the correct permissions so run `sudo chown -R influxdb:influxdb /var/lib/influxdb`

### Clear the cache
A few areas of Growdash is cached (Sensor data, map items on the dash) and changing an Entity related to these features will not load unless the cache is cleared. To clear Growdash's cache run:
`docker exec -it growserver_growdash_1 bin/cake cache clear_all`
When working with Entities in the cache, remember to clear the cache keys related to that Entity in it's lifecycle callbacks like:
`public function afterSave($event, $entity, $options) {
    Cache::delete('floorplan_map_items_json_decoded');
    Cache::delete('floorplan_map_items');
}`

### Code Generation
To bake all the CRUD needed for our new Widgets database table, first create the table using MySQL Workbench in your Vagrant database, then run the following commands.

`vagrant ssh
cd /var/www/GrowServer
bin/cake bake all Widgets`

Further reading, to learn how to edit these bake templates: http://book.cakephp.org/3.0/en/bake/development.html#creating-a-bake-theme

### Environment Config Variables
You need to update or add any changes to environment config variables (like $_SERVER['FACILITY_NAME']) in THREE places:

puphpet/config.yaml - This updates the actual apache running in vagrant.

puphpet/files/dot/.bash_profile - This updates the bash profile so that the socket shell script works, and other cake calls work, from the command line

.gitlab-ci.yml - This is so the GitLab CI can run.

### Don't re-invent the wheel

#### Naming Models, Tables, and Fields

label : A short string describing a record or object, suitable for display in a compact view or title on a dashboard chart.

description : A longer user-supplied field big enough to contain several sentences or paragraphs containing additional details about the record or object.

### Layout / Design

Look in the Dashgum folder for pre-made layout elements and classes you should make use of.

### CakePHP functionality

https://github.com/FriendsOfCake/awesome-cakephp
