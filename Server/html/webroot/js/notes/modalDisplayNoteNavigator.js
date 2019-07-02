$(document).ready(function() {
    $('#modalNoteButton').click(function() {
        $('.post-container').click(function() {
            window.location.href = '/notes/edit/' + $(this).find('input').val();
        });
    });
});