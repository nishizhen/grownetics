<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HarvestBatch[]|\Cake\Collection\CollectionInterface $harvestBatches
 */
?>
<?= $this->Html->script('harvestbatches/GanttChart', ['block' => 'scriptBottom']); ?>
<?= $this->Html->script('harvestbatches/updateBatches', ['block' => 'scriptBottom']); ?>
<?= $this->Html->scriptBlock('
        var GrowServer = GrowServer || {};
        GrowServer.ganttData = ' . json_encode($batches) . ';
        GrowServer.ganttDataByRoom = ' . json_encode($batchesByRoom) . ';
    ', ['block' => 'scriptBottom']);
?>

<div class="harvestBatches index large-9 medium-8 columns content">
    <h3><?= __('Harvest Batches') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><button class='btn btn-xs btn-success fa fa-arrow-right' id='updateBatchesBtn' style='display:none'></button></th>
                <th scope="col">Batch No.</th>
                <th scope="col"><?= $this->Paginator->sort('cultivar_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('planted_date', 'Plant Date') ?></th>
                <th scope="col">Next Process Date</th>
                <th scope="col">Recipe</th>
                <th scope="col">Plant Count</th>
                <th scope="col">Current Zone</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?= $this->Form->resetTemplates(); ?>
            <?php foreach ($harvestBatches as $harvestBatch) : ?>
                <tr>
                    <td id='nextBatchProcessID' data-taskID='<?= $harvestBatch->next_task->id ?>'><?= $this->Form->checkbox('next_batch_process', ['value' => $harvestBatch->id, 'class' => 'batch_process_checkbox']); ?></td>
                    <td id='batchNo'><?= $this->Html->link('Batch ' . $harvestBatch->batch_number, ['action' => 'view', $harvestBatch->id]) ?></td>
                    <td id='batchStrain'><?= $harvestBatch->has('cultivar') ? $this->Html->link($harvestBatch->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $harvestBatch->cultivar->id]) : '' ?></td>
                    <td><?= h($harvestBatch->planted_date); ?></td>
                    <?php if ($harvestBatch->next_task) { ?>
                        <td id='nextBatchProcessDate' data-nextZone='<?= $harvestBatch->next_task->zone->label ?>' data-nextTaskType='<?= $harvestBatch->next_task->type ?>'><?= h(date('n/j/y', strtotime($harvestBatch->next_task->due_date))); ?></td>
                    <?php } else { ?>
                        <td></td>
                    <?php } ?>
                    <td><?= $harvestBatch->has('recipe') ? $this->Html->link($harvestBatch->recipe->label, ['controller' => 'Recipes', 'action' => 'view', $harvestBatch->recipe->id]) : '' ?></td>
                    <td><?= h($harvestBatch->plant_count); ?></td>
                    <td id='currentZone'><?= h($harvestBatch->current_zone) && $harvestBatch->current_zone != 'Pending' ? $this->Html->link($harvestBatch->current_zone->label, ['controller' => 'Zones', 'action' => 'view', $harvestBatch->current_zone->id]) : 'Pending' ?></td>
                    <td> <?=
                                $this->Form->postLink(__("<button class='fa fa-trash btn-xs btn btn-danger'></button>"), ['action' => 'delete', $harvestBatch->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete {0} - Batch #{1}?', $harvestBatch->cultivar->label, $harvestBatch->batch_number)]);
                            ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->element('paginator') ?>
</div>
<br>
<?= $this->Html->link(__('New Harvest Batch'), ['action' => 'add'], ['class' => 'btn btn-sm btn-theme03']) ?>

<br>
<br>


<h3>Active Harvest Batch Schedule</h3>
<div class="col-lg-12 mt">
    <div class="row content-panel">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-justified">
                <li class="active"><a data-toggle="tab" href="#overview">Overview</a></li>
                <li><a data-toggle="tab" href="#zone_type">Sorted by Room</a></li>
            </ul>
        </div>
        <div class="row mb ml">
            <div class="panel-body">
                <div class="tab-content">
                    <div id="overview" class="tab-pane active">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 style="text-align: center"><b>All Batches</b></h2>
                                <div id="contain">
                                    <div id="chartdiv"></div>
                                    <i style="position:relative; bottom:160px; left: 470px;" class="spinner fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="zone_type" class="tab-pane">
                        <div class="row">
                            <div class="col-md-12">
                                <?php foreach ($allZones as $zone) : ?>
                                    <?php foreach ($batchesByRoom as $room => $batches) : ?>
                                        <?php if ($room == $zone->label) : ?>
                                            <h2 style="text-align: center"><b><?= $room ?></b></h2>
                                            <div id="contain">
                                                <div id="chartByRoom<?= str_replace(' ', '', $room); ?>"></div>
                                                <i style="position:relative;" class="spinner fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
                                            </div>
                                        <?php endif;
                                endforeach;
                            endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>