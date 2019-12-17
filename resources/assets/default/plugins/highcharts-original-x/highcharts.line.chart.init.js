/**
* CSMBP Highcharts Line Charts
* 
* @package CSMBP
* @param {ojbect} _obj description
* @since 1.0.0
* @author CodeSolz <customer-service@codesolz.com>
*/

Aios_HighCharts_Line = function( _obj){
    this.$ = _obj.$;
    this.bindto = _obj.graph_load_in;
    this.titleText = _obj.title_text;
    this.subTitleText = _obj.sub_title_text;
    this.data = _obj.data;
    this.reverse_y_axis = _obj.reverse_y_axis;
    this.y_axis_text = _obj.y_axis_text;
};

Aios_HighCharts_Line.prototype.init = function(){
    if(typeof Highcharts === 'undefined') return false;
    
    Highcharts.chart(this.bindto, {
    chart: {
        type: 'spline',
        zoomType: 'x'
    },
    title: {
        text: this.titleText
    },
    subtitle: {
        text: this.subTitleText
    },
    tooltip: {
            shared: true,
            crosshairs: true
        },
    xAxis: {
        type: 'datetime',
        maxZoom: 24 * 3600 * 1000
    },
    yAxis: {
        title: {
            text: typeof this.y_axis_text === 'undefined' ? '' : this.y_axis_text 
        },
        reversed: typeof this.reverse_y_axis === 'undefined' ? false : true
    },
    series: this.data
});
 
};
