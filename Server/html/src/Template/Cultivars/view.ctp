<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cultivar $cultivar
 */
echo $this->Html->script('cultivars/notesHandler', ['block' => 'scriptBottom']);
echo $this->Html->script('notes/notesDateHandler', ['block' => 'scriptBottom']);
?>
<div class="col-lg-12 mt cultivars">
    <div class="row content-panel">
        <div class="col-md-4 profile-text mt mb centered">
            <div class="right-divider hidden-sm hidden-xs">
                <h4><?php $lastRecipe = $cultivar->last_recipe_used;
                    if ($lastRecipe) {
                        echo $lastRecipe->label;
                    } else {
                        echo "<a href='/harvest-batches/add'>Make a batch with this cultivar!</a>";
                    } ?></h4>
                <h6>Most Recent Batch's Recipe Created With <?= $cultivar->label ?></h6>
                <h4><?= $cultivar->batch_count ?></h4>
                <h6>Total Batches Grown</h6>
            </div>
        </div>
        <!--/col-md-4 -->

        <div class="col-md-4 profile-text">
            <h2><?= $cultivar->label ?></h2>
            <p><?= $cultivar->description ?></p>
            <?php echo $this->cell('PhotoNoteUploadModal', [$cultivar->id], ['modelType' => 'Cultivar']); ?>
        </div>
        <!--/col-md-4 -->

        <div class="col-md-4 centered">
            <div class="profile-pic">
                <p>
                    <br>
                    <?= $this->Html->link('<img src="https://www.gravatar.com/avatar/' . md5(strtolower(trim($cultivar->id))) . '
                    ?d=identicon" class="img-circle">', ['controller' => 'Cultivars', 'action' => 'view', $cultivar->id], ['escape' => false]); ?>

                </p>
            </div>
        </div>
        <!--/col-md-4 -->
    </div><!-- /row -->
</div>
<!--/col-lg-12 -->

<div class="col-lg-12 mt">
    <div class="row content-panel">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-justified">
                <li class="active">
                    <a data-toggle="tab" href="#overview">Cultivar Data</a>
                </li>
                <li>
                    <a data-toggle="tab" id="notesTab" href="#notes">Notes</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#edit">Edit</a>
                </li>
            </ul>
        </div>
        <!--/panel-heading -->
        <div class="row mb ml">
            <div class="panel-body">
                <div class="tab-content">
                    <div id="overview" class="tab-pane active">
                        <div class="row" style="margin: 0 auto;">

                            <ul class="nav nav-pills nav-justified">
                                <li class="active"><a data-toggle="pill" href="#growInfo">Grow Info</a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="growInfo" class="tab-pane fade in active">
                                    <br>

                                    <div class="harvestBatches index large-9 medium-8 columns content">
                                        <h3><?= __('Harvest Batches grown with ' . $cultivar->label) ?><?= $this->Html->link(__('<i class="fa fa-plus"></i> New Harvest Batch'), ['controller' => 'HarvestBatches', 'action' => 'add'], ['class' => 'btn btn-sm btn-success', 'style' => 'float: right; margin-right: 30px', 'escape' => false]) ?></h3>

                                        <table cellpadding="0" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><?= $this->Paginator->sort('batch_number', ['label' => 'Batch No']) ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('cultivar_id') ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('planted_date', 'Plant Date') ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('next_batch_process') ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('recipe_id') ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('plant_count') ?></th>
                                                    <th scope="col"><?= $this->Paginator->sort('current_zone') ?></th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?= $this->Form->resetTemplates(); ?>
                                                <?php foreach ($harvestBatches as $harvestBatch) : ?>
                                                    <tr>
                                                        <td id='batchNo'><?= $this->Html->link('Batch ' . $harvestBatch->batch_number, ['controller' => 'HarvestBatches', 'action' => 'view', $harvestBatch->id]) ?></td>
                                                        <td id='batchStrain'><?= $harvestBatch->has('cultivar') ? $this->Html->link($harvestBatch->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $harvestBatch->cultivar->id]) : '' ?></td>
                                                        <td><?= h($harvestBatch->planted_date); ?></td>
                                                        <td id='nextBatchProcessDate' data-nextZone='<?= $harvestBatch->next_task->zone->label ?>' data-nextTaskType='<?= $harvestBatch->next_task->type ?>'><?= h(date('n/j/y', strtotime($harvestBatch->next_task->due_date))); ?></td>
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
                                </div>

                            </div>
                        </div>


                    </div>

                    <div id="notes" class="tab-pane fade">
                        <div style="width: 40%">
                            <?php echo $this->cell('PhotoNoteDisplay', [$cultivar->id], ['modelType' => 'Cultivar']); ?>
                        </div>
                    </div>
                    <div id="edit" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-8 col-lg-offset-2 detailed">
                                <?= $this->Form->create($cultivar, ['url' => ['action' => 'edit'], 'templateVars' => ['header' => 'Options'], 'type' => 'file']) ?>
                                <?php
                                echo $this->Form->input('label', ['label' => 'Name']);
                                echo $this->Form->input('description', ['style' => 'width:495px; margin-left: 15px']);
                                ?>
                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-10">
                                        <button class="btn btn-theme" type="submit">Save</button>
                                    </div>
                                </div>

                                <?= $this->Form->end(); ?>
                            </div>
                            <!--/col-lg-8 -->
                        </div>
                        <!--/row -->
                    </div>
                    <!--/tab-pane -->
                </div><!-- /tab-content -->
            </div>
            <!--/panel-body -->
        </div><!-- / row content-panel -->
    </div><!-- /col-lg-12 -->