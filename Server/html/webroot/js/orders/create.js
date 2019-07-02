$().ready(function() {
	$('#OrderAmount').change(function(e) {
		var amount = $(e.currentTarget).val();
		var price = $('#price').html();
		$('#total').html(parseFloat(amount) * parseFloat(price));
	});
});