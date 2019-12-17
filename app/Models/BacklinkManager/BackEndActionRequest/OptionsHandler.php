<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest;

/**
 * Visitor Click Tracking
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class OptionsHandler{
    
    /**
     * Track Visitor Link Click
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function general_optons(){
        $_aios_nonce = CsQuery::check_evil_script($_POST['_wpnonce']);
        $_self_ping = CsQuery::check_evil_script($_POST['input_self_pingback']);
        $_click_track = CsQuery::check_evil_script($_POST['input_click_tracking']);
//        check security
        AjaxHelpers::check_ajax_referer( 'aios-general-options', $_aios_nonce );
        
        CsQuery::Cs_Update_Option(array(
            'option_name' => 'aios_general_option',
            'option_value' => json_encode(array(
                'self_ping' => $_self_ping,
                'click_tracking' => $_click_track
            ))
        ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Option has has been updated successfully.', SR_TEXTDOMAIN )
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
