<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class='wrapper'>
	<div class="cultivars index row">
		<div class="col-md-12 mt">
		  	<div class="form-panel batches form">
				<h4 class="mb"><i class="fa fa-angle-right"></i>New Contract</h4>
			<?php echo $this->Form->create('Contract'); ?>
				<fieldset>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Type</label>
						<div class="col-sm-10">
							<?=$this->Form->input('type',array('label'=>false,'class'=>'form-control round-form'));?>
						</div>
						<label class="col-sm-2 col-sm-2 control-label">Summary</label>
						<div class="col-sm-10">
							<?=$this->Form->textarea('summary',array('label'=>false,'class'=>'form-control round-form'));?>
						</div>
						<label class="col-sm-2 col-sm-2 control-label">Text</label>
						<div class="col-sm-10">
							<?=$this->Form->textarea('full_text',array('label'=>false,'class'=>'form-control round-form'));?>
						</div>
					</div>

				</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Contracts'), array('action' => 'index')); ?></li>
	</ul>
</div>
</div>
</div>
</section>