<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchNote[]|\Cake\Collection\CollectionInterface $batchNotes
 */
?>
<div class="batchNotes index large-9 medium-8 columns content">
    <h3><?= __('Batch Notes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('harvest_batch_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
                <th scope="col"><?= $this->Paginator->sort('deleted_date') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batchNotes as $batchNote): ?>
            <tr>
                <td><?= $this->Number->format($batchNote->id) ?></td>
                <td><?= h($batchNote->created);?>
                </td>
                <td><?= h($batchNote->modified);?>
                </td>
                <td><?= $batchNote->has('user') ? $this->Html->link($batchNote->user->name, ['controller' => 'Users', 'action' => 'view', $batchNote->user->id]) : '' ?></td>
                <td><?= $batchNote->has('harvest_batch') ? $this->Html->link($batchNote->harvest_batch->id, ['controller' => 'HarvestBatches', 'action' => 'view', $batchNote->harvest_batch->id]) : '' ?></td>
                <td><?= h($batchNote->deleted);?>
                </td>
                <td><?= h($batchNote->deleted_date);?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $batchNote->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $batchNote->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $batchNote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $batchNote->id)]) ?>
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
                $this->Html->link(__('New Batch Note'), ['action' => 'add']),

            ]
        ]
    );
?>
