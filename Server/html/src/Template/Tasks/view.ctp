<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<div class="tasks view large-9 medium-8 columns content">
    <h3>Task #<?= h($task->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Harvestbatch') ?></th>
            <td><?= $task->has('harvestbatch') ? $this->Html->link($task->harvestbatch->id, ['controller' => 'harvestBatches', 'action' => 'view', $task->harvestbatch->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($task->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($task->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($task->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Completed Date') ?></th>
            <td><?= h($task->completed_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Assigned to:') ?></th>
            <td><?= $task->has('assignee') ? $this->Html->link($task->user->name, ['controller' => 'Users', 'action' => 'view', $task->assignee]) : '' ?></td>
        </tr>
    </table>

</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Task'), ['action' => 'edit', $task->id]),
        $this->Form->postLink(__('Delete Task'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id)]),
        $this->Html->link(__('List Tasks'), ['action' => 'index']),
        $this->Html->link(__('New Task'), ['action' => 'add']),
]])?>
