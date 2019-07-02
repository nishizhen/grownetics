<?php
/**
 * @var \App\Model\Entity\Sensor[]|\Cake\Collection\CollectionInterface $sensors
 * @var \App\Model\Entity\Zone[]|\Cake\Collection\CollectionInterface $zones
 */
?>
  <div class="dataSelectorSmall">
<span id="dataOptions">Options:</span>

<div class="ui search normal selection dropdown sensorMenuSmall">
  <input type="hidden" name="source">
  <i class="dropdown icon"></i>
  <div class="default text">Select a sensor or zone...</div>
  <div class="menu" id="dataSourceMenu">
      <?php foreach ($zones as $zone): ?>
      <div class="item" href="#" data-sourceId="<?=$zone->id?>" data-sourceType="1">
          <?= $zone->label; ?>
      </div>
      <?php endforeach; ?>
      <?php foreach ($sensors as $sensor):?>
          <div class="item" data-sourceId="<?=$sensor->id?>" data-dataLabel="<?=$this->Enum->EnumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id);?>" data-dataSymbol="<?=$this->Enum->EnumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id)?>" data-displayClass="<?=$this->Enum->EnumKeyToValue('Sensors', 'sensor_display_class', $sensor->sensor_type_id)?>" data-sourceDataType="<?=$sensor->sensor_type_id?>" data-sourceType="0">
              <?= $sensor->label.' - '."<i class='".$this->Enum->EnumKeyToValue('Sensors', 'sensor_display_class', $sensor->sensor_type_id)."'></i> ".$this->Enum->EnumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id); ?>
          </div>
      <?php endforeach; ?>
  </div>
 </div>
</div>

