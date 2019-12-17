<?php namespace CsSeoMegaBundlePack;
/**
 * Enqueue Script Backlink Manager
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

/*tab slug - contains into common file*/
$tab = isset( $_GET['tab'] ) ? trim( $_GET['tab'] ) : '';
$page = isset( $_GET['page'] ) ? trim( $_GET['page'] ) : '';


//property manage page
$property_compare = isset($_GET['compare']) ? trim($_GET['compare']) : '';
$property_compare_by_date = isset($_GET['compare_by_date']) ? trim($_GET['compare_by_date']) : '';

/*************Common Scripts start*************/
if( $tab  === '' && $page === 'cs-backlink-manager'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.waypoints',
        'src'    => Helper::assetUrl('/default/plugins/waypoints/lib/jquery.waypoints.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.counterup.min',
        'src'    => Helper::assetUrl('/default/plugins/counterup/jquery.counterup.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.knob',
        'src'    => Helper::assetUrl('/default/plugins/jquery-knob/jquery.knob.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-dashboard',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/dashboard.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-c3.min',
        'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-d3.min.js',
        'src'    => Helper::assetUrl('/default/plugins/d3/d3.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-c3.min.js',
        'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.c3-chart.init.js',
        'src'    => Helper::assetUrl('/default/pages/jquery.c3-chart.init_2.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-morris',
        'src'    => Helper::assetUrl('/default/plugins/morris/morris.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-morris',
        'src'    => Helper::assetUrl('/default/plugins/morris/morris.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-raphael',
        'src'    => Helper::assetUrl('/default/plugins/raphael/raphael-min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-morris.init',
        'src'    => Helper::assetUrl('/default/pages/morris.init.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    
}else {
        

if( $tab === 'AddNewRedirectsRule' || $tab === 'AddNewProperty' || $tab === 'FindBackLinksMatrix' || $tab === 'AddNewLinkRule' || $tab === 'Options301Redirection' || $tab === 'BacklinkOptions' || $tab === 'PropertyOptions' || $tab === 'GeneralOptions'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-sweetalert',
        'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-sweetalert.min.js',
        'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}
else if( $tab === 'All404Error' || $tab === 'AllRedirectsRules' || $tab === 'RedirectionGroups' || $tab === 'ManageProperty' || $tab === 'ManageBacklinkGroups' || $tab === 'ManageBacklinkCost' || $tab === 'ManageWebsiteCost' || $tab === 'ManageBacklinks' || $tab === 'ManageLinkRules' || $tab === 'ManageLinkRulesGroups' || $tab === 'ManagePropertyGroups' || $tab === 'VisitorAllClickedLink' || $tab === 'VisitorAllClickedLinkCount'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-sweetalert',
        'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-bootstrap-table.min',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-table/dist/bootstrap-table.min.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-bootstrap-table.min.js',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-table/dist/bootstrap-table.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.bs-table',
        'src'    => Helper::assetUrl('/default/pages/jquery.bs-table.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-sweetalert.min.js',
        'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}

if($tab === 'ManageBacklinks'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-radial',
        'src'    => Helper::assetUrl('/default/plugins/radial/radial.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}
/*************Common Scripts end*************/


if( $tab === 'AddNewProperty' || $tab === 'ManageProperty' || $tab == 'ManagePropertyGroups' || $tab === 'PropertyOptions' ){
//    $enqueue->admin([
//        'as'     => 'all-in-one-seo-tool-add_manage_property',
//        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_property.js'),
//        'filter' => [ 'panel' => 'cs-backlink-manager' ]
//    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_property',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/common.action.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}
else if( $tab === 'AddNewRedirectsRule' || $tab === 'AllRedirectsRules' || $tab === 'All404Error' || $tab === 'RedirectionGroups' || $tab === 'Options301Redirection'){ //301 redirection
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_redirection_rule',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_redirection_rule.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}
else if( $tab === 'FindBackLinksMatrix' || $tab === 'ManageBacklinkGroups' || $tab === 'ManageBacklinks' || $tab === 'BacklinkOptions' || $tab === 'ManageBacklinkCost' || $tab === 'ManageWebsiteCost' ){
//    $enqueue->admin([
//        'as'     => 'all-in-one-seo-tool-add_manage_backlinks',
//        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_backlinks.js'),
//        'filter' => [ 'panel' => 'cs-backlink-manager' ]
//    ], 'footer');
    
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_property',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/common.action.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}

else if( $tab === 'AddNewLinkRule' || $tab === 'ManageLinkRules' || $tab === 'ManageLinkRulesGroups'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_internal_link',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_internal_link.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}

/*************property compare script*************/
if( ($tab === 'ManageProperty' || $tab === 'ManageBacklinks') && ($property_compare === 'true' || $property_compare_by_date === 'true') ){ //last condiiton - is dashboard
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-c3.min',
        'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-d3.min.js',
        'src'    => Helper::assetUrl('/default/plugins/d3/d3.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-c3.min.js',
        'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}



if( ($tab === 'ManageProperty' || $tab === 'ManageBacklinks') && $property_compare === 'true' ){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.c3-chart.init.js',
        'src'    => Helper::assetUrl('/default/pages/jquery.c3-chart.init.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}
else if( ($tab === 'ManageProperty' || $tab === 'ManageBacklinks') && $property_compare_by_date === 'true' ){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-jquery.c3-chart.init.js',
        'src'    => Helper::assetUrl('/default/pages/jquery.c3-chart.init_2.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-daterangepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-moment',
        'src'    => Helper::assetUrl('/default/plugins/moment/moment.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-daterangepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
}

if( $tab === 'ManageBacklinkCost' || $tab === 'ManageWebsiteCost' || $tab === 'VisitorAllClickedLink' || $tab === 'VisitorAllClickedLinkCount'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-daterangepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-bootstrap-datepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-moment',
        'src'    => Helper::assetUrl('/default/plugins/moment/moment.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-daterangepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-bootstrap-datepicker',
        'src'    => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ]);
}
    
/*************property compare script*************/

// new standard admin ajax action

if( $tab === 'VisitorAllClickedLink' || $tab === 'VisitorAllClickedLinkCount'){
    $enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_property',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/new.common.action.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager' ]
    ], 'footer');
}


} // if not dashboard 


