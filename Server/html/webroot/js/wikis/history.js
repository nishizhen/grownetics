$(document).ready(function () {

	var prev;
	var curr;


$("input[name='pfield']").click(function(){


  prev = $("input[name='pfield']:checked").val();
  console.log(prev);
  

});

$("input[name='cfield']").click(function(){


  curr = $("input[name='cfield']:checked").val();
  console.log(curr);
  

});

$(".diffSubmit").click(function() {
	
	prev = $("input[name='pfield']:checked").val();
	curr = $("input[name='cfield']:checked").val();

	if (prev != null && curr != null) {

		var slug = window.location.pathname;

		var newSlug = slug.split("/");
		var sl = (newSlug[newSlug.length-1]);

		window.location.pathname = "/wikis/diff/"+sl+"/"+prev+"/"+curr;
	} else {
		alert("Please select both a current and previous version.");
	}
	

});



});

