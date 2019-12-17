<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics;
/**
 * Social Analytics Dashboard
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class SocialAnalyticsTabs{

    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
    /**
     *
     * @var type Check Google App Token
     */
    public $googleAppAuth = false;
    
    private $stats;


    public function __construct(\Herbert\Framework\Http $http) {
        global $AiosGooAppToken;
        $this->http = $http;
        
        //app auth
        $this->googleAppAuth =&$AiosGooAppToken;
    }
    
    /**
     * Tab Loader
     * 
     * @return String
     */
    public function social_analytics_tabs_landing(){
        $current_tab = $this->http->get('tab', false); 
        $current_page = $this->http->get('page', false);
        
        if( $this->http->has('tab') && 'cs-social-analytics' == $current_page){
            $tabTypes = $this->get_tab_type($current_tab); 
            $newClassPath = "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\SocialAnalytics\\Tabs\\{$tabTypes}\\" . $current_tab;
            if(class_exists($newClassPath)){
                $newObj = new $newClassPath( $this->http );
                return $newObj->load_page_builder( CommonText::common_text( array( $current_page, $current_tab)) );
            }else{
                $data = array(
                   'current_tab' => __( '', '' ),
                   'CommonText' => CommonText::common_text(),
                   'page_title' =>  __( 'Error 404', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Error page redirection', SR_TEXTDOMAIN ),
                    
                   'oops' =>  __( 'Whoops! Page not found!', SR_TEXTDOMAIN ),
                   'not_found_msg' =>  __( 'This page cannot found or is missing.', SR_TEXTDOMAIN ),
                   'dir_msg' =>  __( 'Use the navigation left or the button below to get back and track.', SR_TEXTDOMAIN ),
                    
                   'error_page_msg' =>  __( 'Sorry! we do not find the page you are looking for.', SR_TEXTDOMAIN ),
                   'back_btn_label' =>  __( 'Back to Dashbaoard', SR_TEXTDOMAIN ),
                   'back_btn_href' => admin_url('admin.php?page=cs-social-analytics'),
                );
                return  view( '@CsSeoMegaBundlePack/error/error_404.twig', $data );
            }
            
        }else{ //load social dashboard
            
            //get summary data
            $this->get_summary_stats();
            
            $data = array(
               'CommonText' => CommonText::common_text( array( $current_page, $current_tab)),
               'page_title' =>  __( 'Social Analytics', SR_TEXTDOMAIN ),
               'page_subtitle' =>  __( 'Overview of social analytics', SR_TEXTDOMAIN ),
               'stats' => $this->stats,
                
            );
            return  view( '@CsSeoMegaBundlePack/SocialAnalytics/Dashboard.twig', $data );
        }
    }
    
    /**
     * Get Tab Types
     * 
     * @param type $tab
     * @return boolean|string
     */
    private function get_tab_type( $current_tab ){
        if( empty($current_tab)) return false;
        
        if(strpos( $current_tab, 'Google') !== false){
            $get_token = (array)CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_token', 'json' => true ) ); 
            if(isset($get_token['refresh_token']) && !empty($get_token['refresh_token'])){
                $this->googleAppAuth['auth_status'] = true;
                $this->googleAppAuth['token'] = $get_token;
                $this->googleAppAuth['auth_error_msg'] = '';
            }else{
                $this->googleAppAuth['status'] = false;
                $this->googleAppAuth['auth_error_msg'] = __( 'You need to %1$s authorize the app %2$s from %1$s settings %2$s to access this section. ', SR_TEXTDOMAIN );
                $settings_url = CommonText::common_text()['base_url'].'&tab=GoogleSettings';
                $this->googleAppAuth['auth_error_msg'] = sprintf( $this->googleAppAuth['auth_error_msg'], "<a href='{$settings_url}'>", '</a>');
            }
            return 'Google';
        }
        else if(strpos( $current_tab, 'Fb') !== false){
            return 'Facebook';
        }
        else if(strpos( $current_tab, 'Bing') !== false){
            return 'Bing';
        }
        else if(strpos( $current_tab, 'Yandex') !== false){
            return 'Yandex';
        }
        else if(strpos( $current_tab, 'Pinterest') !== false){
            return 'Pinterest';
        }
        
    }
    
    /**
     * Get summary stats
     */
    private function get_summary_stats(){
        global $wpdb;
        $stats = [];
        $get_result = CsQuery::Cs_Get_Results(array(
            'select' => ' sum(fb_likes), sum(fb_comments), sum(fb_shares)',
            'from' => $wpdb->prefix . 'aios_facebook_statistics',
            'query_type' => 'get_var'
        ));
        
        $this->stats['ribon_facebook']['Likes'] = isset($get_result->fb_likes) ? $get_result->fb_likes : 0;
        $this->stats['ribon_facebook']['Comments'] = isset($get_result->fb_comments) ? $get_result->fb_comments : 0;
        $this->stats['ribon_facebook']['Shares'] = isset($get_result->fb_shares) ? $get_result->fb_shares : 0;
        $this->stats['ribon_facebook']['Post Queue'] = CsQuery::Cs_Count(array(
            'table' => 'aios_social_publish_schedule',
            'where' => 'type = 1'
        ));
        $this->stats['ribon_google']['Crawler Errors'] = CsQuery::Cs_Count(array(
            'table' => 'aios_crawler_errors',
        ));
        $this->stats['ribon_google']['Indexed Found'] = CsQuery::Cs_Count(array(
            'table' => 'aios_se_indexed_pages',
            'where' => 'se_type = 1'
        ));
        
        
        
        $get_speed = CsQuery::Cs_Get_Results(array(
            'select' => 'desktop_score, mobile_score',
            'from' => $wpdb->prefix . 'aios_pagespeed_insights',
            'where' => "url = '" . CommonText::common_text()['site_url'] . "'",
            'query_type' => 'get_var'
        ));
        $this->stats['ribon_facebook']['% Website Speed'] = isset($get_speed->desktop_score) ? $get_speed->desktop_score : 'n/a';
        
        $site_map = CsQuery::Cs_Get_Option(array(
            'option_name' => 'aios_sitemap_count'
        ));
        $this->stats['ribon_facebook']['Total Sitemaps'] = empty( $site_map ) ? 0 : $site_map;
        
        
        if( GeneralHelpers::check_internet_status() === true ){
            $GA = new \CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google\GoogleStatistics( $this->http );
            $get_gogole_op = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );   
            if( isset( $get_gogole_op->profile_id ) && !empty($get_gogole_op->profile_id)){
                $GA->get_report( $get_gogole_op->selected_profile_id );
            }
            add_action('admin_footer', [$GA, '_addFooter_script']);
        }else{
            $this->stats['no_internet'] = __( 'Your internet connection has down!', SR_TEXTDOMAIN );
        }
        
        
    }
    
}
