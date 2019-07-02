<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Photo $photo
 */
?>
<div class="photos form large-9 medium-8 columns content">
    <?= $this->Form->create($photo,['templateVars'=>['header'=>'photo']]) ?>
    <fieldset>
        <?php
            echo $this->Form->input('deleted');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
