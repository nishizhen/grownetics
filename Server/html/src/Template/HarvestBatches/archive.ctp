<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatch[]|\Cake\Collection\CollectionInterface $harvestBatches
 */
?>
<div class="harvestBatches index large-9 medium-8 columns content">
    <h3><?= __('Batch Archive') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>

            <th scope="col">Batch No.</th>
            <th scope="col"><?= $this->Paginator->sort('cultivar_id') ?></th>
            <th scope="col"><?= $this->Paginator->sort('planted_date') ?></th>
            <th scope="col"><?= $this->Paginator->sort('recipe_id') ?></th>
            <th scope="col"><?= $this->Paginator->sort('plant_count') ?></th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($harvestBatches as $harvestBatch): ?>
        <tr>
            <td><?=$this->Html->link('Batch '.$harvestBatch->batch_number, ['action' => 'view', $harvestBatch->id])?></td>
            <td><?= $harvestBatch->has('cultivar') ? $this->Html->link($harvestBatch->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $harvestBatch->cultivar->id]) : '' ?></td>
            <td><?= h($harvestBatch->planted_date);?>
            </td>
            <td><?= $harvestBatch->has('recipe') ? $this->Html->link($harvestBatch->recipe->label, ['controller' => 'Recipes', 'action' => 'view', $harvestBatch->recipe->id]) : '' ?></td>
            <td><?= h($harvestBatch->plant_count);?></td>  

            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $harvestBatch->id]) ?>
                <?php $this->Form->resetTemplates(); ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $harvestBatch->id], ['confirm' => __('Are you sure you want to delete # {0}?', $harvestBatch->id)]) ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
<br />
<?=$this->Html->link(__('New Harvest Batch'), ['action' => 'add'],['class'=>'btn btn-sm btn-theme03'])?>
