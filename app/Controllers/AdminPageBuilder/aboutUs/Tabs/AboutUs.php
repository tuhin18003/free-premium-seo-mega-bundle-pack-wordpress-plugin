<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\aboutUs\Tabs;
/**
 * About Us
 * 
 * @package Main
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\aboutUs\Common\CommonText;

class AboutUs {
    
    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
     public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }
    
    /**
     * Generate about us page
     * 
     * @return type
     */
    public function about_us(){
        $current_tab = $this->http->get('tab', false); 
        $current_page = $this->http->get('page', false);
        
        $data = array(
           'CommonText' => CommonText::common_text( array( $current_page, $current_tab)),
           'page_title' =>  __( 'Social Analytics', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Overview of social analytics', SR_TEXTDOMAIN ),
           'panel_site_subtitle' =>  __( 'Thanks for choosing us!', SR_TEXTDOMAIN ),
           'content_title' =>  _( 'Welcome to ', SR_TEXTDOMAIN ) . Helper::get('PLUGIN_NAME') .' '. Helper::get('PLUGIN_VERSION'),
           'content_subtitle' =>  sprintf( __( "Thank you for choosing %s! Whether you're a business owner or a developer building sites for your clients, we're hoping this plugin will help you reach your goals.", SR_TEXTDOMAIN ), Helper::get('PLUGIN_NAME') ),
        );
        return  view( '@CsSeoMegaBundlePack/AboutUs/Dashboard.twig', $data );
    }
    
    
}
