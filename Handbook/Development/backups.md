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
