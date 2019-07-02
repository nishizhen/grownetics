<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="api_test form row mt">
<?php echo $this->Form->create(null,['templateVars'=>['header'=>'API Test']]);
	echo $this->Form->input('device');
	echo $this->Form->input('data');
	echo $this->Form->button(__('Submit'));
    echo $this->Form->end()?>
<blockquote>Response: <?=$response?></blockquote>
