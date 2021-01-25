<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Device $device
 */
?>
<div class="devices form row mt">
    <?= $this->Form->create($device,['templateVars'=>['header'=>'Edit Device']]); ?>	
    <?=$this->Form->input('label');?>
    <div class="form-group" style="-webkit-box-shadow: none !important; border-bottom: 1px solid #eff2f7; padding-bottom: 15px; margin-bottom: 15px;">
        <label class="col-lg-2">Device Type</label>
        <?php
        echo $this->Form->select('type', $this->Enum->selectValues('Devices', 'type'));?>
    </div>
	<?=$this->Form->input('api_id',['type'=>'text']);?>
	<?=$this->Form->input('refresh_rate');?>
	<?=$this->Form->input('reboot_rate');?>
	<?=$this->Form->submit('Submit');?>
	<?=$this->Form->end(); ?>
</div>
