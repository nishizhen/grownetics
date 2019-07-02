var GrowServer = GrowServer || {};
GrowServer.zoneChartConfig = {
    "type": "serial",
    "theme": "light",
    "fontFamily": "Ruda",
    "autoMargins": true,
    "addClassNames": true,
    "dataProvider": [{}],
    "height": "96%",
    "zoomOutText": '',
    "creditsPosition": "bottom-left",
    "path": "/js/bower_components/amcharts3/amcharts",
    "legend": {
        "position": "bottom",
        "labelText" : "[[title]]",
        "fontFamily": "Lato",
        "valueText" : "",
        "marginLeft" : 0,
        "marginRight" : 0,
        "fontSize": 9,
        "markerLabelGap": 3,
        "markerSize": 12,
        "markerType": "line",
        "rollOverGraphAlpha" : 0.15,
        "spacing": 10,
        "verticalGap" : 0,
        "valueAlign":"left",
        "align": "center"
    },
    "valueAxes": [{
        "includeAllValues": true,
        "id":"v2",
        "unit": "",
        "axisColor": "#CC2529",
        "axisThickness": 1.5,
        "axisAlpha": 1,
        "gridAlpha": 0.05,
        "fontSize": 10,
        "position": "left"
    }, {
        "includeAllValues": true,
        "unit": "\u0025",
        "id":"v1",
        "axisColor": "#1a8cff",
        "axisThickness": 1.5,
        "axisAlpha": 1,
        "gridAlpha": 0.05,
        "fontSize": 10,
        "offset": 50,
        "position": "left"
    }, {
        "includeAllValues": true,
        "id":"v3",
        "unit": "ppm",
        "axisColor": "#69e069",
        "axisThickness": 1.5,
        "gridAlpha": 0.05,
        "fontSize": 10,
        "axisAlpha": 1,
        "position": "right"
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
        "balloonText": "", //<b>[[value]]"+data.dataSymbol+"</b>
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
        "parseDates": true,
        "minPeriod": "ss",
        "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
    }
};
GrowServer.sensorChartConfig = {
     "type": "serial",
     "theme": "light",
     "fontFamily": "Ruda",
     "creditsPosition": "bottom-left",
     "autoMargins": true,
     "addClassNames": true,
     "path": "/js/bower_components/amcharts3/amcharts",
     "fontSize": 12,
     "dataProvider": [{}],
     "valueAxes": [{
         "unit": "",
         "position": "left",
         "includeAllValues": true,
         "axisColor" : "#000",
         "axisThickness": 1.5,
         "guides": [{
             "fillAlpha": 0.1,
             "fillColor": "#00d176",
             "lineAlpha": 0.1,
             "value": "",
             "toValue": "",
         }],
     }],
     "balloon": {
         "borderThickness": 1,
         "shadowAlpha": 0,
         "drop":true
     },
     "graphs": [{
         "id": "g1",
         "lineColor": "#CC2529",
         "lineThickness": 1.5,
         "negativeLineColor": "#3E9651",
         "negativeBase": "",
         "type": "line",
         "showBalloon": false,
         "valueField": "mean",
     }, {
         "id": "g2",
         "lineColor": "#CC2529",
         "balloonColor": "#000",
         "lineThickness": 1.5,
         "lineAlpha": 0,
         "negativeLineColor": "#CC2529",
         "negativeBase": "",
         "negativeLineAlpha": 1,
         "showBalloon": true,
         "type": "line",
         "valueField": "mean",
         "balloonText": ""
     }],
     "chartCursor": {
         "valueLineEnabled": false,
         "valueLineBalloonEnabled": false,
         "cursorAlpha":.3,
         "cursorColor":"#258cbb",
         "limitToGraph":"g1",
         "valueLineAlpha":0.2,
         "categoryBalloonDateFormat": "L:NN:SS A, MMM DD",
         "categoryBalloonAlpha": 0.9
     },
     "balloon": {
         "borderAlpha": 0.7,
         "fillAlpha": 0.9,
         "fillColor" : "#fff",
         "shadowAlpha": 0,
         "offsetX": 40,
         "offsetY": -50
     },
     "categoryField": "time",
     "categoryAxis": {
         "equalSpacing": true,
         "minPeriod": "ss",
         "parseDates": true,
         "color": "#000",
         "dateFormats" : [{"period":"fff","format":"L:NN:SS"},{"period":"ss","format":"L:NN:SS A"},{"period":"mm","format":"L:NN A"},{"period":"hh","format":"L:NN A"},{"period":"DD","format":"MMM DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}]
     }
};
var chartUpdateInterval = 30000;

/**
 * Grownetics Charts Object
 *
 * Keeps tracks of Chart.js chart objects and updates multiple charts at once
 * in response to api data recieved.
 *
 * Depends on Chart.js and Moment.js
 */
(function() {
    
    GrowServer.Charts = function( options) {
        
        $.extend(this, options);
        
        var self = this;

        // connect event listeners to dom
        $(document).ready(function() {

            // bootstrap the charts
            self.getDashboardConfig();
            var data_type, data_label, data_symbol, data_display_class, source_id, source_type, zoneName, sensorName;

            // click handler for dashgum menu
            $(".sensorMenuSmall").on('click keyup', function(evt) {
                var keycode = (evt.keyCode ? evt.keyCode : evt.which);
                var temp = $(evt.target);
                if (temp.hasClass('item') || keycode == 13) {
                    
                    var $this = $(this);
                    var panelEl = $this.parents(".content-panel");
                    var canvas = panelEl.find("canvas")[0];
                    var row = $(evt.currentTarget).closest('.row');
                    var $canvas = $(canvas);
                    if ($(this).find('.active').data('sourcetype') == 0) {
                        $this.parents(".content-panel").find('.spinner').attr('style', 'visibility:visible; bottom:200px;');
                        data_type = $(this).find('.active').data('sourcedatatype');
                        source_id = $(this).find('.active').data('sourceid');
                        source_type = $(this).find('.active').data('sourcetype');
                        data_label = $(this).find('.active').data('datalabel');
                        data_symbol = $(this).find('.active').data('datasymbol');
                        data_display_class = $(this).find('.active').data('displayclass');
                        sensorName = $(this).find('.active').html();
                        if (canvas) {
                            $canvas.data("sourcedatatype", data_type);
                            $canvas.data("data-label", data_label);
                            $canvas.data("data-symbol", data_symbol);
                            $canvas.data("data-display-class", data_display_class);
                            $canvas.data("source-type", source_type);
                            $canvas.data("source-id", source_id);
                            $canvas.data("source-label", sensorName);
                            $this.closest(".row").find('.btn-primary').trigger('click');
                            var chartId = $canvas.offsetParent().find('.chart-title').data('chartid');
                            self.updateDashboardConfig(true);
                            clearInterval(self.chart_refresh);
                            self.updateChart(chartId, source_id, source_type, data_type);
                            self.chart_refresh = setInterval(function() {
                                self.updateChart(chartId, source_id, source_type, data_type);
                             }, chartUpdateInterval);                            
                            $canvas.parents('.content-panel').find('.sensorMenuSmall .text').html(sensorName);
                            $canvas.parents('.content-panel').find('.chart-title').html(sensorName);
                        }
                    } else {
                        zoneName = $(this).find('.active').text().trim();
                        var source_id = $(this).find('.active').data('sourceid');
                        var source_type = $(this).find('.active').data('sourcetype');
                        var data_type = null;
                        $canvas.data("source-type", source_type);
                        $canvas.data("source-id", source_id);
                        $canvas.data("source-label", zoneName);
                        $canvas.data("sourcedatatype", data_type);
                        $canvas.data("data-label", $(this).find('.active').data('datalabel'));
                        $canvas.data("data-display-class", $(this).find('.active').data('displayclass'));
                        $canvas.data("data-symbol", $(this).find('.active').data('datasymbol'));
                        var chartId = $canvas.offsetParent().find('.chart-title').data('chartid');
                        $this.parents(".content-panel").find('.spinner').attr('style', 'visibility:visible; position:relative; bottom:100px;');
                        var dataIcon = "<i class='" + $(this).find('.active').data('displayclass')+ "'></i>";
                        $this.closest(".row").find('.btn-primary').trigger('click');
                        $canvas.parents('.content-panel').find('.chart-title').html($this.parents(".content-panel").find('.sensorMenuSmall .text').text()+" - <i class='wi wi-humidity'></i> <i class='wi wi-thermometer'></i> <i class='wi wi-raindrop'></i>");
                        self.updateDashboardConfig(true);
                        clearInterval(self.chart_refresh);
                        self.updateChart(chartId, source_id, source_type, data_type);
                        self.chart_refresh = setInterval(function() {
                            self.updateChart(chartId, source_id, source_type, data_type);
                         }, chartUpdateInterval);
                    }
                }
            });

            $('.green-header').hover(
                function() {
                    $(this).offsetParent().find('.editChartBtn').attr('style', 'visibility:visible');
                }, 
                function () {
                    if ($(this).offsetParent().find('.collapse').is('.in') == false) {
                        $(this).offsetParent().find('.editChartBtn').attr('style', 'visibility:hidden');
                    }
                }
            );

            $('.thresholdInput #highThresholdInput, #lowThresholdInput').keypress( function (event) {
                $(this).parents('.content-panel').find('#thresholdHelpText').hide();
                $(this).parents('.content-panel').find('.submitEditChartBtn').fadeIn();
                $(this).closest(".row").find('.btn-primary').html('Cancel');
                $(this).closest(".row").find('.btn-primary').click(function(event) {
                    $(this).parents('.content-panel').find('#lowThresholdInput').val($(this).parents('.content-panel').find('canvas').data('lowThreshold'));
                    $(this).parents('.content-panel').find('#highThresholdInput').val($(this).parents('.content-panel').find('canvas').data('highThreshold'));
                    $(this).parents('.content-panel').find('.submitEditChartBtn').hide();
                    $(this).parents('.content-panel').find('#thresholdHelpText').show();
                    $(this).parents('.content-panel').find('.form-control').attr('style', 'border-color: ');
                    $(this).parents('.content-panel').find('.help-block span:last').fadeOut();
                    $(this).text('Edit');
                });
            });

            $('#addChart').click(function(event) {
                event.preventDefault();
                self.addDashboardChart();
            });

            $('.deleteChart').click(function(event) {
                event.preventDefault();
                var chartId = $(event.currentTarget).attr('dataId');
                self.deleteChart(chartId);
                $('.modal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove()
            });

            $('.thresholdInput .submitEditChartBtn').click(function(event) {
                var highInput = $(this).parents('.content-panel').find('#highThresholdInput').val();
                var lowInput = $(this).parents('.content-panel').find('#lowThresholdInput').val();
                if ($.isNumeric(highInput) && $.isNumeric(lowInput)) {
                    if (lowInput > highInput) {
                        var temp = lowInput;
                        lowInput = highInput;
                        highInput = temp;
                    }
                    $(this).parents('.content-panel').find('canvas').data('lowThreshold', lowInput);
                    $(this).parents('.content-panel').find('canvas').data('highThreshold', highInput);
                    $(this).closest(".row").find('.btn-primary').trigger('click');
                    var source_id = $(this).parents('.content-panel').find('canvas').data('source-id');
                    var source_type = $(this).parents('.content-panel').find('canvas').data('source-type');
                    var data_type = $(this).parents('.content-panel').find('canvas').data('sourcedatatype');
                    var chartId = $(this).parents('.content-panel').find('.chart-title').data('chartid');
                    self.updateDashboardConfig(true);
                    clearInterval(self.chart_refresh);
                    self.updateChart(chartId, source_id, source_type, data_type, lowInput, highInput);
                    self.chart_refresh = setInterval(function() {
                        self.updateChart(chartId, source_id, source_type, data_type, lowInput, highInput);
                     }, chartUpdateInterval); 
                } else {
                    $(this).parents('.form-group').find('.form-control').attr('style', 'border-color: red');
                    $(this).parents('.form-group').find('.help-block span:last').fadeIn();
                }
            });

        });
    };
    
    GrowServer.Charts.prototype = {
        
        UPDATE_INTERVAL : 5000,
        dateFormat : "h:mm:ss a",
        maxPointsShown : 1000,
        configUrl: "/dash/config.json",

        charts : [],
              
        getDatapointParams: function getDatapointParams() {
            var datapoints = [];
            var canvasses = $(".content-panel canvas");

            canvasses.each( function (i) {            
                var $this = $(this);

                if ($this.data("source-type") == 1) {
                    datapoints.push({
                        "data_type" : [2, 3, 4],
                        "source_type": $this.data("source-type"),
                        "source_id" : $this.data("source-id"),
                        "limit" : 3
                    });
                } else {
                    datapoints.push({
                        "data_type" : $this.data("sourcedatatype"),
                        "source_type": $this.data("source-type"),
                        "source_id" : $this.data("source-id"),
                        "limit" : 1
                    });
                }
            });
            return datapoints;
        },

         getDashboardConfig: function getDashboardConfig() {
             var self = this;
             $.getJSON(this.configUrl, function(configData) {

                 if (configData.dashboard_config) {
                     configData = JSON.parse(configData.dashboard_config);
                 }

                 if (configData && configData.length) {
                     var canvasses = $(".content-panel canvas");

                     for (var i = 0; i < configData.length; i++) {
                         (function(counter){
                             var config = configData[i];

                             var $canvas = $(canvasses[i]);
                             $canvas.data("sourcedatatype", config.data_type);
                             $canvas.data("source-type", config.source_type);
                             $canvas.data("source-id", config.source_id);
                             $canvas.data("source-label", config.source_label);
                             $canvas.data("lowThreshold", config.lowThreshold);
                             $canvas.data("highThreshold", config.highThreshold);
                             $canvas.data("data-label", config.data_label);
                             $canvas.data("data-symbol", config.data_symbol);
                             $canvas.data("data-display-class", config.data_display_class);
                             $canvas.parents('.content-panel').find('#lowThresholdInput').val(config.lowThreshold);
                             $canvas.parents('.content-panel').find('#highThresholdInput').val(config.highThreshold);
                             if ($canvas.data("source-label") != "") {
                                $canvas.parents('.content-panel').find('.sensorMenuSmall .text').html(config.source_label);
                             } else {
                                $canvas.parents('.content-panel').find('.sensorMenuSmall .text').text("Sensors/Zones");
                             }
                             var dataIcon = "<i class='"+config.data_display_class+"'></i>";
                             if ($canvas.data("source-type") == 0) {
                                if (config.source_label != '') {
                                    $canvas.parents('.content-panel').find('.chart-title').html(config.source_label);
                                } else {
                                    $canvas.parents('.content-panel').find('.chart-title').html('Select an input...');
                                }
                             } else {
                                $canvas.parents('.content-panel').find('.chart-title').html(config.source_label+" - <i class='wi wi-humidity'></i> <i class='wi wi-thermometer'></i> <i class='wi wi-raindrop'></i>");
                             }
                             var chartId = $canvas.offsetParent().find('.chart-title').data('chartid');
                             self.updateChart(chartId, config.source_id, config.source_type, config.data_type, config.lowThreshold, config.highThreshold);
                             self.chart_refresh = setInterval(function() {
                                self.updateChart(chartId, config.source_id, config.source_type, config.data_type, config.lowThreshold, config.highThreshold);
                             }, chartUpdateInterval);


                         })(i);

                         //button labels and title should match up
                         //refresh should display correct format

                     }
                 }
             });
         },

         updateDashboardConfig: function updateDashboardConfig( doSave) {
             var configData = this.getConfigData();
             if (doSave) {
                 $.post(this.configUrl, { "dashboard_config": configData }, function(response) {
                     console.log('dashboard config saved', response); //FIXME
                 }, "json");
             }
             
         },

         addDashboardChart: function addDashboardChart() {
             var configData = this.getConfigData();
             configData.push({"data_type":"","source_type":"","source_id":"","lowThreshold":"","highThreshold":"","source_label":"","data_label":"","data_symbol":"","data_display_class":""});
             $.post(this.configUrl, { "dashboard_config": configData }, function(response) {
                 console.log('dashboard config saved', response); //FIXME
                 location.reload()
             }, "json");
         },

         deleteChart: function(chartId) {
            var configData = this.getConfigData();
            $('.small_chart:eq('+(chartId-1)+')').remove();
            this.updateDashboardConfig(true);
         },

        getConfigData: function() {
            var configData = [];
            var self = this;
            this.charts = [];
            var canvasses = $(".content-panel canvas");
            canvasses.each( function (ii) {
                var $this = $(this);
                configData.push({
                    "data_type" : $this.data("sourcedatatype"),
                    "data_label" : $this.data("data-label"),
                    "data_symbol" : $this.data("data-symbol"),
                    "data_display_class" : $this.data("data-display-class"),
                    "source_type" : $this.data("source-type"),
                    "source_id" : $this.data("source-id"),
                    "source_label" : $this.data("source-label"),
                    "lowThreshold" : $this.data("lowThreshold"),
                    "highThreshold" : $this.data("highThreshold")
                });
            });
            return configData;
        },
        updateChart: function(chartId, source_id, source_type, data_type, lowThreshold=null, highThreshold=null) {
            var chartConfig = {"chart_id": chartId,"source_type": source_type, "source_id": source_id, "data_type": data_type, "timeframe": '12h'};
            $.ajax({
                url: "/DataPoints/recent.json",
                type: 'post',
                data: chartConfig,
                success: function (data) {
                    var chart_div_id = 'chartdiv' + chartId;
                    var prev_chart = getChart(chart_div_id);
                    if (prev_chart) {
                        prev_chart.clear();
                    }
                    var numericValue = parseInt(data.dataSymbol.slice(2, -1),10);
                    if ($.isNumeric(numericValue)) {
                        var symbol = String.fromCharCode(numericValue);
                    } else {
                        var symbol = data.dataSymbol;
                    }
                    
                    if (chartConfig.source_type == 0) {
                        var sensorChartConfigCopy = clone(GrowServer.sensorChartConfig);
                        var chart = AmCharts.makeChart(chart_div_id, sensorChartConfigCopy);
                        data.results = data.results.filter(item => item.mean !== null);
                        data.results.forEach(function(ii) {
                            if (data.dataSymbol == '&#8457;') {
                                ii.mean = parseFloat((ii.mean * 9 / 5) + 32).toFixed(2);
                            } else {
                                ii.mean = parseFloat(ii.mean).toFixed(2);
                            }
                        });
                        chart.dataProvider = data.results;
                        chart.valueAxes[0].unit = symbol;
                        chart.graphs[1].balloonText = "[[mean]]"+data.dataSymbol;
                        chart.valueAxes[0].guides[0].value = lowThreshold;
                        chart.valueAxes[0].guides[0].toValue = highThreshold;
                        chart.graphs[0].negativeBase = highThreshold;
                        chart.graphs[1].negativeBase = lowThreshold;
                        if (chartConfig.data_type == 3) {
                            chart.graphs[0].lineColor = "#CC2529";
                            chart.graphs[1].lineColor = "#CC2529";
                            chart.graphs[1].negativeLineColor = "#CC2529"; 
                            chart.valueAxes[0].axisColor = "#CC2529"; 
                        } else if (chartConfig.data_type == 2) {
                            chart.graphs[0].lineColor = "#1a8cff";
                            chart.graphs[1].lineColor = "#1a8cff";
                            chart.graphs[1].negativeLineColor = "#1a8cff"; 
                            chart.valueAxes[0].axisColor = "#1a8cff"; 
                        } else if (chartConfig.data_type == 4) {
                            chart.graphs[0].lineColor = "#69e069";
                            chart.graphs[1].lineColor = "#69e069";
                            chart.graphs[1].negativeLineColor = "#69e069";
                            chart.valueAxes[0].axisColor = "#69e069";  
                        } else {
                            chart.graphs[0].lineColor = "#CC2529";
                            chart.graphs[1].lineColor = "#CC2529";
                            chart.graphs[1].negativeLineColor = "#CC2529";
                            chart.valueAxes[0].axisColor = "#000";  
                        } 
                        if (data.results.length == 0) {
                            chart.allLabels = [{"text": "No datapoints found", "size": 11, "align": "center", "bold": true}];
                            chart.dataProvider = [{time: '', empty: 0}];
                            chart.valueAxes[0].minimum = 0;
                            chart.valueAxes[0].maximum = 100;
                            chart.valueAxes[0].unit = symbol;
                            chart.categoryAxis.parseDates = false;
                            chart.chartCursor.enabled = false;
                        }
                    } else {
                        data.results.hum_values = data.results.hum_values.filter(item => item.mean !== null);
                        data.results.co2_values = data.results.co2_values.filter(item => item.mean !== null);
                        data.results.temp_values = data.results.temp_values.filter(item => item.mean !== null);
                        data.results.hum_values.forEach(function(ii) {
                            ii.hum_value = ii.mean.toFixed(2);
                        });
                        data.results.co2_values.forEach(function(ii) {
                            ii.co2_value = ii.mean.toFixed(2);
                        });
                        data.results.temp_values.forEach(function(ii) {
                            if (data.dataSymbol == '&#8457;') {
                                ii.temp_value = parseFloat((ii.mean * 9 / 5) + 32).toFixed(2);
                            } else {
                                ii.temp_value = ii.mean.toFixed(2);
                            }
                        });
                        var full_data = normalizeData(data.results);
                        var zoneChartConfigCopy = clone(GrowServer.zoneChartConfig);
                        var chart = AmCharts.makeChart(chart_div_id, zoneChartConfigCopy);
                        chart.dataProvider = full_data;
                        chart.valueAxes[0].unit = symbol;
                        chart.graphs[1].balloonText = "[[value]]"+data.dataSymbol+"";
                        if (full_data.length == 0) {
                            chart.allLabels = [{"text": "No datapoints found", "size": 11, "align": "center", "bold": true}];
                            chart.dataProvider = [{time: '', empty: 0}];
                            chart.valueAxes[0].minimum = 0;
                            chart.valueAxes[0].maximum = 100;
                            chart.valueAxes[0].unit = symbol;
                            chart.categoryAxis.parseDates = false;
                            chart.chartCursor.enabled = false;
                        }
                    }
                    chart.validateData();
                    $('#'+chart_div_id).parents('.content-panel').find('.spinner').attr('style', 'display:none;');
                },
                error: function(err) {
                    console.log(err);
                    //location.reload();
                }
            });
        }
    }; // Grownetics.Charts
    function getChart(id) {
        var allCharts = AmCharts.charts;
        for (var i = 0; i < allCharts.length; i++) {
            if (id == allCharts[i].div.id) {
                return allCharts[i];
            }
        }
    }
    function getMinMax(arr) {
        var values = arr.concat();
        values.sort(function(a, b) {
            return a - b;
        });    
        var q1 = values[Math.floor((values.length / 4))];
        var q3 = values[Math.ceil((values.length * (3 / 4)))];
        var iqr = q3 - q1;
        var maxValue = q3 + iqr*1.5;
        var minValue = q1 - iqr*1.5;

        return {"minValue": minValue, "maxValue": maxValue};
    }
    function normalizeData(data) {
        var full_data = data.temp_values.concat(data.co2_values, data.hum_values);
        full_data.sort(function(a,b) {
            var c = new Date(a.time);
            var d = new Date(b.time);
            return c - d;
        });
        return full_data;
    }
    function removeItems(arr, item) {
        for ( var i = 0; i < item; i++ ) {
            arr.shift();
        }
    }
    function clone(obj) {
        var copy;
        // Handle the 3 simple types, and null or undefined
        if (null == obj || "object" != typeof obj) return obj;
        // Handle Date
        if (obj instanceof Date) {
            copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }
        // Handle Array
        if (obj instanceof Array) {
            copy = [];
            for (var i = 0, len = obj.length; i < len; i++) {
                copy[i] = clone(obj[i]);
            }
            return copy;
        }
        // Handle Object
        if (obj instanceof Object) {
            copy = {};
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
            }
            return copy;
        }

        throw new Error("Unable to copy obj! Its type isn't supported.");
    }
})();