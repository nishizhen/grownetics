<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Floorplan[]|\Cake\Collection\CollectionInterface $floorplans
 */
?>
<div class="floorplans index columns content">
    <h3><?= __('Floorplans') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('label') ?></th>
                <th><?= $this->Paginator->sort('facility_id') ?></th>
                <th><?= $this->Paginator->sort('floor_level') ?></th>
                <th><?= $this->Paginator->sort('description') ?></th>
                <th><?= $this->Paginator->sort('latitude') ?></th>
                <th><?= $this->Paginator->sort('longitude') ?></th>
                <th><?= $this->Paginator->sort('offsetAngle') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th><?= $this->Paginator->sort('deleted') ?></th>
                <th><?= $this->Paginator->sort('deleted_date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($floorplans as $floorplan): ?>
            <tr>
                <td><?= $this->Number->format($floorplan->id) ?></td>
                <td><?= h($floorplan->label) ?></td>
                <td><?= $floorplan->has('facility') ? $this->Html->link($floorplan->facility->name, ['controller' => 'Facilities', 'action' => 'view', $floorplan->facility->id]) : '' ?></td>
                <td><?= $this->Number->format($floorplan->floor_level) ?></td>
                <td><?= h($floorplan->description) ?></td>
                <td><?= $this->Number->format($floorplan->latitude) ?></td>
                <td><?= $this->Number->format($floorplan->longitude) ?></td>
                <td><?= $this->Number->format($floorplan->offsetAngle) ?></td>
                <td><?= h($floorplan->created) ?></td>
                <td><?= h($floorplan->modified) ?></td>
                <td><?= $this->Number->format($floorplan->status) ?></td>
                <td><?= h($floorplan->deleted) ?></td>
                <td><?= h($floorplan->deleted_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $floorplan->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $floorplan->id]) ?>
                    <?php
                    $this->Form->resetTemplates();
                    echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $floorplan->id], ['confirm' => __('Are you sure you want to delete # {0}?', $floorplan->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
<?php
$actions = [
    $this->Html->link(__('New Floorplan'), ['action' => 'add']),
    $this->Html->link(__('List Facilities'), ['controller' => 'Facilities', 'action' => 'index']),
    $this->Html->link(__('New Facility'), ['controller' => 'Facilities', 'action' => 'add'])
];

if (isset($navRole) && $navRole == 'Admin') {
    $actions = array_merge($actions, [
        $this->Form->postLink(__('Wipe Floorplan'), ['controller' => 'Floorplans', 'action' => 'clearImport'], ['confirm' => __('Are you sure you want to clear the floorplan (including all devices, zones, sensors, etc?)')]),
        $this->Form->postLink(__('Import Demo Floorplan'), ['controller' => 'Floorplans', 'action' => 'importDemo'], ['confirm' => __('Sure you want to seed a demo floorplan and overwrite any existing floorplan, device, zone data?')])
    ]);
}
$this->Form->resetTemplates();
echo $this->element('actionsMenu',['label'=>'Actions','actions'=>$actions]);
?>

