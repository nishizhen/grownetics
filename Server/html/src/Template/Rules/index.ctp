<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rule[]|\Cake\Collection\CollectionInterface $rules
 */
?>
<div class="rules index ">
    <h3><?= __('Rules') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('label') ?></th>
                <th><?= $this->Paginator->sort('type') ?></th>
                <th>Description</th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $this->Form->resetTemplates();
            foreach ($rules as $rule): ?>
            <tr>
                <td><?= $this->Html->link($this->Number->format($rule->id), ['action' => 'edit', $rule->id]) ?></td>
                <td><?= $this->Html->link(h($rule->label), ['action' => 'edit', $rule->id]) ?></td>
                <td><?= $this->Enum->enumKeyToValue('Rules','type',$rule->type) ?></td>
                <td>
                    <?php foreach ($rule->rule_conditions as $rule_condition) { ?>
                        If 
                        <!-- <span class="badge"> -->
                            <?=$this->Enum->enumKeyToValue('Sensors','sensor_type',$rule_condition->sensor_type)?>
                        <!-- </span> -->
                        from
                        <!-- <span class="badge bg-primary"> -->
                            <?=$this->Enum->enumKeyToValue('RuleConditions','data_source',$rule_condition->data_source)?>
                        <?php
                            if ($rule_condition->source_target) {
                                echo $rule_condition->source_target->label;
                            } ?>
                        <!-- </span> -->
                        is 
                        <!-- <span class="badge bg-info"> -->
                            <?=$this->Enum->enumKeyToValue('RuleConditions','operator',$rule_condition->operator)?>
                        <!-- </span> -->
                        <!-- <span class="badge bg-success"> -->
                            <?=$rule_condition->trigger_threshold ?>
                        <!-- </span> -->
                    <?php } ?>
                    then
                    <?php foreach ($rule->rule_actions as $rule_action) { ?>
                        <!-- <span class="label label-primary"> -->
                            <?=$this->Enum->enumKeyToValue('RuleActions','type',$rule_action->type)?>
                        <!-- </span> -->
                        <?php if ($rule_action->rule_action_targets) { ?>
                            <?php foreach ($rule_action->rule_action_targets as $rule_action_target) { ?>
                                <!-- <span class="label label-default"> -->
                                    <?=$this->Enum->enumKeyToValue('RuleActionTargets','target_type',$rule_action_target->target_type)?> <?=$rule_action_target->target_id?>
                                <!-- </span> -->
                            <?php } ?>
                        <?php } ?>
                        with alert level
                        <!-- <span class="label label-default"> -->
                            <?=$this->Enum->enumKeyToValue('RuleActions','notification_level',$rule_action->notification_level)?>
                        <!-- </span> -->
                        <?php if ($rule->autoreset) { ?>
                            then <?=$this->Enum->enumKeyToValue('RuleActions','type',$rule_action->reset_type)?>
                            once 
                            <?php foreach ($rule->rule_conditions as $rule_condition) { ?>
                                <!-- <span class="badge"> -->
                                    <?=$this->Enum->enumKeyToValue('Sensors','sensor_type',$rule_condition->sensor_type)?>
                                <!-- </span> -->
                                from
                                <!-- <span class="badge bg-primary"> -->
                                    <?=$this->Enum->enumKeyToValue('RuleConditions','data_source',$rule_condition->data_source)?>
                                <?php
                                    if ($rule_condition->source_target) {
                                        echo $rule_condition->source_target->label;
                                    } ?>
                                <!-- </span> -->
                                is 
                                <!-- <span class="badge bg-info"> -->
                                    <?=$this->Enum->enumKeyToValue('RuleConditions','operator',$rule_condition->reset_operator)?>
                                <!-- </span> -->
                                <!-- <span class="badge bg-success"> -->
                                    <?=$rule_condition->reset_threshold ?>
                                <!-- </span> -->
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td><?php
                    switch ($rule->status) {
                            case $this->Enum->enumValueToKey('Rules','status',"Disabled"): ?>
                        <span class="label label-danger label-mini">Disabled</span>
                        <?php   break;
                            case $this->Enum->enumValueToKey('Rules','status',"Enabled"): ?>
                        <span class="label label-success label-mini">Enabled</span>
                        <?php   break;
                            case $this->Enum->enumValueToKey('Rules','status',"Triggered"): ?>
                        <span class="label label-warning label-mini">Triggered</span>
                        <?php   break;
                    }
                ?>&nbsp;</td>
                <td class="actions">
                    <?=$this->element('actionsMenu',['actions'=>[
                        $this->Form->postLink(__("Delete"), ['action' => 'delete', $rule->id], ['class'=>'delete','escape' => false, 'confirm' => __('Are you sure you want to delete {0}?', $rule->label)])
                    ]])?>
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
        <li><?= $this->Html->link(__('New Rule'), ['action' => 'add']) ?></li>
    </ul>
</div>
