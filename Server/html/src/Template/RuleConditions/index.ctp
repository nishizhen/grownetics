<div class="ruleConditions index large-9 medium-8 columns content">
    <h3><?= __('Rule Conditions') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('label') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data_source') ?></th>
                <th scope="col"><?= $this->Paginator->sort('sensor_type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('data_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('operator') ?></th>
                <th scope="col"><?= $this->Paginator->sort('trigger_threshold') ?></th>
                <th scope="col"><?= $this->Paginator->sort('reset_threshold') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('zone_behavior') ?></th>
                <th scope="col"><?= $this->Paginator->sort('trigger_delay') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pending_time') ?></th>
                <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
                <th scope="col"><?= $this->Paginator->sort('rule_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_default') ?></th>
                <th scope="col"><?= $this->Paginator->sort('averaging_method') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ruleConditions as $ruleCondition): ?>
            <tr>
                <td><?= $this->Number->format($ruleCondition->id) ?></td>
                <td><?= h($ruleCondition->created);?>
                </td>
                <td><?= h($ruleCondition->modified);?>
                </td>
                <td><?= h($ruleCondition->label);?>
                     <?=$this->element('editBtn',['url'=>'/ruleConditions/edit/'.h($ruleCondition->id)]);?>
                </td>
                <td><?= $this->Number->format($ruleCondition->data_source) ?></td>
                <td><?= $this->Number->format($ruleCondition->sensor_type) ?></td>
                <td><?= $this->Number->format($ruleCondition->data_id) ?></td>
                <td><?= h($ruleCondition->operator);?>
                </td>
                <td><?= $this->Number->format($ruleCondition->trigger_threshold) ?></td>
                <td><?= $this->Number->format($ruleCondition->reset_threshold) ?></td>
                <td><?= $this->Number->format($ruleCondition->status) ?></td>
                <td><?= $this->Number->format($ruleCondition->zone_behavior) ?></td>
                <td><?= $this->Number->format($ruleCondition->trigger_delay) ?></td>
                <td><?= $this->Number->format($ruleCondition->pending_time) ?></td>
                <td><?= h($ruleCondition->deleted);?>
                </td>
                <td><?= $ruleCondition->has('rule') ? $this->Html->link($ruleCondition->rule->id, ['controller' => 'Rules', 'action' => 'view', $ruleCondition->rule->id]) : '' ?></td>
                <td><?= h($ruleCondition->is_default);?>
                </td>
                <td><?= $this->Number->format($ruleCondition->averaging_method) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $ruleCondition->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $ruleCondition->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $ruleCondition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ruleCondition->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>

<?=$this->element('actionsMenu',
        [
            'label'=>'Actions',
            'actions'=>[
                $this->Html->link(__('New Rule Condition'), ['action' => 'add']),

            ]
        ]
    );
?>
