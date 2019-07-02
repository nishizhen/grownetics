<?php
echo $this->Html->script('cell/task/batchWorkflowCard', ['block' => 'scriptBottom']);
echo $this->Html->script('harvestbatches/saveWeights', ['block' => 'scriptBottom']);
echo $this->Html->script('plants/add', ['block' => 'scriptBottom']);
echo $this->Html->script('notes/recentNotesNavigator', ['block' => 'scriptBottom']);
echo $this->Html->script('notes/notesDateHandler', ['block' => 'scriptBottom']);
echo $this->Html->script('harvestbatches/add', ['block' => 'scriptBottom']);
?>
<div class="col-lg-12 mt">
    <div class="row content-panel">
        <div class="col-md-4 profile-text mt mb centered">
            <div class="right-divider hidden-sm hidden-xs">
                <h4><?= $this->Html->link($harvestBatch->recipe->label, ['controller' => 'Recipes', 'action' => 'view', $harvestBatch->recipe->id], ['name' => 'recipe_id', 'id' => $harvestBatch->recipe->id]); ?></h4>
                <h6>Recipe</h6>
                <h4><?= $harvestBatch->batch_number ?></h4>
                <h6>BATCH NUM</h6>
                <?php echo $this->cell('PhotoNoteUploadModal', [$harvestBatch->id], ['modelType' => 'Batch']); ?>
                <button type="button" class="btn btn-primary btn-sm btn-success" data-toggle="modal" data-target="#myModalPlants"><i class="fa fa-plus" aria-hidden="true"></i> Plants <i class="fa fa-leaf fa-.5x" aria-hidden="true"></i><i style="position:relative; display: none;" class="plantsSpinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i></button>
                <button type="button" id="openBatchChartBtn" class="btn btn-primary" onclick="location.href = '/charts/harvest_batch_view/<?= $harvestBatch->id ?>'"><i class="fa fa-area-chart"></i></button>
                <div class="modal fade" id="myModalPlants" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myModalLabel">Add Plants</h4>
                            </div>
                            <?= $this->Form->resetTemplates(); ?>
                            <?= $this->Form->create($plant, ['id' => 'newPlantsForm', 'url' => ['controller' => 'Plants', 'action' => 'add'], 'templateVars' => ['header' => 'Add Plant for <b>' . $harvestBatch->cultivar->label . ' - Batch #' . $harvestBatch->batch_number . '</b>']]) ?>
                            <div class="modal-body">
                                <?= $this->Form->hidden('batch_id', ['id' => 'harvest_batch_id', 'value' => $harvestBatch->id]); ?>
                                <div>Range of Plant IDs:</div>
                                <div class="form-group">
                                    <?= $this->Form->label('plant_start_id', null, ['class' => 'control-label col-sm-3']); ?>
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('plant_start_id', ['id' => 'plant_start_id', 'class' => 'form-control', 'placeholder' => 'e.g. BD-3-001']); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= $this->Form->label('plant_end_id', null, ['class' => 'control-label col-sm-3']); ?>
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('plant_end_id', ['id' => 'plant_end_id', 'class' => 'form-control', 'placeholder' => 'e.g. BD-3-254']); ?>
                                    </div>
                                </div>
                                <div>Individual ID's:</div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?= $this->Form->textarea('plant_list', ['id' => 'plant_list', 'class' => 'form-control', 'placeholder' => '1AF001, 1AF002, 1AF003, ...']); ?>
                                        <span class="help-block">Add Plant ID's seperated by commas.</span>
                                    </div>
                                </div>
                                <div>Cultivar</div>
                                <div class="form-group">
                                    <?= $this->Form->label('Cultivar', null, ['class' => 'control-label col-sm-3']); ?>
                                    <div class="col-sm-9">
                                        <?= $this->Form->select('cultivar_id', $cultivars); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-default">Close</button>
                                <?= $this->Form->button('Submit', ["type" => "submit", "class" => "btn btn-primary submitAddPlants", 'data-dismiss' => "modal"]) ?>
                            </div>
                            <?= $this->Form->end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/col-md-4 -->

        <div class="col-md-4 profile-text">
            <h3><?= $this->Html->link($harvestBatch->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $harvestBatch->cultivar->id], ['name' => 'cultivar_id', 'id' => $harvestBatch->cultivar->id]); ?>
            </h3>
            <p>
                <?php
                echo $this->Form->control('whole_weight (' . $weightUnit . ')', [
                    'style' => 'margin-left:5px; width: 70px;',
                    'type' => 'number',
                    'onkeyup' => "saveHarvestBatchWeights($(this));",
                    'data-harvest_batch_id' => $harvestBatch->id,
                    'id' => 'dry_whole_weight',
                    'value' => $harvestBatch['dry_whole_weight'],
                    'templates' => [
                        'inputContainer' => '<div>{{content}}<i style="display: none; margin-left:5px;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; margin-left:5px; color:#009966" class="checkMark fa fa-check"></i></div>'
                    ]
                ]);
                echo $this->Form->input('waste_weight (' . $weightUnit . ')', [
                    'style' => 'margin-left:5px; width: 70px;',
                    'type' => 'number',
                    'onkeyup' => "saveHarvestBatchWeights($(this));",
                    'data-harvest_batch_id' => $harvestBatch->id,
                    'id' => 'dry_waste_weight',
                    'value' => $harvestBatch['dry_waste_weight'],
                    'templates' => [
                        'inputContainer' => '<div>{{content}}<i style="display: none; margin-left:5px;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; margin-left:5px; color:#009966" class="checkMark fa fa-check"></i></div>'
                    ]
                ]);
                echo $this->Form->input('whole_trimmed_weight (' . $weightUnit . ')', [
                    'style' => 'margin-left:5px; width: 70px;',
                    'type' => 'number',
                    'onkeyup' => "saveHarvestBatchWeights($(this));",
                    'data-harvest_batch_id' => $harvestBatch->id,
                    'id' => 'dry_whole_trimmed_weight',
                    'value' => $harvestBatch['dry_whole_trimmed_weight'],
                    'templates' => [
                        'inputContainer' => '<div>{{content}}<i style="display: none; margin-left:5px;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; margin-left:5px; color:#009966" class="checkMark fa fa-check"></i></div>'
                    ]
                ]);
                ?>
            </p>
        </div>
        <!--/col-md-4 -->

        <div class="col-md-4 centered">
            <div class="profile-pic">
                <p><?= $this->Html->link('<img src="https://www.gravatar.com/avatar/' . md5(strtolower(trim($harvestBatch->cultivar->id))) .
                        '?d=identicon" class="img-circle">', ['controller' => 'Cultivars', 'action' => 'view', $harvestBatch->cultivar->id], ['escape' => false]); ?></p>
            </div>
        </div>
        <!--/col-md-4 -->
    </div><!-- /row -->
