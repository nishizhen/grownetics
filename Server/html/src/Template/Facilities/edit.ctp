<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Facility $facility
 */
    $this->element('leaflet');
        $this->Html->scriptBlock('
        $(document).ready(function(){
            var map = new L.Map("mapid", {center: ['.$facility->latitude.','.$facility->longitude.' ], zoom: 16})
            .addLayer(new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"));

             L.marker(['.$facility->latitude.','.$facility->longitude.']).addTo(map);
        });
    ', ['block' => 'scriptBottom']);
    $this->Form->resetTemplates();
?>
<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $facility->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $facility->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Facilities'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Floorplans'), ['controller' => 'Floorplans', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Floorplan'), ['controller' => 'Floorplans', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="facilities form col-lg-9 col-md-8 columns content">

 <!-- map div for leaflet -->
 <style>
 #mapid { height: 480px; width: 75%; margin: 20px 0px; }
 </style>
 <div id="mapid"></div>

    <?= $this->Form->create($facility) ?>
    <fieldset>
        <legend><?= __('Edit Facility') ?></legend>
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
