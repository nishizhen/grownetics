$( "#type" ).change(function(e) {
	if (e.target.value == 2) {
		$('#emailContactMethod').closest('span').show();
		$('#phoneContactMethod').closest('.form-group').hide();
	} else {
		$('#emailContactMethod').closest('span').hide();
		$('#phoneContactMethod').closest('.form-group').show();
	}
});