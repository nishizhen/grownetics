var currentRequest = [];
function saveHarvestBatchWeights(input) {
    if (!$.inArray( input.attr('id'), currentRequest )) {
      currentRequest.push(input.attr('id'));
    }
    var new_weight = {};
    new_weight[input.attr('id')] = input.val();
    input.parent().find('.spinner').show();
    input.parent().find('.checkMark').hide();
    currentRequest[input.attr('id')] = $.ajax({
        url: "/harvest-batches/edit/"+input.data('harvest_batch_id'),
        type: 'patch',
        data: new_weight,
        beforeSend : function()    {
        if (currentRequest[input.attr('id')] != null) {
                currentRequest[input.attr('id')].abort();
            }
        },
        success: function(res) {
            input.parent().find('.spinner').hide();
            input.parent().find('.checkMark').show();
        }
    });
}