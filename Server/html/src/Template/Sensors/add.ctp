<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sensor $sensor
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Sensors'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Devices'), ['controller' => 'Devices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Device'), ['controller' => 'Devices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="sensors form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($sensor) ?>
    <fieldset>
        <legend><?= __('Add Sensor') ?></legend>
            <?php
            echo $this->Form->input('sensor_type_id', array('label'=>'Sensor Type','options'=>$this->Enum->selectValues('Sensors', 'sensor_type'),'default'=>h($sensor->sensor_type_id)));
            echo $this->Form->input('sensor_pin');
            echo $this->Form->input('device_id', ['options' => $devices]);
            echo $this->Form->input('label');
            echo $this->Form->input('zone_id', ['options' => $zones]);
            echo $this->Form->input('status', ['options' => $this->Enum->selectValues('Sensors', 'status')]);
            echo $this->Form->input('calibration');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
