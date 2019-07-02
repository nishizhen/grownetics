<?php /**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Facility[]|\Cake\Collection\CollectionInterface $facilities
 */

    $this->element('leaflet');
    $this->Html->script('facilities/index', ['block' => 'scriptBottom']);
    $this->Form->resetTemplates();
?>

<div class="facilities index col-lg-9 col-md-8 columns content">
    <h3><?= __('Facilities') ?></h3>

 <!-- map div for leaflet -->
 <style>
 #mapid { height: 480px; width: 75%; margin: 20px 0px; }
 </style>
 <div id="mapid"></div>

    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('street_address') ?></th>
                <th><?= $this->Paginator->sort('latitude') ?></th>
                <th><?= $this->Paginator->sort('longitude') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facilities as $facility): ?>
            <tr>
                <td><?= $this->Number->format($facility->id) ?></td>
                <td><?= h($facility->name) ?></td>
                <td><?= h($facility->street_address) ?></td>
                <td><?= $this->Number->format($facility->latitude) ?></td>
                <td><?= $this->Number->format($facility->longitude) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $facility->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $facility->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $facility->id], ['confirm' => __('Are you sure you want to delete # {0}?', $facility->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$this->element('paginator')?>
</div>
