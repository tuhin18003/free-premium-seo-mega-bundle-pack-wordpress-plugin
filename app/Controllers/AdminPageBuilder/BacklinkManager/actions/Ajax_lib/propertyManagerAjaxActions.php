<?php 
/**
 * Add New Property Ajax Action
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Models\CommonQuery\CsFlusher;

//add_action( 'wp_ajax_CSaddNewProperty', 'CSaddNewProperty' );
if(!function_exists('CSaddNewProperty')){
    /**
     * Add New property
     */
    function CSaddNewProperty(){
        global $wpdb;
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-property', $data_arr['_wpnonce'] );
        
        $property_url = $data_arr['input_new_property']; 
        if( empty($property_url) ){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Please fill up the required fields.', SR_TEXTDOMAIN )
            );
        }else{
            try {
                $get_row = CsQuery::Cs_Get_Results(array(
                        'select' => 'id',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "item_domain = '{$property_url}' and item_type = 1",
                        'query_type' => 'get_row'
                    ));
                if( !isset( $get_row->id ) && empty( $get_row->id )){
                    
                    //create or get group
                    $group_id = isset($data_arr['group']) ? $data_arr['group'] : '';
                    $new_group_name = isset($data_arr['new_group_name']) ? $data_arr['new_group_name'] : '';
                    if( $group_id === 'new'){
                        
                        $get_cat_id = CsQuery::Cs_Get_Results(array(
                            'select' => 'id',
                            'from' => $wpdb->prefix.'aios_blmanager_item_groups',
                            'where' => "group_name = '{$new_group_name}' and group_type = 1",
                            'query_type' => 'get_row'
                        ));
                        
                        if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                            $group_id = $get_cat_id->id;
                        }else{
                            $insert_group = array(
                                'table' => 'aios_blmanager_item_groups',
                                'insert_data' => array(
                                    'group_name' => $new_group_name,
                                    'group_type' => 1,
                                    'group_created_on' => date('Y-m-d H:i:s')
                                )
                            );
                            $group_id = CsQuery::Cs_Insert( $insert_group );
                        }
                    }
                    
                    $get_properties_matrix = cs_properties_matrix( $property_url );
                    //add property
                    $alexa = empty($get_properties_matrix['alexa']) ? '' : $get_properties_matrix['alexa'];
                    $moz = empty($get_properties_matrix['moz']) ? '' : $get_properties_matrix['moz'];
                    $auto_update = empty($data_arr['auto_update_status']) ? 2 : 1;
                    $propertyData =array( 
                        'table' => 'aios_blmanager_items',
                        'insert_data' => array(
                            'item_domain' => $property_url,
                            'item_domain_status' => $get_properties_matrix['domain_status'],
                            'item_group_id' => $group_id,
                            'item_type' => 1,
                            'item_auto_update' => $auto_update,
                            'item_created_on' => date('Y-m-d')
                        )
                    );
                    $property_id = CsQuery::Cs_Insert( $propertyData );
                    $propertyAssetData = array(
                        'table' => 'aios_blmanager_item_assets',
                        'insert_data' => array(
                            'item_id' => $property_id,
                            'url_status' => $get_properties_matrix['domain_status'],
                            'alexa_data' => $alexa,
                            'moz_data' => $moz,
                            'created_on' => date('Y-m-d'),
                        )
                    );
                    CsQuery::Cs_Insert( $propertyAssetData );
                    
                    $json_data = array(
                        'error' => false,
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'Property has been added successfully.', SR_TEXTDOMAIN )
                    );
                }else{
                    $json_data = array(
                        'error' => true,
                        'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'This Property has been already added.', SR_TEXTDOMAIN )
                    );
                }
        } catch (\Exception $e) {
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
            );
        }
            
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
        
    }
}

