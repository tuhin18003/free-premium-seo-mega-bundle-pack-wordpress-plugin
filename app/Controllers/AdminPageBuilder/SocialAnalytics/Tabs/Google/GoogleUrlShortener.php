<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Site Maps
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google\GoogleSettings;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleUrlShortener {
    
    protected $http;
    private $rows;
    
    public function __construct( $Http ) {
        $this->http = $Http;
        @$this->populate_shortned_analytics();
    }
    
    public function load_page_builder( $common_text ){
        global $AiosGooAppToken;
        $settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );  
        $is_app_has_set = isset($settings->profile_id) ? 'true' : 'false';
        // check for token
        $settings_url = admin_url('?page=cs-social-analytics&tab=GoogleSettings');
        $data = array_merge( CommonText::form_element( 'googleurl_shortner', 'aios-google-url-shortner' ), array(
           'CommonText' => $common_text,
           'aios_goo_app_token' => $AiosGooAppToken,
           'page_title' =>  __( 'Google URL Shortener', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you create, inspect, and manage goo.gl short URLs.', SR_TEXTDOMAIN ),
           'panel_title' => __( 'Google Url Shortener', SR_TEXTDOMAIN ),
           'label_long_url' => __( 'Enter Long URL*', SR_TEXTDOMAIN ),
           'placeholder_long_url' => __( 'Enter long url you want to short', SR_TEXTDOMAIN ),
           'des_long_url' => __( "The URL you want to make shorter. For example: http://www.example.com/long_url", SR_TEXTDOMAIN ),
            
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'label_submit_btn' =>  __( 'Create', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'Short URL', SR_TEXTDOMAIN ),
               __( 'Original URL', SR_TEXTDOMAIN ),
               __( 'Visits', SR_TEXTDOMAIN ),
               __( 'Created On', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => $this->rows->rows,
            'is_app_has_set' => $is_app_has_set,
            'setup_msg' => __( "Before using this section please make sure you have authorize the app from <a href='{$settings_url}'>Settings</a> section.", SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleUrlShortener.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            //delete group
                            var _obj = {
                                $: $,
                                data_section_id: '#demo-custom-toolbar',
                                row_id : "#item_id_",
                                action_type: 'delete',
                                redirect: window.location.href
                            };
                            var action_handler = new Aios_Action_Handler( _obj );
                            action_handler.setup( "#btn_delete" ); 
                            
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#urlshortner_form" );
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Get Existing Site Maps
     */
    private function populate_shortned_analytics(){
        
        $this->get_rows();
        $get_last_row_count = CsQuery::Cs_Get_Option(array('option_name' => 'aios_total_url_shorted'));
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === true ){
            if( !empty($this->rows) && (empty($get_last_row_count) || date('Y-m-d') > $get_last_row_count )){
                $GoogleSettings = new GoogleSettings( $this->http );
                if(empty($GoogleSettings->client)){
                    echo 'Google APPs not set. Please go to setting section and setup the required information.'; 
                }
                $GoogleSettings->populate_access_token();
                $urlShortner = isset($GoogleSettings->client) ?  new \Google_Service_Urlshortener( $GoogleSettings->client ) : '';
                try{
                    if($this->rows->rows){
                        foreach($this->rows->rows as $row){
                            $short = $urlShortner->url->get($row->short_url, array('projection' => 'FULL', 'fields' => 'analytics/allTime'));
                            $click_count =  isset($short['analytics']['allTime']) ? $short['analytics']['allTime']['shortUrlClicks'] : 0; 
                            CsQuery::Cs_Update(array(
                                'table' => 'aios_google_shorteners_urls',
                                'update_data' => array( 'analytics' => $click_count ),
                                'update_condition' => array( 'short_url' => $row->short_url )
                            ));
                        }
                        CsQuery::Cs_Update_Option(array(
                            'option_name' => 'aios_total_url_shorted',
                            'option_value' => date('Y-m-d')
                        ));
                    }
                } catch (\Google_Service_Exception $ex) {
                    return $ex->getErrors()[0]['message'];
                }

                $this->get_rows();
            }
        }
    }
    
    /**
     * Get Rwos
     */
    private function get_rows(){
        global $wpdb;
        $this->rows = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_google_shorteners_urls',
            'order_by' => 'id DESC',
            'num_rows' => true
        ));
    }
    
}
