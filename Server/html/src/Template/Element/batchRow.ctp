<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="col-lg-12" style='margin-bottom: 3em;'>
<a href='/batches/view/<?=$batch['HarvestBatch']['id']?>' class='linkWrap'></a>
	<h4><?=$batch['Strain']['name']?></h4>
	<?
	if (!empty($batch['HarvestBatch']['photo'])) {
		$photo = $batch['HarvestBatch']['photo'];
	} elseif (!empty($batch['Strain']['photo'])) {
		$photo = $batch['Strain']['photo'];
	}
	$percent = $this->HarvestBatch->getPercentDone($batch);
	$offset = ($percent < 50) ? 'left: '.$percent.'%;' : 'right: '.(100-$percent).'%;';
	?>
	<div class='moreInfoBox'>
		<div class="col-lg-4 col-md-4 col-sm-4 mb">
				<div class="weather-2 pn">
					<div class="weather-2-header">
						<div class="row">
							<div class="col-sm-6 col-xs-6">
								<p><?=$batch['Strain']['name']?></p>
							</div>
							<div class="col-sm-6 col-xs-6 goright">
								<p class="small">
									<? if ($batch['HarvestBatch']['status']==1) { ?>
									Planted: <?=$batch['HarvestBatch']['planted_date']?>
									<? } elseif ($batch['HarvestBatch']['status']==2) { ?>
									Harvested: <?=$batch['HarvestBatch']['harvest_date']?>
									<? } ?>
									</p>
							</div>
						</div>
					</div><!-- /weather-2 header -->
					<div class="row centered">
						<? if (!empty($batch['HarvestBatch']['photo'])) {
							$photo = $batch['HarvestBatch']['photo'];
						} elseif (!empty($batch['Strain']['photo'])) {
							$photo = $batch['Strain']['photo'];
						} ?>
						<img src='/thumbs/?src=<?=$photo?>&h=150&w=150' class='photo img-circle' height=150 width=150 />
					</div>
					<div class="row data">
						<div class="col-sm-8 col-xs-8 goleft">
							<h4>Price: <b><?=$batch['HarvestBatch']['price']?></b>/oz</h4>
							<h6>
							<? if ($batch['HarvestBatch']['estimated_amount']>0) { ?>
								Estimated Stock: <?=$batch['HarvestBatch']['estimated_amount']?> oz
							<? } elseif ($batch['HarvestBatch']['available_amount']>0) { ?>
								Available: <?=$batch['HarvestBatch']['available_amount']?> oz
							<? } ?>
							</h6>
						</div>
						<div class="col-sm-4 col-xs-4 goright">
							<h5><i class="fa fa-leaf fa-2x"></i></h5>
							<h6><b>Indica</b></h6>
						</div>
					</div>
				</div>
			</div>
		<h5>HarvestBatch Description</h5>
		<p><?=$batch['HarvestBatch']['short_desc']?></p>
		<p><a href='#'>More Info</a></p>
		<a href='/batches/view/<?=$batch['HarvestBatch']['id']?>' class='linkWrap'></a>
	</div><!-- // More info box -->
	<div class='imageWrap'>
		<? if (isset($photo)) { ?>
		<img src='/thumbs/?src=<?=$photo?>&h=50&w=50' class='timelinePhoto' height=50 width=50 style='<?=$offset?>' />
		<? } ?>
	</div>
	<div class="progress progress-striped">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$percent?>%">
            <span class="sr-only"><?=$percent?>% Complete</span>
        </div>
    </div>
</div>
