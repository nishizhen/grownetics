/*
 * facilities/edit.js
 */
$(document).ready(function() {

	//var
	 mymap = new L.Map("mapid", {center: [37.8, -96.9], zoom: 4})
    .addLayer(new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"));

	mymap.setView([40.01734, -105.27168],13); // Boulder

	var boundsArray = [];

	
	var lat = parseFloat($("#FacilityLatitude").val());
	var lon = parseFloat($("#FacilityLongitude").val());

	var marker = L.marker([lat,lon]).addTo(mymap);

	boundsArray.push(L.latLng(lat, lon));


	mymap.fitBounds(L.latLngBounds(boundsArray));


/*
	mymap.pm.addControls();


	//var
	// polygonLayer = L.layerGroup();

	//polygonLayer.pm.addControls();
	//polygonLayer.pm.enable();



	//polygonLayer.addTo(mymap);


	//var
	geoJSONLayer = L.geoJSON();
	geoJSONLayer.pm.enable();


	geoJSONLayer.pm.toggleEdit();

	geoJSONLayer.on('pm:edit', function(eeheehee) {
		console.log(eeheehee);
	});

	*/


	// Initialise the FeatureGroup to store editable layers
//var
 drawnItems = new L.FeatureGroup();
mymap.addLayer(drawnItems);

// Initialise the draw control and pass it the FeatureGroup of editable layers
var drawControl = new L.Control.Draw({
    edit: {
        featureGroup: drawnItems        
    },
    options: {
        rectangle: {
            shapeOptions: {
            	color: "#bada55",
           		clickable: false
           	}
        }
    }
});
mymap.addControl(drawControl);


mymap.on('draw:created', function (e) {
    var type = e.layerType,
        layer = e.layer;

    if (type === 'marker') {
        layer.bindPopup('A popup!');
    }

    drawnItems.addLayer(layer);
});

});

