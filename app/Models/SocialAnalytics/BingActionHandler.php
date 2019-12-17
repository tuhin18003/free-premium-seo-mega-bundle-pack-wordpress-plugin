<?php namespace CsSeoMegaBundlePack\Models\SocialAnalytics;

/**
 * Actions Handler
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class BingActionHandler {

    protected $http;
    
    /**
     * Hold Site url
     *
     * @var type 
     */
    public $site_url;
  

    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
        $this->site_url = site_url('/');
    }
    
    /**
     * Bing Meta Tag Saving
     */
    public function Cs_bingMetaTag(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'bing-webmaster-verification', $_aios_nonce, true );
        $meta_tag = $this->http->get('meta_tag', false); 

        if( !empty($meta_tag)){
            
            $get_webmaster_data = CsQuery::Cs_Get_Option(array(
                'option_name' => 'aios_webmaster_verify_meta',
                'json' => true,
                'json_array' => true,
            ));
            $get_webmaster_data['bing'] = $meta_tag;
            
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_webmaster_verify_meta',
                'option_value' => $get_webmaster_data,
                'json' => true
            ));
            
            //delete previous status
            $get_webmaster_status = CsQuery::Cs_Get_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'json' => true,
                'json_array' => true,
            ));
            $get_webmaster_status['bing'] = '';
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'option_value' => $get_webmaster_status,
                'json' => true
            ));
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Bing Meta tag has been added successfully to head section of your website.', SR_TEXTDOMAIN ),
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
}
