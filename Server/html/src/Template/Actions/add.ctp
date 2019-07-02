<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="actions form">
<?php echo $this->Form->create('Action'); ?>
	<fieldset>
		<legend><?php echo __('Add Action'); ?></legend>
	<?php
		echo $this->Form->input('status');
		echo $this->Form->input('type_id');
		echo $this->Form->input('device_id');
		echo $this->Form->input('zone_id');
		echo $this->Form->input('rule_id');
		echo $this->Form->input('message');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Actions'), array('action' => 'index')); ?></li>
	</ul>
</div>
