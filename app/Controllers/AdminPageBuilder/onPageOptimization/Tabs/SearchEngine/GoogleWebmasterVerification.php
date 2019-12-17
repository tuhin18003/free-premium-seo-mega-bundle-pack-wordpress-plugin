<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\SearchEngine;
/**
 * Google Submission
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalOptions;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class GoogleWebmasterVerification {
    /**
     * @var type Obj
     */
    protected $http;
    
    /**
     * @var type array
     */
    private $required_options = [ 'webmaster_verify_code', 'webmaster_verify_status'];
    
    /**
     * @var type obj
     */
    private $options;

    /**
     * Google app token
     */
    private $googleAppAuth;

    /**
     * Class Constructor
     * 
     * @param type $Http
     */
    public function __construct( $Http ) {
        $this->http = $Http;
        $this->options = CsGlobalOptions::get_options( $this->required_options );
    }
    
    public function load_page_builder( $common_text ){
        // check for token
        $data = array_merge( CommonText::form_element( 'site_submission', 'aios-website-verification', array( 'site_verify_action' => 'verify_website') ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Site Verification', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'This section lets you automatically verifiy ownership of websites or domains with Google Webmaster Tool.', SR_TEXTDOMAIN ),
           'panel_site_subtitle' => __( 'Site Submission', SR_TEXTDOMAIN ),
           'label_website_url' => __( 'Website URL*', SR_TEXTDOMAIN ),
           'placeholder_website_url' => __( 'Enter website url', SR_TEXTDOMAIN ),
           'des_website_url_1' => __( "Your website url going to connect Google Webmaster tool.", SR_TEXTDOMAIN ),
           'verify_meta_tag' => isset($this->options->webmaster_verify_code->google) ? $this->options->webmaster_verify_code->google : '',
           'notificatoin' => $this->verification_status(),
           'aios_goo_app_token' => $this->googleAppAuth,
           'label_meta_tag' => __( 'Meta Tag Content', SR_TEXTDOMAIN ),
           'des_meta_tag' => __( 'Google webmaster verification meta tag has been added automatically to your website head section with the above content.', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Submit Now', SR_TEXTDOMAIN ),
           'label_verify_btn' => __( 'Verify Now', SR_TEXTDOMAIN ),
           'des_verification' => __( 'Click this button to verify your website.', SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/SearchEngine/GoogleWebmasterVerification.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                jQuery( document ).ready( function( $ ) {
                    var _obj = {
                        $: $,
                        form_reset: true,
                        redirect: window.location.href
                    };
                    var form_general_options = new Aios_Form_Handler( _obj );
                    form_general_options.setup( "#site_submission" );

                    var _obj = {
                        $: $,
                        single_btn_action: true,
                        additional_data: {
                            site_url : '<?php echo 'http://codesolz.com'; ?>'
                        },
                        redirect: window.location.href
                    };
                    var action_handler = new Aios_Action_Handler( _obj );
                    action_handler.setup( "#btn_stie_verify" ); 

                } );
            </script>
        <?php
    }
    
    /**
     * Verification status
     */
    private function verification_status(){
        $get_token = (array)CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_token', 'json' => true ) ); 
        if(isset($get_token['refresh_token']) && !empty($get_token['refresh_token'])){
            $this->googleAppAuth['auth_status'] = true;
            $this->googleAppAuth['token'] = $get_token;
            $this->googleAppAuth['auth_error_msg'] = '';
        }else{
            $this->googleAppAuth['status'] = false;
            $this->googleAppAuth['auth_error_msg'] = __( 'You need to %1$s authorize the app %2$s from %1$s settings %2$s to access this section. ', SR_TEXTDOMAIN );
            $settings_url = admin_url('admin.php?page=cs-social-analytics&tab=GoogleSettings');
            $this->googleAppAuth['auth_error_msg'] = sprintf( $this->googleAppAuth['auth_error_msg'], "<a href='{$settings_url}'>", '</a>');
        }
        
        $notificatoin = '';
        if( isset($this->options->webmaster_verify_status->google) && !empty($this->options->webmaster_verify_status->google) && $this->options->webmaster_verify_status->google == 'success'){
            $notice =  __( '%s Successfully Verified! %s your website has been successfully verifed by Google webmaster tool.', SR_TEXTDOMAIN );
            $notificatoin = sprintf( $notice, '<strong>', '</strong>' );
        }
        return $notificatoin;
    }
}
