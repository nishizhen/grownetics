


this.editLayersControl = L.control({ position: 'topright' });

this.editLayersControl.onClick = function( evt) {
    var target = $(evt.target);
    var name = target.attr("name");
    var value = target.attr("value");

    switch(value) {
        case "outlines":
            GrowServer.map.leafletMap.removeLayer(GrowServer.map.editLayersControl.layers[name + " Markers"]);
            GrowServer.map.leafletMap.addLayer(GrowServer.map.editLayersControl.layers[name + " Outlines"]);
            break;
        case "markers":
            GrowServer.map.leafletMap.removeLayer(GrowServer.map.editLayersControl.layers[name + " Outlines"]);
            GrowServer.map.leafletMap.addLayer(GrowServer.map.editLayersControl.layers[name + " Markers"]);
            break;
        case "hide":
            GrowServer.map.leafletMap.removeLayer(GrowServer.map.editLayersControl.layers[name + " Outlines"]);
            GrowServer.map.leafletMap.removeLayer(GrowServer.map.editLayersControl.layers[name + " Markers"]);
            break;
    }
};

this.editLayersControl.onAdd = function() {
    this.layers = {};

    var container = L.DomUtil.create('div', 'edit-layers-control leaflet-control-layers leaflet-control');

    //'<a class="leaflet-control-layers-toggle" href="#" title="Layers"></a>'

    var list = L.DomUtil.create('ul', 'edit-layers-list', container);

    L.DomEvent.addListener(list, "click", this.onClick);

    var listItem;
    var mapItemType;

    for (var ii = 0; ii< mapItemTypes.length; ii++) {
        mapItemType = mapItemTypes[ii];
        this.layers[mapItemType.label + " Outlines"] = L.featureGroup();
        this.layers[mapItemType.label + " Markers"] = L.featureGroup();

        //<div class="leaflet-control-layers-separator"></div>
        L.DomUtil.create("div", "leaflet-control-layers-separator", list);

        listItem = L.DomUtil.create('li', 'edit-layers-list-item', list);
        $(listItem).html('<div>' +
            '<label>'+mapItemType.label+'</label>' +
            '<input type="radio" name="'+mapItemType.label+'" value="outlines">Show Outlines</input><br/>' +
            '<input type="radio" name="'+mapItemType.label+'" value="markers">Show Markers</input><br/>' +
            '<input type="radio" name="'+mapItemType.label+'" value="hide">Hide</input><br/>' +
            '</div>'
        );
    }


    var growIcon  =  L.icon({
        iconUrl: '/img/map-icons/grownetics-logo-marker-icon.png',
        iconRetinaUrl: '/img/map-icons/grownetics-logo-marker-icon-2x.png',
        iconSize: [25, 41],
        iconAnchor: [1, 26],
        shadowUrl: '/leaflet/images/marker-shadow.png',
        shadowSize: [41, 41]
    });

    var mapItems = JSON.parse(GrowServer.Floorplan.layers.map_items);

    for (var ix = 0; ix < mapItems.length; ix++) {
        var mapItem = mapItems[ix];
        mapItemType = getMapItemTypeById(mapItem.map_item_type_id).label;

        if (mapItem.geoJSON) {
            L.geoJSON(mapItem.geoJSON, {
                style: function (feature) {
                    return {
                        color: GrowServer.Color.getColorForMapItemType(mapItemType),
                        fill: true,
                        fillColor: GrowServer.Color.getColorForMapItemType(mapItemType),
                        weight: 1
                    };
                }
            }).bindTooltip(mapItem.label, {
                opacity: 1.0,
                className: "mapItemLabel",
                direction: "center",
                permanent: false
            }).addTo(this.layers[mapItemType + " Outlines"]);
        }
        L.marker([mapItem.latitude,mapItem.longitude], {
            title: mapItem.label,
            icon: growIcon
        }).bindTooltip(mapItem.label, {
            opacity: 1.0,
            className: "mapItemLabel",
            direction: "center",
            permanent: false
        }).addTo(this.layers[mapItemType + " Markers"]);
    }


    //////
    // var sensors = JSON.parse(GrowServer.sensorData);

    // for (var i = 0; i < sensors.length; i++) {
    //     var sensor = sensors[i];

    //     var sensorType = sensor.sensor_type.label;

    //     var color;
    //     color = GrowServer.Color.getColorForSensorType(sensorType);

    //     var sensorMarker = getMarkerForMapItemType(sensor.map_item, "Sensor", {
    //         "id": sensor.id,
    //         "color": color
    //     });

    //     if (sensorType == "Humidity Sensor") {
    //         var height = sensor.map_item.offsetHeight;
    //         if (height == 1) {
    //             sensorMarker.addTo(getDataLayer("Humidity Low"));
    //         } else if (height == 2) {
    //             sensorMarker.addTo(getDataLayer("Humidity High"));
    //         }
    //     } else {
    //            sensorMarker.addTo(getDataLayer(sensorType));
    //     }
    // }

    return container;
};



this.editMap = function() {
    this.editing = true;

    // remove leaflet default layers control
    this.leafletMap.removeControl(this.layerControl);

    // add editLayersControl control
    this.leafletMap.addControl(this.editLayersControl);

};

this.saveMap = function() {

    // remove editLayersControl control
    this.leafletMap.removeControl(this.editLayersControl);

    // add leaflet default layers control
    this.leafletMap.addControl(this.layerControl);

    this.editing = false;

};