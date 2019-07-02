<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="strains form row mt">
	<?php echo $this->Form->create($user,array(
		'templateVars'=>['header'=>'Edit User'],
    	)); 
		echo $this->Form->input('email');
		echo $this->Form->input('name');
		echo $this->Form->input('role_id');
		echo $this->Form->input('password');
		echo $this->Form->input('password_confirm',
		array(
		'type'=>'password'
		)
		);
		echo $this->Form->submit('Submit');
		echo $this->Form->end();
	?>
</div>