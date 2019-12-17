<?php 
/**
 * Backlink Functions
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Library\SerpTracker\Services\Alexa;
use CsSeoMegaBundlePack\Library\SerpTracker\Services\Mozscape;
use CsSeoMegaBundlePack\Library\SerpTracker\Services\BacklinkStatus;

if( ! function_exists('cs_properties_matrix')){
    
    /**
     * Get website matrix
     * 
     * @param String $url
     * @return array
     */
    function cs_properties_matrix( $url, $backlink_to = false ){
            try {
                // Create a new instance.

                $aios_serp = new \AiosSerp\AiosSerp;
                
                if ($aios_serp->setUrl($url)) {
//                    
                    $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'text' => __( print_r(Mozscape::getMozComparison()), SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
                
                $bl_status = array();
                $alexa_data = array();
                $moz_data = array();
                $url_status_code = $aios_serp->getDomainStatus();
                if( 200 === $url_status_code){
                    $countryRank = Alexa::getCountryRank();
                    $country = ''; $al_country_rank = '';
                    if(is_array($countryRank)) {
                        $al_country_rank = isset($countryRank['rank']) ? $countryRank : '';
                        $country = isset($countryRank['country']) ? $countryRank['country'] : '';
                    }
                    $alexa_data = json_encode(array(
                        'alexa_global_rank' => Alexa::getGlobalRank(),
                        'alexa_country' => $country,
                        'alexa_country_rank' => $al_country_rank,
                        'alexa_backlink_count' => Alexa::getBacklinkCount(),
                        'alexa_page_speed' => Alexa::getPageLoadTime()
                    ));
                    $moz_data = json_encode(Mozscape::getMozComparison());
                    
                    if($backlink_to){
                        //link status
                        $bl_status = BacklinkStatus::getBacklinkStatus($backlink_to);
                    }
                }
                        
                    return array(
                        'alexa_data' => $alexa_data,
                        'moz_data' => $moz_data,
                        'domain_status' => $url_status_code,
                        'backlinks' => $bl_status
                    );
                }
                
            } catch (Exception $e) {
                $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Error!', SR_TEXTDOMAIN ),
                    'text' => __( 'Caught CSAllInOneSeoException: ' .  $e->getMessage(), SR_TEXTDOMAIN )
                );
                AjaxHelpers::output_ajax_response( $json_data );
            }
    }
    
}

add_action( 'aios_blc_cron', 'aios_afoblc');
if( !function_exists('aios_afoblc')){
    /**
     * Backlink automatic SERP update
     * 
     * @global type $wpdb
     */
    function aios_afoblc(){
        global $wpdb;
        
        $blc_ojb = CsQuery::Cs_Get_Results(array(
            'select' => 'p.item_domain as domain, b.item_domain as backlink,b.id as b_id',
            'from' => $wpdb->prefix.'aios_blmanager_items as b',
            'where' => "b.item_auto_update = 1 and b.item_type = 2",
            'join' => "left join ". $wpdb->prefix.'aios_blmanager_items' . " as p on b.item_parent = p.id",
        ));
        
        $date_today = date('Y-m-d');
        
        if( $blc_ojb ){
            foreach($blc_ojb as $item){
                $get_row = CsQuery::Cs_Count(array(
                    'table' => 'aios_blmanager_item_assets',
                    'where' => "item_id = {$item->b_id} and created_on = '{$date_today}'",
                ));
                if($get_row === 0){
                    $serp = cs_properties_matrix( $item->backlink, $item->domain );
                    $link_found = empty( $serp['backlinks']) ? 1 : count($serp['backlinks']);
                    for($l=0; $l<$link_found; $l++){
                        $blc_arr = $serp['backlinks'][$l];
                        $blcAssetData = array(
                            'table' => 'aios_blmanager_item_assets',
                            'insert_data' => array(
                                'item_id' => $item->b_id,
                                'keyword' => isset($blc_arr['link_text']) ? $blc_arr['link_text'] : '',
                                'backlink_type' => aios_filter_backlink_status($blc_arr['backlink_type']),
                                'link_to_url' => isset($blc_arr['link_to']) ? $blc_arr['link_to'] : $item->domain,
                                'domain_status' => $serp['domain_status'],
                                'alexa_data' => $serp['alexa_data'],
                                'moz_data' => $serp['moz_data'],
                                'created_on' => date('Y-m-d'),
                            )
                        );
                        CsQuery::Cs_Insert( $blcAssetData );
                    }
                }    
                    
            }
        }
        
    }
    
}

