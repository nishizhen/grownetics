<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Output $output
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Output'), ['action' => 'edit', $output->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Output'), ['action' => 'delete', $output->id], ['confirm' => __('Are you sure you want to delete # {0}?', $output->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Outputs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Output'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Devices'), ['controller' => 'Devices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Device'), ['controller' => 'Devices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Zones'), ['controller' => 'Zones', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Zone'), ['controller' => 'Zones', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Rules'), ['controller' => 'Rules', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rule'), ['controller' => 'Rules', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="outputs view col-lg-9 col-md-8 columns content">
    <h3><?= h($output->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Label') ?></th>
            <td><?= h($output->label) ?></td>
        </tr>
        <tr>
            <th><?= __('Output Target') ?></th>
            <td><?= h($output->output_target) ?></td>
        </tr>
        <tr>
            <th><?= __('Device') ?></th>
            <td><?= $output->has('device') ? $this->Html->link($output->device->id, ['controller' => 'Devices', 'action' => 'view', $output->device->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Zone') ?></th>
            <td><?= $output->has('zone') ? $this->Html->link($output->zone->id, ['controller' => 'Zones', 'action' => 'view', $output->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($output->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->Number->format($output->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Output Type') ?></th>
            <td><?= $this->Number->format($output->output_type) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted') ?></th>
            <td><?= $this->Number->format($output->deleted) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($output->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($output->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted Date') ?></th>
            <td><?= h($output->deleted_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Rules') ?></h4>
        <?php if (!empty($output->rules)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Label') ?></th>
                <th><?= __('Data Source') ?></th>
                <th><?= __('Data Type') ?></th>
                <th><?= __('Data Id') ?></th>
                <th><?= __('Operator') ?></th>
                <th><?= __('Trigger Threshold') ?></th>
                <th><?= __('Reset Threshold') ?></th>
                <th><?= __('Action Type') ?></th>
                <th><?= __('Notification Level') ?></th>
                <th><?= __('Status') ?></th>
                <th><?= __('Rule Type') ?></th>
                <th><?= __('Output Id') ?></th>
                <th><?= __('Output On Value') ?></th>
                <th><?= __('Output Off Value') ?></th>
                <th><?= __('Autoreset') ?></th>
                <th><?= __('Trigger Delay') ?></th>
                <th><?= __('Pending Time') ?></th>
                <th><?= __('Deleted') ?></th>
                <th><?= __('Deleted Date') ?></th>
                <th><?= __('Parent Rule Id') ?></th>
                <th><?= __('Parent Rule Trigger Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($output->rules as $rules): ?>
            <tr>
                <td><?= h($rules->id) ?></td>
                <td><?= h($rules->created) ?></td>
                <td><?= h($rules->modified) ?></td>
                <td><?= h($rules->label) ?></td>
                <td><?= h($rules->data_source) ?></td>
                <td><?= h($rules->data_type) ?></td>
                <td><?= h($rules->data_id) ?></td>
                <td><?= h($rules->operator) ?></td>
                <td><?= h($rules->trigger_threshold) ?></td>
                <td><?= h($rules->reset_threshold) ?></td>
                <td><?= h($rules->action_type) ?></td>
                <td><?= h($rules->notification_level) ?></td>
                <td><?= h($rules->status) ?></td>
                <td><?= h($rules->rule_type) ?></td>
                <td><?= h($rules->output_id) ?></td>
                <td><?= h($rules->output_on_value) ?></td>
                <td><?= h($rules->output_off_value) ?></td>
                <td><?= h($rules->autoreset) ?></td>
                <td><?= h($rules->trigger_delay) ?></td>
                <td><?= h($rules->pending_time) ?></td>
                <td><?= h($rules->deleted) ?></td>
                <td><?= h($rules->deleted_date) ?></td>
                <td><?= h($rules->parent_rule_id) ?></td>
                <td><?= h($rules->parent_rule_trigger_status) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Rules', 'action' => 'view', $rules->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Rules', 'action' => 'edit', $rules->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Rules', 'action' => 'delete', $rules->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rules->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
