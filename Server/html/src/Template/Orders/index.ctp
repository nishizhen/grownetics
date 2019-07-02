<?php
/**
 * @var \App\View\AppView $this
 */
?>
	<div class="strains index row">

		 <div class="col-md-12 mt">
		  	<div class="content-panel">
		          <table class="table table-hover">
			  	  	  <h4><i class="fa fa-angle-right"></i> Orders</h4>
			  	  	  <hr />
					   	<thead>
						<tr>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('shipping_name'); ?></th>
			<th><?php echo $this->Paginator->sort('address_1'); ?></th>
			<th><?php echo $this->Paginator->sort('address_2'); ?></th>
			<th><?php echo $this->Paginator->sort('state'); ?></th>
			<th><?php echo $this->Paginator->sort('country'); ?></th>
			<th><?php echo $this->Paginator->sort('zip'); ?></th>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th><?php echo $this->Paginator->sort('batch_id'); ?></th>
			<th><?php echo $this->Paginator->sort('notes'); ?></th>
			<th><?php echo $this->Paginator->sort('shipped_date'); ?></th>
			<th><?php echo $this->Paginator->sort('contract_id'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($orders as $order): ?>
	<tr>
		<td><?php echo $this->Html->link($order['Order']['created'], array('action' => 'view', $order['Order']['id'])); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['shipping_name']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['address_1']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['address_2']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['state']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['country']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['zip']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['amount']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($order['Batch']['harvest_date'], array('controller' => 'batches', 'action' => 'view', $order['Batch']['id'])); ?>
		</td>
		<td><?php echo h($order['Order']['notes']); ?>&nbsp;</td>
		<td><?php echo h($order['Order']['shipped_date']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($order['Contract']['type'], array('controller' => 'contracts', 'action' => 'view', $order['Contract']['id'])); ?>
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
</div></div>