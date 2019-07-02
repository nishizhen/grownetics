<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php $this->Html->script('cell/chart/DataSelector', ['block' => 'scriptBottom']); ?>
<?php echo $this->cell('DataSelector::display_large'); ?>
<br>
<br>
<div id="chartcontainer">
  <div id="loadingIcon"><i id='spinner' class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>
    <div id="chartdiv"></div>
 </div>



