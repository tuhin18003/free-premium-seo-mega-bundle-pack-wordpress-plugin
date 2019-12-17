<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\Dashboard;
/**
 * Social Analytics Dashboard
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\Dashboard\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class Dashboard{

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
    public function dashboard_landing(){
        $current_tab = $this->http->get('tab', false); 
        $current_page = $this->http->get('page', false);
        
        //get summary data
        $this->get_summary_stats();

        $data = array(
           'CommonText' => CommonText::common_text( array( $current_page, $current_tab)),
           'page_title' =>  __( 'Dashboard', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Overview of website statistics', SR_TEXTDOMAIN ),
           'stats' => $this->stats,

        );
        return  view( '@CsSeoMegaBundlePack/SocialAnalytics/Dashboard.twig', $data );
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
