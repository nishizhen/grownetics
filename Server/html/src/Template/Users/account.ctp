<div class="strains form row mt">
	<?php echo $this->Form->create($user,array(
		'templateVars'=>['header'=>'Edit Your Account'],
    	));
        echo $this->Form->input('email');
        echo $this->Form->input('name');
        echo $this->Form->label('Unit Display Preference');
        echo $this->Form->radio('show_metric', ['Imperial', 'Metric'], ['hiddenField' => false]);
        echo "<p>&nbsp;</p>";
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
<div class="userContactMethods index large-9 medium-8 columns content">
    <h3><?= __('Contact Preferences') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('value') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userContactMethods as $userContactMethod): ?>
            <tr>
                <td><?= $this->Enum->enumKeyToValue('UserContactMethods', 'type', $userContactMethod->type) ?></td>
                <td><?= h($userContactMethod->value);?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(__('Edit'), ['controller' => 'UserContactMethods', 'action' => 'edit', $userContactMethod->id]); 
                    $this->Form->resetTemplates(); ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'UserContactMethods', 'action' => 'delete', $userContactMethod->id], ['confirm' => __('Are you sure you want to delete "{0}"?', $userContactMethod->value)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->Html->link(__('+ Add New Contact Method'), ['controller' => 'UserContactMethods', 'action' => 'add']);    ?>
</div>


