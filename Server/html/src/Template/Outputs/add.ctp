<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Output $output
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Outputs'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Devices'), ['controller' => 'Devices', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Device'), ['controller' => 'Devices', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Rules'), ['controller' => 'Rules', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Rule'), ['controller' => 'Rules', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="outputs form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($output) ?>
    <fieldset>
        <legend><?= __('Add Hardware') ?></legend>
        <?php
            echo $this->Form->input('status');
            echo $this->Form->input('label');
            echo $this->Form->input('output_target');
            echo $this->Form->input('output_type');
            echo $this->Form->input('device_id', ['options' => $devices, 'empty' => true]);
            echo $this->Form->input('zone_id', ['options' => $zones, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
