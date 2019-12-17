/**
* Theme: Compare or detail
* Author: codesolz
* Chart c3 page
*/

Aios_C3_Chart = function( _obj){
    this.$ = _obj.$;
    this.bindto = _obj.graph_load_in;
    this.column_data = _obj.column_data;
    this.colors = _obj.colors;
    this.categories = _obj.categories;
};

Aios_C3_Chart.prototype.init = function(){
    if(typeof c3 === 'undefined') return false;
    c3.generate({
        bindto: this.bindto,
        data: {
            columns: this.column_data,
            labels: true,
            type: 'area-spline',
            colors: this.colors,
        },
        axis: {
            x: {
                type: 'category',
                categories: this.categories
            }
        }
    });
};


