$(document).ready(function() {
      $('#deletePhoto').click(function() {
        $.ajax({
            url: '/photos/delete/'+$('#myImg').attr('name'),
            type: 'post',
            success: function(data) {
                $('.spinner').hide();
                console.log(data);
                alert('check Console');
                // window.history.back();
            },
            error: function(error) {
                console.log(error);
                alert('Been an error!');
                window.history.back();
            }
        });

    });

    //http://jsfiddle.net/LvsYc/
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#myImg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#myPhotoSelector").change(function(){
        readURL(this);
    });
});