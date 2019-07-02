<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sensor $sensor
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <!-- <li><?= $this->Html->link(__('Edit Sensor'), ['action' => 'edit', $sensor->id]) ?> </li> -->
        <!-- <li><?= $this->Form->postLink(__('Delete Sensor'), ['action' => 'delete', $sensor->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sensor->id)]) ?> </li> -->
        <li><?= $this->Html->link(__('List Sensors'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('List Devices'), ['controller' => 'Devices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Device'), ['controller' => 'Devices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="sensors view col-lg-9 col-md-8 columns content">
    <h3><?= h($sensor->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Sensor Pin') ?></th>
            <td><?= h($sensor->sensor_pin) ?></td>
        </tr>
        <tr>
            <th><?= __('Device') ?></th>
            <td><?= $sensor->has('device') ? $this->Html->link($sensor->device->id, ['controller' => 'Devices', 'action' => 'view', $sensor->device->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Label') ?></th>
            <td><?= h($sensor->label) ?></td>
        </tr>
        <tr>
            <th><?= __('Zone') ?></th>
            <td><?= $sensor->has('zone') ? $this->Html->link($sensor->zone->id, ['controller' => 'Zones', 'action' => 'view', $sensor->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Last Good Data') ?></th>
            <td><?= h($sensor->last_good_data) ?></td>
        </tr>
        <tr>
            <th><?= __('Calibration') ?></th>
            <td><?= h($sensor->calibration) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($sensor->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Sensor Type') ?></th>
            <td><?= h($sensor_type_name) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->Number->format($sensor->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($sensor->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($sensor->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted Date') ?></th>
            <td><?= h($sensor->deleted_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Last Good Data Time') ?></th>
            <td><?= h($sensor->last_good_data_time) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted') ?></th>
            <td><?= $sensor->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
