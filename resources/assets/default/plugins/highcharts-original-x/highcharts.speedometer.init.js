/**
* Theme: AIOS Highcharts speedometer Charts
* Author: codesolz
*/

Aios_HighCharts_speedometer = function( _obj){
    this.$ = _obj.$;
    this.bindto = _obj.graph_load_in;
    this.titleText = _obj.title_text;
    this.subTitleText = _obj.sub_title_text;
    this.data = _obj.data;
};

Aios_HighCharts_speedometer.prototype.init = function(){
    if(typeof Highcharts === 'undefined') return false;
    
    
    Highcharts.chart( this.bindto, {

        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: this.titleText
        },

        pane: {
            startAngle: -150,
            endAngle: 150,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: 100,

            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',

            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: this.subTitleText
            },
            plotBands: [{
                from: 80,
                to: 100,
                color: '#55BF3B' // green
            }, {
                from: 60,
                to: 80,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: 60,
                color: '#DF5353' // red
            }]
        },

        series: [{
            name: 'Speed',
            data: [this.data],
            /*tooltip: {
                valueSuffix: ' 100'
            }*/
        }]
    });
 
};
