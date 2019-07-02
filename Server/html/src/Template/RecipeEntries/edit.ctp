<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecipeEntry $recipeEntry
 */
?>
<div class="recipeEntries form large-9 medium-8 columns content">
    <?= $this->Form->create($recipeEntry,['templateVars'=>['header'=>'Edit Entry for Recipe: <b>'.$recipeEntry->recipe->label.'</b>']]) ?>
    <fieldset>
        <?php
            if ( !isset($recipeEntry->task_type_id) ) {
                echo $this->Form->input('plant_zone_type_id', ['options' => $types]);
            }
            echo $this->Form->input('days');
            if ( isset($recipeEntry->task_type_id) ) {
                echo $this->Form->input('task_type_id', ['options' => $taskTypes]);
                echo $this->Form->input('task_label');
            }
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
