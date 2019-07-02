<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchNote $batchNote
 */
?>
<div class="batchNotes form large-9 medium-8 columns content">
    <?= $this->Form->create($batchNote,['templateVars'=>['header'=>'batchNote']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('harvest_batch_id', ['options' => $harvestBatches]);
            echo $this->Form->input('note');
            echo $this->Form->input('deleted');
            echo $this->Form->input('deleted_date', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
