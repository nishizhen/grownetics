$(document).ready(function() {
    $('.editNoteBtn').click(function() {
        window.location.href = '/notes/edit/' + $(this).parents('.post-container').find('input').val();
    });
});