<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest;

/**
 * My Website Menu - Actions Handler
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\CsFlusher;

class MyWebsites{
    
    /**
     * Track Visitor Link Click
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function property_add(){
        global $wpdb;
//        $user_data = CsQuery::check_evil_script($_POST);
        
        $_aios_nonce = CsQuery::check_evil_script($_POST['_wpnonce']);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-property', $_aios_nonce );
        
        $property_url = CsQuery::check_evil_script($_POST['input_new_property']); 
        
        if( empty($property_url) ){
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Please fill up the required fields.', SR_TEXTDOMAIN )
            );
        }else{
            try{
                
                $get_row = CsQuery::Cs_Get_Results(array(
                        'select' => 'id',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "item_domain = '{$property_url}' and item_type = 1",
                        'query_type' => 'get_row'
                    ));
                if( !isset( $get_row->id ) && empty( $get_row->id )){
                    
                    //create or get group
                    $group_id = isset($_POST['group']) ? $_POST['group'] : '';
                    $new_group_name = isset($_POST['new_group_name']) ? $_POST['new_group_name'] : '';
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
                    $alexa = empty($get_properties_matrix['alexa_data']) ? '' : $get_properties_matrix['alexa_data'];
                    $moz = empty($get_properties_matrix['moz_data']) ? '' : $get_properties_matrix['moz_data'];
                    $auto_update = empty($_POST['auto_update_status']) ? 2 : 1;
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
                        'type' => 'success',
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'text' => __( 'Property has been added successfully.', SR_TEXTDOMAIN )
                    );
                }else{
                    $json_data = array(
                        'type' => 'error',
                        'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                        'text' => __( 'This Property has been already added.', SR_TEXTDOMAIN )
                    );
                }
                
                
            } catch (\Exception $e) {
                $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
                );
            }
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
