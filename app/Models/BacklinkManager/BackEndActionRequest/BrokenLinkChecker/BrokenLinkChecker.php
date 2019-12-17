<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker;

/**
 * Broken Link - Actions Handler
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\CsFlusher;
use CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module\modules;

class BrokenLinkChecker{
    
    protected $http;


    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }
    
    public function general_options(){
        global $wpdb;
        
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-brokenlink-general-options', $_aios_nonce );
        
        $module = array();
        if( $this->http->has('m') ){
            $module = array('module'=>$this->http->get('m', false));
        }
        
        if( $this->http->has('ps')){
            $module = array_merge($module ,array('statuses'=>$this->http->get('ps', false)));
        }
        
        if( $this->http->has('lt')){
            $module = array_merge($module ,array('link_types'=>$this->http->get('lt', false)));
        }
        
        if( $this->http->has('pa')){
            $module = array_merge($module ,array('protocols_apis'=>$this->http->get('pa', false)));
        }
        
        if( $this->http->has('cron_schedule')){
            $schedule = $this->http->get('cron_schedule', false); 
            $module = array_merge($module , array( 'cron_schedule' => $schedule ));
            
            //cron flush
            if( $schedule !== 'stop'){
                //update schedule for automatic backlink finder
                CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_check_links_cron'));
            }else{
                CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_check_links_cron'));
            }
        }
        
        if( $this->http->has('cron_schedule_entire_db')){
            $schedule = $this->http->get('cron_schedule_entire_db', false); 
            $module = array_merge($module , array( 'cron_schedule_entire_db' => $schedule ));
            
            //cron flush
            if( $schedule !== 'stop'){
                //update schedule for automatic backlink finder
                CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_check_links_entire_db_cron'));
            }else{
                CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_check_links_entire_db_cron'));
            }
        }
        
        if( $this->http->has('timeout') ){
            $module = array_merge($module ,array('timeout' => $this->http->get('timeout', false)));
        }
        
        CsQuery::Cs_Update_Option(array(
            'option_name' => 'aios_broken_link_options',
            'option_value' => $module,
            'json' => true
        ));
        
        $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Options has been updated successfully.', SR_TEXTDOMAIN ),
            );
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Delete Link
     */
    public function delete_link(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-manage-all-keywords', $ajax_nonce, true );
//        aios-delete-link
        
        
    }
    
    /**
     * Broken link checker
     */
    public static function entire_db_sync(){
        // get options
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_broken_link_options', 'json' => true ) );   
        /**Truncate table**/
        CsQuery::Cs_Truncate('aios_all_internal_links');
        if( isset( $get_options->module )){
            foreach($get_options->module as $module => $value){
                modules::$module($get_options);
            }
        }
//        error_log('Database synce done!');
    }
    
    /**
     * Link Status Checker
     * 
     * @global type $wpdb
     */
    public static function link_status_checker(){
        global $wpdb;
        
        
    }
    
}
