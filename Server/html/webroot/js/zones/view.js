var GrowServer = GrowServer || {};

$(document).ready(function() {
    $.ajax({
        url: "/DataPoints/recent.json",
        type: 'post',
        data: {"source_id":$('.zone_id').text().trim(), "source_type":1, "data_type":2, "limit":1000, "timeframe":'7d'},
        success: function(response) {
            console.log(response);
            $('#spinner').css('visibility', 'hidden');
            if (response.results.length == 0) {
                var dataPoint = [{
                    time: 0,
                    mean: 0
                }];
                GrowServer.chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "dark",
        "marginRight": 80,
        "autoMargins": true,
        "addClassNames": true,
        "dataProvider": dataPoint,
        "path": "/js/bower_components/amcharts3/amcharts",
        "valueAxes": [{
            "position": "left",
            "includeAllValues": true,
            "title": '',
            "color": "#000",
            "titleColor": "#000",
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
            "balloonText": "<div style='margin-bottom:30px;text-shadow: 2px 2px rgba(0, 0, 0, 0.1); font-weight:200;font-size:30px; color:#000'>[[value]]"+response.dataSymbol+"</div>"
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
            "enabled": false,
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
            "color": "#000",
            "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
        },
        "export": {
            "enabled": true,
            "dateFormat": "YYYY-MM-DD HH:NN:SS"
        }

    });
                                GrowServer.chart.addLabel(50, '20%', 'No datapoints available', 'center', 14, '#000');


            } else {
                response.results.forEach(function (ii) {
                    ii.time = new Date(ii.time*1000);
                });
         GrowServer.chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "dark",
        "marginRight": 80,
        "autoMargins": true,
        "addClassNames": true,
        "dataProvider": response.results,
        "path": "/js/bower_components/amcharts3/amcharts",
        "valueAxes": [{
            "position": "left",
            "includeAllValues": true,
            "title": '',
            "color": "#000",
            "titleColor": "#000",
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
            "balloonText": "<div style='margin-bottom:30px;text-shadow: 2px 2px rgba(0, 0, 0, 0.1); font-weight:200;font-size:30px; color:#000'>[[value]]"+response.dataSymbol+"</div>"
        }],
        "chartScrollbar": {
            "enabled":true,
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
            "color": "#000",
            "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
        },
        "export": {
            "enabled": true,
            "dateFormat": "YYYY-MM-DD HH:NN:SS"
        }

    });
                GrowServer.chart.validateData();
            }
        }
    });
});