<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common;
/**
 * Common Text
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class CommonText {
    
    /**
     * Common Text
     * 
     * @since 1.0.0
     * @return array
     */
    public static function common_text( $current_page = array()){
        return array(
            'menu' => array(
               __( 'Dashboard', '' ) => array( 'home', '' ),
               __( 'Facebook', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'facebook',
                   'FbSettings' => array( 'plus', __( 'APP Settings', SR_TEXTDOMAIN ) ),
                   'FbScheduledPost' => array( 'plus', __( 'Auto Publish Queue', SR_TEXTDOMAIN ) ),
                   'FbStatistics' => array( 'plus', __( 'All Published Posts', SR_TEXTDOMAIN ) ),
               ),
               __( 'Google', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'google',
                   'GoogleSettings' => array( 'plus', __( 'Settings', SR_TEXTDOMAIN ) ),
                   'GoogleStatistics' => array( 'plus', __( 'Analytics Overview', SR_TEXTDOMAIN ) ),
//                   'GoogleWebmasterVerification' => array( 'plus', __( 'Site Verification', SR_TEXTDOMAIN ) ),
                   'GoogleSiteMapSubmission' => array( 'plus', __( 'Submit Sitemaps', SR_TEXTDOMAIN ) ),
                   'GoogleSiteMapsList' => array( 'plus', __( 'Manage Sitemaps', SR_TEXTDOMAIN ) ),
                   'GoogleCrawlerError' => array( 'plus', __( 'Crawler Error Monitor', SR_TEXTDOMAIN ) ),
                   'GoogleIndexMonitor' => array( 'plus', __( 'Index Monitor', SR_TEXTDOMAIN ) ),
                   'GoogleRemoveUrls' => array( 'plus', __( 'Remove Indexed URLs', SR_TEXTDOMAIN ) ),
                   'GooglePageSpeedInsights' => array( 'plus', __( 'Pagespeed Insights', SR_TEXTDOMAIN ) ),
//                   'GoogleLocalSeo' => array( 'plus', __( 'Local Seo Listing', SR_TEXTDOMAIN ) ),
                   'GoogleUrlShortener' => array( 'plus', __( 'URL Shortner', SR_TEXTDOMAIN ) ),
               ),
//               __( 'Bing', SR_TEXTDOMAIN ) => array(
//                   'has_sub', 'windows',
//                   'BingWebmasterVerification' => array( 'plus', __( 'Site Verification', SR_TEXTDOMAIN ) ),
//               ),
//               __( 'Yandex', SR_TEXTDOMAIN ) => array(
//                   'has_sub', 'yc',
//                   'YandexWebmasterVerification' => array( 'plus', __( 'Site Verification', SR_TEXTDOMAIN ) ),
//               ),
//               __( 'Pinterest', SR_TEXTDOMAIN ) => array(
//                   'has_sub', 'pinterest',
//                   'PinterestWebmasterVerification' => array( 'plus', __( 'Site Verification', SR_TEXTDOMAIN ) ),
//               ),
            ),
            'current_tab' => isset( $current_page[1] ) ? stripslashes( $current_page[1] ) : '',
            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            'plugin_name' => 'Social Tools',
            'plugin_version' => '1.0.0',
            'base_url' => admin_url("admin.php?page=cs-social-analytics"),
            'site_url' => 'https://codesolz.net'
        );
    }
    
    /**
     * Common Form Elements
     * 
     * @param type $nonce_string
     * @return boolean
     */
    public static function form_element( $action_hook, $nonce_string, $multiple_actions = false ){
        if(empty($action_hook) || empty($nonce_string)) return false;
        
        $mult_actions = array();
        if( $multiple_actions ){
            foreach( $multiple_actions as $key=>$hook ){
//                $mult_actions = array_merge( $mult_actions, array( $key => admin_url( "admin.php?page=cs-social-analytics&action={$hook}" ) ));
                $mult_actions = array_merge( $mult_actions, array( $key => $hook ));
            }
        }
        
        return array_merge($mult_actions, array(
            'nonce_field' => wp_create_nonce( $nonce_string ),
            'form_action' => admin_url( "admin.php?page=cs-social-analytics&action={$action_hook}" ),
            'base_url' => admin_url("admin.php?page=cs-social-analytics"),
            'post_edit_url' => admin_url("post.php?action=edit&post="),
        ));
    }
    
}
