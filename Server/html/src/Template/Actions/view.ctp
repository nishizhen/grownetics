<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="actions view">
<h2><?php echo __('Action'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($action['Action']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($action['Action']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($action['Action']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo h($action['Action']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type Id'); ?></dt>
		<dd>
			<?php echo h($action['Action']['type_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Device Id'); ?></dt>
		<dd>
			<?php echo h($action['Action']['device_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Zone Id'); ?></dt>
		<dd>
			<?php echo h($action['Action']['zone_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rule Id'); ?></dt>
		<dd>
			<?php echo h($action['Action']['rule_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Message'); ?></dt>
		<dd>
			<?php echo h($action['Action']['message']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Action'), array('action' => 'edit', $action['Action']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Action'), array('action' => 'delete', $action['Action']['id']), array(), __('Are you sure you want to delete # {0}?', $action['Action']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Actions'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Action'), array('action' => 'add')); ?> </li>
	</ul>
</div>
