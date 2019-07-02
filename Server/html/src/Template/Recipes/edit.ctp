<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Recipe $recipe
 */
?>
<div class="recipes form large-9 medium-8 columns content">
    <?= $this->Form->create($recipe,['templateVars'=>['header'=>'recipe']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('label');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