add_action( 'wp_ajax_CSAddNewPropertyGroup', 'CSAddNewPropertyGroup' );
if(!function_exists('CSAddNewPropertyGroup')){
    /**
     * Add Property Groups
     * 
     * @since 1.0.0
     */
    function CSAddNewPropertyGroup(){
        global $wpdb;
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-property-groups', $data_arr['_wpnonce'] );
        
        //create or get group
        $group_name = isset($data_arr['new_cat_name']) ? $data_arr['new_cat_name'] : '';
        $new_group_des = isset($data_arr['new_cat_des']) ? $data_arr['new_cat_des'] : '';
        $get_cat_id = CsQuery::Cs_Get_Results(array(
            'select' => 'id',
            'from' => $wpdb->prefix.'aios_blmanager_item_groups',
            'where' => "group_name = '{$group_name}' and group_type = 1",
            'query_type' => 'get_row'
        ));
        if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
            $group_id = $get_cat_id->id;
        }else{
             $insert_group = array(
                'table' => 'aios_blmanager_item_groups',
                'insert_data' => array(
                    'group_name' => $group_name,
                    'group_type' => 1,
                    'group_description' => $new_group_des,
                    'group_created_on' => date('Y-m-d H:i:s')
                )
            );
            $group_id = CsQuery::Cs_Insert( $insert_group );
            
            if( $group_id > 0 ){
                $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Group has been created successfully.', SR_TEXTDOMAIN )
                );
            }else{
                $json_data = array(
                    'error' => true,
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Something went wrong. Please try again.', SR_TEXTDOMAIN )
                );
            }
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

add_action( 'wp_ajax_CSdeleteProperty', 'CSdeleteProperty' );
if(!function_exists('CSdeleteProperty')){
    /**
     * Delete Property
     * 
     * @since 1.0.0
     */
    function CSdeleteProperty(){
        global $wpdb;
        
        $tbl_name = $wpdb->prefix .'aios_blmanager_items';
        $get_ids = $_POST['item_id'];
        $get_type = $_POST['type'];
        if(is_array($get_ids)){
            $id_string = implode(',', $get_ids);
            $msg_type = '';
            if( $get_type === 'properties'){
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_items',
                    'where' => array(
                        'field_name' => 'id',
                        'field_value' => $id_string
                    )
                ));
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_items',
                    'where' => array(
                        'field_name' => 'item_parent',
                        'field_value' => $id_string
                    )
                ));
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_item_assets',
                    'where' => array(
                        'field_name' => 'item_id',
                        'field_value' => $id_string
                    )
                ));
                
                $msg_type = 'Website(s) ';
            }
            else if( $get_type === 'property_groups'){
                //delete all groups
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_item_groups',
                    'where' => array(
                        'field_name' => 'id',
                        'field_value' => $id_string
                    )
                ));
                $get_pid = CsQuery::Cs_Get_Results(array(
                    'select' => "GROUP_CONCAT(id) as id",
                    'from' => $wpdb->prefix . 'aios_blmanager_items',
                    'where' => "item_group_id in ({$id_string})",
                    'query_type' => 'get_row'
                ));
                    
                if( isset($get_pid->id) && !empty($get_pid->id)){
                    $get_bid = CsQuery::Cs_Get_Results(array(
                        'select' => "GROUP_CONCAT(id) as id",
                        'from' => $wpdb->prefix . 'aios_blmanager_items',
                        'where' => "item_parent in ({$get_pid->id})",
                        'query_type' => 'get_row'
                    ));
                    //delete property assets
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_item_assets',
                        'where' => array(
                            'field_name' => 'item_id',
                            'field_value' => $get_pid->id
                        )
                    ));
                    // delete all backlinks
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_items',
                        'where' => array(
                            'field_name' => 'item_parent',
                            'field_value' => $get_pid->id
                        )
                    ));
                    // delete backlinks assets
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_item_assets',
                        'where' => array(
                            'field_name' => 'item_id',
                            'field_value' => $get_bid->id
                        )
                    ));
                }    
                    
                //delete all properties
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_items',
                    'where' => array(
                        'field_name' => 'item_group_id',
                        'field_value' => $id_string
                    )
                ));
                $msg_type = 'Group(s)';
            }
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( "$msg_type has been deleted successfully.", SR_TEXTDOMAIN ),
                'id' => $get_ids
            );
        }else{
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Error occrred! please try again later.', SR_TEXTDOMAIN ),
                'id' => $get_ids
            );
            
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSsetAutoUpdate', 'CSsetAutoUpdate' );
if(!function_exists('CSsetAutoUpdate')){
    /**
     * Property Auto update setup
     * 
     * @since 1.0.0
     */
    function CSsetAutoUpdate(){
        global $wpdb;
        $get_ids = $_POST['item_id'];
        $get_type = $_POST['type'];
        
        if(is_array($get_ids)){
            foreach($get_ids as $post_ID){
                if($get_type == 'set_auto_update'){
                    $update = array( 'item_auto_update' => 1 );
                }else if( $get_type == 'remove_auto_update' ){
                    $update = array( 'item_auto_update' => 2 );
                }
                
                CsQuery::Cs_Update(array(
                    'table' => 'aios_blmanager_items',
                    'update_data' => $update,
                    'update_condition' => array( 'id' => $post_ID)
                ));
            }
            
            if($get_type == 'set_auto_update' || $get_type == 'set_auto_update_backlink'){
                $msg = __( 'Automatic update has been set successfully.', SR_TEXTDOMAIN );
            }else{
                $msg = __( 'Automatic update has disabled for the selected properties successfully.', SR_TEXTDOMAIN );
            }
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success', SR_TEXTDOMAIN ),
                'response_text' => $msg,
                'id' => $get_ids
            );
        }else{
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Error occrred! please try again later.', SR_TEXTDOMAIN )
            );
        }
        
       AjaxHelpers::output_ajax_response( $json_data );
    }
    
}

