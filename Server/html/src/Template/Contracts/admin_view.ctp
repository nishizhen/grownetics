<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="contracts view">
<h2><?php echo __('Contract'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($contract['Contract']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($contract['Contract']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($contract['Contract']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($contract['Contract']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Text'); ?></dt>
		<dd>
			<?php echo h($contract['Contract']['full_text']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Contract'), array('action' => 'edit', $contract['Contract']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Contract'), array('action' => 'delete', $contract['Contract']['id']), array(), __('Are you sure you want to delete # {0}?', $contract['Contract']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Contracts'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Contract'), array('action' => 'add')); ?> </li>
	</ul>
</div>
