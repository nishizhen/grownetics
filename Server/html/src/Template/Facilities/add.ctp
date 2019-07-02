<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Facility $facility
 */
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Facilities'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Floorplans'), ['controller' => 'Floorplans', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Floorplan'), ['controller' => 'Floorplans', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="facilities form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($facility) ?>
    <fieldset>
        <legend><?= __('Add Facility') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('street_address');
            echo $this->Form->input('latitude');
            echo $this->Form->input('longitude');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>

