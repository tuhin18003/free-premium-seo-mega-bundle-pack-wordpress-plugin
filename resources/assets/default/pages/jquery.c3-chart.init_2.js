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
                columns: typeof $c3_alexa_columns_data === 'undefined' ? '' : $c3_alexa_columns_data,
                labels: true,
                type: 'area-spline',
                colors: typeof $c3_charts_colors === 'undefined' ? '' : $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: typeof $c3_categories === 'undefined' ? '' : $c3_categories
                }
            }
        }),
        c3.generate({
            bindto: '#website_domain_auth_graph',
            data: {
                columns: typeof $c3_domAuth_columns_data === 'undefined' ? '' : $c3_domAuth_columns_data,
                labels: true,
                type: 'area-spline',
                colors: typeof $c3_charts_colors === 'undefined' ? '' : $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: typeof $c3_categories === 'undefined' ? '' : $c3_categories
                }
            }
        }),
        c3.generate({
            bindto: '#website_trust_graph',
            data: {
                columns: typeof $c3_sitetrust_columns_data === 'undefined' ? '' : $c3_sitetrust_columns_data,
                labels: true,
                type: 'area-spline',
                colors: typeof $c3_charts_colors === 'undefined' ? '' : $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: typeof $c3_categories === 'undefined' ? '' : $c3_categories
                }
            }
        }),
        c3.generate({
            bindto: '#website_spam_graph',
            data: {
                columns: typeof $c3_spam_columns_data === 'undefined' ? '' : $c3_spam_columns_data,
                labels: true,
                type: 'area-spline',
                colors: typeof $c3_charts_colors === 'undefined' ? '' : $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: typeof $c3_categories === 'undefined' ? '' : $c3_categories
                }
            }
        }),
        c3.generate({
            bindto: '#website_backlinks_graph',
            data: {
                columns: typeof $c3_backlink_columns_data === 'undefined' ? '' : $c3_backlink_columns_data,
                labels: true,
                type: 'area-spline',
                colors: typeof $c3_charts_colors === 'undefined' ? '' : $c3_charts_colors,
            },
            axis: {
                x: {
                    type: 'category',
                    categories: typeof $c3_categories === 'undefined' ? '' : $c3_categories
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



