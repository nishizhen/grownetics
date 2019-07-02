/* Floorplans edit.js */

$(document).ready(function() {
	var editor = new GrowServer.Floorplan.Editor();



//----------------------
    var rotateAngle = 0;

    $("#clockwise").on("click", function() {
        rotateAngle += 1;
        d3.selectAll("#dimensions-svg").selectAll("rect").attr("transform", "rotate("+rotateAngle +")");

        $(".leaflet-overlay-pane svg path").attr("transform", "rotate("+rotateAngle +")");
        //$("input.floorplan-dim").trigger("change");

        $("input[name='offsetRotation']").val(rotateAngle);

        return false;
    });

    $("#anti-clockwise").on("click", function() {
        rotateAngle -= 1;
        d3.selectAll("#dimensions-svg").selectAll("rect").attr("transform", "rotate("+rotateAngle+")");

        $(".leaflet-overlay-pane svg path").attr("transform", "rotate("+rotateAngle +")");

       //$("input.floorplan-dim").trigger("change");
       $("input[name='offsetRotation']").val(rotateAngle);

       return false;
    });

    


/*
    // Initialise the FeatureGroup to store editable layers
//var
 drawnItems = new L.FeatureGroup();
mymap.addLayer(drawnItems);

// Initialise the draw control and pass it the FeatureGroup of editable layers
var drawControl = new L.Control.Draw({
    draw: {
        polyline: false,
        circle: false,
        marker: false
    },
    edit: {
        featureGroup: drawnItems        
    },
    options: {
        polygon: {
            allowIntersection: false, // Restricts shapes to simple polygons
            drawError: {
                color: '#e1e100', // Color the shape will turn when intersects
                message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
            },
            shapeOptions: {
                color: "#bada55",
                clickable: false
            }
        },
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

    // if (type === 'marker') {
    //     layer.bindPopup('A popup!');
    // }

    drawnItems.addLayer(layer);

});

*/


}); // $(document).ready()