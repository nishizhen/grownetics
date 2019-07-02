<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="contracts form">
<?php echo $this->Form->create('Contract'); ?>
	<fieldset>
		<legend><?php echo __('Edit Contract'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('type');
		echo $this->Form->input('text');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Contract.id')), array(), __('Are you sure you want to delete # {0}?', $this->Form->value('Contract.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Contracts'), array('action' => 'index')); ?></li>
	</ul>
</div>
