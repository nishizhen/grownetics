<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecipeEntry $recipeEntry
 */
?>
<div class="recipeEntries form large-9 medium-8 columns content">
    <?= $this->Form->create($recipeEntry,['templateVars'=>['header'=>'recipeEntry']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('zone_id', ['options' => $zones]);
            echo $this->Form->input('recipe_id');
            echo $this->Form->input('days');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
