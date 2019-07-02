<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<?php $this->Html->script('tasks/add', ['block' => 'scriptBottom']); ?>
<div class="tasks form large-9 medium-8 columns content">
    <?= $this->Form->create($task,['templateVars'=>['header'=>'task']]) ?>
    <fieldset>
    <div class="form-group">
        <?php
            echo $this->Form->label('label');
            echo $this->Form->text('label');
            ?>
            </div>
            <?php
            echo $this->Form->input('zone_id');
            echo $this->Form->input('assignee', ['options' => $users]);
           ?>
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