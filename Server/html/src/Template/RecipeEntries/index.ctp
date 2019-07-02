<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecipeEntry[]|\Cake\Collection\CollectionInterface $recipeEntries
 */
?>
<div class="recipeEntries index large-9 medium-8 columns content">
    <h3><?= __('Recipe Entries') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('zone_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('recipe_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('days') ?></th>
                <th scope="col"><?= $this->Paginator->sort('deleted') ?></th>
                <th scope="col"><?= $this->Paginator->sort('deleted_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipeEntries as $recipeEntry): ?>
                
            <tr>
                <td><?= $this->Number->format($recipeEntry->id) ?></td>
                <td><?= $recipeEntry->zone_id ? $this->Html->link($recipeEntry->zone->label, ['controller' => 'Zones', 'action' => 'view', $recipeEntry->zone->id]) : '' ?></td>
                <td><?= $recipeEntry->has('recipe') ? $this->Html->link($recipeEntry->recipe->label, ['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe->id]) : '' ?></td>
                <td><?= $this->Number->format($recipeEntry->days) ?></td>
                <td><?= h($recipeEntry->deleted);?>
                </td>
                <td><?= h($recipeEntry->deleted_date);?>
                </td>
                <td><?= h($recipeEntry->created);?>
                </td>
                <td><?= h($recipeEntry->modified);?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $recipeEntry->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $recipeEntry->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $recipeEntry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $recipeEntry->id)]) ?>
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
                $this->Html->link(__('New Recipe Entry'), ['action' => 'add']),
            ]
        ]
    );
?>
