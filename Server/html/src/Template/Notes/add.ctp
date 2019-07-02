<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Note $note
 */
?>
<div class="notes form large-9 medium-8 columns content">
    <?= $this->Form->create($note,['templateVars'=>['header'=>'note']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('batch_id');
            echo $this->Form->input('note');
            echo $this->Form->input('deleted');
            echo $this->Form->input('cultivar_id', ['options' => $cultivars, 'empty' => true]);
            echo $this->Form->input('zone_id', ['options' => $zones, 'empty' => true]);
            echo $this->Form->input('plants._ids', ['options' => $plants]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
