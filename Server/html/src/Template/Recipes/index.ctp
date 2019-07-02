<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Recipe[]|\Cake\Collection\CollectionInterface $recipes
 */
?>
<div class="recipes index large-9 medium-8 columns content">
    <h3><?= __('Recipes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('label', 'Name') ?></th>
                <th scope="col"># of Processes</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?=$this->Form->resetTemplates();?>
            <?php foreach ($recipes as $recipe): ?>
            <tr>
                <td><?= h($recipe->label) ? $this->Html->link($recipe->label, ['action' => 'view', $recipe->id]) : '';?>
                <?=$this->element('editBtn',['url'=>'/recipes/edit/'.h($recipe['id'])])?>
                </td>
                <td><?=sizeof($recipe->recipe_entries);?></td>
                <td>
                    <?= $this->Form->postLink(__("<button class='fa fa-trash btn-xs btn btn-danger'></button>"), ['action' => 'delete', $recipe->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete {0}?', $recipe->label)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
<br>
<?=$this->Html->link(__('New Recipe'), ['action' => 'add'],['class'=>'btn btn-sm btn-theme03'])?>
