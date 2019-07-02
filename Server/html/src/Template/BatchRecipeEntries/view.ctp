<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchRecipeEntry $batchRecipeEntry
 */
?>
<div class="batchRecipeEntries view large-9 medium-8 columns content">
    <h3><?= "Batch Recipe Entry #".h($batchRecipeEntry->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Zone') ?></th>
                <td><?= $batchRecipeEntry->has('zone') ? $this->Html->link($batchRecipeEntry->zone->label, ['controller' => 'Zones', 'action' => 'view', $batchRecipeEntry->zone->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Recipe Entry Id') ?></th>
            <td><?= $batchRecipeEntry->has('recipe_entry_id') ? $this->Html->link($batchRecipeEntry->recipe_entry_id, ['controller' => 'RecipeEntries', 'action' => 'view', $batchRecipeEntry->recipe_entry_id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Planned Start Date') ?></th>
            <td><?= h($batchRecipeEntry->planned_start_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Planned End Date') ?></th>
            <td><?= h($batchRecipeEntry->planned_end_date) ?></td>
        </tr>
    </table>
</div>
