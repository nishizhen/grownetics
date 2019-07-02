// plantsList
function fnFormatDetails(oTable, nTr, data) {
    var aData = oTable.fnGetData(nTr);
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    sOut += '<thead><tr>' +
        '<th>Whole Plant Wet weight (' + data.plant.weightUnit + ')</th>' +
        '<th>Green Waste Defoliate weight (' + data.plant.weightUnit + ')</th>' +
        '<th>Defoliated Plant Wet weight (' + data.plant.weightUnit + ')</th>' +
        '</tr></thead><tbody><tr class="plant_id" data-plant_id="' + data.plant.id + '">';
    sOut += '<td>' +
        '<input type="number" value="' + data.plant.wet_whole_weight + '" class="form-control" onkeyup="savePlantWeights($(this));" id="wet_whole_weight' + data.plant.id + '"></input><i style="display: none" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; color:#009966" class="checkMark fa fa-check"></i>' +
        '</td>';
    sOut += '<td>' +
        '<input type="number" value="' + data.plant.wet_waste_weight + '" class="form-control" onkeyup="savePlantWeights($(this));" id="wet_waste_weight' + data.plant.id + '"></input><i style="display: none" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; color:#009966" class="checkMark fa fa-check"></i>' + '</td>';
    sOut += '<td>' +
        '<input type="number" value="' + data.plant.wet_whole_defoliated_weight + '" class="form-control" onkeyup="savePlantWeights($(this));" id="wet_whole_defoliated_weight' + data.plant.id + '"></input><i style="display: none" class="spinner fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i><i style="display: none; color:#009966" class="checkMark fa fa-check"></i>' + '</td></tr>';
    sOut += '</tbody></table><div style="text-align: center"><button style="display:none; font-size: 0.9em;" class="btn btn-xs btn-success saveWeightBtn">Save</button>' +
        '<button style="display:none; font-size: 0.9em;" class="btn btn-xs btn-danger revertWeightBtn">Cancel</button></div>';

    return sOut;
}

var currentRequest = [];
function savePlantWeights(input) {
    if (!$.inArray(input.attr('id'), currentRequest)) {
        currentRequest.push(input.attr('id'));
    }
    var plant_id = input.parents('tr').data('plant_id');
    var new_weights = {};
    var weightFields = ['wet_whole_weight', 'wet_waste_weight', 'wet_whole_defoliated_weight', 'dry_whole_weight', 'dry_waste_weight', 'dry_whole_trimmed_weight']
    input.parents('tr').find('input').each(function (ind, val) {
        var new_val = $(val).val();
        if (new_val == '') {
            new_weights[weightFields[ind]] = 0;
        } else {
            new_weights[weightFields[ind]] = new_val;
        }
    });
    input.parent().find('.spinner').show();
    input.parent().find('.checkMark').hide();
    currentRequest[input.attr('id')] = $.ajax({
        url: "/Plants/edit/" + plant_id + "/" + input.attr('id'),
        type: 'patch',
        data: new_weights,
        beforeSend: function () {
            if (currentRequest[input.attr('id')] != null) {
                currentRequest[input.attr('id')].abort();
            }
        },
        success: function (res) {
            input.parent().find('.spinner').hide();
            input.parent().find('.checkMark').show();
        }
    });
}
$(document).ready(function () {

    $('#deletePlants').click(function (event, target) {
        event.preventDefault()
        if (confirm("Are you sure you want to delete these plants?")) {
            $.ajax({
                url: '/plants/delete',
                data: plants
            });
        }
    })
    /*
   * Insert a 'details' column to the table
   */
    var nCloneTh = document.createElement('th');
    var nCloneTd = document.createElement('td');
    var movePlantCheckTh = document.createElement('th');
    var movePlantCheckTd = document.createElement('td');
    movePlantCheckTd.className = 'center';

    nCloneTd.innerHTML = '<img src="../../img/plantsTable/details_open.png">';

    nCloneTd.className = "center";

    $('.hidden-table-info thead tr').each(function () {
        this.insertBefore(nCloneTh, this.childNodes[0]);
    });

    $('.hidden-table-info tbody tr').each(function () {
        this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
    });

    $('.hidden-table-info thead tr').each(function () {
        this.insertBefore(movePlantCheckTh, this.childNodes[0]);
    });

    $('.hidden-table-info tbody tr').each(function () {
        movePlantCheckTd.innerHTML = '<input type="checkbox" class="center moveCheckbox" id="' + $(this).data('plant_id') + '"/>';
        this.insertBefore(movePlantCheckTd.cloneNode(true), this.childNodes[0]);
    });

    $('.hidden-table-info').find('img').each(function () {
        $(this).off('click').click(function (e) {
            openDetails($(this));
        });
    });

    /*
    * Initialse DataTable, with no sorting on the 'details' column
    */
    var oTable = $('.hidden-table-info').dataTable({
        "oLanguage": {
            "sEmptyTable": "Add Plants by clicking the +Plants button above."
        },
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [0] }
        ],
        "aaSorting": [[1, 'asc']]
    });


    //   var plants = GrowServer.plantWeights;

    function openDetails(row) {
        var nTr = row.parents('tr')[0];
        if (oTable.fnIsOpen(nTr)) {
            /* This row is already open - close it */
            row[0].src = "../../img/plantsTable/details_open.png";
            oTable.fnClose(nTr);
        } else {
            /* Open this row */
            row[0].src = "";
            row.addClass('spinner fa fa-circle-o-notch fa-spin fa-fw');
            $.ajax({
                url: "/Plants/view/" + row.parents('tr').data('plant_id') + ".json",
                type: 'get',
                success: function (response) {
                    oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr, response), 'details');
                    row.removeClass('spinner fa fa-circle-o-notch fa-spin fa-fw');
                    row[0].src = "../../img/plantsTable/details_close.png";
                },
                error: function (err) {
                    console.log(err);
                    row.removeClass('spinner fa fa-circle-o-notch fa-spin fa-fw');
                    row[0].src = "../../img/plantsTable/details_close.png";
                }
            });
        }
    }
});

$(document).ready(function () {
    $('#newBatchBtn').click(function (e) {
        e.preventDefault();
        gatherSelectedPlants('#newBatchData');

        var form = $('#newBatchData');
        form.submit();
    });

    $('#deletePlantsBtn').click(function (e) {
        e.preventDefault();
        gatherSelectedPlants('#deletePlants');

        var form = $('#deletePlants');
        form.submit();
    });

    $('.chooseBatch').click(function (e) {
        e.preventDefault();
        gatherSelectedPlants('#existingBatch');
        if (confirm('Move selected plants to batch #' + $(this).data('batch')) + '?') {
            var form = $('#existingBatch');
            const newBatchId = $(this).data('batch');
            $('#newBatchId').val(newBatchId);
            form.submit();
        } else {
            return;
        }
    });
});

function gatherSelectedPlants(tableId) {
    var plantIds = [];
    var dTable = $('.display').dataTable();
    var allPages = dTable.fnGetNodes();

    allPages.forEach(function (row) {
        if ($(row).find('input[type="checkbox"]')[0].checked) {
            const id = $(row).find('input[type="checkbox"]').attr('id')
            if (plantIds.indexOf(id) < 0) {
                plantIds.push(id);
            }
        }
    });

    plantIds.forEach(function (plant) {
        $('<input />').attr('type', 'hidden').attr('name', 'plants[]').attr('value', plant).appendTo(tableId);
    });
}
