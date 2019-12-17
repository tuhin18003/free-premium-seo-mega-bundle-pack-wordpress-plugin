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

class MetaContactFields {
    
    protected $http;
    private $options;


    public function __construct( $Http ) {
        $this->http = $Http;
        
        //init settings
        $this->init_settings();
        
    }

    /**
     * 
     * @param type $common_text
     * @return typePage loader
     */
    public function load_page_builder( $common_text ){
        $data = array_merge( CommonText::form_element( 'admin_author_contacts', 'aios-admin-auth-contacts-settings' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Admin Author Custom Contact', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'s you manage author custom contacts url', SR_TEXTDOMAIN ),
           'inputs' => (new MetaTagsTabs())->get_author_contact_fields(),
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           'des_input' => __( 'Select Meta Robots for name', SR_TEXTDOMAIN ),
           'settings' => $this->options,
           'panel_subtitle' => __( 'Basic Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Author Cutoms Contact Options', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'You can manage "Admin" author custom contact fields from here.', SR_TEXTDOMAIN ),
               __( 'If you don\'t have any matching social url just keep it empty.', SR_TEXTDOMAIN ),
               __( 'Other user can manage their contacts from user profile page', SR_TEXTDOMAIN ),
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/Meta/MetaContactFields.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: false,
//                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#settings_author_contacts" );
                           
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
            'option_name' => 'aios_auth_contact_options',
            'json' => true,
            'json_array' => true
        ));
    }
}
