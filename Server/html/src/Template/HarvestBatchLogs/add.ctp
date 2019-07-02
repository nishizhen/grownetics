<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatchLog $harvestBatchLog
 */
?>
<div class="harvestBatchLogs form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($harvestBatchLog) ?>
    <fieldset>
        <legend><?= __('Add Harvest Batch Log') ?></legend>
        <?php
            echo $this->Form->input('label');
            echo $this->Form->input('description');
            echo $this->Form->input('zone_id', ['options' => $zones]);
            echo $this->Form->input('batch_id', ['options' => $harvestbatches]);
            echo $this->Form->input('entry_date');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
