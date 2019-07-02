<?php
/**
  * @var \App\View\AppView $this
  */
?>
<div class="setPoints form large-9 medium-8 columns content">
    <?= $this->Form->create($setPoint,['templateVars'=>['header'=>'setPoint']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('deleted', ['empty' => true]);
            echo $this->Form->input('label');
            echo $this->Form->input('status');
            echo $this->Form->input('value');
            echo $this->Form->input('target_type');
            echo $this->Form->input('target_id');
            echo $this->Form->input('data_type');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
