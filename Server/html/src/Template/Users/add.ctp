<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="strains form row mt">
	<div class="col-lg-12">
		<div class="form-panel">
			<h4 class="mb"><i class="fa fa-angle-right"></i>Add User</h4>
			<?php echo $this->Form->create('User',array(
				'class'=>'form-horizontal style-form',
		    )); ?>
				<fieldset>
					<div class="form-group">
						<label class="col-sm-2 col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<?=$this->Form->input('email',
							array(
							'class'=>'form-control round-form',
							'label'=>false,

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
				</fieldset>
			<?php echo $this->Form->submit('Submit',array('class'=>'btn btn-theme')); echo $this->Form->end(); ?>
		</div>
	</div>
</div>
