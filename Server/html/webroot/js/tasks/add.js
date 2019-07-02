$(document).ready(function() {
    $( "#type" ).change(function(e) {
    if ($('option:selected', this).text() == 'Generic') {
            $('.taskDescription').show();
            $('#recipe-entries').find('.groupDDown .text').text('None');
            $('#recipe-entries').find('.groupDDown #default-option').text('None');
            $('#due_date_field').removeAttr('required');
        } else if ($('option:selected', this).text() == 'Move') {
            $('#due_date_field').prop('required', true);
            $('input[name="room_id"]').prop('required', true);
            $('.taskDescription').hide();
            $('#recipe-entries').find('.groupDDown .text').text('Auto-fill');
            $('#recipe-entries').find('.groupDDown #default-option').text('Auto-fill');
        } else {
            $('#due_date_field').removeAttr('required');
            $('.taskDescription').hide();
            $('#recipe-entries').find('.groupDDown .text').text('Auto-fill');
            $('#recipe-entries').find('.groupDDown #default-option').text('Auto-fill');
      }
    });
    $('.groupDDown').dropdown();
    $('.roomDDown').dropdown({'onChange': function(value, text, ele) {
        var room_id = value;
        var defaultText = "";
        ele.parents('tr').find('.groupDDown').addClass('loading');
        if ($(this).data("batch_id") == '') {
            defaultText = "None";
        } else {
            defaultText = "Auto-fill";
        }
        $.getJSON("/zones/getGroupsForRoom/" + room_id + ".json", function (group_json) {
            var content = "<option id='default-option' class='item' data-value=''>"+defaultText+"</option>";
            $.each(group_json.groups, function(id, label) {
                content += "<option class='item' data-value='"+id+"' value='"+id+"'>"+label+"</option>"
            });
            ele.parents('tr').find('.groupDDown .text').text(defaultText);
            ele.parents('tr').find('#default-option').text(defaultText);
            ele.parents('tr').find('.groupDDown').removeClass('loading');
            ele.parents('tr').find('.groupMenu').html(content);
            $('.spinner').hide();
        });
    }});
});