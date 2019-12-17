<?php namespace CsSeoMegaBundlePack;
/**
 * Plugins Actions Enqueue
 * 
 * @since 1.0.0
 * @var \Herbert\Framework\Enqueue $enqueue
 * @author CodeSolz <customer-service@codesolz.net>
 */
/***********************************Enqueue CSS***********************************/
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-c3.min',
    'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.css'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageProperty', 'ManageBacklinks', 'RnkChkManageKeywords'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-morris',
    'src'    => Helper::assetUrl('/default/plugins/morris/morris.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-sweetalert',
    'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.css'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
            'subPanel' => [ 'AddNewRedirectsRule', 'AddNewProperty', 'blc-find-backLinks-matrix', 'AddNewLinkRule', 'Options301Redirection', 
            'BacklinkOptions', 'PropertyOptions', 'GeneralOptions', 'All404Error', 'AllRedirectsRules', 'RedirectionGroups', 'ManageProperty', 'ManageBacklinkGroups',
            'ManageBacklinkCost', 'ManageWebsiteCost', 'ManageBacklinks', 'ManageLinkRules', 'ManageLinkRulesGroups', 'ManagePropertyGroups', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount',
            'RnkChkAddNewKeyword', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'KeywordOptions', 'BrokenLinkOptions', 'AllDetectedLink', 'FbSettings',  'FbScheduledPost', 'FbStatistics',
            'GoogleSettings', 'GoogleSiteMapSubmission', 'GoogleWebmasterVerification','GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights',
            'GoogleLocalSeo', 'RobotsBots', 'MetaTagLists', 'MetaWebstiePublisher', 'MetaWebsiteGraph', 'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts',  'KresManageSelectedKeywords', 'BingWebmasterVerification', 'YandexWebmasterVerification', 'PinterestWebmasterVerification' ,
                'MetaContactFields'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap-table.min',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-table/dist/bootstrap-table.min.css'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
        'subPanel' => [
            'All404Error', 'AllRedirectsRules', 'RedirectionGroups', 'ManageProperty', 'ManageBacklinkGroups','ManageBacklinkCost', 'ManageWebsiteCost', 'ManageBacklinks', 'ManageLinkRules',
            'ManageLinkRulesGroups', 'ManagePropertyGroups', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'AllDetectedLink',  'FbScheduledPost', 'FbStatistics',
            'GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights', 'GoogleLocalSeo', 'KresManageSelectedKeywords'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-radial',
    'src'    => Helper::assetUrl('/default/plugins/radial/radial.css'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageBacklinks', 'GooglePageSpeedInsights'
        ]
    ]
]);
$enqueue->admin([
    'as' => 'all-in-one-seo-tool-daterangepicker',
    'src' => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.css'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageBacklinks', 'ManageProperty', 'ManageBacklinkCost', 'ManageWebsiteCost', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords',
            'GoogleStatistics'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap-datepicker',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => [
            'ManageBacklinkCost', 'ManageWebsiteCost', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount'
        ]
    ]
]);
/***********************************Enqueue CSS***********************************/


/***********************************Enqueue JS***********************************/
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
    'as'     => 'all-in-one-seo-tool-d3.min.js',
    'src'    => Helper::assetUrl('/default/plugins/d3/d3.min.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageProperty', 'ManageBacklinks', 'RnkChkManageKeywords','GoogleStatistics'
        ]
    ]
], 'footer');
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-c3.min.js',
    'src'    => Helper::assetUrl('/default/plugins/c3/c3.min.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageProperty', 'ManageBacklinks', 'RnkChkManageKeywords','GoogleStatistics'
        ]
    ]
], 'footer');

/*calling custom Aios Object*/
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.c3-chart.init.Aios.Custom',
    'src'    => Helper::assetUrl('/default/pages/jquery.c3-chart.init.Aios.Custom.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'RnkChkManageKeywords','GoogleStatistics'
        ]
    ]
]);



