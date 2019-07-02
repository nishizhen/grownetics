<?php
/**
 * @var \App\Model\Entity\param[]|\Cake\Collection\CollectionInterface $params
 * @var \App\Model\Entity\Sensor[]|\Cake\Collection\CollectionInterface $sensors
 */
?>
<div class="ui search normal selection dropdown sensorMenu">
  <input type="hidden" name="source">
  <i class="dropdown icon"></i>
  <div class="default text">Select a sensor or param...</div>
  <div class="menu">
    <?php foreach ($argus_parameters as $param): ?>
      <div class="item" data-sourceId="<?=$param->argus_parameter_id?>">
          <?=$param->argus_parameter_id . ' - ' . $param->label; ?>
      </div>
    <?php endforeach; ?>
  </div>
 </div>
