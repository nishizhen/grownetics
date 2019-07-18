<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Organization $organization
 */
?>
<div class="organizations form large-9 medium-8 columns content">
    <?= $this->Form->create($organization,['templateVars'=>['header'=>'organization']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('label');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
