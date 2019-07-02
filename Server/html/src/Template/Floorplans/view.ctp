<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Floorplan $floorplan
 */
    $this->Html->script('floorplans/map', ['block' => 'scriptBottom']);
    $this->Html->scriptBlock('
        var GrowServer = GrowServer || {};
        
        $(document).ready(function() {
            GrowServer.map = new GrowServer.Map();
            GrowServer.map.updateMap([]); // hacky way to remove the loading spinner
        });

    ', ['block' => 'scriptBottom']);
?>

<nav class="col-lg-3 col-md-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Floorplan'), ['action' => 'edit', $floorplan->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Floorplan'), ['action' => 'delete', $floorplan->id], ['confirm' => __('Are you sure you want to delete # {0}?', $floorplan->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Floorplans'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Floorplan'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Facilities'), ['controller' => 'Facilities', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Facility'), ['controller' => 'Facilities', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="floorplans view col-lg-9 col-md-8 columns content">
    <h3><?= h($floorplan->label) ?></h3>

    <div class="row">
        <?php $this->element('leaflet');
        $this->element('floorplan_colors');
        echo $this->cell('Floorplan', [$floorplan->id]); ?>
    </div>

    <div class="row">
        <table class="vertical-table">
            <tr>
                <th><?= __('Name') ?></th>
                <td><?= h($floorplan->name) ?></td>
            </tr>
            <tr>
                <th><?= __('Description') ?></th>
                <td><?= h($floorplan->description) ?></td>
            </tr>
            <tr>
                <th><?= __('Id') ?></th>
                <td><?= $this->Number->format($floorplan->id) ?></td>
            </tr>
            <tr>
                <th><?= __('Floor Level') ?></th>
                <td><?= $this->Number->format($floorplan->floor_level) ?></td>
            </tr>
            <tr>
                <th><?= __('Latitude') ?></th>
                <td><?= $this->Number->format($floorplan->latitude) ?></td>
            </tr>
            <tr>
                <th><?= __('Longitude') ?></th>
                <td><?= $this->Number->format($floorplan->longitude) ?></td>
            </tr>
            <tr>
                <th><?= __('offsetAngle') ?></th>
                <td><?= $this->Number->format($floorplan->offsetAngle) ?></td>
            </tr>
            <tr>
                <th><?= __('Status') ?></th>
                <td><?= $this->Number->format($floorplan->status) ?></td>
            </tr>
            <tr>
                <th><?= __('Created') ?></th>
                <td><?= h($floorplan->created) ?></td>
            </tr>
            <tr>
                <th><?= __('Modified') ?></th>
                <td><?= h($floorplan->modified) ?></td>
            </tr>
            <tr>
                <th><?= __('Deleted Date') ?></th>
                <td><?= h($floorplan->deleted_date) ?></td>
            </tr>
            <tr>
                <th><?= __('Deleted') ?></th>
                <td><?= $floorplan->deleted ? __('Yes') : __('No'); ?></td>
            </tr>
        </table>
    </div>
</div>
