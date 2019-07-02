<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatchLog $harvestBatchLog
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Harvest Batch Log'), ['action' => 'edit', $harvestBatchLog->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Harvest Batch Log'), ['action' => 'delete', $harvestBatchLog->id], ['confirm' => __('Are you sure you want to delete # {0}?', $harvestBatchLog->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Harvest Batch Logs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Harvest Batch Log'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="harvestBatchLogs view col-lg-9 col-md-8 columns content">
    <h3><?= h($harvestBatchLog->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Label') ?></th>
            <td><?= h($harvestBatchLog->label) ?></td>
        </tr>
        <tr>
            <th><?= __('Zone') ?></th>
            <td><?= $harvestBatchLog->has('zone') ? $this->Html->link($harvestBatchLog->zone->id, ['controller' => 'Zones', 'action' => 'view', $harvestBatchLog->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Batch Id') ?></th>
            <td><?= h($harvestBatchLog->batch_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Entry Date') ?></th>
            <td><?= h($harvestBatchLog->entry_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($harvestBatchLog->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($harvestBatchLog->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($harvestBatchLog->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($harvestBatchLog->description)); ?>
    </div>
</div>
