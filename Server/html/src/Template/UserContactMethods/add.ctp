<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UserContactMethod $userContactMethod
 */
?>
<?= $this->Html->script('user_contact_methods/add', ['block' => 'scriptBottom']); ?>
<div class="userContactMethods form row mt">
    <?= $this->Form->create($userContactMethod,['templateVars'=>['header'=>'Contact Method']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('type', ['options' => $this->Enum->selectValues('UserContactMethods', 'type')]);
            echo $this->Form->input('phone', ['required' => false, 'id' => 'phoneContactMethod']);
        ?>
        <span style="display:none">
        <?php 
            echo $this->Form->input('email', ['required' => false, 'id' => 'emailContactMethod']);
        ?>
        </span>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
