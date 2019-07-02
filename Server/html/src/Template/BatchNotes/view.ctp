<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchNote $batchNote
 */
?>
<div class="batchNotes view large-9 medium-8 columns content">
    <h3><?= h($batchNote->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $batchNote->has('user') ? $this->Html->link($batchNote->user->name, ['controller' => 'Users', 'action' => 'view', $batchNote->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Harvest Batch') ?></th>
            <td><?= $batchNote->has('harvest_batch') ? $this->Html->link($batchNote->harvest_batch->id, ['controller' => 'HarvestBatches', 'action' => 'view', $batchNote->harvest_batch->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($batchNote->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($batchNote->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($batchNote->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted Date') ?></th>
            <td><?= h($batchNote->deleted_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Deleted') ?></th>
            <td><?= $batchNote->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Note') ?></h4>
        <?= $this->Text->autoParagraph(h($batchNote->note)); ?>
    </div>
</div>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Batch Note'), ['action' => 'edit', $batchNote->id]),
        $this->Form->postLink(__('Delete Batch Note'), ['action' => 'delete', $batchNote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $batchNote->id)]),
        $this->Html->link(__('List Batch Notes'), ['action' => 'index']),
        $this->Html->link(__('New Batch Note'), ['action' => 'add']),
<a href="/users">List Users</a><a href="/users/add">New User</a><a href="/harvest-batches">List Harvest Batches</a><a href="/harvest-batches/add">New Harvest Batch</a>
]])?>
