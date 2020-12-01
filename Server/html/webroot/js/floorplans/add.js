/* Floorplans add.js */

/**
 *  Floorplan-Importer: import floorplans from SVG files to Grownetics geoJSON format.
 *
 */

// declare globals
var GrowServer = GrowServer || {}
GrowServer.Floorplan = GrowServer.Floorplan || {}

GrowServer.Floorplan.count = 0

// anonymous namespace
;(function () {
    GrowServer.Floorplan.Importer = function (options) {
        $.extend(this, options)
        if (!this.map) {
            var mapEl = document.createElement('div')
            mapEl.setAttribute('id', 'floorplan-map')
            mapEl.setAttribute('style', 'display:none;')
            document.body.appendChild(mapEl)
            this.map = new L.Map('floorplan-map', {
                center: [40.01734, -105.27168],
                zoom: 4
            }) // Boulder
        }

        this.center = this.map.getCenter()
        this.bounds = this.map.getBounds()

        //this.convFactor = 1113200 * Math.cos(this.center.lat * (Math.PI / 180)); //google-fu:   111.32km per decimal degree

        this.progressHandlers = []
        this.updateProgress = function (callback) {
            if (typeof callback == 'function') {
                this.progressCallback = callback
                this.progressArgs = arguments
                // callback.apply(this, arguments);
            }
            for (var i = 0; i < this.progressHandlers.length; i++) {
                try {
                    this.progressHandlers[i].apply(
                        this,
                        Array.prototype.slice.apply(arguments, [1])
                    )
                } catch (exc) {
                    console.warn('Exception calling progress handler: ' + exc)
                }
            }
        }

        return this
    }

    GrowServer.Floorplan.Importer.prototype = {
        floorplan: null,
        appliances: [],
        appliances_geoJSON: [],
        devices: [],
        devices_geoJSON: [],
        plant_placeholders: {},
        plant_placeholders_geoJSON: {},
        zones: [],
        zones_geoJSON: [],
        plant_zones: [],
        plant_zones_geoJSON: [],
        map_items: [],
        map_items_geoJSON: [],
        zone_rects: [],
        plant_zone_rects: [],
        plantZoneTypes: {},
        lat: 0.0,
        lon: 0.0,

        setBounds: function (bounds) {
            this.bounds = L.latLngBounds(bounds)
            this.center = this.bounds.getCenter()
        },

        setZoomScale: function (zoomScale) {
            this.zoomScale = zoomScale
        },

        onProgress: function (progressHandler) {
            this.progressHandlers.push(progressHandler)
        },

        moreProgress: function () {
            if (
                this.progressCallback &&
                typeof this.progressCallback == 'function'
            ) {
                this.progressCallback.apply(this, this.progressArgs)
            }
        },

        isDone: function () {
            delete this.progressArgs
            delete this.progressCallback

            for (var i = 0; i < this.progressHandlers.length; i++) {
                try {
                    this.progressHandlers[i].apply(this, ['done'])
                } catch (exc) {
                    console.warn('Exception calling progress handler: ' + exc)
                }
            }
            console.log('floorplan importer is done.')
        },

        importSVG: function (svgXml) {
            this.svgXml = svgXml
            this.importFloorplan()
        },

        importFloorplan: function () {
            var self = this // self-reference to access 'this' within d3.select anon functions

            var svgs = this.svgXml.getElementsByTagName('svg')
            var viewBox = svgs[svgs.length - 1].viewBox

            var svgWidth = viewBox.baseVal.width
            var svgHeight = viewBox.baseVal.height

            var theMap = this.map
            theMap.setView(this.center, this.zoomScale, { animate: false })
            theMap.fitBounds(this.bounds)

            var nE = theMap.project(this.bounds._northEast, this.zoomScale)
            var sW = theMap.project(this.bounds._southWest, this.zoomScale)

            this.xConvFactor = (nE.x - sW.x) / svgWidth
            this.yConvFactor = (sW.y - nE.y) / svgHeight

            this.lat = this.center.lat
            this.lon = this.center.lng

            // -- floorplan
            var floorplanElement = this.getSVGElement(this.svgXml, 'Walls')

            if (!floorplanElement) {
                console.log(
                    'failed to find floorplan walls element. retrying...'
                )
                floorplanElement = this.getSVGElement(
                    this.svgXml,
                    'G-Floorplan'
                )
            }

            var floorplanWalls = d3.select(floorplanElement)
            var floorplanGroup = L.featureGroup()
            floorplanWalls.selectAll('line').each(function () {
                var line = $(this)
                try {
                    var polyline = L.polyline([
                        theMap.containerPointToLatLng(
                            L.point(
                                parseFloat(line.attr('x1')) * self.xConvFactor,
                                parseFloat(line.attr('y1')) * self.yConvFactor
                            ),
                            self.zoomScale
                        ),
                        theMap.containerPointToLatLng(
                            L.point(
                                parseFloat(line.attr('x2')) * self.xConvFactor,
                                parseFloat(line.attr('y2')) * self.yConvFactor
                            ),
                            self.zoomScale
                        )
                    ])
                    floorplanGroup.addLayer(polyline)
                } catch (exc) {
                    console.warn(exc)
                }
            })
            floorplanWalls.selectAll('polyline').each(function () {
                var line = $(this)
                var mapPoints = []
                var points = line.attr('points').split(' ')
                for (var i = 0; i < points.length; i++) {
                    var coords = points[i].split(',')
                    if (coords && coords.length == 2) {
                        mapPoints.push(
                            theMap.containerPointToLatLng(
                                L.point(
                                    parseFloat(coords[0]) * self.xConvFactor,
                                    parseFloat(coords[1]) * self.yConvFactor
                                ),
                                self.zoomScale
                            )
                        )
                    }
                }
                floorplanGroup.addLayer(L.polyline(mapPoints))
            })
            floorplanWalls.selectAll('polygon').each(function () {
                var line = $(this)
                var mapPoints = []
                var points = line.attr('points').split(' ')
                for (var i = 0; i < points.length; i++) {
                    var coords = points[i].split(',')
                    if (coords && coords.length == 2) {
                        mapPoints.push(
                            theMap.containerPointToLatLng(
                                L.point(
                                    parseFloat(coords[0]) * self.xConvFactor,
                                    parseFloat(coords[1]) * self.yConvFactor
                                ),
                                self.zoomScale
                            )
                        )
                    }
                }
                floorplanGroup.addLayer(L.polygon(mapPoints))
            })
            floorplanWalls.selectAll('rect').each(function () {
                var rect = $(this)
                var x = parseFloat(rect.attr('x'))
                var y = parseFloat(rect.attr('y'))
                try {
                    var rectangle = L.rectangle([
                        theMap.containerPointToLatLng(
                            L.point(x * self.xConvFactor, y * self.yConvFactor),
                            self.zoomScale
                        ),
                        theMap.containerPointToLatLng(
                            L.point(
                                (x + parseFloat(rect.attr('width'))) *
                                    self.xConvFactor,
                                (y + parseFloat(rect.attr('height'))) *
                                    self.yConvFactor
                            ),
                            self.zoomScale
                        )
                    ])
                    floorplanGroup.addLayer(rectangle)
                } catch (exc) {
                    console.warn(exc)
                }
            })
            // parse path data attr
            floorplanWalls.selectAll('path').each(function () {
                var linePath = self.pathToPolyLine($(this).attr('d'))
                if (linePath) {
                    floorplanGroup.addLayer(linePath)
                }
            })
            floorplanGroup.addTo(theMap)
            this.floorplan = floorplanGroup.toGeoJSON()

            this.updateProgress(this.importZones, 'floorplan', 1)
        },

        importZones: function () {
            var self = this
            var theMap = this.map

            // Zones
            var zoneFeatureGroup = L.featureGroup()
            zoneFeatureGroup.addTo(theMap)
            this.roomZones = {}

            var zonesEl = d3.select(this.getSVGElement(this.svgXml, 'Zones'))
            zonesEl
                .selectAll('#' + zonesEl.attr('id') + ' > g')
                .each(function () {
                    var plant_zone_type = $(this)
                        .attr('id')
                        .replace(/_\d?_?$/, '')
                        .replace(/[^\w|^\W]|_/gi, ' ')
                    // self.plant_zone_types.push(zone_type);
                    console.log('Processing: ' + plant_zone_type)
                    if (plant_zone_type != 'Plant Zone') {
                        d3.select(this)
                            .selectAll('rect')
                            .each(function () {
                                var rect = $(this)
                                // var room_zone_id;

                                try {
                                    var x = parseFloat(rect.attr('x'))
                                    var y = parseFloat(rect.attr('y'))
                                    var zone_id = rect
                                        .attr('id')
                                        .replace(/_$/, '')

                                    var rectangle = L.rectangle([
                                        theMap.containerPointToLatLng(
                                            L.point(
                                                x * self.xConvFactor,
                                                y * self.yConvFactor
                                            ),
                                            self.zoomScale
                                        ),
                                        theMap.containerPointToLatLng(
                                            L.point(
                                                (x +
                                                    parseFloat(
                                                        rect.attr('width')
                                                    )) *
                                                    self.xConvFactor,
                                                (y +
                                                    parseFloat(
                                                        rect.attr('height')
                                                    )) *
                                                    self.yConvFactor
                                            ),
                                            self.zoomScale
                                        )
                                    ])

                                    zoneFeatureGroup.addLayer(rectangle)

                                    // Mark room zone types as rooms
                                    console.log(plant_zone_type)
                                    if (
                                        plant_zone_type.indexOf('Room') < 0 &&
                                        plant_zone_type.indexOf('HVAC') < 0 &&
                                        plant_zone_type.indexOf('Custom') < 0
                                    ) {
                                        console.log('Mark it as room')
                                        self.roomZones[zone_id] = rectangle
                                        self.plantZoneTypes[
                                            zone_id
                                        ] = plant_zone_type
                                        zone_type = 'Room'
                                    } else {
                                        zone_type = plant_zone_type
                                    }
                                    //FIXME: move to plantZone import??
                                    // for plant placeholder zone lookup
                                    self.zone_rects[zone_id] = rectangle

                                    // var bounds = rectangle.getBounds();
                                    var center = rectangle.getCenter()
                                    self.zones.push({
                                        type: 'zone',
                                        label: self.prettifyLabel(zone_id),
                                        latitude: center.lat,
                                        longitude: center.lng,
                                        // "bounds": bounds,
                                        zone_type: zone_type,
                                        plant_zone_type: plant_zone_type
                                        // "room_zone_id": room_zone_id
                                    })
                                    self.zones_geoJSON.push(
                                        rectangle.toGeoJSON()
                                    )
                                } catch (exc) {
                                    console.warn('failed to zone: ' + exc)
                                }
                            })
                    }
                })
            this.updateProgress(
                this.importPlantZones,
                'zones',
                this.zones.length
            )
        },

        importPlantZones: function () {
            var self = this
            var plant_zones = d3.select(
                this.getSVGElement(this.svgXml, 'Plant_Zone')
            )

            var plantZoneFeatureGroup = L.featureGroup()
            plantZoneFeatureGroup.addTo(this.map)

            plant_zones.selectAll('rect').each(function () {
                var rect = $(this)
                if (rect.attr('id')) {
                    var plant_zone_id = rect.attr('id').replace(/_$/, '')
                } else {
                    var plant_zone_id =
                        'Plant Zone ' + GrowServer.Floorplan.count
                    GrowServer.Floorplan.count++
                }

                try {
                    var x = parseFloat(rect.attr('x'))
                    var y = parseFloat(rect.attr('y'))

                    var rectangle = L.rectangle([
                        self.map.containerPointToLatLng(
                            L.point(x * self.xConvFactor, y * self.yConvFactor),
                            self.zoomScale
                        ),
                        self.map.containerPointToLatLng(
                            L.point(
                                (x + parseFloat(rect.attr('width'))) *
                                    self.xConvFactor,
                                (y + parseFloat(rect.attr('height'))) *
                                    self.yConvFactor
                            ),
                            self.zoomScale
                        )
                    ])
                    self.plant_zone_rects[plant_zone_id] = rectangle
                    plantZoneFeatureGroup.addLayer(rectangle)

                    var room_zone_id = self.roomIdForZone(rectangle)
                    var plantZoneType = self.plantZoneTypes[room_zone_id]
                    console.log('Room zone id = ' + room_zone_id)
                    console.log('plant_zone_type = ' + plantZoneType)
                    // var bounds = rectangle.getBounds();
                    var center = rectangle.getCenter()
                    self.plant_zones.push({
                        type: 'zone',
                        zone_type: 'Group',
                        plant_zone_type: plantZoneType,
                        label: self.prettifyLabel(plant_zone_id),
                        latitude: center.lat,
                        longitude: center.lng,
                        // "bounds": bounds,
                        room_zone_id: room_zone_id
                    })
                    self.plant_zones_geoJSON.push(rectangle.toGeoJSON())
                } catch (exc) {
                    console.warn('failed to  plant_zone: ' + exc)
                }
            })

            this.updateProgress(
                this.importDevices,
                'plant_zones',
                this.plant_zones.length
            )
        },

        importDevices: function () {
            var self = this
            var theMap = this.map

            // 3D crop sensor devices
            var deviceIndex = 1

            //
            var deviceElement = this.getSVGElement(this.svgXml, 'Crop_Sensor')
            if (!deviceElement) {
                console.log('failed to find device element. retrying...')
                deviceElement = this.getSVGElement(
                    this.svgXml,
                    '_x33_D_Crop_Device'
                )
                if (!deviceElement) {
                    console.log(
                        'failed twice to find device element. retrying...'
                    )
                    deviceElement = this.getSVGElement(this.svgXml, 'G-Devices')
                }
            }
            console.log(deviceElement)

            d3.select(deviceElement)
                .selectAll('path')
                .each(function () {
                    var deviceGroup = L.featureGroup()

                    // var sensorIdString;
                    // var sensorText = d3.select(this).select("text");
                    // //console.log(sensorText);
                    // if (sensorText && sensorText.innerHTML) {
                    //     sensorIdString = sensorText.innerHTML;
                    // } else {
                    // sensorIdString = "Device " + deviceIndex++;
                    // }
                    // //console.log(sensorIdString);

                    var deviceLabel = 'Device ' + deviceIndex++
                    console.log(deviceLabel)
                    var polyline = self.pathToPolyLine($(this).attr('d'))
                    if (polyline) {
                        deviceGroup.addLayer(polyline)
                    }

                    deviceGroup.addTo(theMap)

                    var center = deviceGroup.getBounds().getCenter()
                    var zones = self.zonesForMapItem(polyline)

                    self.devices.push({
                        label: deviceLabel,
                        latitude: center.lat,
                        longitude: center.lng,
                        status: 1,
                        zones: zones
                    })

                    self.devices_geoJSON.push(deviceGroup.toGeoJSON())
                })

            this.updateProgress(
                this.importPlants,
                'devices',
                this.devices.length
            )
        },

        importPlants: function () {
            var self = this
            var theMap = this.map

            // Plants
            var ppIndex = 0,
                plantPlaceholdersGroup = L.featureGroup()
            plantPlaceholdersGroup.addTo(theMap)
            d3.select(this.getSVGElement(this.svgXml, 'Plant_Placeholders'))
                .selectAll('circle')
                .each(function () {
                    var circ = d3.select(this)
                    var cx = parseFloat(circ.attr('cx'))
                    var cy = parseFloat(circ.attr('cy'))
                    var radius = parseFloat(circ.attr('r')) // * self.xConvFactor; // FIXME;

                    var center = theMap.containerPointToLatLng(
                        L.point(cx * self.xConvFactor, cy * self.yConvFactor),
                        self.zoomScale
                    )

                    var circle = L.circle(center, {
                        radius: radius
                    })

                    if (circle) {
                        plantPlaceholdersGroup.addLayer(circle)
                        var zone_id = self.plantZoneForMapItemCenter(center)

                        if (zone_id) {
                            // organize plants by zone id
                            if (!self.plant_placeholders[zone_id]) {
                                self.plant_placeholders[zone_id] = []
                                self.plant_placeholders_geoJSON[zone_id] = []
                            }

                            self.plant_placeholders[zone_id].push({
                                zone_id: zone_id,
                                ordinal:
                                    self.plant_placeholders[zone_id].length,
                                label:
                                    self.prettifyLabel(zone_id) +
                                    ' Plant Placeholder ' +
                                    (self.plant_placeholders[zone_id].length +
                                        1),
                                type: 'Plant Placeholder',
                                latitude: center.lat,
                                longitude: center.lng
                            })
                            self.plant_placeholders_geoJSON[zone_id].push(
                                circle.toGeoJSON()
                            )
                            ppIndex++
                        } else {
                            console.warn(
                                'failed to locate zone for plant placeholder: ',
                                circle
                            )
                        }
                    }
                })

            this.updateProgress(
                this.importMapItems,
                'plant_placeholders',
                ppIndex
            )
        },

        importMapItems: function () {
            var self = this
            var theMap = this.map

            // all other map items
            var mapItemsGroup = L.featureGroup()
            mapItemsGroup.addTo(theMap)

            // other layers and/or grownetics devices
            ;[
                "Room_Names",
                "Trays",
                'Doors',
                'Server_Switches',
                'Power_Panel',
                'Res_Devices'
            ].forEach(function (groupName) {
                self.processMapItemGroup(self.svgXml, groupName, mapItemsGroup)
            })

            // Appliances
            var appliancesGroup = L.featureGroup()
            appliancesGroup.addTo(theMap)

            d3.select(this.getSVGElement(this.svgXml, 'Appliances'))
                .selectAll('#Appliances > g')
                .each(function () {
                    var groupId = d3.select(this).attr('id')
                    if (groupId) {
                        if (groupId == 'Lights') {
                            self.processAppliances(
                                self.svgXml,
                                groupId,
                                appliancesGroup
                            )
                        } else {
                            self.processMapItemGroup(
                                self.svgXml,
                                groupId,
                                mapItemsGroup
                            )
                        }
                    }
                })

            // return the entire imported Importer object
            //return this;
            this.updateProgress(this.isDone, 'map_items', this.map_items.length)
        },

        processAppliances: function processAppliances (
            svgXml,
            groupName,
            layerGroup
        ) {
            var idx = 0
            var self = this
            d3.select(this.getSVGElement(this.svgXml, groupName))
                .selectAll('#' + groupName + ' > g')
                .each(function () {
                    var output = $(this).attr('id')
                    d3.select(this)
                        .selectAll('g')
                        .each(function () {
                            d3.select(this)
                                .selectAll('path')
                                .each(function () {
                                    var linePath = self.pathToPolyLine(
                                        $(this).attr('d')
                                    )
                                    if (linePath) {
                                        layerGroup.addLayer(linePath)
                                        // var bounds = linePath.getBounds();
                                        var center = linePath.getCenter()
                                        var zones = self.zonesForMapItem(
                                            linePath
                                        )
                                        idx++
                                        self.appliances.push({
                                            order: idx,
                                            appliance_template: groupName,
                                            label: groupName + ' ' + idx,
                                            latitude: center.lat,
                                            longitude: center.lng,
                                            // "bounds": bounds,
                                            zones: zones,
                                            output: output
                                        })
                                        self.appliances_geoJSON.push(
                                            linePath.toGeoJSON()
                                        )
                                    }
                                })
                        })
                })

            //this.updateProgress(groupName, idx);
        },

        processMapItemGroup: function (svgXml, groupName, layerGroup) {
            var idx = 0
            var self = this
            var groupEl = d3.select(this.getSVGElement(svgXml, groupName))
            try {
                groupEl
                    .selectAll('#' + groupEl.attr('id') + ' > g')
                    .each(function () {
                        var label = ''
                        d3.select(this)
                            .selectAll('text')
                            .each(function () {
                                d3.selectAll(this.childNodes).each(function () {
                                    //label += this.data + " ";
                                    label += $(this).text() + ' '
                                })
                                console.log(label)
                            })
                        d3.select(this)
                            .selectAll('path')
                            .each(function () {
                                var linePath = self.pathToPolyLine(
                                    $(this).attr('d')
                                )
                                if (linePath) {
                                    layerGroup.addLayer(linePath)
                                    // var bounds = linePath.getBounds();
                                    var center = linePath.getCenter()
                                    idx++
                                    self.map_items.push({
                                        order: idx,
                                        type: groupName,
                                        label: label
                                            ? label
                                            : groupName + ' ' + idx,
                                        latitude: center.lat,
                                        longitude: center.lng
                                        // "bounds": bounds
                                    })
                                    self.map_items_geoJSON.push(
                                        linePath.toGeoJSON()
                                    )
                                }
                            })
                    })
            } catch (x) {
                console.log(
                    'Failed to process appliance group: ' + groupName,
                    x
                )
            }
            //this.updateProgress(groupName, idx);
        },

        /**
         * Cleanup zone id's so we have a nice label for display purposes.
         */
        prettifyLabel: function (label) {
          return label.replace(/[\-|_]/g, ' ')
        },

        /**
         * Try three times to find an element, as a workaround
         * for Illustrator substituting underscores for spaces.
         * @param svgXml The svg document
         * @param elementId element ID to search for
         * @returns {*}
         */
        getSVGElement: function (svgXml, elementId) {
            var el
            try {
                el = svgXml.getElementById(elementId)
                if (!el) {
                    el = svgXml.getElementById(elementId + '_')
                    if (!el) {
                        el = svgXml.getElementById(elementId + '_1_')
                    }
                }
            } catch (x) {
                console.log('failed to find element with ID: ' + elementId, x)
            }
            return el
        },

        /**
         * Using Leaflet's API,
         * Determine a single zone for a map item based on whether the map item's center point is within the zone.
         */
        zoneForMapItemCenter: function (center) {
            for (var zone_id in this.zone_rects) {
                if (this.zone_rects[zone_id].getBounds().contains(center)) {
                    return zone_id
                }
            }
        },

        plantZoneForMapItemCenter: function (center) {
            for (var plant_zone_id in this.plant_zone_rects) {
                if (
                    this.plant_zone_rects[plant_zone_id]
                        .getBounds()
                        .contains(center)
                ) {
                    return plant_zone_id
                }
            }
        },

        /**
         * Using Leaflet's API,
         * Determine all zones which overlap with a map item.
         */
        zonesForMapItem: function (itemPoly) {
            var alle_zoner = []
            for (var zone_id in this.zone_rects) {
                if (
                    this.zone_rects[zone_id]
                        .getBounds()
                        .intersects(itemPoly.getBounds())
                ) {
                    alle_zoner.push(zone_id)
                }
            }
            return alle_zoner
        },

        /**
         * Using Leaflet's API,
         * Determine all zones which overlap with a map item.
         */
        roomIdForZone: function (itemPoly) {
            for (var zone_id in this.roomZones) {
                if (
                    this.roomZones[zone_id]
                        .getBounds()
                        .contains(itemPoly.getBounds())
                ) {
                    return zone_id
                }
            }
        },

        /*
         * Convert an svg path to a leaflet polyline object
         */
        pathToPolyLine: function (svgPath, polylineOptions) {
            var linePath = null

            try {
                var parsedPath = parse(svgPath)

                var initialCommand = parsedPath[0]

                var initialPoint = this.segmentToPoint(initialCommand)

                var nextPoint = this.segmentToPoint(
                    parsedPath[1],
                    initialPoint,
                    initialPoint
                )

                if (initialPoint && nextPoint) {
                    linePath = L.polyline(
                        [
                            this.map.containerPointToLatLng(
                                initialPoint,
                                this.zoomScale
                            ),
                            this.map.containerPointToLatLng(
                                nextPoint,
                                this.zoomScale
                            )
                        ],
                        polylineOptions
                    )

                    var currentPoint = nextPoint
                    for (var i = 2; i < parsedPath.length; i++) {
                        nextPoint = this.segmentToPoint(
                            parsedPath[i],
                            currentPoint,
                            initialPoint
                        )
                        if (nextPoint) {
                            linePath.addLatLng(
                                this.map.containerPointToLatLng(
                                    nextPoint,
                                    this.zoomScale
                                )
                            )
                        }
                        currentPoint = nextPoint
                        nextPoint = null
                    }
                }
            } catch (exc) {
                console.warn('failed to convert svg path to polyline')
            }
            return linePath
        },

        segmentToPoint: function (segment, currentPoint, initialPoint) {
            var command = segment[0]

            if (command == 'M') {
                // move-to
                return L.point(
                    parseFloat(segment[1]) * this.xConvFactor,
                    parseFloat(segment[2]) * this.yConvFactor
                )
            } else if (command == 'V') {
                // vertical line
                return L.point(
                    currentPoint.x,
                    parseFloat(segment[1]) * this.yConvFactor
                )
            } else if (command == 'H') {
                // horizontal line
                return L.point(
                    parseFloat(segment[1]) * this.xConvFactor,
                    currentPoint.y
                )
            } else if (command == 'v') {
                // relative vertical line
                return L.point(
                    currentPoint.x,
                    currentPoint.y + parseFloat(segment[1]) * this.yConvFactor
                )
            } else if (command == 'h') {
                // relative horizontal line
                return L.point(
                    currentPoint.x + parseFloat(segment[1]) * this.xConvFactor,
                    currentPoint.y
                )
            } else if (command == 'L') {
                // absolute line-to
                return L.point(
                    parseFloat(segment[1]) * this.xConvFactor,
                    parseFloat(segment[2]) * this.yConvFactor
                )
            } else if (command == 'l') {
                // relative line-to
                return L.point(
                    currentPoint.x + parseFloat(segment[1]) * this.xConvFactor,
                    currentPoint.y + parseFloat(segment[2]) * this.yConvFactor
                )
            } else if (command == 'C') {
                // cubic bezier
                // TODO: actually draw curves
                return L.point(
                    parseFloat(segment[5]) * this.xConvFactor,
                    parseFloat(segment[6]) * this.yConvFactor
                )
            } else if (command == 'c') {
                // relative cubic bezier
                // TODO: actually draw curves
                return L.point(
                    currentPoint.x + parseFloat(segment[5]) * this.xConvFactor,
                    currentPoint.y + parseFloat(segment[6]) * this.yConvFactor
                )
            } else if (command == 'S' || command == 'Q' || command == 'T') {
                // other bezier
                // TODO: actually draw curves
                return L.point(
                    parseFloat(segment[5]) * this.xConvFactor,
                    parseFloat(segment[6]) * this.yConvFactor
                )
            } else if (command == 's' || command == 'q' || command == 't') {
                // relative other bezier
                // TODO: actually draw curves
                return L.point(
                    currentPoint.x + parseFloat(segment[5]) * this.xConvFactor,
                    currentPoint.y + parseFloat(segment[6]) * this.yConvFactor
                )
            } else if (command == 'a' || command == 'A') {
                // elliptical
                // TODO: actually draw curves
                return L.point(initialPoint.x, initialPoint.y)
            } else if (command == 'z' || command == 'Z') {
                // close path
                return L.point(initialPoint.x, initialPoint.y)
            } else {
                console.warn(
                    'failed to create point for path command => ' + segment
                )
            }
        }
    } // Importer.prototype

    /***********/

    /**
     * expected argument lengths
     * @type {Object}
     */

    var length = { a: 7, c: 6, h: 1, l: 2, m: 2, q: 4, s: 4, t: 2, v: 1, z: 0 }

    /**
     * segment pattern
     * @type {RegExp}
     */

    var segment = /([astvzqmhlc])([^astvzqmhlc]*)/gi

    /**
     * parse an svg path data string. Generates an Array
     * of commands where each command is an Array of the
     * form `[command, arg1, arg2, ...]`
     *
     * @param {String} path
     * @return {Array}
     */

    function parse (path) {
        var data = []
        path.replace(segment, function (_, command, args) {
            var type = command.toLowerCase()
            args = parseValues(args)

            // overloaded moveTo
            if (type == 'm' && args.length > 2) {
                data.push([command].concat(args.splice(0, 2)))
                type = 'l'
                command = command == 'm' ? 'l' : 'L'
            }

            while (true) {
                if (args.length == length[type]) {
                    args.unshift(command)
                    return data.push(args)
                }
                if (args.length < length[type])
                    throw new Error('malformed path data')
                data.push([command].concat(args.splice(0, length[type])))
            }
        })
        return data
    }

    var number = /-?[0-9]*\.?[0-9]+(?:e[-+]?\d+)?/gi

    function parseValues (args) {
        var numbers = args.match(number)
        return numbers ? numbers.map(Number) : []
    }
})() // ----- end anonymous namespace -----

