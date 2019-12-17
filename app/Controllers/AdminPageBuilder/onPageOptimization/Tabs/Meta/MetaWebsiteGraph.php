<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta;
/**
 * Settings
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagsTabs;

class MetaWebsiteGraph {
    
    protected $http;
    private $options;


    public function __construct( $Http ) {
        $this->http = $Http;
        
        //init settings
        $this->init_settings();
        
    }
    
    public function load_page_builder( $common_text ){
        $TA = new MetaTagsTabs();
        $data = array_merge( CommonText::form_element( 'social_web_graph', 'aios-social-websites-graph' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'All Social Websties & Open Graph', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'s you control the meta tag options for all social website\'s or open graph', SR_TEXTDOMAIN ),
           'tabs' => $TA->tabs_website_graph(),
           'tab_assets' => $TA->tabs_website_graph_assets(),
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           'des_input' => __( 'Select Meta Robots for name', SR_TEXTDOMAIN ),
           'label_follow' => __( 'Follow', SR_TEXTDOMAIN ),
           'label_nofollow' => __( 'No Follow', SR_TEXTDOMAIN ),
           'settings' => $this->options,
           'panel_subtitle' => __( 'Basic Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'All Social Websites & Publishers', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'Following options will be set in your website meta tags for all social website\'s.' , SR_TEXTDOMAIN ),
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/Meta/MetaWebsiteGraph.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true
//                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#websites_graph_settings" );
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Init settings
     */
    private function init_settings(){
        $this->options = CsQuery::Cs_Get_Option(array(
            'option_name' => 'aios_web_graph_options',
            'json' => true,
            'json_array' => true
        ));
    }
}
