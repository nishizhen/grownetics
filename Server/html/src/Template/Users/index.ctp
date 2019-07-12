<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<section class='wrapper'>
	<div class="strains index row">

		 <div class="col-md-12 mt">
		  	<div class="content-panel">
		          <table class="table table-hover">
			  	  	  <h4><i class="fa fa-angle-right"></i> Users</h4>
			  	  	  <hr />
					   	<thead>
						<tr>
				<th><?php echo $this->Paginator->sort('id'); ?></th>
				<th><?php echo $this->Paginator->sort('username'); ?></th>
				<th><?php echo $this->Paginator->sort('email'); ?></th>
				<th><?php echo $this->Paginator->sort('name'); ?></th>
				<th class="actions"><?php echo __('Actions'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($users as $user): ?>
		<tr>
			<td><?php echo h($user['id']); ?>&nbsp;</td>
			<td><?php echo h($user['username']); ?>&nbsp;</td>
			<td><?php echo h($user['email']); ?>&nbsp;</td>
			<td><?php echo h($user['name']); ?>&nbsp;</td>
			<td class="actions">
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['id'])); ?>
                <?php if (isset($navRole) && $navRole == 'Admin') {
                    echo $this->Html->link(__('Impersonate'), array('action' => 'impersonate', $user['id']));
                } ?>
				<?php 
				$this->Form->resetTemplates();
				echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['id']), array(), __('Are you sure you want to delete # {0}?', $user['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
		</table>
		<?=$this->element('paginator')?>
	</div>
	<div class="actions">
		<h3><?php echo __('Actions'); ?></h3>
		<ul>

			<a href="/users/add">Create User</a>
		</ul>
	</div>
</section>