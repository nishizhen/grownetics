$(document).ready(function() {

    recipeChangeListener();
});

function recipeChangeListener() {
    $("select[name='recipe_id']").change(function (value, text, ele) {
        $('.spinner').show();
        var recipe_id = $(this).val();
        $.getJSON("/recipes/view/" + recipe_id + ".json", function (recipe_json) {
            var content = '<tr> <th> </th> <th> Room </th> <th> Bench </th> </tr>';
            $.each(recipe_json.recipeEntries, function (ind, recipeEntry) {
                content += '<tr> <td>' + recipeEntry.zone_type_label + '</td> <td>' +
                    '<div class="form-group ui search normal selection dropdown roomDDown"> <input type="hidden" name="room_ids[]" form="harvestBatchForm" required> <i class="dropdown icon"></i> <div class="default text">Select a Room...</div> <div class="menu roomMenu">';
                $.each(recipeEntry.plant_zones, function (ind, zone) {
                    if (zone.zone_type_id == 1 && zone.plant_zone_type_id != 0) {
                        content += '<option name="zones[]" class="item" data-value="' + zone.id + '" value="' + zone.id + '">' + zone.label + '</option>';
                    }
                });
                content += ' </div> </div> </td> <td> <div class="form-group ui search normal selection dropdown groupDDown" > <input type="hidden" name="group_ids[]" form="harvestBatchForm" required>' +
                    ' <i class="dropdown icon"></i> <div class="text" data-value="0">Auto-fill</div> <div class="menu groupMenu"> <option class="item" data-value="0" value="0">Auto-fill</option>' +
                    ' </div> </div> </td> </tr>';
            });
            $('.spinner').hide();
            $('.recipeForm tbody').html(content);
            $('.roomDDown').dropdown({
                'onChange': function (value, text, ele) {
                    var room_id = value;
                    ele.parents('tr').find('.groupDDown').addClass('loading');
                    $.getJSON("/zones/getGroupsForRoom/" + room_id + ".json", function (group_json) {
                        var content = "<option class='item' data-value='0'>Auto-fill</option>";
                        $.each(group_json.groups, function (id, label) {
                            content += "<option class='item' data-value='" + id + "' value='" + id + "'>" + label + "</option>"
                        });
                        ele.parents('tr').find('.groupDDown .text').text('Auto-fill');
                        ele.parents('tr').find('.groupDDown').removeClass('loading');
                        ele.parents('tr').find('.groupMenu').html(content);
                        $('.spinner').hide();
                    });
                }
            });
        });

    });

    $('.roomDDown').dropdown({
        'onChange': function (value, text, ele) {
            var room_id = value;
            ele.parents('tr').find('.groupDDown').addClass('loading');
            $.getJSON("/zones/getGroupsForRoom/" + room_id + ".json", function (group_json) {
                var content = "<option class='item' data-value='0'>Auto-fill</option>";
                $.each(group_json.groups, function (id, label) {
                    content += "<option class='item' data-value='" + id + "' value='" + id + "'>" + label + "</option>"
                });
                ele.parents('tr').find('.groupDDown .text').text('Auto-fill');
                ele.parents('tr').find('.groupDDown').removeClass('loading');
                ele.parents('tr').find('.groupMenu').html(content);
                $('.spinner').hide();
                $('.ui.dropdown.groupDDown').dropdown()
            });
        }});

}
