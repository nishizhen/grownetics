<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RuleCondition $ruleCondition
 */
?>
<div class="ruleConditions form large-9 medium-8 columns content">
    <?= $this->Form->create($ruleCondition,['templateVars'=>['header'=>'ruleCondition']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('label');
            echo $this->Form->input('data_source');
            echo $this->Form->input('sensor_type');
            echo $this->Form->input('data_id');
            echo $this->Form->input('operator');
            echo $this->Form->input('trigger_threshold');
            echo $this->Form->input('reset_threshold');
            echo $this->Form->input('status');
            echo $this->Form->input('zone_behavior');
            echo $this->Form->input('trigger_delay');
            echo $this->Form->input('pending_time');
            echo $this->Form->input('deleted');
            echo $this->Form->input('rule_id', ['options' => $rules, 'empty' => true]);
            echo $this->Form->input('is_default');
            echo $this->Form->input('averaging_method');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
