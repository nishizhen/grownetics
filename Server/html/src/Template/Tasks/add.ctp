<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<?php $this->Html->script('tasks/add', ['block' => 'scriptBottom']); ?>
<div class="tasks form large-9 medium-8 columns content">
    <?php if ($batch !== 0) { ?>
    <?= $this->Form->create($task,['templateVars'=>['header'=>'Add Task for <b>'.$batch->cultivar->label.' - Batch #'.$batch->batch_number.'</b>'], 'id'=>'taskAddForm']) ?>
    <?php } else { ?>
    <?= $this->Form->create($task,['templateVars'=>['header'=>'Add Task for the Facility or a specific Zone'], 'id'=>'taskAddForm']) ?>
    <?php } ?>
    <fieldset>
            <?php 
            $options = $this->Enum->selectValues('Tasks', 'type');
            foreach (array_keys($options, 'Harvest', true) as $key) {
                unset($options[$key]);
            }
            if ($batch === 0) {
                foreach (array_keys($options, 'Move', true) as $key) {
                    unset($options[$key]);
                }
            }
            echo $this->Form->input('type', [
                'options' => $options
            ]); ?>
        <div class="form-group taskDescription" style='display:none' > 
        <?php
            echo $this->Form->label('Task description'); 
            echo $this->Form->text('label');
            ?>
            </div>
            <?=$this->Form->label('Zone');?> 
            <div id="recipe-entries">
            <ul>
                <li>
                    <table class="recipeForm">
                        <tr>
                            <th>
                            </th>
                            <th>
                                Room
                            </th>
                            <th>
                                Bench
                            </th>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <div class="form-group ui search normal selection dropdown roomDDown">
                                    <input type="hidden" name="room_id" form="taskAddForm" required>
                                    <i class="dropdown icon"></i>
                                    <div class="text" data-value="">None</div>
                                    <div class="menu roomMenu">
                                        <option id="option" class='item' data-value='' value=''>None</option>
                                        <?php foreach ($rooms as $room): ?>
                                        <option name="zones[]" class="item" data-value="<?=$room->id?>" value="<?=$room->id?>">
                                            <?=$room->label?>
                                        </option>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group ui search normal selection dropdown groupDDown" >
                                    <input type="hidden" name="group_id" form="taskAddForm" required>
                                    <i class="dropdown icon"></i>
                                    <div class="text" data-value="">None</div>
                                    <div class="menu groupMenu">
                                    <option id="default-option" class='item' data-value='' value=''>None</option>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </li>
            </ul>
        </div>
        <?php
            echo $this->Form->input('assignee', ['options' => $users]);  
        ?>
        
        <div class="form-group">
        <?php
            echo $this->Form->label('due_date');
            
           
            echo $this->Form->text('due_date', ['id' => 'due_date_field', 'type' => 'datepicker', 'placeholder' => 'Leave blank to not assign a date. (Move Batch tasks require a date)', 'required' => true]);
            ?>
        </div>
        <? if ($batch) { ?>
        <?= $this->Form->hidden('harvestbatch_id', ['val' => $batch->id]);?>
        <? } ?>
        <?= $this->Form->hidden('status', ['val' => $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete')]);?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>