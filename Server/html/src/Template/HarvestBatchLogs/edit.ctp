<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatchLog $harvestBatchLog
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $harvestBatchLog->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $harvestBatchLog->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Harvest Batch Logs'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="harvestBatchLogs form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($harvestBatchLog) ?>
    <fieldset>
        <legend><?= __('Edit Harvest Batch Log') ?></legend>
        <?php
            echo $this->Form->input('label');
            echo $this->Form->input('description');
            echo $this->Form->input('zone_id', ['options' => $zones]);
            echo $this->Form->input('batch_id');
            echo $this->Form->input('entry_date');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