$addTo = '';
if(isset($_GET['compare_by_date'])){
    $addTo = ['ManageProperty', 'ManageBacklinks'];
}
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.c3-chart.init.js',
    'src'    => Helper::assetUrl('/default/pages/jquery.c3-chart.init_2.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => $addTo
    ]
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
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-sweetalert.min.js',
    'src'    => Helper::assetUrl('/default/plugins/sweetalert/dist/sweetalert.min.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
            'subPanel' => [ 'AddNewRedirectsRule', 'AddNewProperty', 'blc-find-backLinks-matrix', 'AddNewLinkRule', 'Options301Redirection', 
            'BacklinkOptions', 'PropertyOptions', 'GeneralOptions', 'All404Error', 'AllRedirectsRules', 'RedirectionGroups', 'ManageProperty', 'ManageBacklinkGroups',
            'ManageBacklinkCost', 'ManageWebsiteCost', 'ManageBacklinks', 'ManageLinkRules', 'ManageLinkRulesGroups', 'ManagePropertyGroups', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount',
            'RnkChkAddNewKeyword', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'KeywordOptions', 'BrokenLinkOptions', 'AllDetectedLink', 'FbSettings',  'FbScheduledPost', 'FbStatistics',
            'GoogleSettings', 'GoogleSiteMapSubmission', 'GoogleWebmasterVerification','GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights',
            'GoogleLocalSeo', 'RobotsBots', 'MetaTagLists', 'MetaWebstiePublisher', 'MetaWebsiteGraph', 'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts',  'KresManageSelectedKeywords', 'BingWebmasterVerification', 'YandexWebmasterVerification', 'PinterestWebmasterVerification', 
                'MetaContactFields'
        ]
    ]
], 'footer');
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-dashboard',
    'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/dashboard.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager' ]
], 'footer');
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap-table.min.js',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-table/dist/bootstrap-table.min.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
        'subPanel' => [
            'All404Error', 'AllRedirectsRules', 'RedirectionGroups', 'ManageProperty', 'ManageBacklinkGroups','ManageBacklinkCost', 'ManageWebsiteCost', 'ManageBacklinks', 'ManageLinkRules',
            'ManageLinkRulesGroups', 'ManagePropertyGroups', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'AllDetectedLink',  'FbScheduledPost', 'FbStatistics',
            'GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights', 'KresManageSelectedKeywords'
        ]
    ]
], 'footer');
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-jquery.bs-table',
    'src'    => Helper::assetUrl('/default/pages/jquery.bs-table.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
        'subPanel' => [
            'All404Error', 'AllRedirectsRules', 'RedirectionGroups', 'ManageProperty', 'ManageBacklinkGroups','ManageBacklinkCost', 'ManageWebsiteCost', 'ManageBacklinks', 'ManageLinkRules',
            'ManageLinkRulesGroups', 'ManagePropertyGroups', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'AllDetectedLink',  'FbScheduledPost', 'FbStatistics',
            'GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights', 'KresManageSelectedKeywords'
        ]
    ]
], 'footer');
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-moment',
    'src'    => Helper::assetUrl('/default/plugins/moment/moment.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageProperty', 'ManageBacklinks', 'ManageBacklinkCost', 'ManageWebsiteCost', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords',
            'GoogleStatistics'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-daterangepicker',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-daterangepicker/daterangepicker.js'),
    'filter' => [ 'panel' => [ 'cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'ManageProperty', 'ManageBacklinks', 'ManageBacklinkCost', 'ManageWebsiteCost', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount', 'RnkChkManageKeywords',
            'GoogleStatistics'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-bootstrap-datepicker',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => [
            'ManageBacklinkCost', 'ManageWebsiteCost', 'VisitorAllClickedLink', 'VisitorAllClickedLinkCount'
        ]
    ]
]);

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-aios.event.handler',
    'src'    => Helper::assetUrl('/default/app_core/common/js/aios.event.handler.js'),
    'deps'   => [ 'jquery-form' ],
    'filter'   => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
        'subPanel' => [ 
            'AddNewProperty', 'GeneralOptions', 'blc-find-backLinks-matrix', 'RnkChkAddNewKeyword', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'KeywordOptions', 'BrokenLinkOptions', 'AllDetectedLink',
            'FbSettings',  'FbScheduledPost', 'FbStatistics', 'GoogleSettings', 'GoogleSiteMapSubmission', 'GoogleWebmasterVerification','GoogleSiteMapsList', 'GoogleCrawlerError', 'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls',
            'GooglePageSpeedInsights','GoogleLocalSeo', 'RobotsBots', 'MetaTagLists', 'MetaWebstiePublisher', 'MetaWebsiteGraph', 'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts',  'KresManageSelectedKeywords', 'BingWebmasterVerification', 'YandexWebmasterVerification', 'PinterestWebmasterVerification', 'MetaContactFields'
        ]
    ]
]);
$enqueue->admin([
    'as'       => 'all-in-one-seo-tool-aios.event.handler',
    'lc'       => true,
    'name'     => 'AIOS',
    'data'     => [ 
            'error_msg' => __( 'Sorry, unable to do that. Please try refreshing the page.', SR_TEXTDOMAIN ), 
            'swal_processing_title' => __( 'Processing...', SR_TEXTDOMAIN ), 
            'swal_error_title' => __( 'Error!', SR_TEXTDOMAIN ), 
            'swal_processing_text' => __( 'Please wait a while...', SR_TEXTDOMAIN ),
            'swal_confirm_title' => __( 'Are you sure?', SR_TEXTDOMAIN),
            'swal_confirm_text' => __( 'You will not be able to recover the data!', SR_TEXTDOMAIN),
            'swal_confirm_btn_text' => __( 'Yes, delete it!', SR_TEXTDOMAIN),
            'SwalErrorTextNoIdSelection' => __( 'Please select an item and try this action.', SR_TEXTDOMAIN),
            'loading_gif_url' => Helper::assetUrl('default/images/loader/loading.gif')
        ],
    'filter'   => [ 'panel' => ['cs-backlink-manager', 'cs-keyword-manager', 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard'],
        'subPanel' => [ 
            'AddNewProperty', 'GeneralOptions', 'blc-find-backLinks-matrix', 'RnkChkAddNewKeyword', 'RnkChkManageKeywords', 'RnkChkManageKeywordGroups', 'KeywordOptions', 'BrokenLinkOptions', 'AllDetectedLink',
            'FbSettings',  'FbScheduledPost', 'FbStatistics', 'GoogleSettings', 'GoogleSiteMapSubmission', 'GoogleWebmasterVerification','GoogleSiteMapsList', 'GoogleCrawlerError',
            'GoogleIndexMonitor', 'GoogleUrlShortener', 'GoogleRemoveUrls', 'GooglePageSpeedInsights', 'GoogleLocalSeo', 'RobotsBots', 'MetaTagLists', 'MetaWebstiePublisher', 'MetaWebsiteGraph',
            'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts',  'KresManageSelectedKeywords', 'BingWebmasterVerification', 'YandexWebmasterVerification', 'PinterestWebmasterVerification', 'MetaContactFields'
        ]
    ]
]);


/*********only for keyword manager*********/
$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-aios.search.handler',
    'src'    => Helper::assetUrl('/default/app_core/common/js/aios.search.handler.js'),
    'deps'   => [ 'jquery-form' ],
    'filter'   => [ 'panel' => 'cs-keyword-manager',
        'subPanel' => [ 
            'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts'
        ]
    ]
]);
$enqueue->admin([
    'as'       => 'all-in-one-seo-tool-aios.search.handler',
    'lc'       => true,
    'name'     => 'AIOS_APIS',
    'data'     => [ 
            'g' => 'https://suggestqueries.google.com/complete/search',
            'b' => 'https://api.bing.com/osjson.aspx?JsonType=callback&JsonCallback=?',
            'u' => 'https://suggestqueries.google.com/complete/search',
            'y' => 'https://search.yahoo.com/sugg/gossip/gossip-us-ura/',
            'e' => 'https://autosug.ebay.com/autosug',
            'a' => 'https://completion.amazon.com/search/complete',
            'nkf' => __( 'No Keyword Found Yet!', SR_TEXTDOMAIN),
        ],
    'filter'   => [ 'panel' => 'cs-keyword-manager',
        'subPanel' => [ 
            'KresKeywordSuggestions', 'BulkTags',  'TagsPages',  'TagsProducts'
        ]
    ]
]);
/*********only for keyword manager*********/


