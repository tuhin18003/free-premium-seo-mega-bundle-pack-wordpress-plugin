<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google page speed insights
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class GooglePageSpeedInsights {
    
    protected $http;
    private $current_url;
    private $detail_data;
    
    public function __construct( $Http ) {
        $this->http = $Http;
        if($this->http->has('tab')){
            $this->current_url = CommonText::common_text()['base_url'].'&tab='.$this->http->get('tab',false);
        }
        if($this->http->has('id')){
            $this->detail_data = $this->get_details( $this->http->get('id', false) );
        }
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb, $AiosGooAppToken;
        $settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );  
        $is_app_has_set = isset($settings->profile_id) ? 'true' : 'false';
        
        $items = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_pagespeed_insights',
            'oder_by' => 'id desc'
        ));
        // check for token
        $settings_url = admin_url('?page=cs-social-analytics&tab=GoogleSettings');
        $data = array_merge( CommonText::form_element( 'google_pagespeed_test', 'aios-google-pagespeed' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Pagespeed Insights', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Pagespeed Insights lets you analyzes the performance of a web page and provides tailored suggestions to make that page faster.', SR_TEXTDOMAIN ),
           'panel_title' => __( 'Google Pagespeed Insights', SR_TEXTDOMAIN ),
           'label_test_url' => __( 'Enter URL*', SR_TEXTDOMAIN ),
           'placeholder_test_url' => __( 'Enter website url you want to analyzie', SR_TEXTDOMAIN ),
           'des_test_url' => __( "The URL you want to make analyze. For example: http://www.example.com/test_url", SR_TEXTDOMAIN ),
            
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'label_submit_btn' =>  __( 'Analyze', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'URL', SR_TEXTDOMAIN ),
               __( 'Mobile Speed', SR_TEXTDOMAIN ),
               __( 'Desktop Speed', SR_TEXTDOMAIN ),
               __( 'Last Checked', SR_TEXTDOMAIN ),
               __( 'Actions', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => $items,
            'is_app_has_set' => $is_app_has_set,
            'setup_msg' => __( "Before using this section please make sure you have authorize the app from <a href='{$settings_url}'>Settings</a> section.", SR_TEXTDOMAIN ),
           'retest_view_detail' =>  __( 'Test again / View Reports', SR_TEXTDOMAIN ),
           'desktop' =>  __( 'Desktop', SR_TEXTDOMAIN ),
           'mobile' =>  __( 'Mobile', SR_TEXTDOMAIN ),
           'ps_panel_title' =>  __( 'Page Speed', SR_TEXTDOMAIN ),
           'label_re_url' =>  __( 'URL', SR_TEXTDOMAIN ),
           'last_checked' =>  __( 'Last Checked : ', SR_TEXTDOMAIN ),
           'label_re_submit_btn' =>  __( 'Analyze Again', SR_TEXTDOMAIN ),
           'panel_po' =>  __( 'Possible Optimizations', SR_TEXTDOMAIN ),
           'panel_of' =>  __( 'Optimizations Found', SR_TEXTDOMAIN ),
            'detail_data' => $this->detail_data,
            'current_url' => $this->current_url,
            'aios_goo_app_token' => $AiosGooAppToken
       ));
            
       if( isset($this->detail_data) && !empty($this->detail_data)){
           add_action('admin_footer', [$this, '_addFooter_script_detail']);
           return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GooglePageSpeedInsightsDetail.twig', $data );
       }else{
           add_action('admin_footer', [$this, '_addFooter_script']);
           return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GooglePageSpeedInsights.twig', $data );
       }     
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
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
                    redirect: '<?php echo $this->current_url;  ?>'
                };
                var form_general_options = new Aios_Form_Handler( _obj );
                form_general_options.setup( "#form_pagepseedtest" );
                
                jQuery( document ).ready( function( $ ) {
                    $('[data-toggle="tooltip"]').tooltip({html: true});
                } );
            </script>
        <?php
    }
    
    public function _addFooter_script_detail(){
        
        ?>
            <script type="text/javascript">
                var _obj = {
                    $: $,
                    form_reset: true,
                    redirect: '<?php echo $this->current_url;  ?>'
                };
                var form_general_options = new Aios_Form_Handler( _obj );
                form_general_options.setup( "#form_pagepseedtest" );
                
                <?php if( isset($this->detail_data) && !empty($this->detail_data)){ ?>
                    var _obj = {
                        graph_load_in: 'graph_desktop_score',
                        title_text: '<?php _e( 'Desktop Speed Score ', SR_TEXTDOMAIN ); ?>',
                        sub_title_text: '<?php _e( 'Speed', SR_TEXTDOMAIN )?>',
                        data: <?php echo $this->detail_data['desktop_score'] ?>
                    };
                    AiosHighChartsSpeed = new Aios_HighCharts_speedometer( _obj );
                    AiosHighChartsSpeed.init();

                    var _obj = {
                        graph_load_in: 'graph_mobile_score',
                        title_text: '<?php _e( 'Mobile Speed Score ', SR_TEXTDOMAIN ); ?>',
                        sub_title_text: '<?php _e( 'Speed', SR_TEXTDOMAIN )?>',
                        data: <?php echo $this->detail_data['mobile_score'] ?>
                    };
                    AiosHighChartsSpeed = new Aios_HighCharts_speedometer( _obj );
                    AiosHighChartsSpeed.init();
                <?php } ?>
            </script>
        <?php
    }
    
    /**
     * Get Item Detail
     * 
     * @global type $wpdb
     * @param type $id
     * @return boolean
     */
    private function get_details( $id ){
        global $wpdb;
        if(empty($id)) return false;
        $data = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_pagespeed_insights',
            'where' => " id = {$id}",
            'query_type' => 'get_row'
        ));
        return array(
            'url' => $data->url,
            'desktop_score' => empty($data->desktop_score) ? 0 : $data->desktop_score,
            'mobile_score' => empty($data->mobile_score) ? 0 : $data->mobile_score,
            'checked_on' => $data->checked_on,
            'desktop_optimized' => json_decode($data->desktop_optimized, true),
            'desktop_need_optimization' => json_decode($data->desktop_need_optimize, true),
            'mobile_optimized' => json_decode($data->mobile_optimized, true),
            'mobile_need_optimization' => json_decode($data->mobile_need_optimize, true)
        );
        
    }

}
