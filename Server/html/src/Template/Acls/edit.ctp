<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Acl $acl
 */
?>
<div class="acls form large-9 medium-8 columns content">
    <?= $this->Form->create($acl,['templateVars'=>['header'=>'acl']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('controller');
            echo $this->Form->input('action');
            echo $this->Form->input('rule');
            echo $this->Form->input('user_id', ['options' => $users, 'empty' => '( None )']);
            echo $this->Form->input('roles._ids', ['options' => $roles]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
