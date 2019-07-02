<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Floorplan $floorplan
 */
    $this->element('leaflet');
    $this->Html->script('floorplans/floorplan-editor', ['block' => 'scriptBottom']);
    $this->Html->script('floorplans/edit', ['block' => 'scriptBottom']);
?>

<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $floorplan->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $floorplan->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Floorplans'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Facilities'), ['controller' => 'Facilities', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Facility'), ['controller' => 'Facilities', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="floorplans form col-lg-9 col-md-8 columns content">
    <div class="row">
        <?php //echo $this->element('floorplan', [ "floorplan" => $floorplan ]); ?>
    </div>

    <div class="row">
        <?= $this->Form->create($floorplan) ?>
        <fieldset>
            <legend><?= __('Edit Floorplan') ?></legend>
            <?php
                echo $this->Form->input('name');
                echo $this->Form->input('floor_level');
                echo $this->Form->input('description');
                echo $this->Form->input('latitude');
                echo $this->Form->input('longitude');
                echo $this->Form->input('offsetAngle');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
