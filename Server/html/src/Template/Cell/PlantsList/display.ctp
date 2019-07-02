<div class="content-panel">
    <div class="adv-table">
        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered hidden-table-info">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Short ID</th>
                    <th>Zone</th>
                    <th>Status</th>
                    <th>Note Actions</th>
                    <th>Cultivar</th>
                    <th>Plant Action</th>
                </tr>
            </thead>
            <tbody>
                <?= $this->Form->resetTemplates(); ?>
                <?php foreach ($plants as $plant) : ?>
                    <tr class="entry" data-plant_id="<?= $plant->id ?>" <?php if ($plant->status == $this->Enum->enumValueToKey('Plants', 'status', 'Destroyed')) { ?> style="color: rgba(10, 10, 10, 0.3)" <?php } ?>>
                        <td class="center"><?= $plant->plant_id ?></td>
                        <td class="center"><?php if ($plant->status != $this->Enum->enumValueToKey('Plants', 'status', 'Destroyed')) {
                                                echo $plant->short_plant_id;
                                            } ?></td>
                        <td class="center"><?php if ($plant->status != $this->Enum->enumValueToKey('Plants', 'status', 'Destroyed')) {
                                                if ($plant->status == $this->Enum->enumValueToKey('Plants', 'status', 'Harvested')) {
                                                    echo 'None';
                                                } else {
                                                    echo $plant->zone_id ? $this->Html->link($plant->getZone($plant->zone_id)->label, ['controller' => 'Zones', 'action' => 'view', $plant->zone_id]) : 'Pending';
                                                }
                                            } ?></td>
                        <td class="center"><?= $this->Enum->enumKeyToValue('Plants', 'status', $plant->status) ?></td>
                        <td class="center">
                            <?php echo $this->cell('PhotoNoteUploadModal', [$plant->id], ['modelType' => 'Plant']); ?>
                            <?php if ($plant['notes'] != []) {
                                echo $this->cell('PhotoNoteModalDisplay', [$plant->id], ['modelType' => 'Plant']);
                            } ?>
                        </td>
                        <td><?php if ($plant->cultivar) {
                                echo $plant->cultivar->label;
                            } ?></td>
                        <td class="center">
                            <?php if ($plant->status != $this->Enum->enumValueToKey('Plants', 'status', 'Destroyed')) {
                                echo $this->Form->postLink('<i class="btn btn-danger btn-xs fa fa-trash"></i>', ['controller' => 'harvestBatches', 'action' => 'markPlantDestroyed', $plant->id, $plant->harvest_batch_id], ['confirm' => 'Delete plant #' . $plant->short_plant_id . '?', 'escape' => false]);
                            } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div><!-- /content-panel -->
<div style="width: 50%;">
    <?= $this->Form->create(null, ['url' => ['controller' => 'HarvestBatches', 'action' => 'movePlantsToNewBatch'], 'templateVars' => ['header' => 'harvestBatchs'], 'id' => 'newBatchData', 'style' => ['display: inline-block;']]); ?>
    <?= $this->Form->hidden('batch_id', ['value' => $harvest_batch_id]); ?>
    <?= $this->Form->hidden('isNewBatch', ['value' => true]); ?>
    <?= $this->Form->hidden('existing_plants', ['value' => false]); ?>
    <?= $this->Form->button('Move plants to new batch', ['class' => 'btn btn-success', 'name' => 'newBatch', 'id' => 'newBatchBtn', 'value' => '1', 'style' => ['display: inline-block;']]); ?>
    <?= $this->Form->end(); ?>

    <button class="btn btn-primary" id="movePlantsExisting" name="movePlants" style="margin-left:15px; display:inline-block;" data-toggle="modal" data-target="#movePlantsToExistingBatch">Move plants to existing batch</button>

    <?= $this->Form->create(null, ['url' => ['controller' => 'Plants', 'action' => 'delete'], 'templateVars' => ['header' => 'harvestBatchs'], 'id' => 'deletePlants', 'style' => ['display: inline-block;']]); ?>
    <?= $this->Form->hidden('batch_id', ['value' => $harvest_batch_id]); ?>
    <?= $this->Form->button('Delete plants', ['class' => 'btn btn-danger', 'id' => 'deletePlantsBtn', 'value' => '1', 'style' => ['display: inline-block;']]); ?>
    <?= $this->Form->end(); ?>
</div>
<div class="modal fade" id="movePlantsToExistingBatch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Move plants to existing batch</h4>
            </div>
            <div class="modal-body">
                <div class="row" id="modalBatchAdd">
                    <?= $this->Form->create(null, ['url' => ['controller' => 'HarvestBatches', 'action' => 'movePlantsToExistingBatch'], 'templateVars' => ['header' => 'harvestBatch'], 'id' => 'existingBatch']); ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Choose Batch</th>
                                <th>Batch No.</th>
                                <th>Cultivar</th>
                                <th>Planted Date</th>
                                <th>Current Zone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($harvestBatches as $batch) { ?>
                                <tr>
                                    <td><button data-batch="<?= $batch->id ?>" class="btn btn-primary btn-sm chooseBatch"><i class="fa fa-check"></i></button></td>
                                    <td id='batchNo'><?= $this->Html->link('Batch ' . $batch->batch_number, ['action' => 'view', $batch->id]) ?></td>
                                    <td><?= h($batch->planted_date); ?></td>
                                    <td id='currentZone'><?= h($batch->current_zone) && $batch->current_zone != 'Pending' ? $this->Html->link($batch->current_zone->label, ['controller' => 'Zones', 'action' => 'view', $batch->current_zone->id]) : 'Pending' ?></td>
                                </tr>
                            <?php } ?>
                            <input type="hidden" id="newBatchId" name="newBatchId" />
                            <input type="hidden" name="currentBatchId" value="<?= $harvest_batch_id ?>" />
                            <?= $this->Form->end(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>