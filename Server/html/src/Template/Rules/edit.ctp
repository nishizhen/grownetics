<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rule $rule
 */
?>
<?php $this->Html->script('rules/form', ['block' => 'scriptBottom']); ?>

<div class="rules form row mt">
    <?= $this->Form->create($rule,[
        'templateVars'=>['header'=>'Edit Rule']
    ]) ?>
        <?php
            echo $this->Form->input('label');
        ?>
        <div class="form-group">
            <?=$this->Form->label('type');?>
            <?=$this->Form->select('type', $this->Enum->selectValues('Rules','type'));?>
        </div>
        <div class="form-group">
            <?=$this->Form->label('status');?>
            <?=$this->Form->select('status', $this->Enum->selectValues('Rules','status'));?>
        </div>
    <?= $this->Form->submit() ?>
    <?= $this->Form->end() ?>
</div>
