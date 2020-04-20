<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Floorplan $floorplan
 */
    $this->Html->script('floorplans/map', ['block' => 'scriptBottom']);
	$this->Html->script('dash/charts', ['block' => 'scriptBottom']);
	$this->Html->script('dash/index', ['block' => 'scriptBottom']);
	$this->Html->script('deps/zabuto_calendar', ['block' => 'scriptBottom']);
	$this->Html->script('cell/small_calendar/display', ['block' => 'scriptBottom']);
	$this->Html->scriptBlock('
		var GrowServer = GrowServer || {};
		GrowServer.chatData = '.json_encode($chatData).';
        GrowServer.showMetric = '.json_encode($showMetric).';

        $(document).ready(function() {
            GrowServer.map = new GrowServer.Map();
        });

	', ['block' => 'scriptBottom']);
    $this->Html->script('cell/chart/DataSelector', ['block' => 'scriptBottom']);
 ?>
<div class="row">
    <div class="col-lg-9"> <!-- main content -->
        <?php if (env('DEV')) {
            //echo $this->cell('Widgets');
        } ?>
        <?php if ($floorplan) { ?>
        <div class="row"> <!-- Map -->
            <div class="col-lg-12 col-12">
                <div class="green-panel content-panel" style="background-color: transparent;">
                    <div class="green-header" style="margin-bottom:0">
                        <div class="row">
                            <div class="col-sm-11 col-11">
                                <h5 class="chart-title"><?= env('FACILITY_NAME') ?> Facility Map</h5>
                            </div>
                            <div class="col-sm-1 col-1 goright">
                                <button class="btn btn-primary" type="button" data-target="edit-map">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div><!--/header-->

                    <?php
                        $this->element('leaflet');
                        $this->element('floorplan_colors');
                        echo $this->cell('Floorplan', [$floorplan->id]);
                    ?>
                </div>
            </div>
        </div> <!--/ Map -->
        <?php } ?>

<div class="row">

<?php $count = 1; ?>
<?php if (isset($configs)): ?>
<?php foreach ($configs as $config): ?>
	<?php echo $this->cell('SmallChart::small_chart', [$config, $count]); ?>
    <?php $count++ ?>
<?php endforeach; ?>
<?php endif; ?>

</div>
<div class="col-md-6 mb small_chart ghostChart">
  <div class="green-panel content-panel">
    <div class="gray-header">
      <div class="row">
      </div>
    </div>
    <a href='#' id='addChart'><i class="fa fa-plus-circle"></i>Add chart</a>
  </div>
</div>
<canvas></canvas>


</div>
    <div class="col-lg-3 ds"><!-- col-lg-3 -->
        <?php echo $this->cell('Chat'); ?>
        <?php echo $this->cell('Notifications::box'); ?>
        <?php echo $this->cell('SmallCalendar'); ?> 
    </div><!-- /col-lg-3 -->
</div> <!-- /row -->


<audio id="beep">
	<source src="/sounds/beep.wav" type="audio/wav" />
</audio>
<audio id="alarm">
	<source src="/sounds/alarm.wav" type="audio/wav" />
</audio>
<audio id="error">
	<source src="/sounds/error.wav" type="audio/wav" />
</audio>

