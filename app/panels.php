<?php namespace CsSeoMegaBundlePack;

/** @var \Herbert\Framework\Panel $panel */

$panel->add([
    'type'   => 'panel',
    'as'     => 'aiostMainPanel',
    'title'  => 'Seo Mega Pack',
    'rename' => __( 'My Webstie Statistics', SR_TEXTDOMAIN ),
    'slug'   => 'cs-aiost-dashboard',
    'order'  => '76',
    'icon'   => Helper::assetUrl('default/images/icons/icon-21x20.png'),
    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\Dashboard\Dashboard@dashboard_landing'
]);

//$panel->add([
//    'type'   => 'sub-panel',
//    'parent' => 'aiostMainPanel',
//    'as'     => 'cs-backlink-manager',
//    'title'  => __( 'Backlink Manager', SR_TEXTDOMAIN ),
//    'slug'   => 'cs-backlink-manager',
//    'order'  => '76.2',
//    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\BacklinkManager\BacklinkManager@backlink_manager_landing',
//    'post'   => [
//        'delete_vct' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\VisitorClickTracking@delete_visitor_click_log',
//        'general_options' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\OptionsHandler@general_optons',
//        'property_add' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\MyWebsites@property_add',
//        'backlinks_findmatrix' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\BacklinkChecker@find_backlink_matrix',
//        'bl_general_options' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\BrokenLinkChecker@general_options', //borken link checker
//        'bl_delete_link' => __NAMESPACE__ . '\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\BrokenLinkChecker@delete_link', //borken link checker
//    ]
//]);

$panel->add([
    'type'   => 'sub-panel',
    'parent' => 'aiostMainPanel',
    'as'     => 'cs-keyword-manager',
    'title'  => __( 'Keyword Manager', SR_TEXTDOMAIN ),
    'slug'   => 'cs-keyword-manager',
    'order'  => '76.3',
    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\KeywordManager\KeywordManager@keyword_manager_landing',
    'post'   => [
        'keyword_add' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@add_keyword',
        'keyword_delete' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@delete_keyword',
        'keyword_update' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@update_keyword',
        'set_autoupdate' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@change_auto_update_status',
        'remove_autoupdate' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@remove_autoupdate',
        'add_groups' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@add_new_group',
        'delete_groups' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@delete_group',
        'keyword_options' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@keyword_options',
        'save_keyword_suggestion' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@save_keyword_suggestion',
        'selected_keyword_delete' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@delete_selected_keywords',
        'export_keywords' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@export_keywords',
        'get_items' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@get_all_items_posts',
        'get_old_tags' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@get_old_tags',
        'add_tags' => __NAMESPACE__ . '\Models\KeywordManager\KeywordHandler@add_tags',
    ],
    'get' => [
    ]
]);

//$panel->add([
//    'type'   => 'sub-panel',
//    'parent' => 'aiostMainPanel',
//    'as'     => 'cs-article-manager',
//    'title'  => __( 'Article Manager', SR_TEXTDOMAIN ),
//    'slug'   => 'cs-article-manager',
//    'order'  => '76.6',
//    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\LinkMonitoring',
//    'post'   => [
//    ]
//]);

$panel->add([
    'type'   => 'sub-panel',
    'parent' => 'aiostMainPanel',
    'as'     => 'cs-social-analytics',
    'title'  => __( 'Social Tools', SR_TEXTDOMAIN ),
    'slug'   => 'cs-social-analytics',
    'order'  => '76.4',
    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\SocialAnalytics\SocialAnalyticsTabs@social_analytics_tabs_landing',
    'post'   => [
        'fb_settings' => __NAMESPACE__ . '\Models\SocialAnalytics\FacebookActionHandler@facebook_settings',
        'delete_fb_scheduled' => __NAMESPACE__ . '\Models\SocialAnalytics\FacebookActionHandler@facebook_delete_scheduled_post',
        'delete_fb_posts' => __NAMESPACE__ . '\Models\SocialAnalytics\FacebookActionHandler@facebook_delete_posts',
        'google_settings' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_settings',
        'site_submission' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_site_submission',
        'sitemaps_submission' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_sitemaps_submission',
        'sitemaps_manage' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_sitemaps_manage',
        'crawler_manage' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_crawlers_manage',
        'index_delete' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_index_delete',
        'googleurl_shortner' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_url_shortner',
        'shorturl_delete' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_shortnedurl_delete',
        'google_pagespeed_test' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@googlePageSpeedTest',
        'delete_pagespeedrow' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@deleteGooglePageSpeedTest',
        'local_seo' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_local_seo_listing',
        'search_urls_remover' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@remove_urls',
        'remove_url_delete' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@delete_remove_urls',
        'save_bing_meta_tag' => __NAMESPACE__ . '\Models\SocialAnalytics\BingActionHandler@Cs_bingMetaTag',
        'save_yandex_meta_tag' => __NAMESPACE__ . '\Models\SocialAnalytics\YandexActionHandler@Cs_yandexMetaTag',
        'save_pinterest_meta_tag' => __NAMESPACE__ . '\Models\SocialAnalytics\pinterestActionHandler@Cs_pinterestMetaTag',
    ]
]);

$panel->add([
    'type'   => 'sub-panel',
    'parent' => 'aiostMainPanel',
    'as'     => 'cs-on-page-optimization',
    'title'  => __( 'On Page Optimization', SR_TEXTDOMAIN ),
    'slug'   => 'cs-on-page-optimization',
    'order'  => '76.5',
    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\onPageOptimization\Tabs\TitlesMetas\TitlesMetasTabs@tabs_landing',
    'post'   => [
        'title_metas' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@title_metas',
        'meta_robots' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@meta_robots_settings',
        'meta_tags_list' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@meta_tags_list_settings',
        'social_web_publishers' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@social_web_publishers_settings',
        'social_web_graph' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@social_web_graph_settings',
        'admin_author_contacts' => __NAMESPACE__ . '\Models\OnPageOptimization\OpHandler@admin_auth_contact_settings',
        'verify_website' => __NAMESPACE__ . '\Models\SocialAnalytics\GoogleActionHandler@google_site_verification',
    ]
]);

//$panel->add([
//    'type'   => 'sub-panel',
//    'parent' => 'aiostMainPanel',
//    'as'     => 'cs-user-statistics',
//    'title'  => __( 'Survey System', SR_TEXTDOMAIN ),
//    'slug'   => 'cs-user-statistics',
//    'order'  => '76.7',
//    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\SurveySystem',
//    'post'   => [
//    ]
//]);

$panel->add([
    'type'   => 'sub-panel',
    'parent' => 'aiostMainPanel',
    'as'     => 'cs-dashboard',
    'title'  => __( 'About Us', 'sdfsdf' ),
    'slug'   => 'about-us',
    'order'  => '76.8',
    'uses'   => __NAMESPACE__ . '\Controllers\AdminPageBuilder\aboutUs\Tabs\AboutUs@about_us'
]);