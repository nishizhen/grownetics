<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal<?= $model . $id ?>">
    <i class="fa fa-plus" aria-hidden="true"></i> Note
    <i class="fa fa-sticky-note" aria-hidden="true"></i>
    <i style="position:relative; display: none;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i>
</button>
<div class="modal fade" id="myModal<?= $model . $id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add Note to <?= $model ?> #<?= $id ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->resetTemplates(); ?>
                <?= $this->Form->create($note, ['url' => ['controller' => 'Notes', 'action' => 'add'], 'enctype' => 'multipart/form-data']) ?>
                <div class="row">
                    <div class="form-group">
                        <?= $this->Form->label('photo upload', null, ['class' => 'control-label col-sm-3']); ?>
                        <div class="col-sm-9">
                            <?= $this->Form->file('photo_name', ['accept' => '.jpg,.png']); ?>
                        </div>
                    </div>
                    <div class="form-group" style="padding-top: 50px;">
                        <?= $this->Form->label('note description', null, ['class' => 'control-label col-sm-3']); ?>
                        <div class="col-sm-12">
                            <?= $this->Form->textarea('note', ['class' => 'form-control', 'label' => 'note description']);  ?>
                        </div>
                    </div>
                    <?= $this->Form->hidden('cultivar_id', ['value' => $cultivar_id]); ?>
                    <?= $this->Form->hidden('batch_id', ['value' => $batch_id]); ?>
                    <?= $this->Form->hidden('zone_id', ['value' => $zone_id]); ?>
                    <?= $this->Form->hidden('plant_id', ['value' => $plant_id]); ?>
                    <?= $this->Form->hidden('modelName', ['value' => $model]); ?>
                    <?= $this->Form->postButton('Submit', [], ['class' => 'btn btn-success']); ?>
                </div>
            </div>
        </div>
    </div>
</div>