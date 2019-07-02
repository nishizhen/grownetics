<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatchLog[]|\Cake\Collection\CollectionInterface $harvestBatchLogs
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Harvest Batch Log'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="harvestBatchLogs index col-lg-9 col-md-8 columns content">
    <h3><?= __('Harvest Batch Logs') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('label') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('zone_id') ?></th>
                <th><?= $this->Paginator->sort('batch_id') ?></th>
                <th><?= $this->Paginator->sort('entry_date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($harvestBatchLogs as $harvestBatchLog): ?>
            <tr>
                <td><?= $this->Number->format($harvestBatchLog->id) ?></td>
                <td><?= h($harvestBatchLog->label) ?></td>
                <td><?= h($harvestBatchLog->created) ?></td>
                <td><?= h($harvestBatchLog->modified) ?></td>
                <td><?= $harvestBatchLog->has('zone') ? $this->Html->link($harvestBatchLog->zone->id, ['controller' => 'Zones', 'action' => 'view', $harvestBatchLog->zone->id]) : '' ?></td>
                <td><?= h($harvestBatchLog->batch_id) ?></td>
                <td><?= h($harvestBatchLog->entry_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $harvestBatchLog->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $harvestBatchLog->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $harvestBatchLog->id], ['confirm' => __('Are you sure you want to delete # {0}?', $harvestBatchLog->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
