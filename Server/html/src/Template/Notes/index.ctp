<div class="notes index large-9 medium-8 columns content">
    <h3><?= __('Notes') ?></h3>
    <?php foreach ($notes as $note) :
        echo $this->element('photo_note_display', [
            'note' => $note
        ]);
    endforeach; ?>
    <?= $this->element('paginator') ?>
</div>
