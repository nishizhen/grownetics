<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rule $rule
 */
?>
<?php $this->Html->script('rules/form', ['block' => 'scriptBottom']); ?>

<div class="rules form row mt">
    <?= $this->Form->create($rule,[
        'class'=>'style-form form-horizontal',
        'templateVars'=>['header'=>'Add Rule']
    ]) ?>
    <?php
        echo $this->Form->input('label');
    ?>
    <?=$this->Form->hidden('status', ['value' => 1]);?>
    <br />
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
