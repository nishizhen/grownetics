<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="devices view">
	<h2><?php echo __('Device'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($device['api_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Label'); ?></dt>
		<dd>
			<?php echo $device['label']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Device Type'); ?></dt>
		<dd>
			<?php echo $this->Enum->enumKeyToValue('Devices', 'type', $device['type']); ?>
		</dd>
		<dt><?php echo __('Last Message'); ?></dt>
		<dd>
			<?php
			$cachedDevice = $this->Cache->get('device-' . $device['id']);
			if (isset($cachedDevice['last_message'])) {
				echo $this->Time->timeAgoInWords($cachedDevice['last_message']);
			} else {
				echo "None";
			}
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php
			switch ($cachedDevice['status']) {
				case 0: ?>
				<span class="label label-warning label-mini">Disabled</span>
				<?php break;
			case 1: ?>
				<span class="label label-success label-mini">Active</span>
				<?php
				break;
			case 2: ?>
				<span class="label label-warning label-mini">Rebooting</span>
				<?php break;
			case 3: ?>
				<span class="label label-success label-mini">Active</span>
				<?php break;
			case 4: ?>
				<span class="label label-warning label-mini">Offline</span>
				<?php break;
		}
		?>
		</dd>
		<dt><?php echo __('Refresh Rate'); ?></dt>
		<dd>
			<?php echo h($device['refresh_rate']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Reboot Device'), array('action' => 'reboot', $device['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Edit Device'), array('action' => 'edit', $device['id'])); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Sensors'); ?></h3>
	<?php
	if (!empty($device['sensors'])) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th><?php echo __('Id'); ?></th>
				<th><?php echo __('Label'); ?></th>
				<th><?php echo __('Last Data'); ?></th>
				<th><?php echo __('Pin'); ?></th>
				<th><?php echo __('Type'); ?></th>
				<th><?php echo __('Status'); ?></th>
				<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
			<?php
			foreach ($device['sensors'] as $sensor) { ?>
				<tr>
					<td><a href='/sensors/edit/<?php echo $sensor['id']; ?>'><?php echo $sensor['id']; ?></a></td>
					<td><a href='/sensors/edit/<?php echo $sensor['id']; ?>'><?php echo $sensor['label']; ?> <?= $this->element('editBtn', ['url' => '/sensors/edit/' . h($sensor['id'])]) ?></a></td>
					<td><?php echo $this->Cache->get('sensor-value-' . $sensor['id']); ?></td>
					<td><?php echo $sensor['sensor_pin']; ?></td>
					<td><?php echo $this->Enum->enumKeyToValue('Sensors', 'sensor_type', $sensor['sensor_type_id']); ?></td>
					<td><?php echo $this->Enum->enumKeyToValue('Sensors', 'status', $sensor['status']); ?></td>
					<td class="actions">
						<div class="btn-group">
							<button type="button" class="btn btn-theme dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-cog"></i> &nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><?php echo $this->Html->link(
										__('Create Rule'),
										array(
											'controller' => 'rules', 'action' => 'add',
											$sensor['id'], 1
										)
									); ?></li>
								<?php if ($sensor['status'] == 1) { ?>
									<li><?php echo $this->Html->link(__('Disable'), array('controller' => 'sensors', 'action' => 'toggle_active', $sensor['id'])); ?></li>
								<?php } else { ?>
									<li><?php echo $this->Html->link(__('Enable'), array('controller' => 'sensors', 'action' => 'toggle_active', $sensor['id'])); ?></li>
								<?php } ?>

								<li><?php echo $this->Html->link(__('View / Edit'), array('controller' => 'sensors', 'action' => 'edit', $sensor['id'])); ?></li>
								<li class="divider"></li>
								<li><?php echo $this->Form->resetTemplates();
									echo $this->Form->postLink(__('Delete'), array('controller' => 'sensors', 'action' => 'delete', $sensor['id']), ['confirm' => __('Are you sure you want to delete # {0}?', $sensor['id'])]); ?></li>
							</ul>
						</div>

					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Sensor'), array('controller' => 'sensors', 'action' => 'add', $device['id'])); ?> </li>
		</ul>
	</div>

	<h3><?php echo __('Hardware'); ?></h3>
	<?php
	if (!empty($device['outputs'])) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th><?php echo __('Id'); ?></th>
				<th><?php echo __('Label'); ?></th>
				<th><?php echo __('Last Data'); ?></th>
				<th><?php echo __('Pin'); ?></th>
				<th><?php echo __('Type'); ?></th>
				<th><?php echo __('Status'); ?></th>
				<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
			<?php
			foreach ($device['outputs'] as $output) { ?>
				<tr>
					<td><a href='/outputs/edit/<?php echo $output['id']; ?>'><?php echo $output['id']; ?></a></td>
					<td><a href='/outputs/edit/<?php echo $output['id']; ?>'><?php echo $output['label']; ?> <?= $this->element('editBtn', ['url' => '/outputs/edit/' . h($output['id'])]) ?></a></td>
					<td><?php echo $this->Cache->get('output-value-' . $output['id']); ?></td>
					<td><?php echo $output['output_pin']; ?></td>
					<td><?php echo $this->Enum->enumKeyToValue('Outputs', 'output_type', $output['output_type_id']); ?></td>
					<td><?php echo $this->Enum->enumKeyToValue('Outputs', 'status', $output['status']); ?></td>
					<td class="actions">
						<div class="btn-group">
							<button type="button" class="btn btn-theme dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-cog"></i> &nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><?php echo $this->Html->link(__('Edit'), array('controller' => 'outputs', 'action' => 'edit', $output['id'])); ?></li>
								<li class="divider"></li>
								<li><?php echo $this->Form->resetTemplates();
									echo $this->Form->postLink(__('Delete'), array('controller' => 'outputs', 'action' => 'delete', $output['id']), ['confirm' => __('Are you sure you want to delete # {0}?', $output['id'])]); ?></li>
							</ul>
						</div>

					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Add Hardware'), array('controller' => 'outputs', 'action' => 'add', $device['id'])); ?> </li>
		</ul>
	</div>
</div>