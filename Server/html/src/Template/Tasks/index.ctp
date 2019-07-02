<script>
  var users = '<?php echo json_encode($users) ?>';
  var zones = '<?php echo json_encode($zones) ?>';
</script>
<?php 
echo $this->Html->script('cell/task/batchWorkflowCard', ['block' => 'scriptBottom']);
echo $this->Html->script('cell/task/workflowCard', ['block' => 'scriptBottom']);
?>
<div class="tasks index large-9 medium-8 columns content">

    <h1>Workflow</h1>
    <h4><a href='/tasks/archive'>Tasks Archive</a></h4>
    <div class="title" style='display:inline-block;'>
    <div id="high_priority_list"> </div> <div style='float:right;'> High priority (< 7 days).</div>
    </div>
    <div class="title" style='display:inline-block;'>
    <div id="upcoming_list"> </div> <div style='float:right;'> Upcoming (< 14 days).</div>
     </div>
    <div class="title" style='display:inline-block;'>
    <div id="low_priority_list"> </div> <div style='float:right;'> Low priority (>= 14 days).</div>
     </div>
    <div class="title" style='display:inline-block;'>
    <div id="completed_list"> </div> <div style='float:right;'> Completed.</div>
    </div>
    <div class="title" style='display:inline-block;'>
    <div> </div> <div style='float:right; background: rgba(155,0,0,0.1);'> Overdue.</div>
    </div>
    
    <hr>
    <h3>Facility Wide Tasks <?= $this->Html->link("<button class='btn btn-success btn-sm'><i class='fa fa-plus'></i> Add Task</button>", ['controller' => 'tasks', 'action' => 'add', 'returnUrl' => $this->request->params['controller']], ['escape' => false]); ?></h3>
        <div class="row">
            <?php echo $this->cell('WorkflowCard'); ?>
            <?php foreach($zones as $zone): ?>
            <?php if ($zone->zone_tasks) { ?>
            <div class="col-md-4">
                <?php echo $this->cell('ZoneWorkflowCard', [$zone->id]);?>
            </div>
            <?php } ?>
            <?php endforeach; ?>
        </div>
    <hr>


    <h2>Batch Tasks</h2>

    <?php foreach($activeZones as $zone): ?>
    	<h4><?=isset($zone->label) ? $zone->label : 'Pending'?></h4>
    	<div class="row">
    	<?php foreach($batches as $batch): ?>
            <?php if ($batch->current_room_zone == $zone): ?>
                <div class="col-md-4">
    			<?php echo $this->cell('BatchWorkflowCard', [$batch->id]);?>
                </div>
    		<?php endif; ?>
    	<?php endforeach; ?>
    	</div>
		<hr>
	<?php endforeach; ?>
</div>

