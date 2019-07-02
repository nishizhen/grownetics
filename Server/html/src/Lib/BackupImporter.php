<?php
namespace App\Lib;

use Cake\Cache\Cache;

class BackupImporter {

    public function getHostList() {
        $output = [];
        exec('AWS_SECRET_ACCESS_KEY="9xatb1QGWS1/lcIuyg7iQRVBVEk3fvqjB/3Y7Djb" AWS_ACCESS_KEY_ID=AKIAJFNWE4SGKUTKIOIQ RESTIC_REPOSITORY="s3:https://s3.amazonaws.com/grownetics-backups" RESTIC_PASSWORD="qrbWcxjpugLjp8gxjUJcjLT" restic -r s3:s3.amazonaws.com/grownetics-backups snapshots --json --last',$output,$returnVar);
        return json_decode($output[0]);
    }

    public function restoreLatestFromHost($hostname) {
        $output = [];

        # Restore the sql file from restic.
        $command = 'AWS_SECRET_ACCESS_KEY="9xatb1QGWS1/lcIuyg7iQRVBVEk3fvqjB/3Y7Djb" AWS_ACCESS_KEY_ID=AKIAJFNWE4SGKUTKIOIQ RESTIC_REPOSITORY="s3:https://s3.amazonaws.com/grownetics-backups" RESTIC_PASSWORD="qrbWcxjpugLjp8gxjUJcjLT" restic -r s3:s3.amazonaws.com/grownetics-backups restore -H "' . $hostname . '" -i "appdb.sql" latest -t /var/www/html/tmp/backups 2>&1';
        exec($command,$output,$returnVar);

        if ($returnVar) {
            debug($command);
            debug($output);
            debug($returnVar);
            die();
        }

        # Import the sql file into the DB
        exec('mysql -happdb -ugrownetics -pgrownetics grownetics < /var/www/html/tmp/backups/var/data/backups/appdb.sql 2>&1', $output, $returnVar);

        if ($returnVar) {
            debug($command);
            debug($output);
            debug($returnVar);
            die();
        }

        # Make sure to clear the cache so the new data shows up
        Cache::clear(false);
    }
}