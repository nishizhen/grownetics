<div class="tasks index large-9 medium-8 columns content">
    <h3><?= __('Tasks Archive') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('harvestbatch_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('due_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('completed_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('assignee') ?></th>
                <th scope="col"><?= $this->Paginator->sort('zone_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $this->Number->format($task->id) ?></td>
                <td><?= h($task->created);?>
                </td>
                <td><?= h($task->modified);?>
                </td>
                <td><?= $this->Number->format($task->status) ?></td>
                <td><?= $task->has('harvestbatch') ? $this->Html->link($task->harvestbatch->id, ['controller' => 'HarvestBatches', 'action' => 'view', $task->harvestbatch->id]) : '' ?></td>
                <td><?= h($task->due_date);?>
                </td>
                <td><?= h($task->completed_date);?>
                </td>
                <td><?= $task->has('user') ? $this->Html->link($task->user->name, ['controller' => 'Users', 'action' => 'view', $task->user->id]) : '' ?></td>
                <td><?= $task->has('zone') ? $this->Html->link($task->zone->label, ['controller' => 'Zones', 'action' => 'view', $task->zone->id]) : '' ?></td>
                <td><?= $this->Enum->enumKeyToValue('Tasks','type',$task->type) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $task->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id)]) ?>
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
                $this->Html->link(__('New Task'), ['action' => 'add']),

            ]
        ]
    );
?>
