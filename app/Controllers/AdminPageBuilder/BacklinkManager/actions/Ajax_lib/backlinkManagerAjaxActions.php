<?php 
/**
 * Backlinks Ajax Action
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Models\CommonQuery\CsFlusher;

//add_action( 'wp_ajax_CSaddNewBacklinks', 'CSaddNewBacklinks' );
if(!function_exists('CSaddNewBacklinks')){
    /**
     * Add New backlink
     */
    function CSaddNewBacklinks(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-backlink', $data_arr['_wpnonce'] );
        
        $property_id = $data_arr['property_id']; 
        $backlink_from = $data_arr['backlink_to']; 

        try {
            $seostats = new \SEOstats\SEOstats;
            if ($seostats->setUrl($backlink_from)) {
                
                /***get property url***/
                $property = CsQuery::Cs_Get_Results(array(
                        'select' => 'item_domain',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "id = '{$property_id}' and item_type = 1",
                        'query_type' => 'get_row'
                    ));
//                $property = $wpdb->get_row("select url from {$wpdb->prefix}aios_properties where id = {$property_id}");
                $backlink_to = isset($property->item_domain) ? trim($property->item_domain) : '';
                /***get property url***/
                
                //check backlink already exists
                $get_row = CsQuery::Cs_Get_Results(array(
                        'select' => 'id',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "item_parent = '{$property_id}' and item_domain ='{$backlink_from}' and item_type = 2",
                        'query_type' => 'get_row'
                    ));
//                $get_row = $wpdb->get_row("select p_id from {$wpdb->prefix}aios_backlinks where p_id = {$property_id} and url = '{$backlink_from}' ");
                if( !isset( $get_row->id ) && empty( $get_row->id )){
                    
                    
                    //create or get group
                    $group_id = isset($data_arr['group']) ? $data_arr['group'] : '';
                    $new_group_name = isset($data_arr['new_group_name']) ? $data_arr['new_group_name'] : '';
                    if( $group_id === 'new'){
                        
                        $get_cat_id = CsQuery::Cs_Get_Results(array(
                            'select' => 'id',
                            'from' => $wpdb->prefix.'aios_blmanager_item_groups',
                            'where' => "group_name = '{$new_group_name}' and group_type = 2",
                            'query_type' => 'get_row'
                        ));
//                        $get_cat_id = $wpdb->get_row("select id from {$wpdb->prefix}aios_backlink_groups where name = '{$new_group_name}'");
                        
                        if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                            $group_id = $get_cat_id->id;
                        }else{
                            $insert_group = array(
                                'table' => 'aios_blmanager_item_groups',
                                'insert_data' => array(
                                    'group_name' => $new_group_name,
                                    'group_type' => 2,
                                    'group_created_on' => date('Y-m-d H:i:s')
                                )
                            );
                            $group_id = CsQuery::Cs_Insert( $insert_group );
                        }
                    }
                    
                    // get link juice
                    $serp = aios_get_backlink_serp( $backlink_from, $backlink_to );
                    $alexa = empty($serp['alexa_data']) ? '' : json_encode($serp['alexa_data']);
                    $moz = empty($serp['moz_data']) ? '' : json_encode($serp['moz_data']);
                    $auto_update = empty($data_arr['auto_update_status']) ? 2 : 1;
                    
                    //add baclinks
                    $blcData =array( 
                        'table' => 'aios_blmanager_items',
                        'insert_data' => array(
                            'item_parent' => $property_id,
                            'item_domain' => $backlink_from,
                            'item_domain_status' => $serp['domain_status'],
                            'item_group_id' => $group_id,
                            'item_type' => 2,
                            'item_auto_update' => $auto_update,
                            'item_created_on' => date('Y-m-d')
                        )
                    );
                    $blink_id = CsQuery::Cs_Insert( $blcData );
                    
                    $blcAssetData = array(
                        'table' => 'aios_blmanager_item_assets',
                        'insert_data' => array(
                            'item_id' => $blink_id,
                            'keyword' => isset($serp['link_text']) ? $serp['link_text'] : '',
                            'backlink_type' => $serp['backlink_type'],
                            'link_to_url' => isset($serp['link_to']) ? $serp['link_to'] : $backlink_to,
                            'url_status' => $serp['domain_status'],
                            'alexa_data' => $alexa,
                            'moz_data' => $moz,
                            'created_on' => date('Y-m-d'),
                        )
                    );
                    CsQuery::Cs_Insert( $blcAssetData );
                    $json_data = array(
                        'error' => false,
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'Backlink has been added successfully.', SR_TEXTDOMAIN )
                    );
                }else{
                    $json_data = array(
                        'error' => true,
                        'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'This Backlink has been already added.', SR_TEXTDOMAIN )
                    );
                }
                
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

add_action( 'wp_ajax_CSaddNewBacklinksCat', 'CSaddNewBacklinksCat' );
if(!function_exists('CSaddNewBacklinksCat')){
    
    /**
     * Add New backlink Cat
     */
    function CSaddNewBacklinksCat(){
        global $wpdb;
        
        global $wpdb;
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $data_arr );
        
        $data_arr = array_map( 'trim', $data_arr );
        $data_arr = array_map( 'stripslashes', $data_arr );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-backlink-groups', $data_arr['_wpnonce'] );
        
        //create or get group
        $group_name = isset($data_arr['new_cat_name']) ? $data_arr['new_cat_name'] : '';
        $new_group_monthly_price = isset($data_arr['new_cat_monthly_price']) ? $data_arr['new_cat_monthly_price'] : '';
        $description = isset($data_arr['input_description']) ? $data_arr['input_description'] : '';
        $group_id = isset($data_arr['group_id']) ? $data_arr['group_id'] : '';
        if( empty( $group_id )){
            $get_cat_id = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => $wpdb->prefix.'aios_blmanager_item_groups',
                'where' => "group_name = '{$group_name}' and group_type = 2",
                'query_type' => 'get_row'
            ));
            if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                    $group_id = $get_cat_id->id;
                }else{
                    $insert_group = array(
                        'table' => 'aios_blmanager_item_groups',
                        'insert_data' => array(
                            'group_name' => $group_name,
                            'group_type' => 2,
                            'group_description' => $description,
                            'group_created_on' => date('Y-m-d H:i:s')
                        )
                    );
                    $group_id = CsQuery::Cs_Insert( $insert_group );
                    if( $group_id > 0 ){
                        $json_data = array(
                            'error' => false,
                            'title' => __( 'Success!', SR_TEXTDOMAIN ),
                            'response_text' => __( 'Group has been added successfully.', SR_TEXTDOMAIN )
                        );
                    }else{
                        $json_data = array(
                            'error' => true,
                            'title' => __( 'Error!', SR_TEXTDOMAIN ),
                            'response_text' => __( 'Something went wrong. Please try again.', SR_TEXTDOMAIN )
                        );
                    }
                }
        }else{ /// edit group info
            $insert_group =array(
                    'table' => 'aios_blmanager_item_groups',
                    'update_data' => array(
                        'group_name' => $group_name,
                        'group_description' => $description
                    ),
                    'update_condition' => array( 'id' => $group_id)
                );
            $group_id = CsQuery::Cs_Update( $insert_group );
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Group has been updated successfully.', SR_TEXTDOMAIN ),
                'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageBacklinkGroups')
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSdeleteBacklink', 'CSdeleteBacklink' );
if(!function_exists('CSdeleteBacklink')){
    /**
     * Delete Backlink
     * 
     * @since 1.0.0
     */
    function CSdeleteBacklink(){
        global $wpdb;
        
        $get_ids = $_POST['item_id'];
        $get_type = $_POST['type'];
        
        if(is_array($get_ids)){
            $type_msg = '';
//            foreach($get_ids as $id){
            $id_string = implode(',', $get_ids);
                if( $get_type === 'delete_backlink'){
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_items',
                        'where' => array(
                            'field_name' => 'id',
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
                
                    $type_msg = 'Backlink';
                }
                else if( $get_type === 'delete_cat'){
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_item_groups',
                        'where' => array(
                            'field_name' => 'id',
                            'field_value' => $id_string
                        )
                    ));
                    CsQuery::Cs_Delete_In(array(
                        'table' => 'aios_blmanager_items',
                        'where' => array(
                            'field_name' => 'item_group_id',
                            'field_value' => $id_string
                        )
                    ));
                    
                    $type_msg = 'Group ';
                }
//            }
            
             $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( "$type_msg has been deleted successfully.", SR_TEXTDOMAIN ),
                'id' => $get_ids
            );
        }else{
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Error occrred! please try again later.', SR_TEXTDOMAIN ),
            );
            
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSsetAutoUpdateBacklink', 'CSsetAutoUpdateBacklink' );
if(!function_exists('CSsetAutoUpdateBacklink')){
    /**
     * Backlink Auto update setup
     * 
     * @since 1.0.0
     */
    function CSsetAutoUpdateBacklink(){
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
            
            if($get_type == 'set_auto_update'){
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

add_action( 'wp_ajax_CSbacklinkAutoUpdateInstantly', 'CSbacklinkAutoUpdateInstantly' );
if(!function_exists('CSbacklinkAutoUpdateInstantly')){
    /**
     * Backlink Auto update instantly
     * 
     * @since 1.0.0
     */
    function CSbacklinkAutoUpdateInstantly(){
        global $wpdb;
        $post_ID = trim($_POST['item_id'][0]);
        
        try {
            $get_row = CsQuery::Cs_Get_Results(array(
                'select' => 'p.item_domain as domain, b.item_domain as backlink',
                'from' => $wpdb->prefix.'aios_blmanager_items as b',
                'where' => "b.id = $post_ID",
                'join' => "left join ". $wpdb->prefix.'aios_blmanager_items' . " as p on b.item_parent = p.id",
                'query_type' => 'get_row'
            ));
            $get_row_assets = CsQuery::Cs_Get_Results(array(
                'select' => 'created_on',
                'from' => $wpdb->prefix.'aios_blmanager_item_assets',
                'where' => "item_id = $post_ID order by created_on desc limit 1",
                'query_type' => 'get_row'
            ));
                
                $serp = cs_properties_matrix( $get_row->backlink, $get_row->domain );
                $alexa = empty($serp['alexa_data']) ? '' : $serp['alexa_data'];
                $moz = empty($serp['moz_data']) ? '' : $serp['moz_data'];
                
                $link_found = empty( $serp['backlinks']) ? 1 : count($serp['backlinks']);
                for($l=0; $l<$link_found; $l++){
                    $blc_arr = $serp['backlinks'][$l];
                    $blcAssetData = array(
                        'table' => 'aios_blmanager_item_assets',
                        'insert_data' => array(
                            'item_id' => $post_ID,
                            'keyword' => isset($blc_arr['link_text']) ? $blc_arr['link_text'] : '',
                            'backlink_type' => aios_filter_backlink_status($blc_arr['backlink_type']),
                            'link_to_url' => isset($blc_arr['link_to']) ? $blc_arr['link_to'] : $get_row->domain,
                            'url_status' => $serp['domain_status'],
                            'alexa_data' => $serp['alexa_data'],
                            'moz_data' => $serp['moz_data'],
                            'created_on' => date('Y-m-d'),
                        )
                    );
                     if( $get_row_assets->created_on !== date('Y-m-d')){
                            CsQuery::Cs_Insert( $blcAssetData );
                        }
                }
                
//                $blcAssetData = array(
//                    'table' => 'aios_blmanager_item_assets',
//                    'insert_data' => array(
//                        'item_id' => $post_ID,
//                        'keyword' => isset($serp['link_text']) ? $serp['link_text'] : '',
//                        'backlink_type' => $serp['backlink_type'],
//                        'link_to_url' => isset($serp['link_to']) ? $serp['link_to'] : $get_row->domain,
//                        'url_status' => $serp['domain_status'],
//                        'alexa_data' => $alexa,
//                        'moz_data' => $moz,
//                        'created_on' => date('Y-m-d'),
//                    )
//                );
//                if( $get_row_assets->created_on !== date('Y-m-d')){
//                    CsQuery::Cs_Insert( $blcAssetData );
//                }
                
               $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( 'Backlink has been updated successfully.', SR_TEXTDOMAIN )
                );
        }catch (\Exception $e) {
             $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSbacklinkOptoins', 'CSbacklinkOptoins');
if( !function_exists('CSbacklinkOptoins')){
    
    
    function CSbacklinkOptoins(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $user_input );
        $user_input = array_map( 'trim', $user_input );
        $user_input = array_map( 'stripslashes', $user_input );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-backlink-options', $user_input['_wpnonce'] );
        
        $blc_finder_schedule = $user_input['blc_finder_schedule'];
        
        if( empty( $blc_finder_schedule)){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'You didn\'t select any option.', SR_TEXTDOMAIN )
            );
        }else{
             CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_backlink_options',
                'option_value' => array(
                    'blc_finder_schedule' => $blc_finder_schedule
                ),
                'json' => true
            ));
            
            //update schedule for automatic backlink finder
            CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $blc_finder_schedule, 'hook' => 'aios_blc_cron'));
            
            $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Your update has been saved successfully.', SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSaddBlcCost', 'CSaddBlcCost');