$enqueue->admin([
    'as'     => 'aios-bootstrap-filestyle.min',
    'src'    => Helper::assetUrl('/default/plugins/bootstrap-filestyle/src/bootstrap-filestyle.min.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics' ],
        'subPanel' => [
            'GoogleRemoveUrls'
        ]
    ]
]);

/***********************************Enqueue JS***********************************/

/***********************************Need To Update***********************************/
$enqueue->admin([
        'as'     => 'all-in-one-seo-tool-add_manage_redirection_rule',
        'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_redirection_rule.js'),
        'filter' => [ 'panel' => 'cs-backlink-manager',
            'subPanel' => [
                'AddNewRedirectsRule', 'AllRedirectsRules', 'All404Error', 'RedirectionGroups', 'Options301Redirection'
            ]
        ]
], 'footer');

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-add_manage_internal_link',
    'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_internal_link.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => [
            'AddNewLinkRule', 'ManageLinkRules', 'ManageLinkRulesGroups'
        ]
    ]
], 'footer');

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-common.action',
    'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/common.action.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => [
            'ManageProperty', 'ManagePropertyGroups', 'PropertyOptions', 'ManageBacklinkGroups', 'ManageBacklinks', 'BacklinkOptions', 'ManageBacklinkCost', 'ManageWebsiteCost' 
        ]
    ]
], 'footer');

