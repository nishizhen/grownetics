<div class="post-container note-container">
    <?php echo '<input type="hidden" class="note-id" value="' . $note->id . '"></input>'; ?>

    <?php if (isset($note->photos[0]->photo_name)) { ?>
        <div class="post-thumb">
            <?php
            echo $this->Html->image('/photos/load/' . $note->photos[0]->id, ['width' => '100', 'height' => '100', 'class' => 'img-square image-shadow']);
            ?>
        </div>
    <?php } ?>
    <div class="post-content">
        <div class="post-title">
            <p>
                <?= $note->user->name ?> on
                <span class="noteCreatedDate"><?= strtotime($note->created); ?></span>
            </p>
        </div>
        <p style="overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical; border-bottom: 1px solid rgba(160, 160, 160, .5); 
        border-radius: 2px;"><?php echo $note->note; ?></p>
        <div class="" style="font-style: italic; font-size: 0.9em">
            <?php
            if ($note->harvest_batch) {
                echo 'Batch: ' . $this->Html->link('Batch #' . $note->harvest_batch->batch_number . ' - ' . $note->harvest_batch->cultivar->label, ['controller' => 'HarvestBatches', 'action' => 'view', $note->batch_id]);
            }
            ?></div>
        <div class="" style="font-style: italic; font-size: 0.9em">
            <?php
            if ($note->cultivar) {
                echo ' Cultivar: ' . $this->Html->link($note->cultivar->label, ['controller' => 'Cultivars', 'action' => 'view', $note->cultivar_id]);
            }

            ?></div>
        <div class="" style="font-style:italic; font-size: 0.9em">
            <? if ($note->zone_id) {
                echo ' Zone: ' . $this->Html->link($note->zone->label, ['controller' => 'Zones', 'action' => 'view', $note->zone_id]);
            } ?>
        </div>
        <div class="" style="font-style:italic; font-size: 0.9em">
            <?php
            if ($note->plants) {

                if (sizeof($note->plants) > 1) {
                    echo 'Plants: ';
                    foreach ($note->plants as $plant) {
                        echo $plant->short_plant_id . ', ';
                    }
                } else {
                    echo 'Plant #: ' . $note->plants[0]->short_plant_id . '';
                }
            } ?> </div>

        </p>
        <?= $this->Form->resetTemplates(); ?>
        <div>
            <?= $this->Form->postLink('<button class="btn btn-danger btn-xs pull-right deleteNoteBtn"><i class="fa fa-trash"></i></button>', ['controller' => 'Notes', 'action' => 'delete', $note->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete Note #{0}?', $note->id)]); ?>
            <?= $this->Form->resetTemplates(); ?>
            <?= $this->Form->end(); ?>
        </div>
        <div>
            <button class="btn btn-primary btn-xs pull-right editNoteBtn"><i class="fa fa-pencil"></i> </button>
            <?php if (isset($note->photos[0]->photo_name)) { ?>
                <?= $this->Html->link('<button class="btn btn-success btn-xs pull-right enlargePhotoBtn"><i class="fa fa-arrows-alt"></i></button>', ['controller' => 'Photos', 'action' => 'rawImage', $note->photos[0]->id], ['escape' => false, 'target' => '_blank']); ?>
            <?php } ?>
        </div>
    </div>
</div>