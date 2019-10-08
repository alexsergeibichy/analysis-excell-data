var customerPerWeekend = function (data) {
	var chartData = [];
	for(var i=0; i<data.length; i++){
		var temp = {
			"date": data[i]['date']+':am',
            "customers": data[i]['customer_am']
		}
		chartData.push(temp);
        var temp = {
            "date": 'pm',
            "customers": data[i]['customer_pm']
        }
        chartData.push(temp);
        var temp = {
            "date": 'ng',
            "customers": data[i]['customer_ng']
        }
        chartData.push(temp);
	}
	// console.log(chartData);
	var chart = AmCharts.makeChart("chart_15", {
        "type": "serial",
        "theme": "light",
        "pathToImages": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/",
        "autoMargins": false,
        "marginLeft": 45,
        "marginRight": 8,
        "marginTop": 10,
        "marginBottom": 26,

        "fontFamily": 'Open Sans',            
        "color":    '#888',
        
        "dataProvider": chartData,
        "valueAxes": [{
            "axisAlpha": 0,
            "position": "left"
        }],
        "startDuration": 1,
        "graphs": [{
            "alphaField": "alpha",
            "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
            "dashLengthField": "dashLengthColumn",
            "fillAlphas": 1,
            "title": "customers",
            "type": "column",
            "valueField": "customers"
        }, {
            "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
            "bullet": "round",
            "dashLengthField": "dashLengthLine",
            "lineThickness": 3,
            "bulletSize": 7,
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "useLineColorForBulletBorder": true,
            "bulletBorderThickness": 3,
            "fillAlphas": 0,
            "lineAlpha": 1,
            "title": "Expenses",
            "valueField": "expenses"
        }],
        "categoryField": "date",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "tickLength": 0
        }
    });
}