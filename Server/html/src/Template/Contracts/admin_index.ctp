<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class='wrapper'>
	<div class="cultivars index row">
		<div class="col-md-12 mt">
		  	<div class="content-panel">
		        <table class="table table-hover">
			  	  	  <h4><i class="fa fa-angle-right"></i> Contracts</h4>
			  	  	  <hr />
					   	<thead>
						<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Summary</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($contracts as $contract): ?>
	<tr>
		<td><?php echo h($contract['Contract']['id']); ?>&nbsp;</td>
		<td><?php echo h($contract['Contract']['created']); ?>&nbsp;</td>
		<td><?php echo h($contract['Contract']['modified']); ?>&nbsp;</td>
		<td><?php echo h($contract['Contract']['type']); ?>&nbsp;</td>
		<td><?php echo h($contract['Contract']['summary']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $contract['Contract']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $contract['Contract']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $contract['Contract']['id']), array(), __('Are you sure you want to delete # {0}?', $contract['Contract']['id'])); ?>
		</td>
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
		<li><?php echo $this->Html->link(__('New Contract'), array('action' => 'add')); ?></li>
	</ul>
</div>
</div>
</div>
</section>

