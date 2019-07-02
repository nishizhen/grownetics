<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Raw[]|\Cake\Collection\CollectionInterface $raws
 */
?>
<div class="devices index">
	<h2><?php echo __('Raw Device Output'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('device_id'); ?></th>
			<th><?php echo $this->Paginator->sort('data'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($raws as $raw): ?>
	<tr>
		<td><?php echo h($raw['Raw']['id']); ?>&nbsp;</td>
		<td><?php echo h($raw['Raw']['created']); ?>&nbsp;</td>
		<td><?php echo h($raw['Raw']['device_id']); ?>&nbsp;</td>
		<td><?php echo h($raw['Raw']['data']); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Device'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Raws'), array('controller' => 'raws', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Raw'), array('controller' => 'raws', 'action' => 'add')); ?> </li>
	</ul>
</div>
