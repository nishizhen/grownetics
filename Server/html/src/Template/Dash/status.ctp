<h1>Automation System Diagnostics</h1>
<?php $devicesPct = round($workingDevices/$totalDevices*100); ?>
<div class="row">

    <div class="col-md-4 col-sm-4 mb">
        <div class="green-panel pn">
            <div class="green-header">
                <h5>RabbitMQ Status</h5>
            </div>

            <?php if ($messageStats && isset($messageStats->messages)) { ?>
                <h3><?=$messageStats->messages?> messages</h3>
                <?php if (isset($messageStats->message_stats)) { ?>
                    <h3><?=$messageStats->message_stats->publish_details->rate?>/s publish rate</h3>
                    <h3><?=$messageStats->message_stats->confirm_details->rate?>/s confirm rate</h3>
                <?php } ?>
            <?php } else { ?>
                RabbitMQ status unavailable
            <?php } ?>
        </div>
    </div>

    <div class="col-md-4 col-sm-4 mb">
        <div class="darkblue-panel pn">
            <div class="darkblue-header">
                <h5>DEVICE STATUS - <?=$devicesPct?>%</h5>
            </div>
            <footer>
                <div class="pull-left">
                    <h5><i class="fa fa-hdd-o"></i> <?=$totalDevices?> Total</h5>
                </div>
                <div class="pull-right">
                    <h5><?=$totalDevices-$workingDevices?> Faulty</h5>
                </div>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </footer>
            <p>&nbsp;</p><p>&nbsp;</p>
        </div><!-- -- /darkblue panel ---->
    </div><!-- /col-md-4 -->

    <div class="col-md-4 col-sm-4 mb">
        <div class="darkblue-panel pn">
            <div class="darkblue-header">
                <h5>SYSTEM STATUS</h5>
            </div>
            <footer>
                <div class="pull-left">
                    <h5><i class="fa fa-hdd-o"></i> HDD Space Remaining</h5>
                    <h5><i class="fa fa-database"></i> App DB Accessible</h5>
                    <h5><i class="fa fa-heartbeat"></i> Growpulse Running</h5>
                    <h5><i class="fa fa-fire"></i> High Temp Shutdowns</h5>
                    <h5><i class="fa fa-spinner"></i> Device Boot Rate</h5>
                    <h5><i class="fa fa-download"></i> Data Received Rate</h5>
                    <h5><i class="fa fa-building"></i> BACnet Link Online</h5>
                    <h5><i class="fa fa-bolt"></i> Power Overrides Detected</h5>
                    <h5><i class="fa fa-table"></i> Rules Alerting</h5>
                    <h5><i class="fa fa-clock-o"></i> Output Schedules</h5>
                    <h5><i class="fa fa-exclamation-triangle"></i> Notifications</h5>
                    <h5><i class="fa fa-bolt"></i> Power Panels</h5>
                </div>
                <div class="pull-right">
                    <h5><span class="label label-<?=($hdd?"success":"danger")?> label-mini"><?=($hdd?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($appdb?"success":"danger")?> label-mini"><?=($appdb?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($growpulse?"success":"danger")?> label-mini"><?=($growpulse?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($highTempShutdown?"success":"danger")?> label-mini"><?=($highTempShutdown?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($deviceBoots?"success":"danger")?> label-mini"><?=($deviceBoots?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($dataReceived?"success":"danger")?> label-mini"><?=($dataReceived?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($bacnetUpdates?(env('BACNET_ENABLED')?"success":"warning"):"danger")?> label-mini"><?=($bacnetUpdates?(env('BACNET_ENABLED')?"OK":"Disabled"):"Error!")?></span></h5>
                    <h5><span class="label label-<?=($overrides?"success":"danger")?> label-mini"><?=($overrides?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($ruleAlerts?"success":"danger")?> label-mini"><?=($ruleAlerts?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($outputSchedules?"success":"danger")?> label-mini"><?=($outputSchedules?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($notifications?"success":"danger")?> label-mini"><?=($notifications?"OK":"Error!")?></span></h5>
                    <h5><span class="label label-<?=($powerPanels?"success":"danger")?> label-mini"><?=($powerPanels?"OK":"Error!")?></span></h5>
                </div>
                <p>&nbsp;</p><p>&nbsp;</p>
            </footer>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
        </div><!-- -- /darkblue panel ---->
    </div><!-- /col-md-4 -->


</div>