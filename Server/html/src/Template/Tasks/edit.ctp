<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<?php $this->Html->script('batch_recipe_entries/edit', ['block' => 'scriptBottom']); ?>
<div class="tasks form large-9 medium-8 columns content">
    <?= $this->Form->create($task,['templateVars'=>['header'=>'task']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('label');
            echo $this->Form->input('status');
            echo $this->Form->input('assignee', ['options' => $assignees]); ?>
        <div class="form-group">
        <?php
            echo $this->Form->label('due_date');
            echo $this->Form->text('due_date');
        ?>
        </div>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
