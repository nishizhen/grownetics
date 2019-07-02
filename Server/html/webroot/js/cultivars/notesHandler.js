$(document).ready(function() {
    $('#notesTab').click(function(){
        $('.editNoteBtn').click(function() {
            window.location.href = '/notes/edit/' + $(this).parents('.post-container').find('input').val();
        });
    });
});

//http://jsfiddle.net/LvsYc/
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function (e) {
            $('<img src="'+e.target.result+'" width="100" height="100"  id="lastImg" class="img-square image-shadow myPreview" alt="">').insertAfter('#lastImg');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
$("#myImg").change(function(){
    readURL(this);
});