<button type="button" class="btn-primary btn-success" data-toggle="modal" data-target="#plantUploadFor<?=$plant_id?>"><i class="fa fa-plus" aria-hidden="true"></i> Note <i class="fa fa-sticky-note" aria-hidden="true"></i><i style = "position:relative; display: none;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i></button>
<div class="modal fade" id="plantUploadFor<?=$plant_id?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add Note</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <?= $this->Form->resetTemplates(); ?>
                    <?= $this->Form->create($note, ['url' => ['controller' => 'Photos', 'action' => 'add'], 'templateVars' => ['header'=>'<b>Add Photos</b>'], 'enctype'=>'multipart/form-data']) ?>
                    <div class="form-group">
                        <?= $this->Form->label('photo upload', null, ['class' => 'control-label col-sm-3']); ?>
                        <div class="col-sm-9">
                            <?= $this->Form->file('photo', ['accept' => '.jpg,.png']); ?>
                        </div>
                    </div>
                    <?= $this->Form->resetTemplates(); ?>
                    <div class="form-group" style="padding-top: 50px;">
                        <?= $this->Form->label('note description', null, ['class' => 'control-label col-sm-3']); ?>
                        <div class="col-sm-9">
                            <?= $this->Form->textarea('note', ['class' => 'form-control']);  ?>
                        </div>
                    </div>
                    <?= $this->Form->hidden('plant_id', ['value' => $plant_id]); ?>
                    <?= $this->Form->hidden('batch_id', ['value' => null]); ?>
                    <?= $this->Form->hidden('cultivar_id', ['value' => null]); ?>
                    <?= $this->Form->hidden('zone_id', ['value' => null]); ?>
                </div>
            </div>
            <div class="modal-footer">
                <?= $this->Form->postButton('Submit', ['type' => 'submit', "class" => "btn btn-default", 'data-dismiss' =>"modal"]) ?>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>