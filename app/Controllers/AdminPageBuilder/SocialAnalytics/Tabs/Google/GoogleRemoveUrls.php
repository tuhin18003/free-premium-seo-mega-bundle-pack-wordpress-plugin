<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Remove URLs
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class GoogleRemoveUrls {
    
    protected $http;
    private $results;
    
    public function __construct( $Http ) {
        $this->http = $Http;
        
        //get results
        $this->get_results();
    }
    
    public function load_page_builder( $common_text ){
        global $AiosGooAppToken;
                
        $data = array_merge( CommonText::form_element( 'search_urls_remover', 'aios-search-url-removers' ), array(
           'CommonText' => $common_text,
           'aios_goo_app_token' => $AiosGooAppToken,
           'page_title' =>  __( 'URLs Remover Tool', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you remove urls from Google index and other search engines.', SR_TEXTDOMAIN ),
           'panel_title' => __( 'URLs Remover', SR_TEXTDOMAIN ),
           
            'inputs' => array(
                'single_url' => array(
                    'label' =>  __( 'Enter URL', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter URL you want to remove', SR_TEXTDOMAIN ),
                    'help_text' => sprintf( __( 'Please enter your url you want to remove from search engines. Example: %s', SR_TEXTDOMAIN ), site_url('/hello-wordpress')),
                    'input_type' => 'text',
                    'type' => 'input'
                ),
                'multiple_url' => array(
                    'label' =>  __( 'Or Select File', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Select a text file with bulk url', SR_TEXTDOMAIN ),
                    'help_text' => __( 'Please Select your .txt file to upload bulk url. Please enter one url in each line in your .txt file.', SR_TEXTDOMAIN ),
                    'input_type' => 'file',
                    'type' => 'file'
                )
            ),
           'label_submit_btn' =>  __( 'Submit', SR_TEXTDOMAIN ),
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'label_submitted' =>  __( 'Submitted', SR_TEXTDOMAIN ),
           'label_removed' =>  __( 'Removed', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'URL', SR_TEXTDOMAIN ),
               __( 'Status', SR_TEXTDOMAIN ),
               __( 'Removed On', SR_TEXTDOMAIN ),
               __( 'Submitted On', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => isset($this->results) ? $this->results : '',
           'hints_title'=> __( 'Automatic url remover', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'You can add single / bulk url from bellow.', SR_TEXTDOMAIN ),
               __( 'For bulk url upload, create a .txt file with your urls. Please enter 1 url in each line.', SR_TEXTDOMAIN ),
               __( 'Submitted urls will be removed from search engine gradually.', SR_TEXTDOMAIN ),
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleRemoveUrls.twig', $data );
    }
    
    /**
     * Footer script
     */
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
                            
                            jQuery(":file").filestyle({input: false});
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Get Results
     * 
     * @global type $wpdb
     */
    private function get_results(){
        global $wpdb;
        $this->results = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_remove_urls',
            'order_by' => 'id desc'
        ));
    }
    
}
