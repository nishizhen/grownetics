<div class="ruleConditions view large-9 medium-8 columns content">
    <h3><?= h($ruleCondition->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Label') ?></th>
            <td><?= h($ruleCondition->label) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Operator') ?></th>
            <td><?= h($ruleCondition->operator) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rule') ?></th>
            <td><?= $ruleCondition->has('rule') ? $this->Html->link($ruleCondition->rule->id, ['controller' => 'Rules', 'action' => 'view', $ruleCondition->rule->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($ruleCondition->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data Source') ?></th>
            <td><?= $this->Number->format($ruleCondition->data_source) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sensor Type') ?></th>
            <td><?= $this->Number->format($ruleCondition->sensor_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Data Id') ?></th>
            <td><?= $this->Number->format($ruleCondition->data_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Trigger Threshold') ?></th>
            <td><?= $this->Number->format($ruleCondition->trigger_threshold) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Reset Threshold') ?></th>
            <td><?= $this->Number->format($ruleCondition->reset_threshold) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($ruleCondition->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Zone Behavior') ?></th>
            <td><?= $this->Number->format($ruleCondition->zone_behavior) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Trigger Delay') ?></th>
            <td><?= $this->Number->format($ruleCondition->trigger_delay) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pending Time') ?></th>
            <td><?= $this->Number->format($ruleCondition->pending_time) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Averaging Method') ?></th>
            <td><?= $this->Number->format($ruleCondition->averaging_method) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($ruleCondition->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($ruleCondition->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= h($ruleCondition->deleted) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Default') ?></th>
            <td><?= $ruleCondition->is_default ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Rule Condition'), ['action' => 'edit', $ruleCondition->id]),
        $this->Form->postLink(__('Delete Rule Condition'), ['action' => 'delete', $ruleCondition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ruleCondition->id)]),
        $this->Html->link(__('List Rule Conditions'), ['action' => 'index']),
        $this->Html->link(__('New Rule Condition'), ['action' => 'add']),
<a href="/rules">List Rules</a><a href="/rules/add">New Rule</a>
]])?>
