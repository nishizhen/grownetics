<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->Html->scriptBlock('var taskDates = '.json_encode($taskDates).';'); ?>
<div id="calendar" class="mb">
	<div class="panel green-panel no-margin">
		<div class="panel-body">
			<div id="date-popover" class="popover top">
				<div class="arrow"></div>
				<h3 class="popover-title" style="disadding: none;"></h3>
				<div id="date-popover-content" class="popover-content"></div>
			</div>
			<div id="my-calendar"></div>
		</div>
	</div>
</div>