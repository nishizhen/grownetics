$(document).ready(function() {
	
	var mymap = new L.Map("mapid", {center: [37.8, -96.9], zoom: 4})
    .addLayer(new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"));

	mymap.setView([40.01734, -105.27168],13); // Boulder

	var boundsArray = [];

	$("div.facilities > table > tbody > tr").each(function(i, row) { 
		var facilityId = parseInt($(row).children('td:nth-child(1)')[0].innerHTML);
		var lat = parseFloat($(row).children('td:nth-child(4)')[0].innerHTML);
		var lon = parseFloat($(row).children('td:nth-child(5)')[0].innerHTML);

        var growIcon = L.icon({
            iconUrl: "/img/grownetics-logo-small.png",
            iconSize: [30, 40]
        });

		var marker = L.marker([lat,lon], {
			"facilityId": facilityId,
			icon: growIcon
		});

		marker.on('click', function(mouseEvent) {
			console.log(mouseEvent.target.options);
			if (mouseEvent.target.options) {	
				window.open("/facilities/view/" + mouseEvent.target.options.facilityId);
			}
		});

		marker.addTo(mymap);

		boundsArray.push(L.latLng(lat, lon));

	});

	mymap.fitBounds(L.latLngBounds(boundsArray));
});

