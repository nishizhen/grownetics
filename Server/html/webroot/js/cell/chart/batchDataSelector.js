var GrowServer = GrowServer || {};
GrowServer.batch_chart_config = {
    "type": "serial",
    "theme": "light",
    "fontFamily": "Ruda",
    "autoMargins": true,
    "addClassNames": true,
    "dataProvider": [{}],
    "height": "96%",
    "creditsPosition": "bottom-left",
    "path": "/js/bower_components/amcharts3/amcharts",
    "zoomOutButtonColor": "#FFF",
    "zoomOutButtonAlpha": 0.3,
    "zoomOutText": "Zoom out",
    "legend": {
        "position": "bottom",
        "labelText": "[[title]]",
        "fontFamily": "Lato",
        "valueText": "",
        "marginLeft": 0,
        "marginRight": 0,
        "fontSize": 12,
        "markerLabelGap": 3,
        "markerSize": 16,
        "markerType": "line",
        "rollOverGraphAlpha": 0.15,
        "spacing": 10,
        "verticalGap": 0,
        "valueAlign": "left",
        "align": "center",
        "color": "#FFF"
    },
    "valueAxes": [{
        "includeAllValues": true,
        "id": "v2",
        "unit": "",
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
        "id": "v1",
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
        "id": "v3",
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
        "id": "v4",
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
        "id": "g1",
        "valueAxis": "v1",
        "type": "line",
        "title": "Humidity",
        "balloonText": "<b>[[value]]&#37;</b>",
        "valueField": "hum_value",
        "lineColor": "#1a8cff",
        "lineThickness": 1.5,
    }, {
        "id": "g2",
        "title": "Temperature",
        "valueAxis": "v2",
        "type": "line",
        "balloonText": "",
        "valueField": "temp_value",
        "lineColor": "#CC2529",
        "lineThickness": 1.5,
    }, {
        "id": "g3",
        "title": "Co2",
        "valueAxis": "v3",
        "type": "line",
        "balloonText": "<b>[[value]]ppm</b>",
        "valueField": "co2_value",
        "lineColor": "#69e069",
        "lineThickness": 1.5
    }, {
        "id": "g4",
        "title": "Vapor Pressure Deficit",
        "valueAxis": "v4",
        "type": "line",
        "balloonText": "<b>[[value]]mb</b>",
        "valueField": "vpd_value",
        "lineColor": "#d05bff",
        "lineThickness": 1.5,
    }],
    "chartCursor": {
        "categoryBalloonDateFormat": "L:NNA, MMM DD",
        "cursorColor": "#808080",
        "cursorAlpha": 0.8,
        "cursorPosition": "mouse",
        "showNextAvailable": true
    },
    "categoryField": "time",
    "categoryAxis": {
        "color": "#FFF",
        "axisColor": "#FFF",
        "gridColor": "#FFF",
        "parseDates": true,
        "equalSpacing": true,
        "gridPosition": "middle",
        "dashLength": 1,
        "minorGridEnabled": true,
        "minPeriod": "ss",
        "dateFormats": [{ "period": "fff", "format": "L:NN:SS" }, { "period": "ss", "format": "L:NN:SS A" }, { "period": "mm", "format": "L:NN A" }, { "period": "hh", "format": "L:NN A" }, { "period": "DD", "format": "MMM DD" }, { "period": "WW", "format": "MMM DD" }, { "period": "MM", "format": "MMM" }, { "period": "YYYY", "format": "YYYY" }]
    }
};

$(document).ready(function() {
    var sourceId, sourceType, dataType, dataLabel, limit, type;
    $('.ui.normal.dropdown').dropdown({ fullTextSearch: true });
    $('.sensorMenu').on('click keyup', function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        var temp = $(event.target);
        if (temp.hasClass('item') || keycode == 13) {

            sourceType = $(this).find('.active').data('sourcetype');
            dataType = $(this).find('.active').data('datatype');
            sourceId = $(this).find('.active').data('sourceid');
            dataLabel = $(this).find('.active').data('datalabel');
            dataType = $(this).find('.active').data('datatype');
            $('#spinner').css('visibility', 'visible');
            $.ajax({
                url: "/DataPoints/recent.json",
                type: 'post',
                data: { "source_id": sourceId, "source_type": sourceType, "data_type": dataType, "limit": 1000, "timeframe": '7d' },
                success: function(response) {
                    $('#spinner').css('visibility', 'hidden');
                    renderChart(response);
                },
                error: function(res) {
                    $('#spinner').css('visibility', 'hidden');
                    $.gritter.add({
                        title: 'Warning',
                        text: 'Unable to gather the batch\'s data',
                        class_name: 'gritter-light',
                        time: '7500'
                    });
                }
            });
        }
    });
    if (batch_id) {
        $('.ui.normal.dropdown').dropdown('set selected', batch_id);
        $('#spinner').css('visibility', 'visible');
        $.ajax({
            url: "/DataPoints/recent.json",
            type: 'post',
            data: { "source_id": batch_id, "source_type": 3, "data_type": '', "limit": 1000, "timeframe": '7d' },
            error: function(res) {
                $('#spinner').css('visibility', 'hidden');
                $.gritter.add({
                    title: 'Warning',
                    text: 'Unable to gather the batch\'s data',
                    class_name: 'gritter-light',
                    time: '7500'
                });
            },
            success: function(response) {
                $('#spinner').css('visibility', 'hidden');
                renderChart(response);
            }
        });
    }
});

function renderChart(response) {
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
    full_data.sort(function(a, b) {
        var c = new Date(a.time);
        var d = new Date(b.time);
        return c - d;
    });
    var symbolNumeric = parseInt(response.dataSymbol.slice(2, -1), 10);
    var symbol = String.fromCharCode(symbolNumeric);
    var batch_chart = AmCharts.makeChart("chartdiv", GrowServer.batch_chart_config);
    var plantZoneTypeColors = ['#90DDF0', '#cbf7a5', '#f77e42', '#B3CAD4', '#A1674A', '#808782', '#AA3939', '#3C5A14'];
    var guides = [];
    tasks.forEach(function(kk) {
        var guide = {
            "date": new Date(kk.guide_date),
            "toDate": new Date(kk.guide_toDate),
            "lineAlpha": 0.5,
            "tickLength": 10,
            "label": kk.zone.label,
            "color": plantZoneTypeColors[kk.zone.plant_zone_type_id - 1],
            "fillColor": plantZoneTypeColors[kk.zone.plant_zone_type_id - 1],
            "inside": true,
            "fontSize": 12,
            "fillAlpha": 0.04
        }
        guides.push(guide);
    });
    batch_chart.categoryAxis.guides = guides;
    batch_chart.dataProvider = full_data;
    batch_chart.valueAxes[0].unit = symbol;
    batch_chart.graphs[1].balloonText = "[[value]]" + response.dataSymbol;
    batch_chart.validateData();
    if (response.results.temp_values.length == 0 && response.results.co2_values.length == 0 && response.results.vpd_values.length == 0 && response.results.hum_values.length == 0) {
        batch_chart.allLabels = [{ "text": "No datapoints found", "size": 11, "align": "center", "bold": true, "color": "#FFF" }];
        batch_chart.dataProvider = [{ time: '', empty: 0 }];
        batch_chart.valueAxes[0].minimum = 0;
        batch_chart.valueAxes[0].maximum = 100;
        batch_chart.categoryAxis.parseDates = false;
        batch_chart.chartCursor.enabled = false;
        batch_chart.validateData();
    }
}