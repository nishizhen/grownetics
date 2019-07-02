<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="roles form large-9 medium-8 columns content">
    <?= $this->Form->create($role,['templateVars'=>['header'=>'role']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('label');
            echo $this->Form->input('acls._ids', ['options' => $acls]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
