/* Floorplan Map */

var GrowServer = GrowServer || {}

/**
 * Grownetics Map Object
 *
 * Keeps tracks of Grownetics Dashboard InfoMap
 *
 * Depends on Leaflet.js 1.x
 */
;(function () {
    GrowServer.Map = function (options) {
        L.extend(this, options)

        var zoomScale = 29

        var DEFAULT_LAYERS = ['Walls', 'Benches', 'Plants'] //,"Plant Placeholders", "Devices", "Appliances". "Rooms", "HVAC"];
        var DEFAULT_DATA_OVERLAY =
            '<div class="wi wi-thermometer">Temperature High</div>'

        var growIcon = L.icon({
            iconUrl: '/img/map-icons/grownetics-logo-marker-icon.png',
            iconRetinaUrl: '/img/map-icons/grownetics-logo-marker-icon-2x.png',
            iconSize: [25, 41],
            iconAnchor: [1, 26],
            shadowUrl: '/img//leaflet/images/marker-shadow.png',
            shadowSize: [41, 41]
        })

        ////////// Growserver.Map public API //////////
        this.updateMap = function (data) {
            for (var ii = 0; ii < data.length; ii++) {
                this.updateDataPoint(data[ii])
            }
        }

        this.updateDataPoint = function (dataPoint) {
            var sourceId = dataPoint.source_id
            var mapIcon = $('#grownetics-sensor-' + sourceId)
            if (mapIcon) {
                var data_time = moment.utc(
                    dataPoint.created,
                    'YYYY-MM-DD[T]HH:mm[Z]'
                )
                var current_time = moment.utc(
                    moment().subtract(120, 'seconds'),
                    'YYYY-MM-DD[T]HH:mm[Z]'
                )
                // display data < 2 minutes old
                if (data_time.isAfter(current_time)) {
                    var oldValue = mapIcon.html()
                    var newValue = dataPoint.value
                    if (
                        GrowServer.showMetric == false &&
                        dataPoint.data_type == 1
                    ) {
                        newValue = parseFloat(
                            (dataPoint.value * 9) / 5 + 32
                        ).toFixed(1)
                    } else {
                        newValue = dataPoint.value
                    }
                    if (parseFloat(oldValue) != parseFloat(newValue)) {
                        if (parseFloat(newValue) < parseFloat(oldValue)) {
                            $(mapIcon)
                                .offsetParent()
                                .find('.pulse')
                                .addClass('pulse_rays_down')
                                .one(
                                    'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd',
                                    function () {
                                        $(this).removeClass('pulse_rays_down')
                                    }
                                )
                        } else {
                            $(mapIcon)
                                .offsetParent()
                                .find('.pulse')
                                .addClass('pulse_rays_up')
                                .one(
                                    'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd',
                                    function () {
                                        $(this).removeClass('pulse_rays_up')
                                    }
                                )
                        }
                        mapIcon
                            .parent()
                            .css(
                                'background-color',
                                GrowServer.Color.getColorForSensorTypeAndValue(
                                    mapIcon.parent().data('sensortype'),
                                    dataPoint.value
                                )
                            )
                        mapIcon.html(newValue)
                    }
                } else {
                    mapIcon.html('?')
                    mapIcon.parent().css('background-color', 'transparent')
                }
            }
        }

        /////////  end Growserver.map public API //////////

        ////// leaflet map setup/initialization /////
        var leafletMap = (this.leafletMap = new L.Map('leaflet-map', {
            center: GrowServer.Floorplan.center,
            attributionControl: false,
            scrollWheelZoom: false,
            zoomSnap: 0.01
        }))

        //         var accessToken = 'pk.eyJ1Ijoibmlja2dyb3duZXRpY3MiLCJhIjoiY2pzeGxqMTM1MHI2NjQzb2Ruam96MG5nOCJ9.DsdL-qIPMpGYLn8QA5-pmQ';

        // var mapboxTiles = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v10/tiles/{z}/{x}/{y}?access_token=' + accessToken, {
        //        attribution: '© <a href="https://www.mapbox.com/feedback/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        // });
        // leafletMap.addLayer(mapboxTiles);

        GrowServer.Map.loading = L.control({ positon: 'bottomright' })
        GrowServer.Map.loading.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info') // create a div with a class "info"
            GrowServer.Map.loading.update()
            return this._div
        }

        // method that we will use to update the control based on feature properties passed
        GrowServer.Map.loading.update = function (props) {
            this._div.innerHTML =
                '<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        }

        GrowServer.Map.loading.addTo(this.leafletMap)

        this.leafletMap.addControl(
            new L.Control.Fullscreen({
                fullscreenControl: true
            })
        )

        this.leafletMap.on('fullscreenchange', function (e) {
            e.target.fitBounds(GrowServer.floorplanLayer_bounds)
        })

        this.leafletMap.on('baselayerchange', function (e) {
            let sensorType = e.name.replace(/(<([^>]+)>)/gi, '')
            console.log(sensorType)
            switch (sensorType) {
                case 'Humidity Low':
                case 'Humidity High':
                    GrowServer.setMapSensorType(2)
                    break
                case 'Temperature High':
                case 'Temperature Low':
                    GrowServer.setMapSensorType(3)
                    break
                case 'CO2':
                    GrowServer.setMapSensorType(4)
                    break
                case 'PAR':
                    GrowServer.setMapSensorType(11)
                    break
                case 'Soil Moisture':
                    GrowServer.setMapSensorType(13)
                    break
                case 'PH':
                    GrowServer.setMapSensorType(5)
                    break
                case 'EC':
                    GrowServer.setMapSensorType(7)
                    break
                case 'DO':
                    GrowServer.setMapSensorType(6)
                    break
                case 'Waterproof Temperature':
                    GrowServer.setMapSensorType(1)
                    break
                case 'SCD30 Co2':
                    GrowServer.setMapSensorType(16)
                    break
                case 'SCD30 Humidity':
                    GrowServer.setMapSensorType(17)
                    break
                case 'SCD30 Air Temperature':
                    GrowServer.setMapSensorType(18)
                    break
                case 'BME280 Humidity':
                    GrowServer.setMapSensorType(19)
                    break
                case 'BME280 Air Temperature':
                    GrowServer.setMapSensorType(20)
                    break
                case 'BME280 Air Pressure':
                    GrowServer.setMapSensorType(21)
                    break
                case 'LoRa Barometer Temperature':
                    GrowServer.setMapSensorType(22)
                    break
                case 'LoRa Barometric Pressure':
                    GrowServer.setMapSensorType(23)
                    break
                case 'LoRa Battery Level':
                    GrowServer.setMapSensorType(24)
                    break
                case 'LoRa Capacitor Voltage 1':
                    GrowServer.setMapSensorType(25)
                    break
                case 'LoRa Capacitor Voltage 2':
                    GrowServer.setMapSensorType(26)
                    break
                case 'LoRa Co2 Concentration Lpf':
                    GrowServer.setMapSensorType(27)
                    break
                case 'LoRa Co2 Concentration':
                    GrowServer.setMapSensorType(28)
                    break

                case 'LoRa Co2 Sensor Temperature':
                    GrowServer.setMapSensorType(30)
                    break
                case 'LoRa Dielectric Permittivity':
                    GrowServer.setMapSensorType(31)
                    break
                case 'LoRa Electrical Conductivity':
                    GrowServer.setMapSensorType(32)
                    break
                case 'LoRa Light Intensity':
                    GrowServer.setMapSensorType(33)
                    break
                case 'LoRa PAR':
                    GrowServer.setMapSensorType(34)
                    break
                case 'LoRa Raw Ir Reading':
                    GrowServer.setMapSensorType(35)
                    break
                case 'LoRa Raw Ir Reading Lpf':
                    GrowServer.setMapSensorType(36)
                    break
                case 'LoRa Relative Humidity':
                    GrowServer.setMapSensorType(37)
                    break
                case 'LoRa Rssi':
                    GrowServer.setMapSensorType(38)
                    break
                case 'LoRa Soil Temp':
                    GrowServer.setMapSensorType(39)
                    break
                case 'LoRa Temp':
                    GrowServer.setMapSensorType(40)
                    break
                case 'LoRa Temperature':
                    GrowServer.setMapSensorType(41)
                    break
                case 'LoRa Volumetric Water Content':
                    GrowServer.setMapSensorType(42)
                    break
                case 'LoRa GWC':
                    GrowServer.setMapSensorType(46)
                    break
                case 'LoRa lux':
                    GrowServer.setMapSensorType(47)
                    break
                default:
                    GrowServer.setMapSensorType(3)
                    break
            }
        })

        this.leafletMap.on('overlayadd', function (e) {
            if (e.target._layers['PlantsGroup']) {
                e.target._layers['PlantsGroup'].bringToFront()
            }
            if (e.target._layers['FloorplanGroup']) {
                e.target._layers['FloorplanGroup'].bringToFront()
            }
        })

        this.leafletMap.setView(GrowServer.Floorplan.center, zoomScale)

        ///// layer setup and toggling /////
        var layers = {
            Walls: {}
        }

        // if (this.DEBUG) {
        //     layers["Background"]= L.imageOverlay(GrowServer.Floorplan.layers.background_image, bounds.pad(0.05));
        //     layers["Map"] = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        //     {
        //         maxZoom: zoomScale,
        //         maxNativeZoom: 19,
        //         visible: false // 19 is the highest openstreetmap goes apparently?
        //     });
        // }

        var dataOverlays = {}

        GrowServer.Map.getDataLayer = function getDataLayer (dataType) {
            var label, featureGroup, sensorType

            switch (dataType) {
                case 'Temperature High':
                case 'Temperature Low':
                case 'SCD30 Air Temperature':
                case 'BME280 Air Temperature':
                    label =
                        '<div class="wi wi-thermometer">' + dataType + '</div>'
                    sensorType = 'Air Temperature'
                    break
                case 'Humidity High':
                case 'Humidity Low':
                case 'SCD30 Humidity':
                case 'BME280 Humidity':
                    label = '<div class="wi wi-humidity">' + dataType + '</div>'
                    sensorType = 'Humidity'
                    break
                case 'Co2':
                case 'SCD30 Co2':
                    label = '<div class="fa fa-percent">CO2</div>'
                    sensorType = 'Co2'
                    break
                case 'CT':
                    label =
                        '<div class="wi wi-lightning">' + dataType + '</div>'
                    sensorType = 'CT'
                    break
                case 'PAR':
                    label =
                        '<div class="wi wi-lightning">' + dataType + '</div>'
                    sensorType = 'PAR'
                    break
                case 'PH':
                    label = '<div class="wi wi-raindrop">' + dataType + '</div>'
                    sensorType = 'pH'
                    break
                case 'EC':
                    label = '<div class="wi wi-dust">' + dataType + '</div>'
                    sensorType = 'EC'
                    break
                case 'DO':
                    label = '<div class="wi wi-humidity">' + dataType + '</div>'
                    sensorType = 'DO'
                    break
                case 'Volumetric Water Content':
                    label = '<div class="wi wi-humidity">' + dataType + '</div>'
                    sensorType = 'Volumetric Water Content'
                    break
                case 'Waterproof Temperature':
                    label =
                        '<div class="wi wi-raindrops">' + dataType + '</div>'
                    sensorType = 'Waterproof Temperature'
                    break
                default:
                    label = dataType
                    sensorType = dataType
            }

            if (!dataOverlays[label]) {
                featureGroup = L.featureGroup()

                featureGroup.onAdd = function (map) {
                    L.FeatureGroup.prototype.onAdd.call(this, map)

                    var legend = L.control({ position: 'topright' })

                    //Gradient ranges for legend.
                    legend.onAdd = function (map) {
                        var grades
                        switch (sensorType) {
                            case 'Humidity':
                                grades = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90]
                                break
                            case 'Air Temperature':
                                if (GrowServer.showMetric == false) {
                                    grades = [60, 65, 70, 75, 80, 85, 90, 95]
                                } else {
                                    grades = [15, 18, 21, 24, 27, 30, 32, 35]
                                }
                                break
                            case 'Co2':
                                grades = [
                                    400,
                                    500,
                                    600,
                                    700,
                                    800,
                                    900,
                                    1000,
                                    1100,
                                    1200
                                ]
                                break
                            case 'LoRa co2_concentration':
                                grades = [
                                    400,
                                    500,
                                    600,
                                    700,
                                    800,
                                    900,
                                    1000,
                                    1100,
                                    1200
                                ]
                                break
                            case 'BME280 Humidity':
                                grades = [
                                    0,
                                    10,
                                    20,
                                    30,
                                    40,
                                    50,
                                    60,
                                    70,
                                    80,
                                    90
                                ]
                                break
                            case 'BME280 Air Temperature':
                                if (GrowServer.showMetric == false) {
                                   grades = [60, 65, 70, 75, 80, 85, 90, 95]
                                } else {
                                   grades = [15, 18, 21, 24, 27, 30, 32, 35]
                                }
                               break
                            case 'LoRa co2_sensor_temperature':
                             if (GrowServer.showMetric == false) {
                                grades = [60, 65, 70, 75, 80, 85, 90, 95]
                             } else {
                                grades = [15, 18, 21, 24, 27, 30, 32, 35]
                             }
                               break
                            case 'SCD30 Humidity':
                                grades = [
                                    0,
                                    10,
                                    20,
                                    30,
                                    40,
                                    50,
                                    60,
                                    70,
                                    80,
                                    90
                                ]
                                break
                            case 'SCD30 Air Temperature':
                                if (GrowServer.showMetric == false) {
                                   grades = [60, 65, 70, 75, 80, 85, 90, 95]
                                } else {
                                   grades = [15, 18, 21, 24, 27, 30, 32, 35]
                                }
                               break
                            case 'LoRa temperature':
                             if (GrowServer.showMetric == false) {
                                grades = [60, 65, 70, 75, 80, 85, 90, 95]
                             } else {
                                grades = [15, 18, 21, 24, 27, 30, 32, 35]
                             }
                              break
                            case 'LoRa relative_humidity':
                             grades = [
                                 0,
                                 10,
                                 20,
                                 30,
                                 40,
                                 50,
                                 60,
                                 70,
                                 80,
                                 90
                                ]
                                break
                            case 'CT':
                                grades = [
                                    0.1,
                                    0.2,
                                    0.3,
                                    0.4,
                                    0.5,
                                    0.6,
                                    0.7,
                                    0.8,
                                    0.9,
                                    1
                                ]
                                break
                            case 'LoRa volumetric_water_content':
                                grades = [
                                    100,
                                    90,
                                    80,
                                    70,
                                    60,
                                    50,
                                    40,
                                    30,
                                    20,
                                    10
                                ]
                                break
                            case 'EC':
                                grades = [
                                    2.8,
                                    2.5,
                                    2.2,
                                    1.9,
                                    1.6,
                                    1.3,
                                    1.0,
                                    0.7,
                                    0.4,
                                    -100
                                ]
                                break
                            case 'LoRa electrical_conductivity':
                                grades = [
                                    2.8,
                                    2.5,
                                    2.2,
                                    1.9,
                                    1.6,
                                    1.3,
                                    1.0,
                                    0.7,
                                    0.4,
                                    -100
                                ]
                                break
                            case 'RSSI':
                                grades = [
                                    -10,
                                    -20,
                                    -30,
                                    -40,
                                    -50,
                                    -60,
                                    -70,
                                    -80,
                                    -90,
                                    -100
                                ]
                                break
                            case 'LoRa battery_level':
                                grades = [
                                    2.8,
                                    2.5,
                                    2.2,
                                    1.9,
                                    1.6,
                                    1.3,
                                    1.0,
                                    0.7,
                                    0.4,
                                    0.1
                                ]
                                break
                            case 'LoRa GWC':
                                grades = [
                                    100,
                                    90,
                                    80,
                                    70,
                                    60,
                                    50,
                                    40,
                                    30,
                                    20,
                                    10
                                ]
                            case 'PAR':
                                grades = [
                                    2000,
                                    1750,
                                    1500,
                                    1250,
                                    1000,
                                    700,
                                    500,
                                    250,
                                    0
                                ]
                                break
                            case 'LoRa PAR':
                                grades = [
                                    2000,
                                    1750,
                                    1500,
                                    1250,
                                    1000,
                                    700,
                                    500,
                                    250,
                                    0
                                ]
                                break
                            case 'LoRa lux':
                                grades = [
                                    6000,
                                    5250,
                                    4500,
                                    3750,
                                    3000,
                                    2100,
                                    1650,
                                    750,
                                    0
                                ]
                        }
                        var div = L.DomUtil.create('div', 'info legend')

                        if (grades) {
                            // loop through our density intervals and generate a label with a colored square for each interval
                            for (var i = 0; i < grades.length; i++) {
                                if (
                                    GrowServer.showMetric == false &&
                                    sensorType == 'Air Temperature'
                                ) {
                                    var color = GrowServer.Color.getColorForSensorTypeAndValue(
                                        sensorType,
                                        ((grades[i] + 1 - 32) * 5) / 9
                                    )
                                } else {
                                    var color = GrowServer.Color.getColorForSensorTypeAndValue(
                                        sensorType,
                                        grades[i] + 1
                                    )
                                }
                                div.innerHTML +=
                                    '<i style="background:' +
                                    color +
                                    '"></i> ' +
                                    grades[i] +
                                    (grades[i + 1]
                                        ? '&ndash;' + grades[i + 1] + '<br>'
                                        : '+')
                            }
                        }

                        return div
                    }
                    legend.addTo(map)

                    featureGroup.onRemove = function (map) {
                        L.FeatureGroup.prototype.onRemove.call(this, map)
                        map.removeControl(legend)
                    }
                } //featureGroup.onAdd

                if (label)
                  dataOverlays[label] = featureGroup
            } else {
                featureGroup = dataOverlays[label]
            }

            return featureGroup
        }

        // factory function for leaflet markers
        GrowServer.Map.getMarkerForMapItemType = function getMarkerForMapItemType (
            mapItem,
            mapItemType,
            options
        ) {
            var icon

            options = L.extend({}, options)

            var styleOptions = L.extend(
                {
                    color: GrowServer.Color.getColorForMapItemType(mapItemType),
                    weight: 1
                },
                options.style || {}
            )

            var tooltipOptions = L.extend(
                {
                    opacity: 1.0,
                    className: 'mapItemLabel',
                    direction: 'center',
                    permanent: false
                },
                options.tooltip || {}
            )

            var symbol = options.symbol ? options.symbol : ''

            switch (mapItemType) {
                case 'Sensor':
                    return L.marker([mapItem.latitude, mapItem.longitude], {
                        icon: L.divIcon({
                            iconSize: [50, 25],
                            className: 'grownetics-sensor-map-icon ',
                            html:
                                '<div class="pulse_holder"><div class="pulse_marker"><div class="pulse"></div></div></div><div id="sensor-value" data-sensorType="' +
                                options.sensor_type +
                                '" style="background-color:' +
                                options.color +
                                ';"><span id="grownetics-sensor-' +
                                options.id +
                                '">' +
                                (options.value ? options.value : '?') +
                                '</span><span id="sensor-symbol">' +
                                symbol +
                                '</span></div>'
                        })
                    }).bindPopup('<h6>' + mapItem.label + '</h6>')
                case 'Doors':
                case 'Zone':
                    return L.geoJSON(mapItem.geoJSON, {
                        style: function (feature) {
                            return styleOptions
                        }
                    }).bindTooltip(mapItem.label, tooltipOptions)
                case 'Plant Placeholder':
                    return L.circleMarker(
                        [mapItem.latitude, mapItem.longitude],
                        {
                            radius: 4,
                            color: GrowServer.Color.getColorForMapItemType(
                                'Plant Placeholder'
                            ),
                            weight: 1,
                            opacity: 0.66,
                            fillOpacity: 0.33
                        }
                    ).bindPopup('<h6>' + mapItem.label + '</h6>')
                case 'Room_Names':
                    return L.marker([mapItem.latitude, mapItem.longitude], {
                        title: mapItem.label,
                        icon: L.icon({
                            iconUrl: '/img/pixel-trans.png',
                            iconSize: [1, 1]
                        })
                    }).bindTooltip('<h6>' + mapItem.label + '</h6>', {
                        opacity: 0.75,
                        className: 'mapItemLabel',
                        direction: 'center',
                        permanent: true
                    })
                default:
                    var marker
                    if (mapItemType == 'Device') {
                        icon = growIcon
                    } else if (mapItemType == 'Fan') {
                        icon = L.icon({
                            iconUrl: '/img/map-icons/fan-2.svg',
                            iconSize: [15, 13]
                        })
                    } else if (mapItemType == 'Dehum') {
                        icon = L.icon({
                            iconUrl: '/img/map-icons/dehum-2.svg',
                            iconSize: [15, 27]
                        })
                    }

                    if (!icon && mapItem.geoJSON) {
                        marker = L.geoJSON(mapItem.geoJSON, {
                            style: function (feature) {
                                return styleOptions
                            }
                        })
                    } else if (icon) {
                        marker = L.marker(
                            [mapItem.latitude, mapItem.longitude],
                            {
                                title: mapItem.label,
                                icon: icon
                            }
                        )
                    }
                    if (marker) {
                        return marker.bindTooltip(mapItem.label, tooltipOptions)
                    }
            }
        }

        ///// factory functions for creating leaflet map layers /////
        this.createFloorplanLayer = function (data) {
            layers['Walls'] = L.geoJSON(data.entities, {
                style: function (feature) {
                    return {
                        // color: GrowServer.Color.getColorForMapItemType("Walls"),
                        weight: 2,
                        lineCap: 'square',
                        lineJoin: 'miter'
                    }
                }
            })

            var floorplanLayer = L.featureGroup([layers['Walls']])
            floorplanLayer._leaflet_id = 'FloorplanGroup'
            GrowServer.floorplanLayer_bounds = floorplanLayer.getBounds()
            leafletMap.fitBounds(GrowServer.floorplanLayer_bounds)

            floorplanLayer.addTo(leafletMap)
        }

        this.createPlantsLayer = function (data) {
            // plants are special
            var plantGroup = L.featureGroup()

            var plants = data.entities

            function batchIdToColor (batch_id) {
                var goldenRatio = 0.618033988749895
                var batchColors = [
                    { r: 52, g: 106, b: 54 },
                    { r: 74, g: 229, b: 74 },
                    { r: 48, g: 203, b: 1 },
                    { r: 222, g: 213, b: 84 },
                    { r: 15, g: 146, b: 0 },
                    { r: 0, g: 98, b: 3 },
                    { r: 106, g: 188, b: 251 },
                    { r: 0, g: 42, b: 2 },
                    { r: 0, g: 134, b: 0 },
                    { r: 166, g: 100, b: 193 },
                    { r: 127, g: 185, b: 5 },
                    { r: 197, g: 217, b: 0 },
                    { r: 155, g: 224, b: 245 },
                    { r: 235, g: 235, b: 17 },
                    { r: 73, g: 119, b: 125 }
                ]
                var color = batchColors[batch_id % batchColors.length]
                var red = parseInt((color.r + batch_id) * goldenRatio) % 255
                var green = parseInt((color.g + batch_id) * goldenRatio) % 255
                var blue = parseInt((color.b + batch_id) * goldenRatio) % 255
                var rgbString = 'rgb(' + red + ',' + green + ',' + blue + ')'
                return rgbString
            }

            function statusToValue (status) {
                switch (status) {
                    case 0:
                        return 'Planned'
                    case 1:
                        return 'Planted'
                    case 2:
                        return 'Harvested'
                    case 3:
                        return 'Destroyed'
                    default:
                        return ''
                }
            }

            for (var kk = 0; kk < plants.length; kk++) {
                var plantMarker = {
                    radius: 4,
                    fillColor: batchIdToColor(plants[kk].harvest_batch_id),
                    color: '#000',
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                }
                L.circleMarker(
                    [
                        plants[kk].map_item.latitude,
                        plants[kk].map_item.longitude
                    ],
                    plantMarker
                )
                    .bindPopup(
                        '<div style="font-size:1.25em;">' +
                            '<p>Plant ID: ' +
                            plants[kk].plant_id +
                            '</p>' +
                            "<p>Zone: <a href='/zones/view/" +
                            plants[kk].zone.id +
                            "'> " +
                            plants[kk].zone.label +
                            '</a></p>' +
                            "<p>Batch: <a href='/harvestBatches/view/" +
                            plants[kk].harvest_batch_id +
                            "'>" +
                            plants[kk].harvest_batch.cultivar.label +
                            ' - Batch #' +
                            plants[kk].harvest_batch.batch_number +
                            '</a></p>' +
                            '<p>Status: <b>' +
                            statusToValue(plants[kk].status) +
                            '</b></p>' +
                            '</div>'
                    )
                    .openPopup()
                    .addTo(plantGroup)
            }
            plantGroup._leaflet_id = 'PlantsGroup'
            layers['Plants'] = plantGroup
            plantGroup.addTo(leafletMap)
        }

        this.createSensorsLayer = function (data) {
            // sensors are too
            var sensors = data.entities

            for (var i = 0; i < sensors.length; i++) {
                var sensor = sensors[i]
                // var sensorType = sensor.sensor_type_label
                // Should be this, but not working yet. Need high/low for temp
                var sensorType = sensor.data_type
                var sensorMarker
                var color
                var symbol = sensor.sensor_type_symbol

                color = GrowServer.Color.getColorForSensorType(sensorType)

                var height = sensor.map_item.offsetHeight

                if (sensorType == 'Humidity') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    if (height == 1) {
                        sensorMarker.addTo(
                            GrowServer.Map.getDataLayer('Humidity Low')
                        )
                    } else if (height == 2) {
                        sensorMarker.addTo(
                            GrowServer.Map.getDataLayer('Humidity High')
                        )
                    }
                } else if (sensorType == 'Temperature') {
                    if (GrowServer.showMetric == true) {
                        symbol = sensor.sensor_type_metric_symbol
                    }
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    if (height == 1) {
                        sensorMarker.addTo(
                            GrowServer.Map.getDataLayer('Temperature Low')
                        )
                    } else if (height == 2) {
                        sensorMarker.addTo(
                            GrowServer.Map.getDataLayer('Temperature High')
                        )
                    }
                } else if (sensorType == 'Waterproof Temperature') {
                    if (GrowServer.showMetric == true) {
                        symbol = sensor.sensor_type_metric_symbol
                    }
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else if (sensorType == 'Co2') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol:
                                "<span style='font-size: 0.5em;'>" +
                                symbol +
                                '</span>',
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else if (sensorType == 'PAR') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol:
                                "<span style='font-size: 0.5em;'>" +
                                symbol +
                                '</span>',
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else if (sensorType == 'PH') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else if (sensorType == 'EC') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else if (sensorType == 'DO') {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                } else {
                    sensorMarker = GrowServer.Map.getMarkerForMapItemType(
                        sensor.map_item,
                        'Sensor',
                        {
                            id: sensor.id,
                            color: color,
                            symbol: symbol,
                            sensor_type: sensorType
                        }
                    )
                    sensorMarker.addTo(GrowServer.Map.getDataLayer(sensorType))
                }
            }
        }

        this.createPlantPlaceholdersLayer = function (data) {
            var plantPlaceholders = data.entities
            if (plantPlaceholders && plantPlaceholders.length) {
                for (var kk = 0; kk < plantPlaceholders.length; kk++) {
                    plantPlaceholder = plantPlaceholders[kk]
                    if (!layers['Plant Placeholders']) {
                        layers['Plant Placeholders'] = L.featureGroup()
                    }
                    GrowServer.Map.getMarkerForMapItemType(
                        plantPlaceholder,
                        plantPlaceholder.map_item_type.label
                    ).addTo(layers['Plant Placeholders'])
                }
            }
        }

        this.createMapItemsLayer = function (data) {
            ///// create layers for all the map items /////
            var mapItem, mapItemType
            var mapItems = data.entities

            if (mapItems && mapItems.length) {
                // sometimes maybe mapItems fails?
                for (var ix = 0; ix < mapItems.length; ix++) {
                    mapItem = mapItems[ix]
                    if (mapItem.map_item_type) {
                        mapItemType = mapItem.map_item_type.label
                    }

                    if ('Zone' == mapItemType) {
                        // Initialize feature groups to store map items.
                        if (!layers['HVAC']) {
                            layers['HVAC'] = L.featureGroup()
                        }
                        if (!layers['Benches']) {
                            layers['Benches'] = L.featureGroup()
                        }
                        if (!layers['Rooms']) {
                            layers['Rooms'] = L.featureGroup()
                        }
                        if (!layers['Custom_Zones']) {
                            layers['Custom_Zones'] = L.featureGroup()
                        }
                        // Sort each map item into feature groups
                        if (mapItem.zones) {
                            if (mapItem.zones[0].zone_type_id == 1) {
                                GrowServer.Map.getMarkerForMapItemType(
                                    mapItem,
                                    mapItemType
                                ).addTo(layers['Rooms'])
                            } else if (mapItem.zones[0].zone_type_id == 2) {
                                GrowServer.Map.getMarkerForMapItemType(
                                    mapItem,
                                    mapItemType
                                ).addTo(layers['HVAC'])
                            } else if (mapItem.zones[0].zone_type_id == 3) {
                                GrowServer.Map.getMarkerForMapItemType(
                                    mapItem,
                                    mapItemType
                                ).addTo(layers['Benches'])
                            } else {
                                // GrowServer.Map.getMarkerForMapItemType(mapItem, mapItemType).addTo(layers["Custom_Zones"]);
                            }
                        }
                    } else if (mapItem && mapItemType) {
                        if (!layers['Appliances']) {
                            layers['Appliances'] = L.featureGroup()
                        }

                        // Add Doors and Room Names to floorplan layer
                        if ('Doors' == mapItemType) {
                            if (!layers['Doors']) {
                                layers['Doors'] = L.featureGroup()
                            }
                            GrowServer.Map.getMarkerForMapItemType(
                                mapItem,
                                mapItemType,
                                {
                                    style: {
                                        lineCap: 'round',
                                        lineJoin: 'miter'
                                    }
                                }
                            ).addTo(layers['Doors'])
                        } else if ('Room_Names' == mapItemType) {
                            if (!layers['Room Labels']) {
                                layers['Room Labels'] = L.featureGroup()
                            }
                            GrowServer.Map.getMarkerForMapItemType(
                                mapItem,
                                mapItemType
                            ).addTo(layers['Room Labels'])
                        } else {
                            // Add Everything Else to 'Appliances'
                            var marker = GrowServer.Map.getMarkerForMapItemType(
                                mapItem,
                                mapItemType,
                                {
                                    style: {
                                        fill: true,
                                        fillColor: GrowServer.Color.getColorForMapItemType(
                                            mapItemType
                                        )
                                    }
                                }
                            )
                            if (marker) {
                                marker.addTo(layers['Appliances'])
                            }
                        }
                    }
                }
            }
        }
        ///// end layer/marker factories /////

        ///// ajax layer init /////
        var floorplanPromise = $.ajax(
            '/floorplans/layers/walls/' + GrowServer.Floorplan.id + '.json'
        ).then(this.createFloorplanLayer)
        var sensorsPromise = $.ajax(
            '/floorplans/layers/sensors/' + GrowServer.Floorplan.id + '.json'
        ).then(this.createSensorsLayer)
        var plantsPromise = $.ajax(
            '/floorplans/layers/plants/' + GrowServer.Floorplan.id + '.json'
        ).then(this.createPlantsLayer)
        var mapItemsPromise = $.ajax(
            '/floorplans/layers/map_items/' + GrowServer.Floorplan.id + '.json'
        ).then(this.createMapItemsLayer)
        // var plantPlaceholdersPromise = $.ajax(
        //     '/floorplans/layers/plant_placeholders/' +
        //         GrowServer.Floorplan.id +
        //         '.json'
        // ).then(this.createPlantPlaceholdersLayer)

        $.when(
            floorplanPromise,
            sensorsPromise,
            plantsPromise,
            mapItemsPromise,
            // plantPlaceholdersPromise
        ).done(function () {
            // create layers
            var initialLayers = {}

            for (var layerName in layers) {
                //; i < layers.length;i++) {
                initialLayers[layerName] = layers[layerName]
                for (var ii = 0; ii < DEFAULT_LAYERS.length; ii++) {
                    var defaultLayerName = DEFAULT_LAYERS[ii]
                    if (layerName == defaultLayerName) {
                        layers[layerName].addTo(leafletMap)
                        break
                    }
                }
            }

            var initialDataLayer = dataOverlays[DEFAULT_DATA_OVERLAY] //this.getDataLayer(DEFAULT_DATA_OVERLAY);
            if (initialDataLayer) {
                initialDataLayer.addTo(leafletMap)
            }

            dataOverlays['None'] = L.featureGroup()

            var layerControl = L.control.layers(dataOverlays, initialLayers, {
                position: 'topright'
            })
            if (layers['HVAC']) {
                layers['HVAC'].bringToBack()
            }
            if (layers['Rooms']) {
                layers['Rooms'].bringToBack()
            }
            if (layers['Benches']) {
                layers['Benches'].bringToBack()
            }

            if (GrowServer.Map.loading) {
                GrowServer.Map.loading.remove()
            }

            layerControl.addTo(leafletMap)

            GrowServer.setMapSensorType(GrowServer.map_data_type)
        })
        ;(function () {
            var control = new L.Control({ position: 'topright' })
            control.onAdd = function (map) {
                var azoom = L.DomUtil.create('a', 'resetzoom')
                azoom.innerHTML = '[Reset]'
                L.DomEvent.disableClickPropagation(azoom).addListener(
                    azoom,
                    'click',
                    function () {
                        GrowServer.Floorplan.leafletMap.setView(
                            GrowServer.Floorplan.center,
                            15
                        )
                    },
                    azoom
                )
                return azoom
            }
            return control
        })().addTo(this.leafletMap)

        // expose the leaflet map object externally
        GrowServer.Floorplan.leafletMap = this.leafletMap
    } // GrowServer.Map
})()
