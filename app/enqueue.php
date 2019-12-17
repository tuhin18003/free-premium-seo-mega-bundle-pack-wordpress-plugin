<?php namespace CsSeoMegaBundlePack;

/** @var \Herbert\Framework\Enqueue $enqueue */

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap',
    'src'    => Helper::assetUrl('/default/css/bootstrap.min.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-core',
    'src'    => Helper::assetUrl('/default/css/core.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-components',
    'src'    => Helper::assetUrl('/default/css/components.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-icons',
    'src'    => Helper::assetUrl('/default/css/icons.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-pages',
    'src'    => Helper::assetUrl('/default/css/pages.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-responsive',
    'src'    => Helper::assetUrl('/default/css/responsive.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-sweetalert',
//    'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.css'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-style',
    'src'    => Helper::assetUrl('/default/app_core/backlink_manager/css/custom-style.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-modernizr',
    'src'    => Helper::assetUrl('/default/js/modernizr.min.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);


//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jQuery',
//    'src'    => Helper::assetUrl('/default/js/jQuery.min.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap',
    'src'    => Helper::assetUrl('/default/js/bootstrap.min.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-detect',
    'src'    => Helper::assetUrl('/default/js/detect.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-fastclick',
    'src'    => Helper::assetUrl('/default/js/fastclick.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.slimscroll',
    'src'    => Helper::assetUrl('/default/js/jquery.slimscroll.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.blockUI',
//    'src'    => Helper::assetUrl('/default/js/jquery.blockUI.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-waves',
    'src'    => Helper::assetUrl('/default/js/waves.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-wow',
    'src'    => Helper::assetUrl('/default/js/wow.min.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.nicescroll',
//    'src'    => Helper::assetUrl('/default/js/jquery.nicescroll.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);
//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.scrollTo',
//    'src'    => Helper::assetUrl('/default/js/jquery.scrollTo.min.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);



//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-sweetalert',
//    'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.min.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);


//$enqueue->admin([
//    'as'     => 'all-in-one-seo-tool-jquery.core',
//    'src'    => Helper::assetUrl('/default/js/jquery.core.js'),
//    'filter' => [ 'panel' => 'cs-backlink-manager' ]
//]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.app',
    'src'    => Helper::assetUrl('/default/js/jquery.app.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-aios.event.handler',
    'src'    => Helper::assetUrl('/default/app_core/common/js/aios.event.handler.js'),
    'deps'   => [ 'jquery-form' ],
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'       => 'all-in-one-seo-tool-aios.event.handler',
    'lc'       => true,
    'name'     => 'AIOS',
    'data'     => [ 
            'error_msg' => __( 'Sorry, unable to do that. Please try refreshing the page.', SR_TEXTDOMAIN ), 
            'swal_processing_title' => __( 'Processng...', SR_TEXTDOMAIN ), 
            'swal_error_title' => __( 'Error!', SR_TEXTDOMAIN ), 
            'swal_processing_text' => __( 'Please wait a while...', SR_TEXTDOMAIN ),
            'loading_gif_url' => plugin_dir_url( __FILE__ ) . 'resources/assets/default/images/loading.gif'
        ],
    'filter'   => [ 'panel' => 'cs-backlink-manager',
            'subPanel' => [ 'GeneralOptions']
        ]
]);