$(document).ready(function () {
    var mymap

    var zoomScale = 29 // max leaflet zoom scale

    var boundsPolygon = null
    var bboxWidth = 100,
        bboxHeight = 50 // meters

    var bounds = []
    var boundsControl = L.control({ position: 'topright' })

    boundsControl.onClick = function (mouseEvt) {
        mouseEvt.stopPropagation()
        mouseEvt.preventDefault()

        var evtName = $(mouseEvt.target).data('evt-name')
        switch (evtName) {
            case 'rotate-right':
                var offsetAngle = $("input[name='offsetAngle']").val()
                offsetAngle++
                if (offsetAngle > 180) {
                    offsetAngle = -179
                }
                $("input[name='offsetAngle']").val(offsetAngle)
                break
            case 'rotate-left':
                var offsetAngle = $("input[name='offsetAngle']").val()
                offsetAngle--
                if (offsetAngle < -180) {
                    offsetAngle = 179
                }
                $("input[name='offsetAngle']").val(offsetAngle)
                break
            case 'expand':
                bboxHeight++
                bboxWidth++
                break
            case 'expand-vertical':
                bboxHeight++
                break
            case 'expand-horizontal':
                bboxWidth++
                break
            case 'compress':
                bboxHeight--
                bboxWidth--
                break
            case 'compress-vertical':
                bboxHeight--
                break
            case 'compress-horizontal':
                bboxWidth--
                break
        }

        updateMap($('#facility-id').val())

        return false
    }

    boundsControl.onAdd = function (map) {
        // create the control container with a particular class name
        var container = L.DomUtil.create('div', 'bounds-control')

        var barContainer = L.DomUtil.create('div', 'leaflet-bar', container)
        $(barContainer).html(
            '<a data-evt-name="rotate-right" class="fa fa-rotate-right" aria-hidden="true"></a>' +
                '<a data-evt-name="rotate-left" class="fa fa-rotate-left" aria-hidden="true"></a>'
        )
        L.DomEvent.addListener(
            barContainer,
            'click',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'dblclick',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'mousedown',
            boundsControl.onClick,
            this
        )

        barContainer = L.DomUtil.create('div', 'leaflet-bar', container)
        $(barContainer).html(
            '<a data-evt-name="expand" class="fa fa-expand" aria-hidden="true"></a>' +
                '<a data-evt-name="expand-vertical" class="fa fa-arrows-v" aria-hidden="true"></a>' +
                '<a data-evt-name="expand-horizontal" class="fa fa-arrows-h" aria-hidden="true"></a>'
        )
        L.DomEvent.addListener(
            barContainer,
            'click',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'dblclick',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'mousedown',
            boundsControl.onClick,
            this
        )

        barContainer = L.DomUtil.create('div', 'leaflet-bar', container)
        $(barContainer).html(
            '<a data-evt-name="compress" class="fa fa-compress" aria-hidden="true"></a>' +
                '<a data-evt-name="compress-vertical" class="fa fa-arrows-v" aria-hidden="true"></a>' +
                '<a data-evt-name="compress-horizontal" class="fa fa-arrows-h" aria-hidden="true"></a>'
        )
        L.DomEvent.addListener(
            barContainer,
            'click',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'dblclick',
            boundsControl.onClick,
            this
        )
        L.DomEvent.addListener(
            barContainer,
            'mousedown',
            boundsControl.onClick,
            this
        )

        return container
    }

    var updateMap = function (facilityId) {
        if (!mymap) {
            mymap = new L.Map('facility-map', { attributionControl: false })
            mymap.addLayer(
                new L.TileLayer(
                    'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: zoomScale,
                        maxNativeZoom: 19 // 19 is the highest openstreetmap goes apparently?
                    }
                )
            )

            mymap.doubleClickZoom.disable()
            L.control.scale().addTo(mymap)
            boundsControl.addTo(mymap)
        }

        var lat = 40.01734,
            lon = -105.27168
        var growIcon = L.icon({
            iconUrl: '/img/map-icons/grownetics-logo-marker-icon.png',
            iconRetinaUrl: '/img/map-icons/grownetics-logo-marker-icon-2x.png',
            iconSize: [25, 41],
            iconAnchor: [1, 26],
            shadowUrl: '/leaflet/images/marker-shadow.png',
            shadowSize: [41, 41]
        })
        var marker = L.marker([lat, lon], { icon: growIcon }).addTo(mymap)
        mymap.setView([lat, lon], 20)

        var convFactor = 1113200 * Math.cos(lat * (Math.PI / 180)) //google-fu:   111.32km per decimal degree

        bounds = [
            [lat - bboxHeight / convFactor, lon - bboxWidth / convFactor],
            [lat - bboxHeight / convFactor, lon + bboxWidth / convFactor],
            [lat + bboxHeight / convFactor, lon + bboxWidth / convFactor],
            [lat + bboxHeight / convFactor, lon - bboxWidth / convFactor]
        ]

        var offsetAngle = $("input[name='offsetAngle']").val()
        if (offsetAngle && -180 < offsetAngle && 180 > offsetAngle) {
            bounds = rotatePoints([lat, lon], bounds, offsetAngle)
        }

        if (boundsPolygon) {
            mymap.removeLayer(boundsPolygon)
        }

        boundsPolygon = L.polygon(bounds, {
            weight: 3,
            color: '#000000',
            fillColor: '#dddddd',
            opacity: 0.66,
            fillOpacity: 0.33
        })

        boundsPolygon.addTo(mymap)
    }

    updateMap()

    $("input[name='floorplan_image']").on('change', function (evt) {
        var floorplanImage = document.querySelector(
            'input[type=file][name=floorplan_image]'
        )

        // Use HTML5 file API
        if (floorplanImage.files.length > 0) {
            var file = floorplanImage.files[0]

            var objUrl = window.URL.createObjectURL(file)
            L.imageOverlay(objUrl, bounds).addTo(mymap)
        }
    })

    $("button[type='import_svg']").on('click', function (evt) {
        var floorplanImage = document.querySelector(
            'input[type=file][name=floorplan_image]'
        )

        // Use HTML5 file API
        if (floorplanImage.files.length > 0) {
            var file = floorplanImage.files[0]

            var objUrl = window.URL.createObjectURL(file)
            L.imageOverlay(objUrl, bounds).addTo(mymap)

            $('#importingModal .progress-bar').width('100%')
            $('#importingModal .progress').width('1%')

            $('#importingModal').modal('show')
            // This timeout lets the Modal fade in before processing starts on the actual import.
            setTimeout(function () {
                // check for SVG
                if (file.type == 'image/svg+xml') {
                    d3.xml(objUrl)
                        .mimeType('image/svg+xml')
                        .get(function (error, xml) {
                            if (error) {
                                throw error
                            }

                            var importer = new GrowServer.Floorplan.Importer({
                                map: mymap
                            })
                            importer.setZoomScale(zoomScale)
                            importer.setBounds(bounds)
                            var progress = 0
                            var progressIncrement = 17
                            var floorplanId
                            importer.onProgress(function (group, count) {
                                if (group == 'done') {
                                    $('#import-summary').append(
                                        '<div id="import-group-done" class="import-group">Done!</div>'
                                    )
                                    $('#importingModal .progress').width('100%')

                                    // setTimeout(function() {
                                    // window.location = '/floorplans/view/' + floorplanId;
                                    // });
                                } else {
                                    if (group == 'zones') {
                                        $.post(
                                            '/floorplans/add.json',
                                            {
                                                label: $(
                                                    "input[name='label'"
                                                ).val(),
                                                description: $(
                                                    "input[name='description'"
                                                ).val(),
                                                floor_level: $(
                                                    "input[name='floor_level'"
                                                ).val(),
                                                geoJSON: JSON.stringify(
                                                    this.floorplan
                                                ),
                                                latitude: JSON.stringify(
                                                    this.center.lat
                                                ),
                                                longitude: JSON.stringify(
                                                    this.center.lng
                                                ),
                                                zones: JSON.stringify(
                                                    this.zones
                                                ),
                                                zones_geoJSON: JSON.stringify(
                                                    this.zones_geoJSON
                                                )
                                            },
                                            function (response) {
                                                console.log('saved floorplan!')
                                                console.log(response)
                                                floorplanId =
                                                    response.floorplan.id
                                                $('#import-summary').append(
                                                    '<div id="import-group-' +
                                                        group +
                                                        '" class="import-group">' +
                                                        group +
                                                        ' => ' +
                                                        count +
                                                        '</div>'
                                                )
                                                $(
                                                    '#importingModal .progress'
                                                ).width(
                                                    (parseInt(
                                                        Math.random() * 3
                                                    ) +
                                                        1) *
                                                        progressIncrement *
                                                        (progress +=
                                                            parseInt(
                                                                Math.random() *
                                                                    3
                                                            ) + 1) +
                                                        '%'
                                                )
                                                importer.moreProgress()
                                            }
                                        )
                                    } else if (group != 'floorplan') {
                                        var data = {}
                                        data[group] = JSON.stringify(
                                            this[group]
                                        )
                                        data[
                                            group + '_geoJSON'
                                        ] = JSON.stringify(
                                            this[group + '_geoJSON']
                                        )
                                        $.post(
                                            '/floorplans/layers/' +
                                                group +
                                                '.json',
                                            data,
                                            function () {
                                                $('#import-summary').append(
                                                    '<div id="import-group-' +
                                                        group +
                                                        '" class="import-group">' +
                                                        group +
                                                        ' => ' +
                                                        count +
                                                        '</div>'
                                                )
                                                $(
                                                    '#importingModal .progress'
                                                ).width(
                                                    (parseInt(
                                                        Math.random() * 3
                                                    ) +
                                                        1) *
                                                        progressIncrement *
                                                        progress++ +
                                                        '%'
                                                )
                                                importer.moreProgress()
                                            }
                                        )
                                    } else {
                                        $('#importingModal .progress').width(
                                            parseInt(Math.random() * 3) *
                                                progressIncrement *
                                                progress +
                                                '%'
                                        )
                                        importer.moreProgress()
                                    }
                                }
                            })

                            importer.importSVG(xml)

                            // $("textarea[name='geoJSON']").val(JSON.stringify(importer.floorplan));
                            // $("input[name='latitude").val(importer.center.lat);
                            // $("input[name='longitude").val(importer.center.lng);
                        })
                } else {
                    $('#import-summary').append(
                        'No SVG file type detected, 0 floorplan layers imported.'
                    )
                    $("textarea[name='geoJSON']").val(
                        JSON.stringify(boundsPolygon.toGeoJSON())
                    )
                }
                // $('#importingModal button').show();
                // $("input[type='submit']").show();
            }, 500)
        }
        return false
    })

    // stackoverflow
    //
    // rotate a list of points in [lat, lng] format about the center.
    //
    function rotatePoints (center, points, yaw) {
        var res = []
        var angle = yaw * (Math.PI / 180)

        for (var i = 0; i < points.length; i++) {
            var p = points[i]
            // translate to center
            var p2 = [p[0] - center[0], p[1] - center[1]]
            // rotate using matrix rotation
            var p3 = [
                Math.cos(angle) * p2[0] - Math.sin(angle) * p2[1],
                Math.sin(angle) * p2[0] + Math.cos(angle) * p2[1]
            ]
            // translate back to center
            var p4 = [p3[0] + center[0], p3[1] + center[1]]
            // done with that point
            res.push(p4)
        }
        return res
    }
}) // $(document).ready
