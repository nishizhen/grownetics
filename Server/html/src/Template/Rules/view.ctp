<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rule $rule
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Rule'), ['action' => 'edit', $rule->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Rule'), ['action' => 'delete', $rule->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rule->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Rules'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rule'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Notifications'), ['controller' => 'Notifications', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Notification'), ['controller' => 'Notifications', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Outputs'), ['controller' => 'Outputs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Output'), ['controller' => 'Outputs', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="rules view col-lg-9 col-md-8 columns content">
    <h3><?= h($rule->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Label') ?></th>
            <td><?= h($rule->label) ?></td>
        </tr>
        <tr>
            <th><?= __('Data Id') ?></th>
            <td><?= h($rule->data_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Operator') ?></th>
            <td><?= h($rule->operator) ?></td>
        </tr>
        <tr>
            <th><?= __('Trigger Threshold') ?></th>
            <td><?= h($rule->trigger_threshold) ?></td>
        </tr>
        <tr>
            <th><?= __('Reset Threshold') ?></th>
            <td><?= h($rule->reset_threshold) ?></td>
        </tr>
        <tr>
            <th><?= __('Output On Value') ?></th>
            <td><?= h($rule->output_on_value) ?></td>
        </tr>
        <tr>
            <th><?= __('Output Off Value') ?></th>
            <td><?= h($rule->output_off_value) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($rule->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Data Source') ?></th>
            <td><?= $this->Number->format($rule->data_source) ?></td>
        </tr>
        <tr>
            <th><?= __('Data Type') ?></th>
            <td><?= $this->Number->format($rule->data_type) ?></td>
        </tr>
        <tr>
            <th><?= __('Action Type') ?></th>
            <td><?= $this->Number->format($rule->action_type) ?></td>
        </tr>
        <tr>
            <th><?= __('Notification Level') ?></th>
            <td><?= $this->Number->format($rule->notification_level) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->Number->format($rule->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Rule Type') ?></th>
            <td><?= $this->Number->format($rule->rule_type) ?></td>
        </tr>
        <tr>
            <th><?= __('Output Id') ?></th>
            <td><?= $this->Number->format($rule->output_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Trigger Delay') ?></th>
            <td><?= $this->Number->format($rule->trigger_delay) ?></td>
        </tr>
        <tr>
            <th><?= __('Pending Time') ?></th>
            <td><?= $this->Number->format($rule->pending_time) ?></td>
        </tr>
        <tr>
            <th><?= __('Parent Rule Id') ?></th>
            <td><?= $this->Number->format($rule->parent_rule_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Parent Rule Trigger Status') ?></th>
            <td><?= $this->Number->format($rule->parent_rule_trigger_status) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($rule->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($rule->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted Date') ?></th>
            <td><?= h($rule->deleted_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Autoreset') ?></th>
            <td><?= $rule->autoreset ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted') ?></th>
            <td><?= $rule->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Notifications') ?></h4>
        <?php if (!empty($rule->notifications)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Status') ?></th>
                <th><?= __('Notification Level') ?></th>
                <th><?= __('Source Type') ?></th>
                <th><?= __('Source Id') ?></th>
                <th><?= __('Message') ?></th>
                <th><?= __('Rule Id') ?></th>
                <th><?= __('Deleted') ?></th>
                <th><?= __('Deleted Date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($rule->notifications as $notifications): ?>
            <tr>
                <td><?= h($notifications->id) ?></td>
                <td><?= h($notifications->created) ?></td>
                <td><?= h($notifications->modified) ?></td>
                <td><?= h($notifications->status) ?></td>
                <td><?= h($notifications->notification_level) ?></td>
                <td><?= h($notifications->source_type) ?></td>
                <td><?= h($notifications->source_id) ?></td>
                <td><?= h($notifications->message) ?></td>
                <td><?= h($notifications->rule_id) ?></td>
                <td><?= h($notifications->deleted) ?></td>
                <td><?= h($notifications->deleted_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Notifications', 'action' => 'view', $notifications->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Notifications', 'action' => 'edit', $notifications->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Notifications', 'action' => 'delete', $notifications->id], ['confirm' => __('Are you sure you want to delete # {0}?', $notifications->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Outputs') ?></h4>
        <?php if (!empty($rule->outputs)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Status') ?></th>
                <th><?= __('Label') ?></th>
                <th><?= __('Output Target') ?></th>
                <th><?= __('Output Type') ?></th>
                <th><?= __('Device Id') ?></th>
                <th><?= __('Zone Id') ?></th>
                <th><?= __('Deleted') ?></th>
                <th><?= __('Deleted Date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($rule->outputs as $outputs): ?>
            <tr>
                <td><?= h($outputs->id) ?></td>
                <td><?= h($outputs->created) ?></td>
                <td><?= h($outputs->modified) ?></td>
                <td><?= h($outputs->status) ?></td>
                <td><?= h($outputs->label) ?></td>
                <td><?= h($outputs->output_target) ?></td>
                <td><?= h($outputs->output_type) ?></td>
                <td><?= h($outputs->device_id) ?></td>
                <td><?= h($outputs->zone_id) ?></td>
                <td><?= h($outputs->deleted) ?></td>
                <td><?= h($outputs->deleted_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Outputs', 'action' => 'view', $outputs->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Outputs', 'action' => 'edit', $outputs->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Outputs', 'action' => 'delete', $outputs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $outputs->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
