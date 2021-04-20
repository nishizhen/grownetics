<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Device $device
 */
?>
<div class="devices form row mt">
    <?= $this->Form->create($device,['templateVars'=>['header'=>'Edit Device Zones']]); ?>	
	<?=$this->Form->select('update_zones',$zones,['multiple' => 'checkbox',]);?>
	<?=$this->Form->submit('Submit');?>
	<?=$this->Form->end(); ?>
</div>
