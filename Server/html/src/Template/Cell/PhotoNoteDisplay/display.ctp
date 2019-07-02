<?php foreach ($data as $note) {
    echo $this->element('photo_note_display', [
        'note' => $note
    ]);
} ?>