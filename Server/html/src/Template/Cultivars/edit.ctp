<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cultivar $cultivar
 */
?>
<div class="cultivars form mt">
    <?= $this->Form->create($cultivar, [
        'templateVars'=>['header'=>'Edit Cultivar'],
        'type' => 'file'
        ]);
        echo $this->Form->input('label');
        echo $this->Form->input('description');
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>