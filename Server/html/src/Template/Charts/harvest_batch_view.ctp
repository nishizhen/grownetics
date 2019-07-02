<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php $this->Html->script('cell/chart/batchDataSelector', ['block' => 'scriptBottom']); ?>
<?php echo $this->cell('DataSelector::harvest_batch_view', ['batch_id' => $batch_id]); ?>
<br>
<br>
<div id="chartcontainer">
  <div id="loadingIcon"><i id='spinner' class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>
    <div id="chartdiv"></div>
 </div>



