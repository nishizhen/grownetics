<button type="button" id="modalNoteButton" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewNotesFor<?=$id;?>"><i class="fa fa-eye"></i> View Notes <i class="fa fa-sticky-note" aria-hidden="true"></i><i style = "position:relative; display: none;" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i></button>
<div class="modal fade" id="viewNotesFor<?=$id?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; overflow: auto;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">View Notes For <?= $model?> #<?php echo $id?></h4>
            </div>
            <div class="modal-body" style="overflow-y: scroll; height:550px;">
                <?php echo $this->cell('PhotoNoteDisplay', [$id], ['modelType' => $model]); ?>
            </div>
        </div>
    </div>
</div>