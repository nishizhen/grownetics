<?php
namespace App\Lib;

use Cake\Cache\Cache;

class BackupImporter {

    public function getHostList() {
        $output = [];
        $command = "AWS_SECRET_ACCESS_KEY=".env('AWS_SECRET_ACCESS_KEY')." AWS_ACCESS_KEY_ID=\"".env('AWS_ACCESS_KEY_ID')."\" RESTIC_REPOSITORY=".env('RESTIC_REPOSITORY')." RESTIC_PASSWORD=\"".env('RESTIC_PASSWORD')."\" restic snapshots --json --last";
        exec($command,$output,$returnVar);
        return json_decode($output[0]);
    }

    public function restoreLatestFromHost($hostname) {
        $output = [];

        # Restore the sql file from restic.
        $command = "AWS_SECRET_ACCESS_KEY=".env('AWS_SECRET_ACCESS_KEY')." AWS_ACCESS_KEY_ID=\"".env('AWS_ACCESS_KEY_ID')."\" RESTIC_REPOSITORY=".env('RESTIC_REPOSITORY')." RESTIC_PASSWORD=\"".env('RESTIC_PASSWORD')."\" restic -r s3:s3.amazonaws.com/grownetics-backups restore -H \"" . $hostname . "\" -i \"appdb.sql\" latest -t /var/www/html/tmp/backups 2>&1";
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