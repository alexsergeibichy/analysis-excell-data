var employeeAvailablePerHour = function(data) {
    var chartData = [];
    for(var i=0; i<data.length; i++){
        var  hour = data[i]['hour'] + 'h';
        var temp = {
            "country": hour,
            "litres": data[i]['customer_num']
        }
        chartData.push(temp);
    }
    var chart = AmCharts.makeChart("chart_6", {
        "type": "pie",
        "theme": "light",

        "fontFamily": 'Open Sans',
        
        "color":    '#888',

        "dataProvider": chartData,
        "valueField": "litres",
        "titleField": "country",
        "exportConfig": {
            menuItems: [{
                icon: Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                format: 'png'
            }]
        }
    });

    $('#chart_6').closest('.portlet').find('.fullscreen').click(function() {
        chart.invalidateSize();
    });
}