add_action( 'wp_ajax_CSpropertyAutoUpdateInstantly', 'CSpropertyAutoUpdateInstantly' );
if(!function_exists('CSpropertyAutoUpdateInstantly')){
    /**
     * Property Auto update instantly
     * 
     * @since 1.0.0
     */
    function CSpropertyAutoUpdateInstantly(){
        global $wpdb;
        $post_ID = trim($_POST['item_id'][0]);
        
        try {
            $aios_serp = new \AiosSerp\AiosSerp;
            $get_row = CsQuery::Cs_Get_Results(array(
                    'select' => 'id,item_domain',
                    'from' => $wpdb->prefix.'aios_blmanager_items',
                    'where' => "id = $post_ID",
                    'query_type' => 'get_row'
                ));
            
            $get_row_assets = CsQuery::Cs_Get_Results(array(
                'select' => 'created_on',
                'from' => $wpdb->prefix.'aios_blmanager_item_assets',
                'where' => "item_id = $post_ID",
                'order_by'=> " created_on desc limit 1",
                'query_type' => 'get_row'
            ));
            
            if ($aios_serp->setUrl($get_row->item_domain)) {
                
                $get_properties_matrix = cs_properties_matrix( $get_row->item_domain );
                
                //add property
                $alexa = empty($get_properties_matrix['alexa_data']) ? '' : $get_properties_matrix['alexa_data'];
                $moz = empty($get_properties_matrix['moz_data']) ? '' : $get_properties_matrix['moz_data'];
                $propertyAssetData = array(
                        'table' => 'aios_blmanager_item_assets',
                        'insert_data' => array(
                            'item_id' => $get_row->id,
                            'url_status' => $get_properties_matrix['domain_status'],
                            'alexa_data' => $alexa,
                            'moz_data' => $moz,
                            'created_on' => date('Y-m-d'),
                        )
                    );
                if( $get_row_assets->created_on !== date('Y-m-d')){
                    CsQuery::Cs_Insert( $propertyAssetData );
                }
                
                $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'SERP has been added successfully.', SR_TEXTDOMAIN )
                );
            }else{
                $json_data = array(
                    'error' => true,
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Invalid Url submitted.', SR_TEXTDOMAIN )
                );
            }
        } catch (\Exception $e) {
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSpropertyOptoins', 'CSpropertyOptoins');
if( !function_exists('CSpropertyOptoins')){
    
    /**
     * 
     * @global type $wpdb
     */
    function CSpropertyOptoins(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $user_input );
        $user_input = array_map( 'trim', $user_input );
        $user_input = array_map( 'stripslashes', $user_input );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-property-options', $user_input['_wpnonce'] );
        
        $blc_finder_schedule = $user_input['blc_finder_schedule'];
        
        if( empty( $blc_finder_schedule)){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'You didn\'t select any option.', SR_TEXTDOMAIN )
            );
        }else{
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_property_options',
                'option_value' => array(
                    'blc_finder_schedule' => $blc_finder_schedule
                ),
                'json' => true
            ));
            
            //update schedule for automatic backlink finder
            CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $blc_finder_schedule, 'hook' => 'aios_property_cron'));
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Your update has been saved successfully.', SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}