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

class PinterestWebmasterVerification {
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
     * Class Constructor
     * 
     * @param type $Http
     */
    public function __construct( $Http ) {
        $this->http = $Http;
        $this->options = CsGlobalOptions::get_options( $this->required_options );
    }
    
    public function load_page_builder( $common_text ){
        global $AiosGooAppToken;
        // check for token
        $data = array_merge( CommonText::form_element( 'save_pinterest_meta_tag', 'pinterest-webmaster-verification' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Pinterest Site Verification', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'This section lets you automatically verifiy ownership of websites or domains with Pinterest Webmaster Tool.', SR_TEXTDOMAIN ),
           'panel_site_subtitle' => __( 'Site Verification', SR_TEXTDOMAIN ),
           'label_meta_tag' => __( 'Website URL*', SR_TEXTDOMAIN ),
           'placeholder_meta_tag' => __( 'Enter Meta Tag Content. Example: eaa44875f64e3f872875052e1fd8411d', SR_TEXTDOMAIN ),
           'des_meta_tag_1' => __( "Enter the supplied \"p:domain_verify\" meta tag content value here. Example: eaa44875f64e3f872875052e1fd8411d", SR_TEXTDOMAIN ),
           'verify_meta_tag' => isset($this->options->webmaster_verify_code->pinterest) ? $this->options->webmaster_verify_code->pinterest : '',
           'notificatoin' => $this->verification_status(),
           'aios_goo_app_token' => $AiosGooAppToken,
           'label_meta_tag' => __( 'Meta Tag Content', SR_TEXTDOMAIN ),
           'des_meta_tag' => __( 'This tag has been automatically added to your website head section for google webmaster verification.', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Save Now', SR_TEXTDOMAIN ),
           'label_verify_btn' => __( 'Verify Now', SR_TEXTDOMAIN ),
           'des_verification' => __( 'Click this button to verify your website.', SR_TEXTDOMAIN ),
            'hints_title'=> __( 'Basic Hints', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               sprintf(__( '%1$s Sign up / Login %2$s here %1$s%3$s%2$s to submit your website.', SR_TEXTDOMAIN ), '<a href="https://www.pinterest.com/toolbox/webmaster" target="_blank">', '</a>' , 'https://www.pinterest.com/toolbox/webmaster' ),
               __( 'After submitting your website, you will see a message - \'Site ownership has not been verified. Verify now\'. Click verify now.', SR_TEXTDOMAIN ),
               __( 'A new webpage will appear and you will see meta tag. Copy the meta tag content and enter that into following area and save it.', SR_TEXTDOMAIN ),
               __( 'Go Back to the Pinterest webmaster tool page and click Verify button bellow of the page. Your website will be verified successfully.', SR_TEXTDOMAIN )
           )
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/SearchEngine/PinterestWebmasterVerification.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                        jQuery( document ).ready( function( $ ) {
                            var _obj = {
                                $: $,
                                form_reset: true
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#site_verification" );
                           
                        } );
            </script>
        <?php
    }
    
    /**
     * Verification status
     */
    private function verification_status(){
        $notificatoin = '';
        if( isset($this->options->webmaster_verify_status->pinterest) && !empty($this->options->webmaster_verify_status->pinterest) && $this->options->webmaster_verify_status->pinterest == 'success'){
            $notice =  __( '%s Successfully Verified! %s your website has been successfully verifed by Pinterest webmaster tool.', SR_TEXTDOMAIN );
            $notificatoin = sprintf( $notice, '<strong>', '</strong>' );
        }
        return $notificatoin;
    }
}
