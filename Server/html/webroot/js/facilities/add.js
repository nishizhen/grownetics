$(document).ready(function() {

    //var mymap = L.map('mapid');

    // Mapbox
    var mymap = L.mapbox.map('mapid');
    var geocoder = L.mapbox.geocoder('mapbox.places', { "accessToken": 'pk.eyJ1IjoiZ3Jvd25ldGljcyIsImEiOiJjaW1wM3hpbWQwMG50djlreXJ0ZXZ6ejdlIn0.QRo5hHRpNshj8pSOeljtxg'});

    //

    //.setView([51.505, -0.09], 13); // London
    //mymap.setView([40.01734, -105.27168],13); // Boulder


    mymap.on('click', function onMapClick(evt) {
        console.log("clicked on: " + evt.latlng);

        $("#latitude").val(evt.latlng.lat);
        $("#longitude").val(evt.latlng.lng);

        geocoder.reverseQuery(evt.latlng, function(err, geocode) {
            if (err) {
                console.warn("geocoder error:" + err);
            } else {
                console.log("geocode: ", geocode);
                if (geocode.features) {
                    var place_name = geocode.features[0].place_name;
                    var comma = place_name.indexOf(',');
                    $("input#name").val(place_name.substring(0, comma));
                    $("input#street-address").val(place_name);
                }
            }
        });

    });

    mymap.on('locationfound', function onLocationFound(evt) {
        console.log("location found: " + evt);
    });

    mymap.on('locationerror', function onLocationError(evt) {
        console.warn("LOCATION FAIL", evt);
        mymap.setView([40.01734, -105.27168],13);
        //    .addControl(L.mapbox.geocoderControl('mapbox.places', { "accessToken": 'pk.eyJ1IjoiZ3Jvd25ldGljcyIsImEiOiJjaW1wM3hpbWQwMG50djlreXJ0ZXZ6ejdlIn0.QRo5hHRpNshj8pSOeljtxg'}
        //));
    });

    // auto-locate
    mymap.locate({
       "setView": true, 
       "enableHighAccuracy": true,
       "timeout": 30000
    });


    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'grownetics.pk26fle5', //mike.c@grownetics.co:GrowBetter99
        accessToken: 'pk.eyJ1IjoiZ3Jvd25ldGljcyIsImEiOiJjaW1wM3hpbWQwMG50djlreXJ0ZXZ6ejdlIn0.QRo5hHRpNshj8pSOeljtxg'
    }).addTo(mymap);

});
