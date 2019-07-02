<script>
  var users = '<?php echo json_encode($users) ?>';
  var zones = '<?php echo json_encode($zones) ?>';
</script>
    <section class="task-panel tasks-widget">
    	<div class="panel-heading">
        <div class="pull-left"><h5><?=$this->Html->link($batch->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $batch->cultivar->id]).' - '.$this->Html->link('Batch #'.$batch->batch_number, ['controller' => 'HarvestBatches', 'action' => 'view', $batch->id]).' (Plant date: '.$batch->planted_date.')'?></h5></div>
        <br>
      </div>
      <div class="panel-body">
        <div class="task-content">

          <ul class="task-list ui-sortable">
            <?php foreach($tasks as $task): ?>
            <li
            <?php
            if ($task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Completed')) {
              echo "class='list-success'";
            } else if (strtotime($task->due_date) == strtotime($today)) {
              echo "class='list-danger'";
            } else if ($task->due_date == null) {
              echo "class='list-info'";
            } else if ($task->days_until_due == false) {
              echo "class='list-danger'";
            } else if ($task->days_until_due <= 7) {
              echo "class='list-danger'";
            } else if ($task->days_until_due <= 14) {
              echo "class='list-warning'";
            } else {
              echo "class='list-info'";
            }
            ?>
            style="background: <?php
            if ($task->type == $this->Enum->enumValueToKey('Tasks', 'type', 'Move') ||
        $task->type == $this->Enum->enumValueToKey('Tasks', 'type', 'Harvest')) {
              echo 'rgba(182, 186, 193, 0.3);';
            }
            ?>"
            >
              <div class="task-title">
                <span class="task-title-sp" data-taskID='<?=$task->id?>' data-taskBRE="<?=$task->batch_recipe_entry_id?>"><span id="taskLabel">
                  <?php 
                  if (in_array($task->type, [$this->Enum->enumValueToKey('Tasks', 'type', 'Move'), $this->Enum->enumValueToKey('Tasks', 'type', 'Harvest')])) {
                    echo "<b>".$this->Enum->enumKeyToValue('Tasks', 'type', $task->type)."</b> in ";
                  } else if ($task->type == $this->Enum->enumValueToKey('Tasks', 'type', 'Generic') && $task->zone_id == 0) {
                    echo $this->Enum->enumKeyToValue('Tasks', 'type', $task->type).": ".$task->label;
                  } else if ($task->type == $this->Enum->enumValueToKey('Tasks', 'type', 'Generic')) {
                    echo $this->Enum->enumKeyToValue('Tasks', 'type', $task->type).": ".$task->label." in ";
                  } else if ($task->zone_id == 0) {
                    echo $this->Enum->enumKeyToValue('Tasks', 'type', $task->type);
                  } else {
                    echo $this->Enum->enumKeyToValue('Tasks', 'type', $task->type). " in ";
                  }
                  ?>
                    <?php if ($task->zone_id != 0) { ?>
                        <span class='zone' data="<?= $task->zone_id ?>"><?=$this->Html->link($task->getZone($task->zone_id)->label, ['controller' => 'Zones', 'action' => 'view', $task->zone_id]);?></span>
                    <?php } ?>

                 </span>
                    <?php if ($task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete')) { ?><span
                id="taskDate"><?php
                if ($task->due_date) {
                  echo "on ".$task->due_date->format('n/j/y').".";
                }
              
                ?></span><?php } ?><?php if ($task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete')): ?> Assigned to: <span
                        id="assignee" data="<?= $task->user->id ?>"
                        ><?=$this->
                        Html->link($task->user->name, ['controller' => 'Users', 'action' => 'view', $task->user->id]);?></span></span>
                        <?php endif; ?><?php if ($task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Completed') && isset($task->completed_date)): ?>
                <span class="badge bg-success ">Completed: <time class="timeago" datetime="<?=$task->completed_date->format('Y-m-d H:i:sP')?>"></time>
by <?=$this->Html->link($task->user->name, ['controller' => 'Users', 'action' => 'view', $task->user->id]);?> </span>
              <?php endif; ?>
                <div class="pull-right hidden-phone">
                                      <div class="pull-right hidden-phone">
                        <button
                        class="btn btn-default btn-xs cancelEditBtn"
                        style="display:none;"
                        data="<?= $task->id ?>">
                        Cancel
                      </button>

                      <button
                      class="btn btn-primary btn-xs submitEditBtn"
                      style="display:none;"
                      data="<?= $task->harvestbatch_id ?>">
                      Confirm
                    </button>
                    <?php if ($task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete') && ($batch->next_move_task && $batch->next_move_task->id == $task->id || !in_array($task->type, [$this->Enum->enumValueToKey('Tasks', 'type', 'Move'), $this->Enum->enumValueToKey('Tasks', 'type', 'Harvest')]))): ?>
                    <button class="btn btn-success btn-xs completeTaskBtn"><i class="fa fa-check"></i><i style = "position:relative; display: none;" class="completeTaskSpinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i></button>
                    <?php endif; ?>
                    <?php if ($task->zone_id != 0 && $task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete')) { ?>
                  <button class="btn btn-primary btn-xs editTaskBtn"><i class="fa fa-pencil"></i></button>
                  <?php } ?>
                  <?php if ($this->Enum->enumKeyToValue('Tasks', 'type', $task->type) != 'Harvest' && $task->status == $this->Enum->enumValueToKey('Tasks', 'status', 'Incomplete')): ?>
                  <button class="btn btn-danger btn-xs deleteTaskBtn"><i class="fa fa-trash"></i></button>

                  <?php endif; ?>
                </div>
              </div>
            </li>
            <?php endforeach; ?>                            
          </ul><!-- /task-list -->

        </div><!-- /task-content -->
        <div class=" add-task-row">
          <?= $this->Html->link("<button class='btn btn-success btn-sm pull-left'><i class='fa fa-plus'></i> Add Task</button>", ['controller' => 'tasks', 'action' => 'add', $batch->id, 'returnUrl' => $this->request->params['controller']], ['escape' => false]); ?>
          <?php if ($this->request->params['controller'] != 'HarvestBatches'): ?>
          <a class="btn btn-default btn-sm pull-right" href="/harvestBatches/view/<?=$batch->id?>">Go To Batch</a>
        <?php endif; ?>
        </div>
      </div>
    </section>
