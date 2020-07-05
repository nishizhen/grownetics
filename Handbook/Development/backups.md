We use Restic to handle backups and restoration of all onsite server data.

Restic is installed into our base php docker image.

Cron jobs make backups nightly, over writing the same files.

We will use the restic's [snapshot removal policies](https://restic.readthedocs.io/en/stable/060_forget.html#removing-snapshots-according-to-a-policy) to perform our rollups.

`restic forget --keep-daily 7 --keep-weekly 5 --keep-monthly 12 --keep-yearly 75`

# Make backup

This is the manual command to run a backup.

```
export AWS_ACCESS_KEY_ID=KEY
export AWS_SECRET_ACCESS_KEY="KEY"
export RESTIC_REPOSITORY="s3:https://s3.amazonaws.com/grownetics-backups"
export RESTIC_PASSWORD="KEY"
restic backup /var/data/backups/
```

# Restore most recent backup

This command can be run on a server to restore the latest backup, but should never be used on hot servers.

```
restic restore -H "dr1" -i "appdb.sql" latest -t /
mysql -happdb -ugrownetics -pgrownetics grownetics < /var/data/backups/appdb.sql
```

During development, there is a Backup Restore page under the Admin section of the navigation.

`docker exec $(docker ps -q --filter name=influxdb) /usr/bin/influxd backup -database user_actions -retention autogen /tmp/backups/user_actions && docker cp $(docker ps -q --filter name=influxdb):/tmp/backups/user_actions /var/data/backups/influxdb/user_actions`

influxd restore -db user_actions -host tick_influxdb_1 tmp/backups/var/data/backups/influxdb/user_actions

influxd restore -host tick_influxdb_1 -metadir /var/lib/influxdb/meta -datadir /var/lib/influxdb/data/ /var/www/html/tmp/backups/var/data/backups/influxdb/user_actions/user_actions/

influxd restore -host tick_influxdb_1 -metadir /var/lib/influxdb/meta -datadir /var/lib/influxdb/data/ /var/www/html/tmp/backups/var/data/backups/influxdb/system_events/system_events/
