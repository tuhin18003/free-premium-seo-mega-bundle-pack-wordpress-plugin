<?php namespace CsSeoMegaBundlePack\HelperFunctions;
/**
 * Helpers Functions
 * 
 * @package All in One Seo 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
	exit;
}

class AjaxHelpers {
    
    /**
     * Return Ajax Json Encoded Data
     * 
     * @version 1.0.0
     * @param array $data
     */
    public static function output_ajax_response( array $data ) {
        header( 'Content-Type: application/json' );
        $response_code = isset($data['httpResponseCode']) ? $data['httpResponseCode'] : 200;
        http_response_code($response_code);
        wp_send_json( $data );
        die();
    }
    
    /**
     * Check Ajax Referer
     * 
     * @version 1.0.0
     * @param String $nonce
     */
    public static function check_ajax_referer( $security_code, $post_data = false, $response_type = false ) {
        if ( wp_verify_nonce( $post_data, $security_code, false ) === false ){
            if( empty( $response_type ) ){ // will remove for next version - called from Backlink Manager
                $json_data = array(
                    'error' => true,
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Unable to perform action. "' . $post_data.'" - bad nonce', SR_TEXTDOMAIN )
                );
            }else{
                $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'text' => __( 'Unable to perform action. "' . $post_data.'" - bad nonce', SR_TEXTDOMAIN )
                );
            }
            self::output_ajax_response( $json_data );
        }
    }
    
    
}
