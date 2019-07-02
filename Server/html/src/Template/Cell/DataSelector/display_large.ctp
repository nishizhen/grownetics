<?php
/**
 * @var \App\Model\Entity\Zone[]|\Cake\Collection\CollectionInterface $zones
 * @var \App\Model\Entity\Sensor[]|\Cake\Collection\CollectionInterface $sensors
 */
?>
<div class="ui search normal selection dropdown sensorMenu">
  <input type="hidden" name="source">
  <i class="dropdown icon"></i>
  <div class="default text">Select a sensor or zone...</div>
  <div class="menu">
    <?php foreach ($zones as $zone): ?>
      <div class="item" data-sourceId="<?=$zone->id?>" data-sourceType="1">
          <?php echo $zone->label; ?>
      </div>
    <?php endforeach; ?>
    <?php foreach ($sensors as $sensor): ?>
      <div class="item" data-sourceId="<?=$sensor->id?>" data-dataType="<?=$sensor->sensor_type_id?>" data-dataLabel="<?=$this->Enum->EnumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id)?>" data-dataSymbol="<?php
      if ($showMetric == true) {
          echo $this->Enum->EnumKeyToValue('Sensors', 'sensor_metric_symbols', $sensor->sensor_type_id);
      } else {
          echo $this->Enum->EnumKeyToValue('Sensors', 'sensor_symbols', $sensor->sensor_type_id);
      }
      ?>" data-sourceType="0">
        <?= $sensor->label.' - '."<i class='".$this->Enum->EnumKeyToValue('Sensors', 'sensor_display_class', $sensor->sensor_type_id)."'></i> ".$this->Enum->EnumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id).' '. ( $sensor->map_item->offsetHeight == 1 ? 'High' : 'Low' ); ?>
      </div>
    <?php endforeach; ?>
  </div>
 </div>
