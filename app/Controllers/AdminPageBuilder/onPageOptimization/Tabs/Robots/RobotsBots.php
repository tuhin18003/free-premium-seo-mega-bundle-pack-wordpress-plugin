<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Robots;
/**
 * Settings
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\TitlesMetas\Titles_Metas\TabsFields;

class RobotsBots {
    
    protected $http;
    private $options;


    public function __construct( $Http ) {
        $this->http = $Http;
        
        //init settings
        $this->init_settings();
        
    }
    
    public function load_page_builder( $common_text ){
        
        
        $data = array_merge( CommonText::form_element( 'meta_robots', 'aios-metarobots-settings' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Robots & Bots Settings', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'s you manage follow no-follow url', SR_TEXTDOMAIN ),
           'inputs' => TabsFields::aios_get_fields()['tab1']['inputs'],
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           'des_input' => __( 'Choose meta robot\'s options for name', SR_TEXTDOMAIN ),
           'label_follow' => __( 'Follow (default)', SR_TEXTDOMAIN ),
           'label_nofollow' => __( 'No Follow', SR_TEXTDOMAIN ),
           'label_index' => __( 'Index (default)', SR_TEXTDOMAIN ),
           'label_noindex' => __( 'No Index', SR_TEXTDOMAIN ),
           'label_archive' => __( 'Archive (default)', SR_TEXTDOMAIN ),
           'label_noarchive' => __( 'No Archive', SR_TEXTDOMAIN ),
           'label_noodp' => __( 'noodp', SR_TEXTDOMAIN ),
           'label_noydir' => __( 'noydir (for yahoo)', SR_TEXTDOMAIN ),
           'options' => $this->options,
           'panel_subtitle' => __( 'Basic Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Robots & Bots Settings Options', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               'Select meta robot status for different options'
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/RobotsBots/RobotsBots.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#robots_settings" );
                           
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
            'option_name' => 'aios_meta_robots_status',
            'json' => true,
            'json_array' => true
        ));
    }
}