if( !function_exists('CSaddBlcCost')){
    
    
    function CSaddBlcCost(){
        global $wpdb;
        
        $data = trim($_POST['data']);
        empty($data) ? '' : parse_str( $data, $user_input );
        $user_input = array_map( 'trim', $user_input );
        $user_input = array_map( 'stripslashes', $user_input );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-blc-cost', $user_input['_wpnonce'] );
        $group_id = $user_input['group_id'];
        if( empty( $group_id )){
            $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'You didn\'t select any group.', SR_TEXTDOMAIN )
            );
        }else{
            if( isset($user_input['action_type']) && $user_input['action_type'] == 1){
                $type = array( 'type' => 1);
            }else{
                $type = array( 'type' => 2);
            }
            
            CsQuery::Cs_Insert(array(
                'table' => 'aios_blmanager_item_price',
                'insert_data' => array_merge(array(
                    'item_id' => $group_id,
                    'price' => $user_input['input_cost'],
                    'note' => $user_input['input_description'],
                    'created_on' => date('Y-m-d', strtotime($user_input['input_date'])),
                ), $type )
            ));
             $json_data = array(
                'error' => false,
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Your cost has been saved successfully.', SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
}

add_action( 'wp_ajax_CSdeleteCost', 'CSdeleteCost' );
if(!function_exists('CSdeleteCost')){
    /**
     * Delete Backlink Cost
     * 
     * @since 1.0.0
     */
    function CSdeleteCost(){
        
        $get_ids = $_POST['item_id'];
        $get_type = $_POST['type'];
        
        if(is_array($get_ids)){
            $type_msg = '';
            $id_string = implode(',', $get_ids);
                CsQuery::Cs_Delete_In(array(
                    'table' => 'aios_blmanager_item_price',
                    'where' => array(
                        'field_name' => 'id',
                        'field_value' => $id_string
                    )
                ));
                if( $get_type === 'delete_blc_cost'){
                    $type_msg = 'Backlink cost';
                }
                else if( $get_type === 'delete_property_cost'){
                    $type_msg = 'Property cost';
                }
                
                 $json_data = array(
                    'error' => false,
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'response_text' => __( "$type_msg has been deleted successfully.", SR_TEXTDOMAIN ),
                    'id' => $get_ids
                );
        }else{
            $json_data = array(
                'error' => false,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Unexpected error occurred. Please try again later.', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
}
