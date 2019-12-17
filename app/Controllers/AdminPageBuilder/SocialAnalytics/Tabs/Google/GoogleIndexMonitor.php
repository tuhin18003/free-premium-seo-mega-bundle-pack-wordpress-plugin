<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Site Indexing monitor
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class GoogleIndexMonitor {
    
    protected $http;
    
    public function __construct( $Http ) {
        $this->http = $Http;
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb, $AiosGooAppToken;
        
        $rows = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_se_indexed_pages',
            'order_by' => 'id DESC',
        ));
        // check for token
        $settings_url = admin_url('?page=cs-social-analytics&tab=GoogleSettings');
        $data = array_merge( CommonText::form_element( 'manage_index', 'aios-manage-index-pages' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Indexing Overview', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you monitor your website pages indexed by Google.', SR_TEXTDOMAIN ),
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'URL', SR_TEXTDOMAIN ),
               __( 'Headline', SR_TEXTDOMAIN ),
               __( 'Visits', SR_TEXTDOMAIN ),
               __( 'Tracking Started', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => $rows,
            'type' => __( 'Sitemap Index', SR_TEXTDOMAIN ),
            'web' => __( 'Web pages', SR_TEXTDOMAIN ),
            'images' => __( 'Images', SR_TEXTDOMAIN ),
            'setup_msg' => __( "If you don't see any result please make sure you have setup indexing option from <a href='{$settings_url}'>Google Custom Search Settings</a> section.", SR_TEXTDOMAIN ),
            'aios_goo_app_token' => $AiosGooAppToken,
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleIndexMonitor.twig', $data );
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
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
}
