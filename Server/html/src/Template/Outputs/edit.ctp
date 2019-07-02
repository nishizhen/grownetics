<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Output $output
 */
?>
<div class="outputs form mt">
    <?= $this->Form->create($output,['templateVars'=>['header'=>'Edit Hardware']]) ?>
        <?php
            $options = $this->Enum->selectValues('Outputs', 'status');
            echo $this->Form->input('status',['options'=>$options]);
            echo $this->Form->input('label');
            echo $this->Form->input('output_target');
            $options = $this->Enum->selectValues('Outputs', 'output_type');
            echo $this->Form->input('output_type',['options'=>$options]);
            $options = $this->Enum->selectValues('Outputs', 'hardware_type');
            echo $this->Form->input('hardware_type',['options'=>$options]);
            echo $this->Form->input('device_id', ['options' => $devices, 'empty' => true]);
        ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
<?php
$this->Form->resetTemplates();
 ?>
<?php

echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
    $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $output->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $output->id)]
            ),
    $this->Html->link(__('List Outputs'), ['action' => 'index'])
        
]])?>