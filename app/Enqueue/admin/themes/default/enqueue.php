<?php namespace CsSeoMegaBundlePack;
/**
 * Theme Common Enqueue
 * 
 * @since 1.0.0
 * @var \Herbert\Framework\Enqueue $enqueue
 * @author CodeSolz <customer-service@codesolz.net>
 */


/**************************Common CSS**************************/
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap',
    'src'    => Helper::assetUrl('/default/css/bootstrap.min.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-core',
    'src'    => Helper::assetUrl('/default/css/core.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-components',
    'src'    => Helper::assetUrl('/default/css/components.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-icons',
    'src'    => Helper::assetUrl('/default/css/icons.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-pages',
    'src'    => Helper::assetUrl('/default/css/pages.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-responsive',
    'src'    => Helper::assetUrl('/default/css/responsive.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-style',
    'src'    => Helper::assetUrl('/default/app_core/common/css/custom-style.css'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
/**************************Common CSS**************************/

/**************************Common JS**************************/
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-modernizr',
    'src'    => Helper::assetUrl('/default/js/modernizr.min.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap',
    'src'    => Helper::assetUrl('/default/js/bootstrap.min.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-detect',
    'src'    => Helper::assetUrl('/default/js/detect.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-fastclick',
    'src'    => Helper::assetUrl('/default/js/fastclick.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);

//new added
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.slimscroll_custom',
//    'src'    => Helper::assetUrl('/default/js/jquery.slimscroll.js'),
//    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us' ],
//        'subpanel' => ['*']
//    ]
//]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.blockUI',
//    'src'    => Helper::assetUrl('/default/js/jquery.blockUI.js'),
//    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us' ],
//        'subpanel' => ['*']
//    ]
//]);


$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-waves',
    'src'    => Helper::assetUrl('/default/js/waves.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-wow',
    'src'    => Helper::assetUrl('/default/js/wow.min.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);

//new 
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.nicescroll',
//    'src'    => Helper::assetUrl('/default/js/jquery.nicescroll.js'),
//    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us' ],
//        'subpanel' => ['*']
//    ]
//]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.scrollTo',
//    'src'    => Helper::assetUrl('/default/js/jquery.scrollTo.min.js'),
//    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us' ],
//        'subpanel' => ['*']
//    ]
//]);


//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.core',
//    'src'    => Helper::assetUrl('/default/js/jquery.core.js'),
//    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us' ],
//        'subpanel' => ['*']
//    ]
//]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.app',
    'src'    => Helper::assetUrl('/default/js/jquery.app.js'),
    'filter' => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics',  'cs-on-page-optimization', 'about-us', 'cs-aiost-dashboard' ],
        'subpanel' => ['*']
    ]
]);
/**************************Common JS**************************/