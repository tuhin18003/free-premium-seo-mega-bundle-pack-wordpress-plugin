<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Seo;
/**
 * Google Submission
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleLocalSeo {
    
    protected $http;
    protected $settings;


    public function __construct( $Http ) {
        $this->http = $Http;
        
        //init settings
        $this->init_settings();
    }
    
    public function load_page_builder( $common_text ){
        
        // check for token
        $data = array_merge( CommonText::form_element( 'local_seo', 'aios-google-local-seo' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Local SEO', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'s you add your business to google local map listings.', SR_TEXTDOMAIN ),
           'panel_subtitle' => __( 'Local Map Listing', SR_TEXTDOMAIN ),
           'settings' => $this->settings,
           'inputs' => array(
                'street_address' => array(
                    'label' => __( 'Street Address*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter street address', SR_TEXTDOMAIN ),
                    'help_text' => __( "Street address of your business", SR_TEXTDOMAIN ),
                    'required' => true,
                    'input_type' => 'text',
                    'type' => 'input'
                ),
                'city_name' => array(
                    'label' => __( 'City Name*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter city name', SR_TEXTDOMAIN ),
                    'help_text' => __( "City name of your business", SR_TEXTDOMAIN ),
                    'required' => true,
                    'input_type' => 'text',
                    'type' => 'input'
                ),
                'state_name' => array(
                    'label' => __( 'State Name', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter state name', SR_TEXTDOMAIN ),
                    'help_text' => __( "State name of your business", SR_TEXTDOMAIN ),
                    'input_type' => 'text',
                    'type' => 'input'
                ),
                'zipcode' => array(
                    'label' => __( 'Zipcode*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter zipcode', SR_TEXTDOMAIN ),
                    'help_text' => __( "Zipcode of your location", SR_TEXTDOMAIN ),
                    'required' => true,
                    'input_type' => 'text',
                    'type' => 'input'
                ),
                'country' => array(
                    'label' => __( 'Country*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter your business country', SR_TEXTDOMAIN ),
                    'help_text' =>  __( "Country of your business", SR_TEXTDOMAIN ),
                    'required' => true,
                    'input_type' => 'text',
                    'type' => 'input',
                ),
                'phone_number' => array(
                    'label' => __( 'Enter your phone', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter your business phone number', SR_TEXTDOMAIN ),
                    'help_text' =>  __( "your business phone number", SR_TEXTDOMAIN ),
                    'required' => true,
                    'input_type' => 'text',
                    'type' => 'input',
                ),
                'type' => array(
                    'label' => __( 'Select your business type', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'select your business type', SR_TEXTDOMAIN ),
                    'help_text' =>  __( "Select your business type", SR_TEXTDOMAIN ),
                    'required' => true,
                    'options' => GeneralHelpers::google_place_types(),
                    'multiple' => true,
                    'type' => 'select',
                    'last_input' => true
                ),
            ),
           'label_feed_path' => __( 'Feed Path*', SR_TEXTDOMAIN ),
           'placeholder_feed_path' => __( 'Enter feed path', SR_TEXTDOMAIN ),
           'des_feed_path' => __( "The URL of the sitemap to add. For example: http://www.example.com/sitemap.xml", SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Submit Now', SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleLocalSeo.twig', $data );
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
                            form_general_options.setup( "#google_lcoalseo" );
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Init Settings
     */
    private function init_settings(){
        $this->settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_oogle_localseo', 'json' => true, 'json_array' => true ) ); 
        
//        pre_print($this->settings);
        
        $google_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true, 'json_array' => true ) ); 
        $this->settings['browser_api_key'] = isset( $google_settings['browser_key']) ? $google_settings['browser_key'] : '';
        $place_added = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_placeid', 'json' => true, 'json_array' => true ) ); 
        if(isset( $place_added['status']) && $place_added['status'] == 'OK'){
            $notificatoin =  __( '%s Success! %s Your business address has been added to Google successfully.', SR_TEXTDOMAIN );
            $notificatoin = sprintf($notificatoin, '<strong>', '</strong>' );
            $this->settings['place_status'] = $notificatoin;
        }
        
        $this->settings['auth_error_msg'] = __( 'You need to %1$s setup api key %2$s from %1$s settings %2$s to submit your listings.. ', SR_TEXTDOMAIN );
        $settings_url = CommonText::common_text()['base_url'].'&tab=GoogleSettings';
        $this->settings['auth_error_msg'] = sprintf( $this->settings['auth_error_msg'], "<a href='{$settings_url}'>", '</a>');
    }
}
