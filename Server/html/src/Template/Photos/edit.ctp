<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Photo $photo
 */
echo $this->Html->script('photos/edit', ['block' => 'scriptBottom']);
?>

<fieldset style="margin: 0 auto;">
    <?php
    echo '<img src="/photos/'.$photo->photo_name.'" width="250" height="250" id="myImg" name="'.$photo->id.'" class="img-square image-shadow" style="margin:10px;">';
    ?>
</fieldset>
<div style="margin-top:20px;">
    <?= $this->Form->create($photo,['templateVars'=>['header'=>'Change photo'], 'enctype'=>'multipart/form-data']) ?>
    <?= $this->Form->file('photo', ['accept' => '.jpg,.png', 'id' => 'myPhotoSelector']); ?>
    <?= $this->Form->button(__('Submit'), ['returnUrl' => $this->request->params['controller'], 'class' => 'btn-primary btn']) ?>
    <?= $this->Form->end() ?>

    <?= $this->Form->resetTemplates();?>
    <?= $this->Form->postLink('<button class="btn btn-danger btn-primary"><i class="fa fa-trash"></i> Delete</button>', ['controller' => 'Photos', 'action' => 'delete', $photo->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete Photo #{0}?', $photo->id)]); ?>
</div>

