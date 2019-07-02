<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Note $note
 */
echo $this->Html->script('cultivars/notesHandler', ['block' => 'scriptBottom']);
echo $this->Html->script('photos/navigate', ['block' => 'scriptBottom']);
?>
<div class="notes form large-9 medium-8 columns content">
    <?= $this->Form->create($note,['templateVars'=>['header'=>'Edit note'], 'enctype'=>'multipart/form-data']) ?>
    <fieldset>
        <div class="form-group">
        <?php
            echo $this->Form->label('photo upload', null, ['class' => 'control-label col-sm-3']);
            
            echo $this->Form->file('photo_name', ['accept' => '.jpg,.png', 'id' => 'myImg']);
            if (count($note->photos) > 0 && $note->photos[0]) {
                $id = '';
                foreach ($note->photos as $index => $photo) {
                    if ($index == count($note->photos) - 1) {
                        $id = 'lastImg';
                    }
                    ?>
                    <div class="img-container">
                        <?php
                            echo $this->Html->image('/photos/load/'. $photo->id, ['width' => '100', 'height' => '100', 'id' => $id, 'name' => $photo->id, 'class' => 'img-square image-shadow myPreview']);
                            echo $this->Form->postLink('<button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>', ['controller' => 'Photos', 'action' => 'delete', $photo->id, 'class' => 'deleteForm',], ['block' => true, 'escape' => false, 'confirm' => __('Are you sure you want to delete photo #{0}?', $photo->id)]);
                        ?>
                    </div>
                    <?php
                }
            }
        ?>
        </div><div class="form-group"><?php
            echo $this->Form->label('note');
            echo $this->Form->textarea('note');
        ?></div>
        <div class="form-group">
            <?php echo $this->Form->label('Harvest Batch'); ?>
            <div class="ui search normal selection dropdown">
                <input type="hidden" name="batch_id" value="<?=$note->batch_id?>"><i class="dropdown icon"></i>
                <div class="text"><?= h($note->harvest_batch) ? 'Batch #'.$note->harvest_batch->batch_number.' - '. $note->harvest_batch->cultivar->label : 'None';?></div>
                <div class="menu">
                <div class="item" data-value="0">None</div>
                <?php foreach ($harvest_batches as $batch): ?>
                    <?php if ($batch->id == $note->batch_id) { ?>
                    <div class="item selected active" data-value="<?=$batch->id?>">
                    <?php } else { ?>
                    <div class="item" data-value="<?=$batch->id?>">
                    <?php } ?>
                        <?php echo 'Batch #'.$batch->batch_number.' - '. $batch->cultivar->label; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?php echo $this->Form->label('Cultivar'); ?>
            <div class="ui search normal selection dropdown">
                <input type="hidden" name="cultivar_id" value="<?=$note->cultivar_id?>"><i class="dropdown icon"></i>
                <div class="text"><?=h($note->cultivar) ? $note->cultivar->label : 'None';?></div>
                <div class="menu">
                <div class="item" data-value="0">None</div>
                <?php foreach ($cultivars as $id => $label): ?>
                    <?php if ($id == $note->cultivar_id) { ?>
                    <div class="item selected active" data-value="<?=$id?>">
                    <?php } else { ?>
                    <div class="item" data-value="<?=$id?>">
                    <?php } ?>
                        <?php echo $label; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?php echo $this->Form->label('Zone'); ?>
            <div class="ui search normal selection dropdown">
                <input type="hidden" name="zone_id" value="<?=$note->zone_id?>"><i class="dropdown icon"></i>
                <div class="text"><?= h($note->zone) ? $note->zone->label : 'None'; ?></div>
                <div class="menu">
                <div class="item" data-value="0">None</div>
                <?php foreach ($zones as $id => $label): ?>
                    <?php if ($note->zone != null && $id == $note->zone->id) { ?>
                    <div class="item selected active" data-value="<?=$id?>">
                    <?php } else { ?>
                    <div class="item" data-value="<?=$id?>">
                    <?php } ?>
                        <?php echo $label; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </fieldset>
    <?= $this->Form->button(__('Submit'), ['returnUrl' => $this->request->params['controller']]) ?>
    
    
    <?= $this->Form->end() ?>
    <?php 
    if ($note->batch_id) {
        echo  $this->Html->link(__('<button class="btn btn-primary" style=" margin-left:25px;"><i class="fa fa-arrow-circle-o-left"></i> Return to Batch #'.$note->harvest_batch->batch_number.' - '. $note->harvest_batch->cultivar->label.'</button>'), ['controller' => 'HarvestBatches', 'action' => 'view', $note->batch_id], ['escape' => false]);
    }
    ?>
    <?php 
    if ($note->cultivar_id) {
        echo  $this->Html->link(__('<button style="margin-left:15px;" class="btn btn-primary"><i class="fa fa-arrow-circle-o-left"></i> Return to '.$note->cultivar->label.'</button>'), ['controller' => 'Cultivars', 'action' => 'view', $note->cultivar_id], ['escape' => false]);
    }
    ?>
    <?= $this->fetch('postLink');?>
</div>
