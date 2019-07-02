<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Notification $notification
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Notification'), ['action' => 'edit', $notification->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Notification'), ['action' => 'delete', $notification->id], ['confirm' => __('Are you sure you want to delete # {0}?', $notification->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Notifications'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Notification'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Rules'), ['controller' => 'Rules', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rule'), ['controller' => 'Rules', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="notifications view col-lg-9 col-md-8 columns content">
    <h3><?= h($notification->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Message') ?></th>
            <td><?= h($notification->message) ?></td>
        </tr>
        <tr>
            <th><?= __('Rule') ?></th>
            <td><?= $notification->has('rule') ? $this->Html->link($notification->rule->id, ['controller' => 'Rules', 'action' => 'view', $notification->rule->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($notification->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->Number->format($notification->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Notification Level') ?></th>
            <td><?= $this->Number->format($notification->notification_level) ?></td>
        </tr>
        <tr>
            <th><?= __('Source Type') ?></th>
            <td><?= $this->Number->format($notification->source_type) ?></td>
        </tr>
        <tr>
            <th><?= __('Source Id') ?></th>
            <td><?= $this->Number->format($notification->source_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($notification->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($notification->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted Date') ?></th>
            <td><?= h($notification->deleted_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted') ?></th>
            <td><?= $notification->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
