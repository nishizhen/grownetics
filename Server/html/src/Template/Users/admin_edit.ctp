<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?$this->Form->inputDefaults(
    array(
        'label' => false,
        'div' => false,
        'class' => 'has-error'
    )
);?>
<section class='wrapper'>
	<div class="strains form row mt">
		<div class="col-lg-12">
			<div class="form-panel">
				<h4 class="mb"><i class="fa fa-angle-right"></i>Edit User</h4>
				<?php echo $this->Form->create('User',array(
					'class'=>'form-horizontal style-form',
				   	'inputDefaults' => array(
				        'error' => array(
				            'attributes' => array(
				                'wrap' => 'small', 'class' => 'has-error'
				            )
				        )
			    	)
			    )); ?>
					<fieldset>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Email</label>
							<div class="col-sm-10">
								<?=$this->Form->input('email',
								array(
								'class'=>'form-control round-form',
								'label'=>false,
								'error-class'=>'has-error',
							    'error'=>array(
							        'attributes' => array('wrap' => 'span', 'class' => 'has-error')
							    )
								));?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Name</label>
							<div class="col-sm-10">
								<?=$this->Form->input('name',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Address</label>
							<div class="col-sm-10">
								<?=$this->Form->input('address',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Address 2</label>
							<div class="col-sm-10">
								<?=$this->Form->input('address_2',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">City</label>
							<div class="col-sm-10">
								<?=$this->Form->input('city',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">State</label>
							<div class="col-sm-10">
								<?=$this->Form->input('state',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Zip</label>
							<div class="col-sm-10">
								<?=$this->Form->input('zip',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 col-sm-2 control-label">Country</label>
							<div class="col-sm-10">
								<?=$this->Form->input('country',
								array(
								'class'=>'form-control',
								'label'=>false
								)
								);?>
							</div>
						</div>
					</fieldset>
				<?php echo $this->Form->end('Submit',array('class'=>'btn btn-theme')); ?>
			</div>
		</div>
	</div>
</section>
