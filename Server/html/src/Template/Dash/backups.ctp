<h1>Restore Latest Backup from Host</h1>
<?php if (env('DEV')) { ?>
    <?php foreach($hosts as $host) { ?>
        <p><a href="/dash/backups/<?=$host->hostname;?>"><?=$host->hostname;?></a> - <?=substr($host->time,0,10)?></p>
    <?php } ?>
<?php } else { ?>
    Backup restored in non-Development servers.
<?php } ?>