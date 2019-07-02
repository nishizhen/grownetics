<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cultivar $cultivar
 */
?>
<div class="cultivars form row mt">
    <?= $this->Form->create($cultivar,['templateVars'=>['header'=>'Add Cultivar']]); ?>
    <?php
        echo $this->Form->input('label');
        #echo $this->Form->input('photo');
        echo $this->Form->input('description',['class'=>'form-control']);
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
