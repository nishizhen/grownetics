<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UserContactMethod $userContactMethod
 */
?>
<div class="userContactMethods form row mt">
    <?= $this->Form->create($userContactMethod,['templateVars'=>['header'=>'Contact Method']]) ?>
    <fieldset>
        <?php if ($userContactMethod->type < $this->Enum->enumValueToKey('userContactMethods', 'type', 'Email')) {
            echo $this->Form->input('phone', ['default' => $prevValue, 'required' => false]);
        }
         else {
            echo $this->Form->input('email', ['default' => $prevValue, 'required' => false]);
        }
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
