<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BatchRecipeEntry $batchRecipeEntry
 */
?>
<?php $this->Html->script('batch_recipe_entries/edit', ['block' => 'scriptBottom']); ?>
<div class="batchRecipeEntries form large-9 medium-8 columns content">
    <?= $this->Form->create($batchRecipeEntry,['templateVars'=>['header'=>'batchRecipeEntry']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('zone_id', ['options' => $zones]);
            ?>

        <div class="form-group">
        <?php
            echo $this->Form->label('planned_start_date');
            echo $this->Form->text('planned_start_date');
        ?>
        </div>
        <div class="form-group">
        <?php
            echo $this->Form->label('planned_end_date');
            echo $this->Form->text('planned_end_date');
        ?>
        </div>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