add_action( 'aios_property_cron', 'aios_automatic_property_update');
if( !function_exists('aios_automatic_property_update')){
    
    /**
     * Automatic update property SERP
     * 
     * @global type $wpdb
     * @return boolean
     */
    function aios_automatic_property_update(){
        global $wpdb;
        
        $blc_ojb = CsQuery::Cs_Get_results(array(
            'select' => 'id,item_domain',
            'from' => $wpdb->prefix . 'aios_blmanager_items',
            'where' => 'item_auto_update = 1 and item_type = 1'
        ));
        
        if( $blc_ojb ){
            foreach($blc_ojb as $item){
                $serp = cs_properties_matrix( $item->item_domain );
                $date_today = date('Y-m-d');
                $get_row = CsQuery::Cs_Count(array(
                    'table' => 'aios_blmanager_item_assets',
                    'where' => "item_id = {$item->id} and created_on = '{$date_today}'",
                ));
                    
                if( $get_row === 0 ){
                    $propertyAssetData = array(
                        'table' => 'aios_blmanager_item_assets',
                        'insert_data' => array(
                            'item_id' => $item->id,
                            'domain_status' => $serp['domain_status'],
                            'alexa_data' => $serp['alexa_data'],
                            'moz_data' => $serp['moz_data'],
                            'created_on' => date('Y-m-d'),
                        )
                    );
                    CsQuery::Cs_Insert( $propertyAssetData );
                }
            }
        }
        return true;
    }
}

if( !function_exists('aios_filter_backlink_status')){
    
    function aios_filter_backlink_status( $_backlink_type ){
        $backlink_type = '';
        if( trim($_backlink_type) === '' || 0 === strlen($_backlink_type)) {
            $backlink_type = 1;
        }else if( 'follow' === strtolower( $_backlink_type ) ) {
            $backlink_type = 2;
        }else{
            $backlink_type = '$_backlink_type';
        }
        return $backlink_type;
    }
}

if( !function_exists('aios_get_backlink_serp')){
    
    /**
     * Get backlinks serp
     * 
     * @param type $url
     * @return array 
     */
    function aios_get_backlink_serp( $url, $backlink_to = false ){
        if(empty($url)) return false;
        
        $serp = cs_properties_matrix( $url, $backlink_to );
        $alexa = empty($serp['alexa']) ? '' : json_encode($serp['alexa']);
        $moz = empty($serp['moz']) ? '' : json_encode($serp['moz']);


        $blc_status = array();
        $bl_status = empty($serp['backlink_status']) ? '' : $serp['backlink_status'];
        if( ! empty( $bl_status ) ){
                        $_backlink_type = $bl_status[0]['backlink_type'];
                        $backlink_type = '';
                        if( $_backlink_type == '' && 0 == strlen( $_backlink_type ) ) {
                            $backlink_type = 1;
                        }else if( 'follow' == strtolower( $_backlink_type ) ) {
                            $backlink_type = 2;
                        }else{
                            $backlink_type = $_backlink_type;
                        }
                $blc_status = array(
                    'link_to' => $bl_status[0]['link_to'],
                    'link_text' => $bl_status[0]['link_text'],
                    'backlink_type'=> $backlink_type
                );
//                        }
        }else{ // link not found
            $backlink_status = 3;
            $blc_status = array( 'backlink_type' => $backlink_status);
        }

        $propertyAssetData = array_merge( $blc_status, array(
            'domain_status' => $serp['domain_status'],
            'alexa_data' => $alexa,
            'moz_data' => $moz,
            'created_on' => date('Y-m-d'),
        ));
        
        return $propertyAssetData;
    }
}

function pre_print( $data = '', $text = '' ){
    echo "<pre> \n------------------------------DEBUG MODE START------------------------------\n\n";
    echo empty($text) ? '': $text ."\n\n";
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    echo "File Name: " . $caller['file'];
    echo "\nLine No: " . $caller['line'] . "\n\n";
    if( empty($data) ){
        echo 'HELLO! TUHIN!';
    }else{
        print_r( $data );
    }
    die( "\n\n------------------------------DEBUG MODE END------------------------------");
}
