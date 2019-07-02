<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecipeEntry $recipeEntry
 */
?>
<div class="recipeEntries view large-9 medium-8 columns content">
    <h3><?= "Entry #".h($recipeEntry->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Zone') ?></th>
            <td><?= $recipeEntry->has('zone') ? $this->Html->link($recipeEntry->zone->label, ['controller' => 'Zones', 'action' => 'view', $recipeEntry->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Recipe') ?></th>
            <td><?= $recipeEntry->has('recipe') ? $this->Html->link($recipeEntry->recipe->label, ['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Days') ?></th>
            <td><?= $this->Number->format($recipeEntry->days) ?></td>
        </tr>
    </table>
<?php
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>[
        $this->Html->link(__('Edit Recipe Entry'), ['action' => 'edit', $recipeEntry->id]),
        $this->Form->postLink(__('Delete Recipe Entry'), ['action' => 'delete', $recipeEntry->id], ['confirm' => __('Are you sure you want to delete # {0}?', $recipeEntry->id)]),
        $this->Html->link(__('List Recipe Entries for This Recipe'), ['controller' => 'Recipes', 'action' => 'view', $recipeEntry->recipe_id]),
        $this->Html->link(__('New Recipe Entry'), ['action' => 'add']),
]])?>
