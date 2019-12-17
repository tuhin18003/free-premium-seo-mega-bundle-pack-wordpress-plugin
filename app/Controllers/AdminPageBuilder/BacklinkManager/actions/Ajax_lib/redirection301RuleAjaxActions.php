<?php 
/**
 * Add New Redirection Ajax Action
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\Helpers\Redirection_Helper as Redirection_Helper;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Flusher as Aios_Flusher;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Group as Aios_Group;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Redirect as Aios_Redirect;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Monitor as Aios_Monitor;
use CsSeoMegaBundlePack\Models\BacklinkManager\getRedirectionData as getRedirectionData;

add_action( 'wp_ajax_CSaddNewRedirectionRule', 'CSaddNewRedirectionRule' );
if(!function_exists('CSaddNewRedirectionRule')){
    
    /**
     * Add New Redirection Rule
     */
    function CSaddNewRedirectionRule(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-redirection-redirect_add', $data_arr['_wpnonce'] );
        
        if( isset( $data_arr['rule_id']) && !empty( $data_arr['rule_id'] )){
            /*********** update redirects rules***********/
            $redirect = Aios_Redirect::get_by_id( $data_arr['rule_id'] );
		if ( $redirect ) {
                    $ret = $redirect->update_redirect( $data_arr );
                    if($ret){
                        $json_data = array(
                            'error' => false,
                            'title' => __( 'Success!', SR_TEXTDOMAIN ),
                            'response_text' => __( 'Your redirection has been updated successfully.', SR_TEXTDOMAIN )
                        );
                        AjaxHelpers::output_ajax_response( $json_data );
                    }
		}
		$json_data = array(
                    'error' => true,
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Please refresh the page and try again.', SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
            
        }else{
            /*********** add redirects rules***********/
            $json_data = Aios_Redirect::add_new_redirects($data_arr);
            AjaxHelpers::output_ajax_response( $json_data );
        }
    }
    
}

add_action( 'wp_ajax_CSaddNewRedirectionGroup', 'CSaddNewRedirectionGroup' );
if(!function_exists('CSaddNewRedirectionGroup')){
    /**
     * Add New Internal Cat
     */
    function CSaddNewRedirectionGroup(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-redirect_add_new_group', $data_arr['_wpnonce'] );
        $new_cat_name = trim($data_arr['new_cat_name']);
        
        if(empty($new_cat_name)){
           $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Please refresh the page and try again.', SR_TEXTDOMAIN )
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }else{
            $get_cat_id = $wpdb->get_row("select id from {$wpdb->prefix}aios_redirection_groups where name = '{$new_cat_name}'");
            if( isset($get_cat_id->id) && !empty($get_cat_id->id) ){
                $succ_msg = __( 'Group already exists.', SR_TEXTDOMAIN );
            }else{
                Aios_Group::create($new_cat_name, 1);
                $succ_msg = __( 'Group has been created successfully.', SR_TEXTDOMAIN );
            }
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => $succ_msg
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
       
        $json_data = array(
            'error' => true,
            'title' => __( 'Error!', SR_TEXTDOMAIN ),
            'response_text' => __( 'Something went wrong. Please try again.', SR_TEXTDOMAIN )
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
}


add_action( 'wp_ajax_CSdeleteRedirectionRule', 'CSdeleteRedirectionRule' );
if(!function_exists('CSdeleteRedirectionRule')){
    /**
     * Delete Property
     * 
     * @since 1.0.0
     */
    function CSdeleteRedirectionRule(){
        global $wpdb;
        $type_msg = '';
        $get_ids = $_POST['item_id'];
        $delete_type = $_POST['type'];
        if(is_array($get_ids)){
            if( $delete_type === 'errors_404'){
                $string_id = implode( ",", $get_ids);
                (new getRedirectionData())->delete404Errors($string_id);
                $type_msg = '404 error log(s)';
            }
            else if( $delete_type === 'redirects_rules'){
                foreach($get_ids as $id){
                    $Aios_Redirect = Aios_Redirect::get_by_id( $id );
                    $Aios_Redirect->delete_redirect(); 
                    $type_msg = 'Redirection rule(s)';
                }
            }
            else if( $delete_type === 'redirects_group'){
                if($get_ids){
                    foreach($get_ids as $group_id ){
                        $aios_grup = Aios_Group::get( $group_id );
                        $aios_grup->delete();
                    }
                }
                $type_msg = 'Redirection groups(s)';
            }
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( $type_msg . ' has been deleted successfully.', SR_TEXTDOMAIN ),
                'id' => $get_ids
            );
            AjaxHelpers::output_ajax_response( $json_data );
           
        }else{
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Error occrred! please try again later.', SR_TEXTDOMAIN ),
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
    }
}

add_action( 'wp_ajax_CSRedirectionBulkActBtn', 'CSRedirectionBulkActBtn' );
if(!function_exists('CSRedirectionBulkActBtn')){
    /**
     * Bulk actions
     * 
     * @since 1.0.0
     */
    function CSRedirectionBulkActBtn(){
        global $wpdb;
        $get_ids = $_POST['item_id'];
        $get_type = $_POST['type'];
        
        if(is_array($get_ids)){
            if($get_type == 'make_inactive'){
                Aios_Group::disable($get_ids);
                $mgs1 = __( 'disabled', SR_TEXTDOMAIN );
            } else if($get_type == 'make_active'){
                Aios_Group::enable($get_ids);
                $mgs1 = __( 'enabled', SR_TEXTDOMAIN );
            } 
            
            $msg = __( "Redirect rule has been {$mgs1} successfully.", SR_TEXTDOMAIN );
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => $msg
            );
            AjaxHelpers::output_ajax_response( $json_data );
            
        }else{
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Error occrred! please try again later.', SR_TEXTDOMAIN ),
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
    }
    
}


add_action( 'wp_ajax_CSaddRedirectionOptoins', 'CSaddRedirectionOptoins' );
if(!function_exists('CSaddRedirectionOptoins')){
    
    /**
     * Add New Redirection Rule
     */
    function CSaddRedirectionOptoins(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-301_options', $data_arr['_wpnonce'] );
        
        $options = Redirection_Helper::aios_get_options();
        
        $options['monitor_post']    = stripslashes( $data_arr['monitor_post'] );
        $options['expire_redirect'] = min( intval( $data_arr['expire_redirect'] ), 60 );
        $options['expire_404']      = min( intval( $data_arr['expire_404'] ), 60 );

        update_option( 'aios_redirection_options', $options );
        Aios_Flusher::schedule();
        
        $json_data = array(
            'error' => false,
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'response_text' => __( '301 options has been updated successfully.', SR_TEXTDOMAIN ),
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
}


add_action( 'init', 'Aios_monitor_post_changes');
if( !function_exists('Aios_monitor_post_changes')){
    
    /**
     * Action hook for automatic post changes monitor
     */
    function Aios_monitor_post_changes(){
        return new Aios_Monitor(Redirection_Helper::aios_get_options());
    }
}