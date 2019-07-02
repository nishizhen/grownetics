var GrowServer = GrowServer || {};


GrowServer.fnFormatDetails = function( oTable, nTr, cellName, taskEntryList = null, taskTypes, dateRange )
{
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    if (taskEntryList != null) {
        taskEntryList.forEach(function(entry) {
            var task = entry.task_label != '' ? taskTypes[entry.task_type_id] + ' - ' + entry.task_label : taskTypes[entry.task_type_id];
            sOut += '<tr><td>'+ cellName.substr(0, cellName.indexOf('-')) +' - Task'+'<button id="'+entry.id+'" class="btn btn-primary btn-xs edit-btn" onclick="editEntry(this.id)"><i class="fa fa-pencil" id="edit-task"></i></button></td><td>'+task+'</td><td>'+entry.days+'</td>';
            sOut += '<td><button id="'+entry.id+'" class="fa fa-trash btn-xs btn btn-danger" onclick="deleteEntry(this.id)"></button></td></tr>'
        });
    }
    var first = dateRange.substr(0, dateRange.indexOf('-'));
    var second = dateRange.split(' - ')[1];

    sOut += '<tr></tr><td><select class="form-control" id="tasks" name="'+cellName+'"></select></td>'+
        '<td style="text-align: center;"><p style="display: inline-block; margin-right: 10px;">'+first+'</p><input type="range" name="'+cellName+'_slider" min="'+parseInt(first)+'" max="'+parseInt(second)+'" value="'+parseInt(first)+'" class="slider" id="myRange" style="display: inline-block !important; width: 50% !important;"><p style="display: inline-block; margin-left: 10px;">'+second+'</p><br/><div style="text-align: center"><p id="'+cellName+'sliderValue">Day: '+ parseInt(first)+'</p></div></td>' +
        '<td style="display:none;" id="td-'+cellName+'"><input placeholder="Task description"class="form-control" type="text" id="label" name="'+cellName+'-task-label"></td>' +
        '<td><button type="submit" id="add-'+cellName+'-task" class="btn btn-success"><i class="fa fa-plus"></i></button></td></tr>';
    sOut += '</table>';
    sOut += '<form></form>'

    return sOut;
}

function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this task entry?')) {
        $.ajax({
            url: '/recipeEntries/delete/'+id,
            type: 'post',
            success: function() {
                $('.spinner').hide();
                window.location.reload();
            },
            error: function() {
                $.gritter.add({
                    title: 'Warning',
                    text: data.responseText,
                    class_name: 'gritter-light',
                    time: '7500'
                });
            }
        });
    }
}

function editEntry(id) {
    window.location.replace('/recipeEntries/edit/' + id);
}

$(document).ready(function() {
    var nCloneTh = document.createElement('th');
    var nCloneTd = document.createElement('td');
    nCloneTd.innerHTML = '<img src="../../img/plantsTable/details_open.png">';
    nCloneTd.className = "center";

    $('#hidden-table-info thead tr').each(function () {
        this.insertBefore(nCloneTh, this.childNodes[0]);
    });

    $('#hidden-table-info tbody tr').each(function () {
        this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
    });

    /*
     * Initialse DataTables, with no sorting on the 'details' column
     */
    var oTable = $('#hidden-table-info').dataTable({
        "oLanguage": {
            "sEmptyTable": "Add Recipe Entries using the fields below."
        },
        "aoColumnDefs": [
            {"bSortable": false, "aTargets": [0]}
        ]
    });


    /* Add event listener for opening and closing details
     * Note that the indicator for showing which row is open is not controlled by DataTables,
     * rather it is done here
     */
    $('#hidden-table-info tbody td img').on('click', function () {
        var nTr = $(this).parents('tr')[0];
        var cellName = $(this).parents('td').next().attr('name');
        var zone_type_id = $(this).parents('td').next().attr('id');
        var tdStringData = $(this).parents('td').next().children().attr('href');
        var parentRecipeEntryId = tdStringData.match(/\/\d+/i)[0].substring(1);
        var taskEntries;

        if (oTable.fnIsOpen(nTr)) {
            /* This row is already open - close it */
            this.src = "../../img/plantsTable/details_open.png";
            oTable.fnClose(nTr);
        } else {
            var dateRange = $('#days_in_' + cellName).text();
            var firstDate = parseInt(dateRange.substr(0, dateRange.indexOf('-')));

            /* Get data and open this row */
            this.src = "../../img/plantsTable/details_close.png";
            var types;
            $.when(
                $.ajax({
                    url: "/tasks/get.json",
                    type: 'get',
                    data: JSON,
                    success: function (data) {
                        types = data['enums']['types'];
                    },
                    error: function (data) {
                        alert('Error retrieving task types');
                        window.location.reload();
                        throw new Error('Error retrieving task types!');
                    }
                }),
                $.ajax({
                    url: "/recipes/getTaskRelatedEntries.json",
                    type: 'post',
                    data: {recipe_id: parentRecipeEntryId},
                    success: function (data) {
                        taskEntries = data.taskEntries;
                    },
                    error: function (data) {
                        alert('Error retrieving this zones tasks\'');
                        window.location.reload();
                    }
                })
            ).then(function () {
                oTable.fnOpen(nTr, GrowServer.fnFormatDetails(oTable, nTr, cellName, taskEntries, types, dateRange), 'details');
                for (var key in types) {
                    $('select[name="' + cellName + '"]').append($('<option>', {
                        value: key,
                        text: types[key]
                    }));
                }

                $('select[name="' + cellName + '"]').on('change', function () {
                    if ($('select[name="' + cellName + '"]').find(':selected').text() == 'Generic') {
                        $('#td-' + cellName).css('display', 'block');
                    } else {
                        $('#td-' + cellName).css('display', 'none');
                    }
                });


                $('input[name="' + cellName + '_slider"]').on('input', function () {
                    $('#' + cellName + 'sliderValue').html('Day: ' + $('input[name="' + cellName + '_slider"]').val());
                });

                var buttonId = '#add-' + cellName + '-task';
                $(buttonId).on('click', function () {
                    var taskTypeId = $('select[name="' + cellName + '"]').find(':selected').val();
                    var taskLabel = $('input[name="' + cellName + '-task-label"]').val();
                    var recipeId = $('input[name="recipe_id"]').val();
                    var daysUntilTask = $('input[name="' + cellName + '_slider"]').val();

                    if (daysUntilTask == '') {
                        alert("Must provide days until task is required!");
                        throw new Error("Must provide days until task is required!");
                    }
                    var newRecipeEntry = {
                        'recipe_id': recipeId,
                        'days': daysUntilTask,
                        'parent_recipe_entry_id': parentRecipeEntryId,
                        'plant_zone_type_id': zone_type_id,
                        'task_type_id': taskTypeId,
                        'task_label': taskLabel
                    }

                    $.ajax({
                        url: "/recipeEntries/add",
                        type: 'post',
                        data: newRecipeEntry,
                        success: function (data) {
                            $('.spinner').hide();
                            window.location.reload();
                        },
                        error: function (data) {
                            $.gritter.add({
                                title: 'Warning',
                                text: data.responseText,
                                class_name: 'gritter-light',
                                time: '7500'
                            });
                        }
                    })
                });
            });
        }
    });
});