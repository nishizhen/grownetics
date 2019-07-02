<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class='wrapper'>
	<div class="orders create row">
		<div class="col-md-12 mt">
		  	<div class="form-panel batches form form-horizontal style-form">
				<h4 class="mb"><i class="fa fa-angle-right"></i>New Order</h4>
			<?php echo $this->Form->create('Order');
			echo $this->Form->hidden('Order.batch_id')
			?>
				<fieldset>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Strain</label>
						<div class="col-sm-10">
							<?=$batch['Strain']['name']?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Price</label>
						<div class="col-sm-10">
							$<span id='price'><?=$batch['Batch']['price']?></span>/ounce
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Available</label>
						<div class="col-sm-10">
							<span id='price'><?=$batch['Batch']['available_amount']?></span> ounces
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Order Amount (ounces)</label>
						<div class="col-sm-10">
							<?=$this->Form->input('amount',array('label'=>false,'max'=>$batch['Batch']['available_amount'],'min'=>1));?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Total</label>
						<div class="col-sm-10">
							$<span id='total'>0</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Address</label>
						<div class="col-sm-10">
						Ship To
							<?=$this->Form->input('shipping_name',array('label'=>false,'class'=>'form-control round-form'));?>
						Address
							<?=$this->Form->input('address_1',array('label'=>false,'class'=>'form-control round-form'));?>
						Address Continued
							<?=$this->Form->input('address_2',array('label'=>false,'class'=>'form-control round-form','required'=>false));?>
						City
							<?=$this->Form->input('city',array('label'=>false,'class'=>'form-control round-form'));?>
						State
							<?=$this->Form->input('state',array('label'=>false,'class'=>'form-control round-form'));?>
						Zip
							<?=$this->Form->input('zip',array('label'=>false,'class'=>'form-control round-form'));?>
						</div>
					</div>
				</fieldset>
			<?php echo $this->Form->end(__('Submit')); ?>
			</div>
		</div>
	</div>
</section>