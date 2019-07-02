$(document).ready(function() {
  $("time.timeago").timeago();
});
$(".editTaskBtn").on('click', function(e) {

	var row = $(this).offsetParent();
	var curr_assignee = row.find('#assignee').text();
	var curr_zone_label = row.find('.zone a').text();
	row.find('#assignee').text('');
	row.find('.zone').text('');
	row.find('.task-title').css('overflow', 'visible');

	//hide edit, complete, delete btns
	this.setAttribute("style", "display:none;");
	row.find('.completeTaskBtn').toggle();
	row.find('.deleteTaskBtn').toggle();
	
	//populate users dropdown
	var curr_assignee_id = row.find('#assignee').attr('data');
	var selector_assignee = "<select id='assignee_dropdown' />";
	var s = $(selector_assignee);
	$.each(JSON.parse(users), function( index, value ) {
		$("<option />", {value: value.name, text: value.name}).appendTo(s);
	});
	s.appendTo(row.find('#assignee'));
	var originalAssignee = "#assignee_dropdown option[value='"+curr_assignee+"']";
	$(originalAssignee).attr("selected",true);
	// /end users dropdown

	//populate zones dropdown
	var curr_zone_id = row.find('.zone').attr('data');
	var selectEle = row.find('.zone');

	$('.ui .dropdown').dropdown(
		{'onChange': function(value, text, ele) {
		}
	});
	var content = '<div class="ui fluid search selection dropdown" style="width:75%"> <i class="dropdown icon"></i> <div class="default text" data-value="'+curr_zone_id+'">'+ curr_zone_label + '</div>' + '<div class="menu"> ';
	$.each(JSON.parse(zones), function( ind, val ) {
		content += '<div class="item" data-value="'+ val.id +'">'+val.label+'</div>';
	});
	content += '</div></div> on';
	row.find('.zone').html(content);
	selectEle.find('.ui.search.dropdown').dropdown();

	row.find('.item').off('click').click(function(e) {
		$(this).offsetParent().find('.taskZone').text($(this).text());
	});

	// task date edit
	var taskDate = row.find('#taskDate').text();
	row.find('#taskDate').text("");
	var newDate = new Date(taskDate);
	var formattedDate = newDate.getFullYear().toString().substr(-2) + "-" + (newDate.getMonth()+1) + "-" + newDate.getDate();

	var selector = "<input type='datepicker' id='taskDateInput' style='width:80px' value='"+formattedDate+"' />";
	var d = $(selector);
	d.appendTo(row.find('#taskDate'));
	$("input[id='taskDateInput']").datetimepicker({
	    icons: {
	        time: "fa fa-clock-o",
	        date: "fa fa-calendar",
	        up: "fa fa-arrow-up",
	        down: "fa fa-arrow-down",
	        previous: 'fa fa-arrow-left',
	        next: 'fa fa-arrow-right',
	        clear: 'fa fa-trash'
	    },
	    format: 'YYYY-MM-DD',
	    showClear: true  
	});
	// /task date edit

	row.find('.cancelEditBtn').show();
	row.find('.submitEditBtn').show();

	row.find('.cancelEditBtn').off('click').click(function(e) {
		row.find('.task-title').css('overflow', 'hidden');
		row.find('.cancelEditBtn').hide();
		row.find('.submitEditBtn').hide();
		row.find('.editTaskBtn').show();
		row.find('.completeTaskBtn').show();
		row.find('.deleteTaskBtn').show();
		row.find('#assignee').html("<a href='/users/view/"+curr_assignee_id+"'>"+curr_assignee+"</a>");
		row.find('.zone').html("<a href='/zones/view/"+curr_zone_id+"'>"+curr_zone_label+"</a>");
		row.find('#taskDate').html(taskDate);
	});
	row.find('.submitEditBtn').off('click').click(function(e) {
		row.find('.task-title').css('overflow', 'hidden');
		var new_assignee = s.find(":selected").text();
		if (row.find(".ui.search").dropdown('get value') == '') {
			var new_zone = row.find(".text").data('value');
		} else {
			var new_zone = row.find(".ui.search").dropdown('get value');
		}

		var assignee_id = 1;
		$.each(JSON.parse(users), function( index, value ) {
			if (new_assignee == value.name) {
				assignee_id = index + 1;
			}
		});


		var new_assignee = assignee_id;
		var task_id = parseInt(row.find('.task-title-sp').data('taskid'));
		var new_date = moment(d.val()).format('YYYY-MM-DD');

		var newdata = {
			task_id: task_id, 
			assignee_id: new_assignee, 
			zone_id: new_zone,
			due_date: new_date	
		};
		$.ajax({
        	url: "/tasks/updateBatchTask",
        	type: 'post',
        	data: newdata,
        	success: function(data) {
        		row.find('#assignee').html('<a href=/users/view/'+new_assignee+'>'+s.find(":selected").text()+'</a>');
        		row.find('#assignee').data(new_assignee);
        		row.find('.zone').html('<a href=/zones/view/'+new_zone+'>'+row.find(".text").text()+'</a>');
        		row.find('.zone').data(new_zone);
        		row.find('#taskDate').html(" on "+moment(new_date).format('M/DD/YY'));
        		row.find('.editTaskBtn').show();
				row.find('.completeTaskBtn').show();
				row.find('.deleteTaskBtn').show();
				row.find('.cancelEditBtn').hide();
				row.find('.submitEditBtn').hide();
				row.find('.zone').attr('data-value', new_zone);
				row.find('.text').attr('data-value', new_zone);
      		},
      		error : function(data) {
     			$.gritter.add({
					title: 'Warning',
					text: 'The due date cannot be on/before the previous task or on/after the next task due date.',
					class_name: 'gritter-light',
					time: '5500'
				});
            }
    	});
	});
});
$(".completeTaskBtn").on('click', function() {
	var now = moment().format('ddd, MMM Do h:mmA');
	var row = $(this).offsetParent();
	var task_id = parseInt(row.find('.task-title-sp').data('taskid'));
	var task_label = row.find('#taskLabel').text().replace(/ +(?= )/g,'');
	var confirmMarkComplete = confirm("CONFIRM: "+task_label.trim()+" ("+now+")?");
	var task = {
		task_id: task_id
	};
	if (confirmMarkComplete) {
		row.find('.completeTaskSpinner').show();
		$.ajax({
    		url: "/tasks/markCompleted",
    		type: 'post',
    		data: task,
    		success: function(data) {
    			row.find('.completeTaskSpinner').hide();
    			location.reload();
  			},
  			error: function(data) {
  				row.find('.completeTaskSpinner').hide();
  				$.gritter.add({
					title: 'Warning',
					text: data.responseText,
					class_name: 'gritter-light',
					time: '7500'
				});
  			}
		});
	}
});
$(".deleteTaskBtn").off('click').click(function() {

	var row = $(this).offsetParent();
	var task_id = parseInt(row.find('.task-title-sp').data('taskid'));
	var batch_recipe_entry_id = parseInt(row.find('.task-title-sp').data('taskbre'));	
	var task_label = row.find('#taskLabel').text().replace(/ +(?= )/g,'');
	var task_date = row.find('#taskDate').text();
	var taskData = {
		task_id: task_id,
		batch_recipe_entry_id: batch_recipe_entry_id
	};
	var confirmDelete = confirm("CONFIRM: You would like to delete: '"+task_label.trim()+" on "+task_date+"'?");
	if (confirmDelete) {	
		$.ajax({
    		url: "/tasks/deleteBatchProcess",
    		type: 'post',
    		data: taskData,
    		success: function(data) {
	    		row.remove();
  			}
		});
	}
});