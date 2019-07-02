var GrowServer = GrowServer || {};
var sourceId, sourceType, dataType, dataLabel, limit, type;
$('.ui.normal.dropdown').dropdown({fullTextSearch:true});
$('.sensorMenu').on('click keyup', function (event) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    var temp = $(event.target);
    if (temp.hasClass('item') || keycode == 13) {

    sourceType = $(this).find('.active').data('sourcetype');
    dataType = $(this).find('.active').data('datatype');
    sourceId = $(this).find('.active').data('sourceid');
    dataLabel = $(this).find('.active').data('datalabel');
    dataType = $(this).find('.active').data('datatype');
        $('#spinner').css('visibility', 'visible');

        limit = 1000;
        timeframe = '7d';
        
        $.ajax({
            url: "/DataPoints/recent.json",
            type: 'post',
            data: {"source_id":sourceId, "source_type":sourceType, "data_type":dataType, "limit":limit, "timeframe":timeframe},
            success: function(response) {
                $('#spinner').css('visibility', 'hidden');
                if (response.results.length == 0) {
                    $.gritter.add({
                        title: 'Warning',
                        text: 'No datapoints found.',
                        class_name: 'gritter-light',
                        time: '3000'
                    });
              } else {
                if (sourceType == 0) {
                response.results = response.results.filter(item => item.mean !== null);
                response.results.forEach(function(ii) {
                    if (response.dataSymbol == '&#8457;') {
                        ii.mean = parseFloat((ii.mean * 9 / 5) + 32).toFixed(2);
                    } else {
                        ii.mean = parseFloat(ii.mean).toFixed(2);
                    }
                });
                GrowServer.chart = AmCharts.makeChart("chartdiv", {
                    "type": "serial",
                    "theme": "dark",
                    "marginRight": 80,
                    "autoMargins": true,
                    "addClassNames": true,
                    "dataProvider": response.results,
                    "path": "/js/bower_components/amcharts3/amcharts",
                    "zoomOutButtonColor": "#FFF",
                    "zoomOutButtonAlpha": 0.3,
                    "zoomOutText": "Zoom out",
                    "valueAxes": [{
                        "position": "left",
                        "includeAllValues": true,
                        "title": dataLabel,
                        "color": "#FFF",
                        "titleColor": "#FFF",
                        "inside": true,
                    }],
                    "defs": {
                        "filter": [
                        {
                            "x": "-50%",
                            "y": "-50%",
                            "width": "200%",
                            "height": "200%",
                            "id": "blur",
                            "feGaussianBlur": {
                                "in": "SourceGraphic",
                                "stdDeviation": "50"
                            }
                        },
                        {
                            "id": "shadow",
                            "width": "150%",
                            "height": "150%",
                            "feOffset": {
                                "result": "offOut",
                                "in": "SourceAlpha",
                                "dx": "2",
                                "dy": "2"
                            },
                            "feGaussianBlur": {
                                "result": "blurOut",
                                "in": "offOut",
                                "stdDeviation": "10"
                            },
                            "feColorMatrix": {
                                "result": "blurOut",
                                "type": "matrix",
                                "values": "0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 .2 0"
                            },
                            "feBlend": {
                                "in": "SourceGraphic",
                                "in2": "blurOut",
                                "mode": "normal"
                            }
                        }
                        ]
                    },
                    "graphs": [{
                        "id": "g1",
                        "type": "line",
                        "lineAlpha": 0.9,
                        "fillAlphas": 0.7,
                        "lineColor": "#5bb5ea",
                        "valueField": "mean",
                        "balloonText": "<div style='margin-bottom:30px;text-shadow: 2px 2px rgba(0, 0, 0, 0.1); font-weight:200;font-size:30px; color:#FFF'>[[value]]"+response.dataSymbol+"</div>"
                    }],
                    "chartScrollbar": {
                        "enabled":false,
                        "graph": "g1",
                        "scrollbarHeight": 80,
                        "backgroundAlpha": 0,
                        "selectedBackgroundAlpha": 0.1,
                        "selectedBackgroundColor": "#888888",
                        "graphFillAlpha": 0,
                        "graphLineAlpha": 0.5,
                        "selectedGraphFillAlpha": 0,
                        "selectedGraphLineAlpha": 1,
                        "autoGridCount": true,
                        "color": "#AAAAAA"
                    },
                    "chartCursor": {
                        "enabled": true,
                        "cursorAlpha": 1,
                        "valueZoomable": true,
                        "cursorColor": "#FFFFFF",
                        "categoryBalloonColor": "#19454B",
                        "limitToGraph":"g1",
                        "categoryBalloonDateFormat": "L:NN A, MMM DD",
                        "balloonText": ""
                    },
                    "balloon": {
                        "borderAlpha": 0,
                        "fillAlpha": 0,
                        "shadowAlpha": 0,
                        "offsetX": 40,
                        "offsetY": -50
                    },
                    "categoryField": "time",
                    "categoryAxis": {
                        "minPeriod": "ss",
                        "parseDates": true,
                        "color": "#FFF",
                        "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
                    },
                    "export": {
                        "enabled": true,
                        "dateFormat": "YYYY-MM-DD HH:NN:SS"
                    }
                });

                } else {
                    response.results.hum_values = response.results.hum_values.filter(item => item.mean !== null);
                    response.results.co2_values = response.results.co2_values.filter(item => item.mean !== null);
                    response.results.temp_values = response.results.temp_values.filter(item => item.mean !== null);
                    response.results.vpd_values = response.results.vpd_values.filter(item => item.mean !== null);
                    response.results.hum_values.forEach(function(ii) {
                        ii.hum_value = ii.mean.toFixed(2);
                    });
                    response.results.co2_values.forEach(function(ii) {
                        ii.co2_value = ii.mean.toFixed(2);
                    });
                    response.results.vpd_values.forEach(function(ii) {
                        ii.vpd_value = ii.mean.toFixed(2);
                    });
                    response.results.temp_values.forEach(function(ii) {
                        if (response.dataSymbol == '&#8457;') {
                            ii.temp_value = parseFloat((ii.mean * 9 / 5) + 32).toFixed(2);
                        } else {
                            ii.temp_value = ii.mean.toFixed(2);
                        }
                    });
                    var full_data = response.results.temp_values.concat(response.results.co2_values, response.results.hum_values, response.results.vpd_values);
                    full_data.sort(function(a,b) {
                        var c = new Date(a.time);
                        var d = new Date(b.time);
                        return c - d;
                    });
                    var symbolNumeric = parseInt(response.dataSymbol.slice(2, -1), 10);
                    var symbol = String.fromCharCode(symbolNumeric);
                    GrowServer.chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "fontFamily": "Ruda",
    "autoMargins": true,
    "addClassNames": true,
    "dataProvider": full_data,
    "height": "96%",
    "creditsPosition": "bottom-left",
    "path": "/js/bower_components/amcharts3/amcharts",
    "zoomOutButtonColor": "#FFF",
    "zoomOutButtonAlpha": 0.3,
    "zoomOutText": "Zoom out",
    "legend": {
        "position": "bottom",
        "labelText" : "[[title]]",
        "fontFamily": "Lato",
        "valueText" : "",
        "marginLeft" : 0,
        "marginRight" : 0,
        "fontSize": 12,
        "markerLabelGap": 3,
        "markerSize": 16,
        "markerType": "line",
        "rollOverGraphAlpha" : 0.15,
        "spacing": 10,
        "verticalGap" : 0,
        "valueAlign":"left",
        "align": "center",
        "color": "#FFF"
    },
    "valueAxes": [{
        "includeAllValues": true,
        "id":"v2",
        "unit": symbol,
        "axisColor": "#CC2529",
        "axisThickness": 1.5,
        "axisAlpha": 1,
        "gridAlpha": 0.05,
        "gridColor": "#FFF",
        "fontSize": 9,
        "position": "left",
        "color": "#FFF"
    }, {
        "includeAllValues": true,
        "unit": "\u0025",
        "id":"v1",
        "axisColor": "#1a8cff",
        "axisThickness": 1.5,
        "axisAlpha": 1,
        "gridAlpha": 0.05,
        "fontSize": 9,
        "offset": 50,
        "position": "left",
        "color": "#FFF"
    }, {
        "includeAllValues": true,
        "id":"v3",
        "unit": "ppm",
        "axisColor": "#69e069",
        "axisThickness": 1.5,
        "gridAlpha": 0.05,
        "fontSize": 10,
        "axisAlpha": 1,
        "position": "right",
        "color": "#FFF"
    }, {
        "includeAllValues": true,
        "id":"v4",
        "unit": "mb",
        "axisColor": "#d05bff",
        "axisThickness": 1.5,
        "gridAlpha": 0.05,
        "fontSize": 10,
        "axisAlpha": 1,
        "offset": 100,
        "position": "left",
        "color": "#FFF"
    }],
    "graphs": [{
        "id" : "g1",
        "valueAxis": "v1",
        "type": "line",
        "title": "Humidity",
        "balloonText": "<b>[[value]]&#37;</b>",
        "valueField": "hum_value",
        "lineColor": "#1a8cff",
        "lineThickness" : 1.5,
    }, {
        "id" : "g2",
        "title": "Temperature",
        "valueAxis": "v2",
        "type": "line",
        "balloonText": "[[value]]"+response.dataSymbol, //<b>[[value]]"+data.dataSymbol+"</b>
        "valueField": "temp_value",
        "lineColor": "#CC2529",
        "lineThickness" : 1.5,
    }, {
        "id" : "g3",
        "title": "Co2",
        "valueAxis": "v3",
        "type": "line",
        "balloonText": "<b>[[value]]ppm</b>",
        "valueField": "co2_value",
        "lineColor": "#69e069",
        "lineThickness" : 1.5,
    }, {
        "id" : "g4",
        "title": "Vapor Pressure Deficit",
        "valueAxis": "v4",
        "type": "line",
        "balloonText": "<b>[[value]]mb</b>",
        "valueField": "vpd_value",
        "lineColor": "#d05bff",
        "lineThickness" : 1.5,
    }],
    "chartCursor" :{
        "categoryBalloonDateFormat": "L:NNA, MMM DD",
        "cursorColor" : "#808080",
        "cursorAlpha" : 0.8,
        "cursorPosition" : "mouse",
        "showNextAvailable" : true
    },
    "categoryField": "time",
    "categoryAxis": {
        "color": "#FFF",
        "axisColor": "#FFF",
        "gridColor": "#FFF",
        "parseDates": true,
        "minPeriod": "ss",
        "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN:SS A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
    }
});
                }   
            }
        }
    });
}
});