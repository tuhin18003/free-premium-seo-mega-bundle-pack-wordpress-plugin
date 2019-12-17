<?php namespace CsSeoMegaBundlePack\Models\SocialAnalytics;

/**
 * Actions Handler
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\CsFlusher;
use CsSeoMegaBundlePack\Library\FbGraph\facebook_graph_helper;

class FacebookActionHandler{
    
    protected $http;

    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }
    
    /**
     * Settings
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function facebook_settings(){
        global $wpdb;
        
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-facebook-settings', $_aios_nonce, true );
        
        
        $fb_app_id = $this->http->get('fb_app_id', false); 
        $fb_app_secret = $this->http->get('fb_app_secret', false); 
        
        if( empty($fb_app_id) || empty($fb_app_secret)){
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Please fill up the required fields.', SR_TEXTDOMAIN )
            );
        }else{
            //create or get group
            $module = array(
                'fb_app_id' => $fb_app_id,
                'fb_app_secret' => $fb_app_secret
            );
            
            if( $this->http->has('fb_publish_to')){
                $fb_publish_to = explode('___',$this->http->get( 'fb_publish_to', false ));
                $module = array_merge( $module, array( 'fb_publish_to' => $fb_publish_to));
            }
            
            if( $this->http->has('all_fb_pages')){
                $module = array_merge( $module, array( 'all_fb_pages' => $this->http->get('all_fb_pages', false)));
            }
            

            if( $this->http->has('fb_stats_update_schedule')){
                $schedule = $this->http->get('fb_stats_update_schedule', false); 
                $module = array_merge($module , array( 'fb_stats_update_schedule' => $schedule ));

                //cron flush
                if( $schedule !== 'stop'){
                    //update schedule for automatic backlink finder
                    CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_facebook_stats_cron'));
                }else{
                    CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_facebook_stats_cron'));
                }
            }
            
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_facebook_settings',
                'option_value' => $module,
                'json' => true
            ));

            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Settings has been updated successfully.', SR_TEXTDOMAIN ),
            );
            AjaxHelpers::output_ajax_response( $json_data );
       }    
    }
    
    
    /**
     * Instantly publish to facebook or create schedule
     * hooked to save_post 
     * 
     * @global $wpdb
     * @return boolean
     */
    public function facebook_settings_on_save_post(){
        global $wpdb;
        if ( 
            ! isset($_POST['aios_facebook_publish']) 
            || ! wp_verify_nonce( $_POST['aios_facebook_publish'], 'aios_facebook_publish_action' ) 
        ) return false;

        if(isset($_POST['fb_custom_title']) && !empty($_POST['fb_custom_title'])){
            CsQuery::Cs_Update_Postmeta($_POST['post_ID'], 'aios_fb_custom_title', sanitize_text_field($_POST['fb_custom_title']));
        }
        
        if(isset($_POST['fb_custom_content']) && !empty($_POST['fb_custom_content'])){
            CsQuery::Cs_Update_Postmeta($_POST['post_ID'], 'aios_fb_custom_content', sanitize_text_field($_POST['fb_custom_content']));
        }
        
        
        if( isset( $_POST['aios_fb_instant_post']) && ! empty( $_POST['aios_fb_instant_post'])){
            $ret = $this->publish_to_facebook( $_POST['post_ID'] );
            if( $ret ){
                foreach( $ret as $item ){
                    if( $item['response_code'] === 200 ){
                        CsQuery::Cs_Insert(array(
                            'table' => 'aios_facebook_statistics',
                            'insert_data'=> array(
                                'post_id' => $_POST['post_ID'],
                                'fb_post_id' => $item['response_text'],
                                'created_on' => date('Y-m-d H:i:s')
                            )
                        ));
                    }
                }
            }
        }
        
        if( isset( $_POST['aios_fb_post_schedule_val'] ) && !empty( $_POST['aios_fb_post_schedule_val'])){
            $aios_fb_post_schedule_val = $_POST['aios_fb_post_schedule_val'];
            if( $aios_fb_post_schedule_val > 0 ){
                
                date_default_timezone_set(get_option('timezone_string'));
                $schedule = strtotime($aios_fb_post_schedule_val);
                
                CsQuery::Cs_Insert(array(
                    'table' => 'aios_social_publish_schedule',
                    'insert_data' => array(
                        'type' => 1, // facebook type 1
                        'post_id' => $_POST['post_ID'],
                        'publish_on' => $schedule,
                        'created_on' => date('Y-m-d H:i:s')
                    )
                ));
                
                
                //insert new schedule
                CsFlusher::Cs_Single_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => "aiosFbAutoPubCron", 'args' => array( 'publish_on' => $schedule )));
            }
        }
    }
    
    
    /**
     * Auto Publish to facebook
     */
    public function auto_publish_queue( $publish_on ){
        global $wpdb;
        $queue = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix.'aios_social_publish_schedule',
            'where' => "type = 1 and publish_on = {$publish_on} "
        ));
            
        if( $queue ){
            foreach ($queue as $que){
                $ret = $this->publish_to_facebook($que->post_id);
                if( isset( $ret[0]['response_code'] ) && $ret[0]['response_code'] === 200 ){
                    CsQuery::Cs_Insert(array(
                        'table' => 'aios_facebook_statistics',
                        'insert_data'=> array(
                            'post_id' => $que->post_id,
                            'fb_post_id' => $ret[0]['response_text'],
                            'created_on' => date('Y-m-d H:i:s')
                        )
                    ));
                    //if published to facebook, delete the queue tbl, cron tbl row
                    CsQuery::Cs_Delete(array(
                        'table' => 'aios_social_publish_schedule',
                        'where' => array( 'id' => $que->id)
                    ));
                    
                }else{
                    //update error
                }
            }
        }
        
    }


    /**
     * Delete Facebook Scheduled Posts
     */
    public function facebook_delete_scheduled_post(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'Aios-manage-fb-scheduled-posts', $ajax_nonce, true );
        
        if( $this->http->has('_item_id')){
            $item_id = $this->http->get('_item_id', false);
            $_item_id = implode( ',', $item_id);
            
            CsQuery::Cs_Delete_In(array(
                'table' => 'aios_social_publish_schedule',
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => $_item_id
                )
            ));
            
            foreach($item_id as $id){
                $dynaic_hook = "aiosFbAutoPubCron_{$id}";
                CsFlusher::Cs_Cron_Remove( array( 'hook' => $dynaic_hook ) );
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Schedule has been deleted successfully.', SR_TEXTDOMAIN ),
                'remove_id' => $item_id
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Publish to facebook
     * 
     * @global  $wpdb
     * @param type $post_id
     * @return string
     */
    public function publish_to_facebook( $post_id = false){
        global $wpdb;
        if( empty( $post_id ) ){ //scheduled post to publish
            
        }else{
            $get_posts = CsQuery::Cs_Get_Results(array(
                'select' => '*',
                'from' => $wpdb->prefix . 'posts',
                'where' => " ID = {$post_id}"
            ));
        }
        
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_facebook_settings', 'json' => true ) );  
        if( !isset($get_options->fb_publish_to[0]) && !issset( $get_options->fb_app_id ) ){
            return 'Facebook token doesn\'t found! ';
        }
        
        $ret_msg = array();
        if( $get_posts ){
            foreach($get_posts as $post){
                
                $custom_title = CsQuery::Cs_Get_Postmeta($post->ID, 'aios_fb_custom_title', true);
                $custom_content = CsQuery::Cs_Get_Postmeta($post->ID, 'aios_fb_custom_content', true);
                
                $message = empty($custom_title) ? $post->post_title : $custom_title;
                $message .= "\n\n";
                $message .= empty($custom_content) ? $post->post_content : $custom_content;
                
                $argc = array(
                    'app_id' => $get_options->fb_app_id, 
                    'app_secret' => $get_options->fb_app_secret,
                    'access_token' => $get_options->fb_publish_to[1],
                    'page_id' => $get_options->fb_publish_to[0],
                    'message' => $message,
                    'picture' => get_the_post_thumbnail_url( $post->ID ),
                    'link' => get_the_permalink( $post->ID )
                );
                
                $ret_msg[] = (new facebook_graph_helper())->publish( $argc );
                
            }
        }
        
        return $ret_msg;
    }
    
    /**
     * Delete posts from facebook
     */
    public function facebook_delete_posts(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'Aios-manage-fb-scheduled-posts', $ajax_nonce, true );
        
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_facebook_settings', 'json' => true ) );  
        if( !isset($get_options->fb_publish_to[0]) && !issset( $get_options->fb_app_id ) ){
            return 'Facebook token doesn\'t found! ';
        }
        
        if( $this->http->has('_item_id')){
            $item_id = $this->http->get('_item_id', false);
            foreach($item_id as $id){
                CsQuery::Cs_Delete(array(
                    'table' => 'aios_facebook_statistics',
                    'where' => array( 'fb_post_id' => $id )
                ));
                
                $argc = (object)array(
                    'app_id' => $get_options->fb_app_id, 
                    'app_secret' => $get_options->fb_app_secret,
                    'access_token' => $get_options->fb_publish_to[1],
                    'page_id' => $get_options->fb_publish_to[0],
                    'post_id' => $id
                );
                (new facebook_graph_helper())->delete_post( $argc );
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Post has been deleted successfully from facebook.', SR_TEXTDOMAIN ),
                'remove_id' => $item_id
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Automatic update facebook statistics
     * 
     * @global $wpdb
     * @return boolean|string
     */
    public static function get_facebook_stats(){
        global $wpdb;
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_facebook_settings', 'json' => true ) );  
        if( !isset($get_options->fb_publish_to[0]) && !issset( $get_options->fb_app_id ) ){
            return 'Facebook token doesn\'t found! ';
        }
        
        $_objects = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix.'aios_facebook_statistics',
        ));
        if( $_objects ){
            foreach($_objects as $_object){
                $argc = (object)array(
                    'app_id' => $get_options->fb_app_id, 
                    'app_secret' => $get_options->fb_app_secret,
                    'access_token' => $get_options->fb_publish_to[1],
                    'page_id' => $get_options->fb_publish_to[0],
                    'post_id' => $_object->fb_post_id
                );
                $ret = (new facebook_graph_helper())->get_stats( $argc );
                
                CsQuery::Cs_Update(array(
                    'table'=> 'aios_facebook_statistics',
                    'update_data' => array_merge($ret, array('last_updated' => date('Y-m-d H:i:s'))),
                    'update_condition' => array(
                        'fb_post_id' => $_object->fb_post_id
                    )
                ));
                
            }
        }
        return true;
    }


}
