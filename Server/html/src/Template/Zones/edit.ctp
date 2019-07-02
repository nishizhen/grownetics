<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Zone $zone
 */
?>
<div class="zones form row mt">
    <?= $this->Form->create($zone,['templateVars'=>['header'=>'Edit Zone']]);?>
        
        <?php
        echo $this->Form->input('label');
        ?>
        <div class='form-group'>
        <?php
        echo $this->Form->label('status');
        echo $this->Form->select('status', $this->Enum->selectValues('Zones','status'),
        array(
            'class'=>'form-control',
            'id'=>'RuleDataSource',
            'empty' => '(choose one)'
        ));
        ?>
        </div>
        <?php
        echo $this->Form->input('plant_zone_type_id', ['options' => $plant_zone_types]);
        ?>
    <?= $this->Form->submit() ?>
    <?= $this->Form->end() ?>
</div>