</div>
<!--/col-lg-12 -->

<div class="col-lg-12 mt">
    <div class="row content-panel">
        <!--/panel-heading -->
        <div class="row mb ml">
            <div class="panel-body">
                <div class="tab-content">
                    <div id="overview" class="tab-pane active">
                        <div class="row">
                            <div class="col-md-6 detailed">
                                <h4>Recent Activity</h4>

                                <?php echo $this->cell('PhotoNoteDisplay', [$harvestBatch->id], ['modelType' => 'Batch', 'limit' => 3]); ?>

                                <!--/recent-activity -->
                            </div><!-- /detailed -->

                            <!--/col-md-6 -->
                            <div class="col-md-6 detailed">
                                <h4>Batch Stats</h4>
                                <div class="row centered mt mb">
                                    <div class="col-sm-4">
                                        <h1><i class="fa fa-money"></i></h1>
                                        <h3><?= $this->Number->currency($harvestBatch->plant_count * 1000) ?></h3>
                                        <h6>ESTIMATED EARNINGS</h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <h1><i class="fa fa-cannabis"></i></h1>
                                        <h3><?= $harvestBatch->plant_count ?></h3>
                                        <h6>PLANTS</h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <h1><i class="fa fa-shopping-cart"></i></h1>
                                        <h3>
                                            <?php
                                            $now = new DateTime();
                                            $diffDate = $now->diff($harvestBatch->planned_harvest_date);
                                            echo $diffDate->days;
                                            ?></h3>
                                        <h6>DAYS UNTIL HARVEST</h6>
                                        <h6>HARVEST DATE: <?php echo $harvestBatch->planned_harvest_date->format('l jS \of F\, Y'); ?> </h6>
                                    </div>
                                </div><!-- /row -->
                                <h4>Pending Tasks</h4>
                                <div class="row centered">
                                    <!--/row -->
                                    <?php echo $this->cell('BatchWorkflowCard', [$harvestBatch->id]); ?>
                                </div><!-- /col-md-6dd -->
                            </div>

                            <section class="wrapper">
                                <h3><i class="fa fa-angle-right"></i>Plants in Batch</h3>
                                <div class="row mb ml mt mr">
                                    <?= $this->cell('PlantsList', [$harvestBatch->id]) ?>
                                </div><!-- /row -->
                            </section><!-- /Plant Table -->
                        </div>
                    </div>
                </div><!-- /col-md-6dd -->
            </div>
        </div>
        <!--/tab-pane -->
    </div><!-- /tab-content -->
</div><!--/panel-body -->
