<?php
/**
 * @var \App\Model\Entity\Zone[]|\Cake\Collection\CollectionInterface $zones
 * @var \App\Model\Entity\Sensor[]|\Cake\Collection\CollectionInterface $sensors
 */
?>
<script>
<?php if (isset($batch_id)) { ?>
  var batch_id = <?=$batch_id?>;
<?php } else { ?>
  var batch_id = null;
<?php } ?>
<?php if (isset($tasks)) { ?>
  var tasks = <?=json_encode($tasks)?>;
<?php } else { ?>
  var tasks = null;
<?php } ?>
</script>
<div class="ui search normal selection dropdown sensorMenu">
  <input type="hidden" name="source">
  <i class="dropdown icon"></i>
  <div class="default text">Select a harvest batch...</div>
  <div class="menu">
    <?php foreach ($harvestBatches as $batch): ?>
      <div class="item" data-value="<?=$batch->id?>" data-sourceId="<?=$batch->id?>" data-sourceType="<?=$this->Enum->enumValueToKey("DataPoints", "source_type", 'Harvest Batch')?>">
          <?php echo '#'.$batch->batch_number.' '.$batch->cultivar->label.' - '.$batch->planted_date; ?>
      </div>
    <?php endforeach; ?>
  </div>
 </div>
