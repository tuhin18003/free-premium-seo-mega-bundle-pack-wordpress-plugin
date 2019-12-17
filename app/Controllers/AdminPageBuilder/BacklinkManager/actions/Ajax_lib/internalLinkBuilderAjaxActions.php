<?php 
/**
 * Add New Property Ajax Action
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;

add_action( 'wp_ajax_CSaddNewInternalLinkRule', 'CSaddNewInternalLinkRule' );
if(!function_exists('CSaddNewInternalLinkRule')){
    /**
     * Add New property
     */
    function CSaddNewInternalLinkRule(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-internal-link', $data_arr['_wpnonce'] );
        
        $link_keywords = $data_arr['link_keywords'];
        $link_url = $data_arr['link_url'];
        $keywords_group_id = isset($data_arr['keywords_group']) ? $data_arr['keywords_group'] : '';
        
        $new_cat_name = isset($data_arr['new_cat_name']) ? trim( wp_kses( stripslashes( $data_arr['new_cat_name'] ), array() ) )  : '';
        
        if( empty($link_keywords) || empty($link_url)){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Please fill up the required fields.', SR_TEXTDOMAIN )
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }else{
            
            if( $keywords_group_id === 'new'){
                $get_cat_id = $wpdb->get_row("select id from {$wpdb->prefix}aios_internal_link_groups where name = '{$new_cat_name}'");
                if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                    $keywords_group_id = $get_cat_id->id;
                }else{
                    $insert_group = array(
                        'name' => $new_cat_name,
                        'status' => 1,
                        'created_on' => date('Y-m-d H:i:s')
                    );
                    $wpdb->insert( $wpdb->prefix.'aios_internal_link_groups', $insert_group );
                    $keywords_group_id =  $wpdb->insert_id;
                }
            }
            
            $insert_item = array(
                'target_keywords' => $link_keywords,
                'group_id' => $keywords_group_id,
                'target_url' => $link_url,
                'status' => 1,
                'created_on' => date('Y-m-d H:i:s')
            );
            $wpdb->insert( $wpdb->prefix.'aios_internal_link_items', $insert_item );
            $post_ID =  $wpdb->insert_id;
            
            if($post_ID > 0){
                $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Rule has been added successfully.', SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
            }else{
                $json_data = array(
                    'error' => true,
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
            }
        }
    }
}

add_action( 'wp_ajax_CSaddNewInternalLinkCat', 'CSaddNewInternalLinkCat' );
if(!function_exists('CSaddNewInternalLinkCat')){
    /**
     * Add New Internal Cat
     */
    function CSaddNewInternalLinkCat(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $user_input );
        
        $new_cat_name = trim($user_input['new_cat_name']);
        if(empty($new_cat_name)){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Please enter group name.', SR_TEXTDOMAIN )
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }else{
            $get_cat_id = $wpdb->get_row("select id from {$wpdb->prefix}aios_internal_link_groups where name = '{$new_cat_name}'");
            if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                $keywords_group_id = $get_cat_id->id;
            }else{
                $insert_group = array(
                    'name' => $new_cat_name,
                    'status' => 1,
                    'created_on' => date('Y-m-d H:i:s')
                );
                $wpdb->insert( $wpdb->prefix.'aios_internal_link_groups', $insert_group );
                $keywords_group_id =  $wpdb->insert_id;
            }
            
            $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Group has been added successfully.', SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
        }
    }
}


add_action( 'wp_ajax_CSdeleteInternalLink', 'CSdeleteInternalLink' );
if(!function_exists('CSdeleteInternalLink')){
    /**
     * Delete Property
     * 
     * @since 1.0.0
     */
    function CSdeleteInternalLink(){
        global $wpdb;
        $type_msg = '';
        $get_ids = $_POST['link_id'];
        $delete_type = $_POST['type'];
        if(is_array($get_ids)){
            foreach($get_ids as $id){
                if( $delete_type === 'link_rules'){
                    $wpdb->delete( $wpdb->prefix.'aios_internal_link_items', array('id' => $id));
                    $type_msg = 'Rule(s)';
                }
                else if( $delete_type === 'link_group'){
                    $wpdb->delete( $wpdb->prefix.'aios_internal_link_groups', array('id' => $id));
                    $wpdb->delete( $wpdb->prefix.'aios_internal_link_items', array('group_id' => $id));
                    $type_msg = 'Group(s) ';
                }
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
                'response_text' => __( ' Error occrred! please try again later.', SR_TEXTDOMAIN ),
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
    }
}

add_action( 'wp_ajax_CSLinkStatusChange', 'CSLinkStatusChange' );
if(!function_exists('CSLinkStatusChange')){
    /**
     * Property Auto update setup
     * 
     * @since 1.0.0
     */
    function CSLinkStatusChange(){
        global $wpdb;
        $get_ids = $_POST['link_id'];
        $get_type = $_POST['type'];
        
        if(is_array($get_ids)){
            foreach($get_ids as $post_ID){
                if($get_type == 'make_inactive'){
                    $wpdb->update($wpdb->prefix.'aios_internal_link_items', array('status' => 2), array('id' => $post_ID));
                    $msg = __( 'Link has been deactivated successfully', SR_TEXTDOMAIN );
                }else if($get_type == 'make_active'){
                    $wpdb->update($wpdb->prefix.'aios_internal_link_items', array('status' => 1), array('id' => $post_ID));
                    $msg = __( 'Link has been activated successfully', SR_TEXTDOMAIN );
                }else if($get_type == 'make_active_group'){
                    $wpdb->update($wpdb->prefix.'aios_internal_link_groups', array('status' => 1), array('id' => $post_ID));
                    $wpdb->update($wpdb->prefix.'aios_internal_link_items', array('status' => 1), array('group_id' => $post_ID));
                    $msg = __( 'Group has been activated successfully', SR_TEXTDOMAIN );
                }else if($get_type == 'make_inactive_group'){
                    $wpdb->update($wpdb->prefix.'aios_internal_link_groups', array('status' => 2), array('id' => $post_ID));
                    $wpdb->update($wpdb->prefix.'aios_internal_link_items', array('status' => 2), array('group_id' => $post_ID));
                    $msg = __( 'Group has been deactivated successfully', SR_TEXTDOMAIN );
                }
            }
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => $msg,
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


