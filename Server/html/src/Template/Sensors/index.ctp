<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sensor[]|\Cake\Collection\CollectionInterface $sensors
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Sensor'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Devices'), ['controller' => 'Devices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Device'), ['controller' => 'Devices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="sensors index col-lg-9 col-md-8 columns content">
    <h3><?= __('Sensors') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('sensor_type_id') ?></th>
                <th><?= $this->Paginator->sort('sensor_pin') ?></th>
                <th><?= $this->Paginator->sort('device_id') ?></th>
                <th><?= $this->Paginator->sort('label') ?></th>
                <th><?= $this->Paginator->sort('zone_id') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th><?= $this->Paginator->sort('last_good_data_time') ?></th>
                <th><?= $this->Paginator->sort('last_good_data') ?></th>
                <th><?= $this->Paginator->sort('calibration') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?=$this->Form->resetTemplates();?>
            <?php foreach ($sensors as $sensor): ?>
            <tr>
                <td><?= h($this->Enum->enumKeyToValue('Sensors', 'sensor_type', $sensor->sensor_type_id)) ?> </td>
                <td><?= h($sensor->sensor_pin) ?></td>
                <td><?= $sensor->has('device') ? $this->Html->link($sensor->device->label, ['controller' => 'Devices', 'action' => 'view', $sensor->device->id]) : '' ?></td>
                <td><?= h($sensor->label) ?></td>
                <td><?= $sensor->has('zone') ? $this->Html->link($sensor->zone->label, ['controller' => 'Zones', 'action' => 'view', $sensor->zone->id]) : '' ?></td>
                <td>
                <?php
                    switch ($this->Enum->enumKeyToValue('Sensors', 'status', $sensor->status)) {
                        case 'Disabled': ?>
                        <span class="label label-danger label-mini">Disabled</span>
                        <?php   break;
                        case 'Enabled': ?>
                        <span class="label label-success label-mini">Enabled</span>
                        <?php   break;
                        case 'Powered': ?>
                        <span class="label label-warning label-mini">Rebooting</span>
                        <?php   break;
                        case 'Errored': ?>
                        <span class="label label-success label-mini">Active</span>
                        <?php   break;
                    }
                ?>
                </td>
                <td><?= h($sensor->last_good_data_time) ?></td>
                <td><?php if (h($sensor->last_good_data)) { echo $this->Converter->displayValue($sensor->last_good_data,  $sensor->sensor_type_id ).$this->Converter->displaySymbol( $sensor->sensor_type_id ); } else { echo ''; }
                    ?>
                </td>
                <td><?= h($sensor->calibration) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $sensor->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sensor->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sensor->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sensor->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
