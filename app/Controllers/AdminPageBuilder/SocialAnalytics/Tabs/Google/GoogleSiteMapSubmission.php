<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Submission
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;

class GoogleSiteMapSubmission {
    
    protected $http;
    
    public function __construct( $Http ) {
        $this->http = $Http;
    }
    
    public function load_page_builder( $common_text ){
        global $AiosGooAppToken;
        
        // check for token
        $data = array_merge( CommonText::form_element( 'sitemaps_submission', 'aios-sitemap-submission' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Sitemap Submission', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you manually submit your website sitemaps to Google webmaster tool for faster indexing', SR_TEXTDOMAIN ),
           'panel_subtitle' => __( 'Sitemap Submission', SR_TEXTDOMAIN ),
           'label_website_url' => __( 'Website URL*', SR_TEXTDOMAIN ),
           'placeholder_website_url' => __( 'Enter website url', SR_TEXTDOMAIN ),
           'des_website_url' => __( "The site's URL, including protocol. For example: http://www.example.com/", SR_TEXTDOMAIN ),
           'label_feed_path' => __( 'Feed Path*', SR_TEXTDOMAIN ),
           'placeholder_feed_path' => __( 'Enter feed path', SR_TEXTDOMAIN ),
           'des_feed_path' => __( "The URL of the sitemap to add. For example: http://www.example.com/sitemap.xml", SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Submit Now', SR_TEXTDOMAIN ),
           'aios_goo_app_token' => $AiosGooAppToken,
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleSiteMapSubmission.twig', $data );
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
                            form_general_options.setup( "#sitemap_submission" );
                        } );
                } )( jQuery );
            </script>
        <?php
    }
}
