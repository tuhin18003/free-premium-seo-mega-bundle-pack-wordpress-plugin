<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Site Maps
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google\GoogleSettings;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleSiteMapsList {
    
    protected $http;
    private $internet_status;


    public function __construct( $Http ) {
        $this->http = $Http;
    }
    
    public function load_page_builder( $common_text ){
        global $AiosGooAppToken;
        
        $site_maps = $this->get_sitemap_list();
//        pre_print($this->get_sitemap_list());
        
        // check for token
        $data = array_merge( CommonText::form_element( 'sitemaps_manage', 'aios-sitemap-manage' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Sitemaps List', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage remotely your Google sitemaps-entries submitted for this site, or included in the sitemap index file (if sitemapIndex is specified in the request).', SR_TEXTDOMAIN ),
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'Sitemap', SR_TEXTDOMAIN ),
               __( 'Type', SR_TEXTDOMAIN ),
               __( 'Processed', SR_TEXTDOMAIN ),
               __( 'Errors', SR_TEXTDOMAIN ),
               __( 'Warning', SR_TEXTDOMAIN ),
               __( 'Items', SR_TEXTDOMAIN ),
               __( 'Submitted', SR_TEXTDOMAIN ),
               __( 'Indexed', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => $site_maps,
            'type' => __( 'Sitemap Index', SR_TEXTDOMAIN ),
            'web' => __( 'Web pages', SR_TEXTDOMAIN ),
            'images' => __( 'Images', SR_TEXTDOMAIN ),
            'aios_goo_app_token' => $AiosGooAppToken,
            'no_internet' => $this->internet_status
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleSiteMapsList.twig', $data );
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
    
    /**
     * Get Existing Site Maps
     */
    public function get_sitemap_list(){
        global $AiosGooAppToken;
        
        if(empty($AiosGooAppToken['auth_status'])){
            return array( 'error' => $AiosGooAppToken['auth_error_msg']);
        }
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            return $this->internet_status = __( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        $GoogleSettings = new GoogleSettings( $this->http );
        $GoogleSettings->populate_access_token();
        $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';
        
        try{
            $map_count = $webmaster->sitemaps->listSitemaps(CommonText::common_text()['site_url']);
            if($map_count) {
                CsQuery::Cs_Update_Option(array(
                    'option_name' => 'aios_sitemap_count',
                    'option_value' => count($map_count)
                ));
            }
            return $map_count;
        } catch (\Google_Service_Exception $ex) {
            return array( 'error' => $ex->getErrors()[0]['message']);
        }
        
    }
    
}
