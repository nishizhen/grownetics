<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="devices form row mt">
	<?=$this->Form->create('Device',['templateVars'=>['header'=>'Add Device']]);?>
	<?=$this->Form->input('label');?>
	<?=$this->Form->input('refresh_rate');?>
    <div class="form-group" style="border: 0px !important; padding: 0px !important; -webkit-box-shadow: none !important;">
        <label class="col-lg-2">Device Type</label>
        <?php
        echo $this->Form->select('type', $this->Enum->selectValues('Devices', 'type'));?>
    </div>
	<?=$this->Form->submit('Submit');?>
	<?=$this->Form->end(); ?>
</div>