$enqueue->admin([
    'as'     => 'all-in-one-seo-tool-new.common.action',
    'src'    => Helper::assetUrl('/default/app_core/backlink_manager/js/new.common.action.js'),
    'filter' => [ 'panel' => 'cs-backlink-manager',
        'subPanel' => [
            'VisitorAllClickedLink', 'VisitorAllClickedLinkCount'
        ]
    ]
], 'footer');
/***********************************Need To Update***********************************/

/*********************************** nascharts ***********************************/
$enqueue->admin([
    'as'     => 'aios-nascharts',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/nascharts.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard', 'cs-keyword-manager' ],
        'subPanel' => [
            'GoogleStatistics', 'GooglePageSpeedInsights', 'RnkChkManageKeywords'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'aios-nascharts.data',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/data.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard', 'cs-keyword-manager' ],
        'subPanel' => [
            'GoogleStatistics', 'RnkChkManageKeywords'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'aios-nascharts.drilldown',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/drilldown.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'GoogleStatistics'
        ]
    ]
]);

$enqueue->admin([
    'as'     => 'aios-nascharts.init',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/nascharts.init.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'GoogleStatistics'
        ]
    ]
]);

//speedometer
$enqueue->admin([
    'as'     => 'aios-nascharts.more',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/nascharts-more.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'GooglePageSpeedInsights'
        ]
    ]
]);

//for line chart start
//$enqueue->admin([
//    'as'     => 'aios-nascharts.exporting',
//    'src'    => Helper::assetUrl('/default/plugins/nascharts/exporting.js'),
//    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard', 'cs-keyword-manager' ],
//        'subPanel' => [
//            'GoogleStatistics', 'GooglePageSpeedInsights', 'RnkChkManageKeywords'
//        ]
//    ]
//]);

$enqueue->admin([
    'as'     => 'aios-nascharts.line.chart.init',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/nascharts.line.chart.init.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard', 'cs-keyword-manager' ],
        'subPanel' => [
            'GoogleStatistics', 'RnkChkManageKeywords'
        ]
    ]
]);
$enqueue->admin([
    'as'     => 'aios-nascharts.speedometer',
    'src'    => Helper::assetUrl('/default/plugins/nascharts/nascharts.speedometer.init.js'),
    'filter' => [ 'panel' => [ 'cs-social-analytics', 'cs-on-page-optimization', 'cs-aiost-dashboard' ],
        'subPanel' => [
            'GooglePageSpeedInsights'
        ]
    ]
]);

/*********************************** nascharts ***********************************/

