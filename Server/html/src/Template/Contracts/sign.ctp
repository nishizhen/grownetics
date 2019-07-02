<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class='wrapper'>
	<div class="strains index row">
		<div class="col-md-12 mt">
		  	<div class="form-panel batches form">
				<h4 class="mb"><i class="fa fa-angle-right"></i>Sign Contract</h4>
			<?php echo $this->Form->create('Contract');?>
				<fieldset>
					<div class="form-group">
						<p><?=$contract['Contract']['full_text']?></p><br />
						<p>I Agree To the Above Contract and Acknowledge that Electro Signatures hold the same binding power as a signature on paper.</p>
						<label class="col-sm-2 col-sm-2 control-label">Enter Full Name</label>
						<div class="col-sm-10">
							<?=$this->Form->input('contract_name',array('label'=>false,'class'=>'form-control round-form'));?>
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