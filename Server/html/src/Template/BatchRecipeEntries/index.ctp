<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchRecipeEntry[]|\Cake\Collection\CollectionInterface $batchRecipeEntries
 */
?>
<div class="batchRecipeEntries index large-9 medium-8 columns content">
    <h3><?= __('Batch Recipe Entries') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('zone_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('recipe_entry_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('planned_start_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('planned_end_date') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batchRecipeEntries as $batchRecipeEntry): ?>
            <tr>
                <td><?= $this->Number->format($batchRecipeEntry->id) ?></td>
                <td><?= $batchRecipeEntry->has('zone') ? $this->Html->link($batchRecipeEntry->zone->label, ['controller' => 'Zones', 'action' => 'view', $batchRecipeEntry->zone->id]) : '' ?></td>
                <td><?= $batchRecipeEntry->has('recipe_entry') ? $this->Html->link($batchRecipeEntry->recipe_entry->id, ['controller' => 'RecipeEntries', 'action' => 'view', $batchRecipeEntry->recipe_entry->id]) : '' ?></td>
                <td><?= h($batchRecipeEntry->planned_start_date);?>
                </td>
                <td><?= h($batchRecipeEntry->planned_end_date);?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $batchRecipeEntry->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $batchRecipeEntry->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $batchRecipeEntry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $batchRecipeEntry->id)]) ?>
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
                $this->Html->link(__('New Batch Recipe Entry'), ['action' => 'add'])
            ]
        ]
    );
?>
