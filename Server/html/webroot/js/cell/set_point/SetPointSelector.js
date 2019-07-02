function saveSetPoint(newVal, set_point_id, origin) {
	origin.parent().find('.spinner').css('visibility', 'visible');
    $.ajax({
	    url: "/SetPoints/edit/"+set_point_id,
	    type: 'patch',
	    data: {"value":newVal},
	    success: function(res) {
			origin.parent().find('.spinner').css('visibility', 'hidden');
	    }
	});
}

function overrideZoneSetPoint(newVal, zone_id, target_type, data_type, origin) {
	origin.parent().find('.spinner').css('visibility', 'visible');
    $.ajax({
	    url: "/SetPoints/edit/"+origin.parent().find('.SaveSetPoint').data('set_point_id'),
	    type: 'post',
	    data: {"value":newVal, "target_id": zone_id, "target_type": target_type, "data_type": data_type, "default_setpoint_id": 0},
	    success: function(zone_set_point_id) {
	    	origin.parent().find('.spinner').css('visibility', 'hidden');
	    	if (data_type == 2) {
	    		origin.find('.setPointInput').attr('style', 'border:1px solid rgb(26,140,255); font-weight: bold;');
	    	} else if (data_type == 3) {
	    		origin.find('.setPointInput').attr('style', 'border:1px solid rgb(204,37,41); font-weight: bold;');
	    	} else {
	    		origin.find('.setPointInput').attr('style', 'border:1px solid rgb(105,224,105); font-weight: bold;');
	    	}
	    	origin.parent().find('.SaveSetPoint').data('set_point_id', origin.parent().find('.SaveSetPoint').data('set_point_id'));	    	
	    	origin.parent().find('.SaveSetPoint').data('set_point_type', 0);
	    	origin.parent().find('.ResetToDefault').css('display', 'inline');
	    }
	});
}

function processInput(RevertSetPoint, SaveSetPoint, setPointInput, zone_id, setPointEle) {
	if ($.isNumeric(setPointInput.val())) {
		if ( SaveSetPoint.data('default_setpoint_id') != 0  && SaveSetPoint.data('controller') == 'Zones') {
			overrideZoneSetPoint(setPointInput.val(), zone_id, 0, SaveSetPoint.data('data_type'), setPointEle);
		} else {
			saveSetPoint(setPointInput.val(), SaveSetPoint.data('set_point_id'), setPointEle);
		}
		RevertSetPoint.css('display', 'none');
		SaveSetPoint.css('display', 'none');
		setPointInput.attr('value', setPointInput.val());
	}
}

$(".setPointInput").off('keyup').keyup(function(evt) {
	var keycode = (evt.keyCode ? evt.keyCode : evt.which);
	var $this = $(this).parent();
	var RevertSetPoint = $this.find('.RevertSetPoint');
	var SaveSetPoint = $this.find('.SaveSetPoint');
	var setPointInput = $this.find('.setPointInput');
	var zone_id = $this.parent().data('zone_id');
	var data_type = SaveSetPoint.data('data_type');

	RevertSetPoint.css('display', 'inline-block');
	SaveSetPoint.css('display', 'inline-block');
	if (data_type == 2 && (setPointInput.val() < 0 || setPointInput.val() > 100)) {
		$this.parent().find('.form-group').addClass('has-error');
		if (!($this.parent().find('p').hasClass('help-block'))) {
			$this.parent().append('<p class="help-block">Enter a number 1 - 100.</p>');
		}
	} else if (data_type == 3 && (setPointInput.val() < 0 || setPointInput.val() > 120)) {
		$this.parent().find('.form-group').addClass('has-error');
		if (!($this.parent().find('p').hasClass('help-block'))) {
			$this.parent().append('<p class="help-block">Enter a number 1 - 120.</p>');
		}
	} else if (data_type == 4 && (setPointInput.val() < 0 || setPointInput.val() > 1500)) {
		$this.parent().find('.form-group').addClass('has-error');
		if (!($this.parent().find('p').hasClass('help-block'))) {
			$this.parent().append('<p class="help-block">Enter a number 1 - 1500.</p>');
		}
	} else {
		$this.parent().find('.form-group').removeClass('has-error');
		$this.parent().find('.help-block').remove();
		if (keycode == 13) {
	    	processInput(RevertSetPoint, SaveSetPoint, setPointInput, zone_id, $this);
	    }
	    SaveSetPoint.off('click').click(function(e) {
	    	processInput(RevertSetPoint, SaveSetPoint, setPointInput, zone_id, $this);
	    });
	}
	if (keycode == 27) {
		$this.parent().find('.form-group').removeClass('has-error');
		$this.parent().find('.help-block').remove();
    	RevertSetPoint.css('display', 'none');
		SaveSetPoint.css('display', 'none');
		setPointInput.val(setPointInput.attr('value'));
	}
	RevertSetPoint.off('click').click(function(e) {
		$this.parent().find('.form-group').removeClass('has-error');
		$this.parent().find('.help-block').remove();
   		RevertSetPoint.css('display', 'none');
		SaveSetPoint.css('display', 'none');
		setPointInput.val(setPointInput.attr('value'));
    });
});

$('.ResetToDefault').off('click').click(function(e) {
	var $this = $(this).parent();
	var data_type = $this.find('.SaveSetPoint').data('data_type');
	var set_point_id = $this.find('.SaveSetPoint').data('set_point_id');
	$this.find('.RevertSetPoint').css('display', 'none');
	$this.find('.SaveSetPoint').css('display', 'none');
	$.ajax({
	    url: "/SetPoints/edit/"+set_point_id+"/"+true,
	    type: 'post',
	    success: function(default_setpoint_value) {
	    	if (data_type == 2) {
	    		$this.find('.setPointInput').attr('style', 'border:1px solid rgba(26,140,255,0.5); color: rgba(0, 0, 0, 0.5);');
	    	} else if (data_type == 3) {
	    		$this.find('.setPointInput').attr('style', 'border:1px solid rgba(204,37,41,0.5); color: rgba(0, 0, 0, 0.5);');
	    	} else {
	    		$this.find('.setPointInput').attr('style', 'border:1px solid rgba(105,224,105,0.5); color: rgba(0, 0, 0, 0.5);');
	    	}
	    	$this.find('.setPointInput').val(default_setpoint_value);
	    	$this.find('.setPointInput').attr('value', default_setpoint_value);
	    	$this.find('.ResetToDefault').css('display', 'none');
	    }
	});	
});