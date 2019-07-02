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
            data: { "source_id": sourceId, "source_type": 4, "limit": 1000, "timeframe": '7d' },
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

               
            }
        }
    });
}
});