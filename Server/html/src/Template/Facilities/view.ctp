<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Facility $facility
 */
    $this->element('leaflet');
        $this->Html->scriptBlock('
        $(document).ready(function(){
            var map = new L.Map("mapid", {center: ['.$facility->latitude.','.$facility->longitude.' ], zoom: 8})
            .addLayer(new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"));

             L.marker(['.$facility->latitude.','.$facility->longitude.']).addTo(map);
        });
    ', ['block' => 'scriptBottom']);
?>

<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Facility'), ['action' => 'edit', $facility->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Facility'), ['action' => 'delete', $facility->id], ['confirm' => __('Are you sure you want to delete # {0}?', $facility->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Facilities'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Facility'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Floorplans'), ['controller' => 'Floorplans', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Floorplan'), ['controller' => 'Floorplans', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="facilities view col-lg-9 col-md-8 columns content">
    <h3><?= h($facility->name) ?></h3>

 <!-- map div for leaflet -->
 <style>
 #mapid { height: 480px; width: 75%; margin: 20px 0px; }
 </style>
 <div id="mapid"></div>

    <table class="vertical-table">
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($facility->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Street Address') ?></th>
            <td><?= h($facility->street_address) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($facility->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Latitude') ?></th>
            <td><?= $this->Number->format($facility->latitude) ?></td>
        </tr>
        <tr>
            <th><?= __('Longitude') ?></th>
            <td><?= $this->Number->format($facility->longitude) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->Number->format($facility->status) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($facility->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($facility->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted Date') ?></th>
            <td><?= h($facility->deleted_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Deleted') ?></th>
            <td><?= $facility->deleted ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Floorplans') ?></h4>
        <?php if (!empty($facility->floorplans)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Name') ?></th>
                <th><?= __('Facility Id') ?></th>
                <th><?= __('Floor Level') ?></th>
                <th><?= __('Description') ?></th>
                <th><?= __('Latitude') ?></th>
                <th><?= __('Longitude') ?></th>
                <th><?= __('OffsetAngle') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Status') ?></th>
                <th><?= __('Deleted') ?></th>
                <th><?= __('Deleted Date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($facility->floorplans as $floorplans): ?>
            <tr>
                <td><?= h($floorplans->id) ?></td>
                <td><?= h($floorplans->name) ?></td>
                <td><?= h($floorplans->facility_id) ?></td>
                <td><?= h($floorplans->floor_level) ?></td>
                <td><?= h($floorplans->description) ?></td>
                <td><?= h($floorplans->latitude) ?></td>
                <td><?= h($floorplans->longitude) ?></td>
                <td><?= h($floorplans->offsetAngle) ?></td>
                <td><?= h($floorplans->created) ?></td>
                <td><?= h($floorplans->modified) ?></td>
                <td><?= h($floorplans->status) ?></td>
                <td><?= h($floorplans->deleted) ?></td>
                <td><?= h($floorplans->deleted_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Floorplans', 'action' => 'view', $floorplans->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Floorplans', 'action' => 'edit', $floorplans->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Floorplans', 'action' => 'delete', $floorplans->id], ['confirm' => __('Are you sure you want to delete # {0}?', $floorplans->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
