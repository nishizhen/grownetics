$('.batch_process_checkbox').change(function() {
	$('tbody').find(".batch_process_checkbox").each(function() {
		if ($(this).prop('checked')==true){ 
        	$('#updateBatchesBtn').show();
        	return false;
    	} else {
    		$('#updateBatchesBtn').hide();
    	}
	});
});

var batch_ids = [];
var task_ids = [];
$('#updateBatchesBtn').on('click', function() {
	var now = new Date();
	$.each($('.batch_process_checkbox:checked'), function(ind, batch) {
		var row = $(batch).parents('tr');
		var due_date = new Date(row.find('#nextBatchProcessDate').html());
		if (Date.parse(due_date) != now.setHours(0,0,0,0)) {
			if (row.find('#nextBatchProcessDate').data('nexttasktype') == 0) {
				var bool = confirm('CONFIRM: Plant '+ row.find('#batchNo').text()+ ' - ' +row.find('#plantStrain').text()+ ' in ' +row.find('#nextBatchProcessDate').data('nextzone')+'?');
			} else if (row.find('#nextBatchProcessDate').data('nexttasktype') == 2) {
				var bool = confirm('CONFIRM: Harvest '+ row.find('#batchNo').text()+ ' - ' +row.find('#plantStrain').text()+ ' from ' +row.find('#currentZone').text()+'?');
			} else {
				var bool = confirm('CONFIRM: Move '+ row.find('#batchNo').text()+ ' - ' +row.find('#plantStrain').text()+ ' from ' +row.find('#currentZone').text()+ ' to ' +row.find('#nextBatchProcessDate').data('nextzone')+'?');
			}
			if (bool == true) {
				batch_ids.push(parseInt(batch.value));
				task_ids.push(parseInt(row.find('#nextBatchProcessID').data('taskid')));
			}	
		} else {
			batch_ids.push(parseInt(batch.value));
			task_ids.push(parseInt(row.find('#nextBatchProcessID').data('taskid')));
		}
	});
	if (!($.isEmptyObject(batch_ids))) {
		for (var xx = 0; xx < task_ids.length; xx++) {
			var task = {
				task_id: task_ids[xx]
			};
			$.ajax({
	    		url: "/tasks/markCompleted",
	    		type: 'post',
	    		data: task,
	    		success: function(data) {
	    			window.location.reload();
	  			},
	  			error: function(data) {
	  				$.gritter.add({
						title: 'Warning',
						text: data.responseText,
						class_name: 'gritter-light',
						time: '3500'
					});
	  			}
			});
		}
		

    }
});