/**
* Theme: nascharts init
* Author: codesolz
*/

Aios_NasCharts = function( _obj){
    this.$ = _obj.$;
    this.bindto = _obj.graph_load_in;
    this.titleText = _obj.title_text;
    this.subTitleText = _obj.sub_title_text;
    this.data = _obj.data;
    this.drillDownData = _obj.drill_down_data;
    this.seriesName = _obj.series_name;
};

Aios_NasCharts.prototype.init = function(){
 if(typeof Nascharts === 'undefined') return false;
  Nascharts.chart( this.bindto, {
    chart: {
        type: 'pie'
    },
    title: {
        text: this.titleText
    },
    subtitle: {
        text: this.subTitleText
    },
    plotOptions: {
        series: {
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y:.1f}%'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },
    series: [{
        name: this.seriesName,
        colorByPoint: true,
        data: this.data
        /****** example data******
         {
            name: 'Microsoft Internet Explorer',
            y: 56.33,
            drilldown: 'Microsoft Internet Explorer'
        }
        ****** example data******/
    }],
    drilldown: {
        series: [this.drillDownData] 
        /****** example series data******
        {
            name: 'Microsoft Internet Explorer',
            id: 'Microsoft Internet Explorer',
            data: [
                ['v11.0', 24.13],
                ['v8.0', 17.2],
                ['v9.0', 8.11],
                ['v10.0', 5.33],
                ['v6.0', 1.06],
                ['v7.0', 0.5]
            ]
        }
        ****** example series data******/
    }
});
  
    
};