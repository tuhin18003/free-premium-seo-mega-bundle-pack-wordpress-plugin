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

class BacklinkChecker{
    
    /**
     * Hold Http
     *
     * @var type 
     */
    
    protected $http;
    
    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }
    
    /**
     * Track Visitor Link Click
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function find_backlink_matrix(){
        global $wpdb;
        
//        $_aios_nonce = CsQuery::check_evil_script($_POST['_wpnonce']);
//        
//        //check security
//        AjaxHelpers::check_ajax_referer( 'aios-add-new-backlink', $_aios_nonce );
        
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-backlink', $_aios_nonce, true );
        
        $property_id = $this->http->get('property_id', false); 
        $backlink_from = $this->http->get('backlink_from', false); 
        
        if( empty( $backlink_from ) || filter_var($backlink_from, FILTER_VALIDATE_URL) === FALSE ){
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Required Field Missing!', SR_TEXTDOMAIN ),
                'text' => __( 'Please enter an url in Backlink From field', SR_TEXTDOMAIN )
            );
            return AjaxHelpers::output_ajax_response( $json_data );
        }
        
        try {
            $aios_serp = new \AiosSerp\AiosSerp;
            if ( $aios_serp->setUrl($backlink_from) ) {
                
                /***get property url***/
                $property = CsQuery::Cs_Get_Results(array(
                        'select' => 'item_domain',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "id = '{$property_id}' and item_type = 1",
                        'query_type' => 'get_row'
                    ));
                $backlink_to = isset($property->item_domain) ? trim($property->item_domain) : '';
                /***get property url***/
                
                //check backlink already exists
                $get_row = CsQuery::Cs_Get_Results(array(
                        'select' => 'id',
                        'from' => $wpdb->prefix.'aios_blmanager_items',
                        'where' => "item_parent = '{$property_id}' and item_domain ='{$backlink_from}' and item_type = 2",
                        'query_type' => 'get_row'
                    ));
                if( !isset( $get_row->id ) && empty( $get_row->id )){
                    
                    
                    //create or get group
                    $group_id = isset($_POST['group']) ? $_POST['group'] : '';
                    $new_group_name = isset($_POST['new_group_name']) ? CsQuery::check_evil_script($_POST['new_group_name']) : '';
                    if( $group_id === 'new'){
                        
                        $get_cat_id = CsQuery::Cs_Get_Results(array(
                            'select' => 'id',
                            'from' => $wpdb->prefix.'aios_blmanager_item_groups',
                            'where' => "group_name = '{$new_group_name}' and group_type = 2",
                            'query_type' => 'get_row'
                        ));
                        
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
                    $serp = cs_properties_matrix( $backlink_from, $backlink_to );
                    $alexa = empty($serp['alexa_data']) ? '' : $serp['alexa_data'];
                    $moz = empty($serp['moz_data']) ? '' : $serp['moz_data'];
                    $auto_update = empty($_POST['auto_update_status']) ? 2 : 1;
                    
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
                    
                    $link_found = empty( $serp['backlinks']) ? 1 : count($serp['backlinks']);
                    for($l=0; $l<$link_found; $l++){
                        $blc_arr = $serp['backlinks'][$l];
                        $blcAssetData = array(
                            'table' => 'aios_blmanager_item_assets',
                            'insert_data' => array(
                                'item_id' => $blink_id,
                                'keyword' => isset($blc_arr['link_text']) ? $blc_arr['link_text'] : '',
                                'backlink_type' => aios_filter_backlink_status(trim($blc_arr['backlink_type'])),
                                'link_to_url' => isset($blc_arr['link_to']) ? $blc_arr['link_to'] : $backlink_to,
                                'url_status' => $serp['domain_status'],
                                'alexa_data' => $serp['alexa_data'],
                                'moz_data' => $serp['moz_data'],
                                'created_on' => date('Y-m-d'),
                            )
                        );
                        CsQuery::Cs_Insert( $blcAssetData );
                    }
                    
//                    $blcAssetData = array(
//                        'table' => 'aios_blmanager_item_assets',
//                        'insert_data' => array(
//                            'item_id' => $blink_id,
//                            'keyword' => isset($serp['link_text']) ? $serp['link_text'] : '',
//                            'backlink_type' => $serp['backlink_type'],
//                            'link_to_url' => isset($serp['link_to']) ? $serp['link_to'] : $backlink_to,
//                            'url_status' => $serp['domain_status'],
//                            'alexa_data' => $alexa,
//                            'moz_data' => $moz,
//                            'created_on' => date('Y-m-d'),
//                        )
//                    );
//                    CsQuery::Cs_Insert( $blcAssetData );
                    
                    
                    $json_data = array(
                        'type' => 'success',
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'text' => __( 'Backlink has been added successfully.', SR_TEXTDOMAIN )
                    );
                }else{
                    $json_data = array(
                        'type' => 'error',
                        'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                        'text' => __( 'This Backlink has been already added.', SR_TEXTDOMAIN )
                    );
                }
                
            }else{
                $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'text' => __( 'Invalid Url submitted.', SR_TEXTDOMAIN )
                );
            }
        } catch (\Exception $e) {
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
