<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Raw[]|\Cake\Collection\CollectionInterface $raws
 */
?>
<?php $this->Html->script('raws/live', ['block' => 'scriptBottom']); ?>

<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo _('id'); ?></th>
			<th><?php echo _('device_id'); ?></th>
			<th><?php echo _('label'); ?></th>
			<th><?php echo _('created'); ?></th>
			<th><?php echo _('data'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($raws as $raw): ?>
	<tr>
		<td><?php echo h($raw['id']); ?>&nbsp;</td>
		<td><a href='/devices/view/<?php echo h($raw['device_id']); ?>'><?php echo h($raw['device_id']); ?>&nbsp;</a></td>
		<td><a href='/devices/view/<?php echo $raw['device_id']; ?>'><?php echo h($raw['device']->label); ?>&nbsp;</a></td>
		<td><?php echo $this->Time->timeAgoInWords($raw['created']); ?>&nbsp;</td>
		<td><?php echo h($raw['data']); ?>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>