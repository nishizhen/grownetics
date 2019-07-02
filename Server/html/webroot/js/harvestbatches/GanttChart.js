$( document ).ready(function() {
	var now  = moment().format("Y-MM-D");
	var formattedNow = moment().format("MMM Do");

	if (GrowServer.ganttData.length == 0) {
		GrowServer.ganttData = [{}];
		GrowServer.ganttDataByRoom = [{}];
	}
	var chart = AmCharts.makeChart( "chartdiv", {
		"type": "gantt",
		"theme": "light",
		"marginRight": 70,
		"path" : "/js/amcharts",
		"period": "DD",
		"dataDateFormat": "YYYY-MM-DD",
		"columnWidth": 0.2,
		"processTimeout" : 1,
		"dataProvider": GrowServer.ganttData,
		"creditsPosition": "bottom-left",
		"listeners" : [
			{
				"event": "init",
				"method": function(event) {
					$('.spinner').attr('style', 'display:none;');
				}
			}
		],
		"valueAxis": {
			"type": "date",
			"color": "#000",
			"gridColor" : "#000",
			"gridAlpha" : 0.11,
			"guides": [{
				"value": AmCharts.stringToDate( now, "YYYY-MM-DD"),
				"lineThickness": 4,
				"lineColor": "#55bb76",
				"label": "Today: "+formattedNow,
				"above": true,
				"position": "top"
			}]
		},
		"categoryAxis" : {
			"color" : "#0645AD",
			"includeAllValues": true,
			"gridColor" : "#000",
			"gridAlpha" : 0.11,
			"autoWrap" : true,
			"labelFunction" : function(valueText) {
				var batchLabel;
				//Search for batch object with category matching valueText (batch id)
			    for (var ii in GrowServer.ganttData) {
			        var batchId = GrowServer.ganttData[ii].category;
			        if (batchId == valueText) {
			        	batchLabel = "Batch #"+GrowServer.ganttData[ii].batch_no+" ("+GrowServer.ganttData[ii].cultivar+")";
			        	return batchLabel; 
			        } else {
			        	batchLabel = '';
			        }
			   	}
				return batchLabel;
			},
			"listeners": [{
				"event": "clickItem",
				"method": function(event) {
					window.location.href = "/HarvestBatches/view/"+event.serialDataItem.category;
				}
			}]
		},
		"brightnessStep": -13,
		"graph": {
			"fillAlphas": 1,
			"lineAlpha": 1,
			"lineColor": "#000",
			"fillAlphas": 0.85,
			"dateFormat": "MMM DD",
			"balloonText": "<b>[[task]]</b> ([[duration]] days)<br />[[open]] - [[value]]<br />[[plantCount]] active plants",
		},
		"rotate": true,
		"categoryField": "category",
		"segmentsField": "segments",
		"colorField": "color",
		"startDateField": "start",
		"endDateField": "end",
		"chartCursor": {
			"cursorColor": "#55bb76",
			"valueBalloonsEnabled": false,
			"cursorAlpha": 0,
			"valueLineAlpha": 0.7,
			"valueLineBalloonEnabled": true,
			"valueLineEnabled": true,
			"zoomable": false,
			"valueZoomable": false,
			"categoryBalloonEnabled" : false,
			"cursorPosition" : "middle"
		}
	});	
	if (GrowServer.ganttData.length >= 10) {
		$('#chartdiv').attr('style', 'height:'+(Math.ceil(GrowServer.ganttData.length / 10) * 300)+'px;');
	}
	$.each(GrowServer.ganttDataByRoom, function(key, val) {
		var chart_id = key.replace(/\s+/g, '');
		chart_id = 'chartByRoom'+chart_id;
		var new_chart = AmCharts.makeChart(chart_id, {
		"type": "gantt",
		"theme": "light",
		"marginRight": 70,
		"path" : "/js/bower_components/amcharts3/amcharts",
		"period": "DD",
		"dataDateFormat": "YYYY-MM-DD",
		"columnWidth": 0.2,
		"height": Math.ceil(val.length / 10) * 300,
		"processTimeout" : 1,
		"dataProvider": val,
		"creditsPosition": "bottom-left",
		"listeners" : [
			{
				"event": "init",
				"method": function(event) {
					$('.spinner').attr('style', 'display:none;');
				}
			}
		],
		"valueAxis": {
			"type": "date",
			"color": "#000",
			"gridColor" : "#000",
			"gridAlpha" : 0.11,
			"guides": [{
				"value": AmCharts.stringToDate( now, "YYYY-MM-DD"),
				"lineThickness": 4,
				"lineColor": "#55bb76",
				"label": "Today: "+formattedNow,
				"above": true,
				"position": "top"
			}]
		},
		"categoryAxis" : {
			"color" : "#0645AD",
			"includeAllValues": true,
			"gridColor" : "#000",
			"gridAlpha" : 0.11,
			"autoWrap" : true,
			"labelFunction" : function(valueText) {
				var batchLabel;
				//Search for batch object with category matching valueText (batch id)
			    for (var ii in val) {
			        var batchId = val[ii].category;
			        if (batchId == valueText) {
			        	batchLabel = "Batch #"+val[ii].batch_no+" ("+val[ii].cultivar+")";
			        	return batchLabel; 
			        } else {
			        	batchLabel = '';
			        }
			   	}
				return batchLabel;
			},
			"listeners": [{
				"event": "clickItem",
				"method": function(event) {
					window.location.href = "/HarvestBatches/view/"+event.serialDataItem.category;
				}
			}]
		},
		"brightnessStep": -13,
		"graph": {
			"fillAlphas": 1,
			"lineAlpha": 1,
			"lineColor": "#000",
			"fillAlphas": 0.85,
			"dateFormat": "MMM DD",
			"balloonText": "<b>[[task]]</b> ([[duration]] days)<br />[[open]] - [[value]]<br />[[plantCount]] active plants",
		},
		"rotate": true,
		"categoryField": "category",
		"segmentsField": "segments",
		"colorField": "color",
		"startDateField": "start",
		"endDateField": "end",
		"chartCursor": {
			"cursorColor": "#55bb76",
			"valueBalloonsEnabled": false,
			"cursorAlpha": 0,
			"valueLineAlpha": 0.7,
			"valueLineBalloonEnabled": true,
			"valueLineEnabled": true,
			"zoomable": false,
			"valueZoomable": false,
			"categoryBalloonEnabled" : false,
			"cursorPosition" : "middle"
		}
		});
	});
});

