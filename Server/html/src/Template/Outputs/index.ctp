<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Output[]|\Cake\Collection\CollectionInterface $outputs
 */
?>

<?php $this->Html->script('outputs/index', ['block' => 'scriptBottom']);?>

<div class="outputs index">
    <h3><?= __('Hardware') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('label') ?></th>
                <th><?= $this->Paginator->sort('hardware_type') ?></th>
                <th>Schedule</th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?=$this->Form->resetTemplates();?>
            <?php foreach ($outputs as $output): 
                if ($output->scheduleRat) {
                    $raID = $output->scheduleRat->rule_action_id;
                } else {
                    $raID = false;
                }
             ?>
            <tr>
                <td><?= $this->Html->link(__($output->id), ['action' => 'edit', $output->id]) ?></td>
                <td><?= $this->Html->link(__($output->label), ['action' => 'edit', $output->id]) ?></td>
                <td><?= $this->Enum->enumKeyToValue('Outputs','hardware_type',$output->hardware_type) ?></td>
                <td>
                <?php if ($output->scheduleRule) { ?>
                    <select class="outputSchedule" data-id="<?=$output->scheduleRat->id?>">
                        <?php 
                        $timezoneOffset = 21600;
                        foreach($lightingRules as $lightingRule) { ?>
                            <?php $selected = ($lightingRule->id == $output->scheduleRule->id ? ' selected="selected"' : ''); ?>
                            <option value="<?=$lightingRule->rule_actions[0]->id?>"<?=$selected?>>
                                <?=$lightingRule->label?>
                                <?php if ($lightingRule->rule_conditions) {?>
                                        - On: <?php $time = new \DateTime("@".($lightingRule->rule_conditions[0]->reset_threshold-$timezoneOffset)); echo $time->format('h:i A'); ?> <?=env('TIMEZONE')?>
                                        - Off: <?php $time = new \DateTime("@".($lightingRule->rule_conditions[0]->trigger_threshold-$timezoneOffset)); echo $time->format('h:i A'); ?> <?=env('TIMEZONE')?>
                                <?php } ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php echo $output->scheduleRule->status;?>
                    <?php echo ($output->schedule_correct)?>
                <?php }
                if ($output->co2Rule) { ?>
                    Turn On Below: <input class="thresholdSet trigger" name="<?=$output->co2RuleCondition->id?>" value="<?=$output->co2RuleCondition->trigger_threshold?>" size="5" />
                    Turn Off Above: <input class="thresholdSet reset" name="<?=$output->co2RuleCondition->id?>" value="<?=$output->co2RuleCondition->reset_threshold?>" size="5" />
                <?php } ?>
                </td>
                <td><?php
                switch ($output->status) {
                        case $this->Enum->enumValueToKey('Outputs','status',"Disabled"): ?>
                    <span class="label label-danger label-mini">Disabled</span>
                    <?php $toggle_button = $this->Html->link(__('Turn On <i class="fa fa-bolt"></i>'), ['action' => 'toggle_power', $output->id, $this->Enum->enumValueToKey('Outputs','status', 'On')], ['escape'=> false]);  
                    break;
                        case $this->Enum->enumValueToKey('Outputs','status',"Off"): ?>
                    <span class="label label-default label-mini">OFF</span>
                    <?php $toggle_button = $this->Html->link(__('Turn On <i class="fa fa-bolt"></i>'), ['action' => 'toggle_power', $output->id, $this->Enum->enumValueToKey('Outputs','status', 'On')], ['escape'=> false]); 
                    break;
                    case $this->Enum->enumValueToKey('Outputs','status',"High Temp Shutdown"): ?>
                        <span class="label label-danger label-mini">High Temp Shutdown</span>
                    <?php $toggle_button = ''; break;
                    case $this->Enum->enumValueToKey('Outputs','status',"On"): ?>
                    <span class="label label-warning label-mini">ON</span>
                    <?php $toggle_button = $this->Html->link(__('Turn Off'), ['action' => 'toggle_power', $output->id, $this->Enum->enumValueToKey('Outputs','status', 'Off')], ['escape'=> false]);  break;
                    default: $toggle_button = '';
                }
                ?>&nbsp;</td>
                <td class="actions">
                    <?=
                    $this->element('actionsMenu',['actions'=>[
                        $toggle_button,
                        '',
                        $this->Html->link(__('Edit'), array('action' => 'edit', $output->id)),
                        $this->Form->postLink(__("Delete"), ['action' => 'delete', $output->id], ['class'=>'delete','escape' => false, 'confirm' => __('Are you sure you want to delete {0}?', $output->label)])
                    ]])?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>

<?=$this->element('actionsMenu',['label'=>'Actions','actions'=>[
    $this->Html->link(__('New Output'), array('action' => 'add'))
]])?>