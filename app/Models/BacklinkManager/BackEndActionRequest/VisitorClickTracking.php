<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest;

/**
 * Visitor Click Tracking
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class VisitorClickTracking{
    
    /**
     * Track Visitor Link Click
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function delete_visitor_click_log(){
        global $wpdb;
        $_aios_nonce = CsQuery::check_evil_script($_POST['_aios_nonce']);
//        check security
        AjaxHelpers::check_ajax_referer( 'aios-visitor-clicked-link', $_aios_nonce );
        
        $id_raw = $_POST['_item_id'];
        if( empty( $id_raw ) ){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Enexpected error occurred. Please try again later.', SR_TEXTDOMAIN )
            );
        }else{
            $type = $_POST['_type'];
            if($type == 'visitor_click_count_log'){
                $tbl = 'aios_visitor_link_click_count';
            }else{
                $tbl = 'aios_visitor_click_tracker';
            }
                    
            $ret = CsQuery::Cs_Delete_In(array(
                'table' => $tbl,
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => implode(",", $id_raw)
                )
            ));
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Log has been deleted successfully.', SR_TEXTDOMAIN ),
                'id' => $id_raw
            );
        }
        
        return AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
