/**
* Theme: Compare or detail
* Author: codesolz
* Chart c3 page
*/

!function($) {
    "use strict";

    var ChartC3 = function() {};

    ChartC3.prototype.init = function () {
        //generating chart 
        c3.generate({
            bindto: '#website_ranking_trust_graph',
            data: {
                columns: $c3_columns_data,
                labels: true,
                type: 'area-spline',
                colors: $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: $c3_categories
                }
            }
        }),
        c3.generate({
            bindto: '#website_auth_trust_graph',
            data: {
                columns: $c3_columns_data1,
                labels: true,
                type: 'area-spline',
                colors: $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: $c3_categories1
                }
            }
        });

    },
    $.ChartC3 = new ChartC3, $.ChartC3.Constructor = ChartC3

}(window.jQuery),

//initializing 
function($) {
    "use strict";
    $.ChartC3.init()
}(window.jQuery);



