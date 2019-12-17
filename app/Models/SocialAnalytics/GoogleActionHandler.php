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
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google\GoogleSettings;
use AiosSerp\Services\Google as Google;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\Includes\HtAccess\CsHtaccesCreator;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;

class GoogleActionHandler {
    
    protected $http;
    
    /**
     * Hold Site url
     *
     * @var type 
     */
    public $site_url;
    
    /**
     * Hold Google settings data
     *
     * @var type ojb
     */
    public $google_settings;
    
    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
        $this->site_url = site_url('/');
        
        //init Google Settings
        $this->init_google_settings();
        
    }

    /**
     * Settings
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function google_settings(){
        
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-settings', $_aios_nonce, true );
        
        
        $app_id = $this->http->get('app_id', false); 
        $app_secret = $this->http->get('app_secret', false); 
        
        if( empty($app_id) || empty($app_secret)){
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Please fill up the required fields.', SR_TEXTDOMAIN )
            );
        }else{
            //create or get group
            $module = array(
                'app_id' => trim($app_id),
                'app_secret' => trim($app_secret)
            );
          
            if( $this->http->has('profile_id')){
                $profile_id = $this->http->get('profile_id', false);
                $module = array_merge( $module, array( 'profile_id' => $profile_id));
                
                $profiles = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_anlytics_profiles', 'json' => true ) );
                if( $profiles ){
                    foreach($profiles as $profile){
                        if( $profile->webPropertyId === $profile_id ){
                            $module = array_merge( $module, array( 'selected_profile_id' => $profile->id));
                            break;
                        }
                    }
                }
            }
            
            if( $this->http->has('annon_ip')){
                $module = array_merge( $module, array( 'annon_ip' => $this->http->get('annon_ip', false)));
            }
            
            if( $this->http->has('analytics_id_for_track')){
                $module = array_merge( $module, array( 'analytics_id_for_track' => $this->http->get('analytics_id_for_track', false)));
            }
            
            if( $this->http->has('auto_add_tracking_code')){
                $module = array_merge( $module, array( 'auto_add_tracking_code' => $this->http->get('auto_add_tracking_code', false)));
            }
            
            if( $this->http->has('google_webmaster_code')){
                $module = array_merge( $module, array( 'google_webmaster_code' => $this->http->get('google_webmaster_code', false)));
            }
            
            if( $this->http->has('send_report')){
                $module = array_merge( $module, array( 'send_report' => $this->http->get('send_report', false)));
            }
            
            if( $this->http->has('crawler_lookup_schedule')){
                $schedule = $this->http->get('crawler_lookup_schedule', false); 
                $module = array_merge($module , array( 'crawler_lookup_schedule' => $schedule ));
                //cron flush
                if( $schedule !== 'stop'){
                    //Cron schedule
                    CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_google_cron'));
                }else{
                    CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_google_cron'));
                }
            }
            
            if( $this->http->has('index_cron_schedule')){
                $schedule = $this->http->get('index_cron_schedule', false); 
                $module = array_merge($module , array( 'index_cron_schedule' => $schedule ));
                //cron flush
                if( $schedule !== 'stop'){
                    //Cron schedule
                    CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_google_index_search_cron'));
                }else{
                    CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_google_index_search_cron'));
                }
            }
            
            if( $this->http->has('google_country')){
                $module = array_merge( $module, array( 'google_country' => $this->http->get('google_country', false)));
            }
            if( $this->http->has('custom_search_id')){
                $module = array_merge( $module, array( 'custom_search_id' => $this->http->get('custom_search_id', false)));
            }
            if( $this->http->has('cse_email_report')){
                $module = array_merge( $module, array( 'cse_email_report' => $this->http->get('cse_email_report', false)));
            }
            if( $this->http->has('checking_limit')){
                $module = array_merge( $module, array( 'checking_limit' => $this->http->get('checking_limit', false)));
            }
            if( $this->http->has('browser_key')){
                $module = array_merge( $module, array( 'browser_key' => $this->http->get('browser_key', false)));
            }
            
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_google_settings',
                'option_value' => $module,
                'json' => true
            ));
            
            
            
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Settings has been updated successfully.', SR_TEXTDOMAIN ),
            );
            if( ! $this->http->has('google_country')){ // for the 1st step
                $json_data = array(
                    'type' => 'success',
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'text' => __( 'Please click the authorize button and then setup the other settings from bellow.', SR_TEXTDOMAIN ),
                );
            }

            AjaxHelpers::output_ajax_response( $json_data );
       }    
    }
    
    /**
     * Init Google settings
     */
    private function init_google_settings(){
        $this->google_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) ); 
    }

        /**
     * Site submission to google
     */
    public function google_site_submission(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-website-verification', $_aios_nonce, true );

        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        $site_url = $this->http->get('website_url', false); 
        
        $GoogleSettings = new GoogleSettings( $this->http );
        $GoogleSettings->populate_access_token();
        if(empty($GoogleSettings->client)){
            _e( 'Google Client Can\'t be Set! Please try again later.', SR_TEXTDOMAIN ); exit;
        }
        
        //submit website 
        $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';
        try{
            $ret = $webmaster->sites->add($site_url);
        } catch (\Google_Service_Exception $ex) {
            echo $ex->getErrors()[0]['message'];
            exit;
        }

        // get verification meta
        $site = new \Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequestSite();
        $site->setIdentifier($site_url);
        $site->setType('SITE');

        $request = new \Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequest();
        $request->setSite($site);
        $request->setVerificationMethod('META');

        $service = new \Google_Service_SiteVerification($GoogleSettings->client);
        $webResource = $service->webResource;
        $result = $webResource->getToken($request);
        
        if( isset($result->token)){
            
            //get content from the tag
            $content = '';
            preg_match('/content="([^"]+)"/', $result->token, $matches);
            if( is_array( $matches )){
                $content = trim($matches[1]);
            }
            
            $get_webmaster_data = CsQuery::Cs_Get_Option(array(
                'option_name' => 'aios_webmaster_verify_meta',
                'json' => true,
                'json_array' => true,
            ));
            $get_webmaster_data['google'] = $content;
            
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_webmaster_verify_meta',
                'option_value' => $get_webmaster_data,
                'json' => true
            ));
            
            //delete previous status
            $get_webmaster_status = CsQuery::Cs_Get_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'json' => true,
                'json_array' => true,
            ));
            $get_webmaster_status['google'] = '';
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'option_value' => $get_webmaster_status,
                'json' => true
            ));
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Site has been submitted to Google successfully.', SR_TEXTDOMAIN ),
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Google webmaster site verification
     */
    public function google_site_verification(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-website-verification', $ajax_nonce, true );
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        if( $this->http->has('_additional_data')){
            $data_arr = $this->http->get('_additional_data', false);
            
            $GoogleSettings = new GoogleSettings( $this->http );
            $GoogleSettings->populate_access_token();
            if(empty($GoogleSettings->client)){
                echo sprintf( __( 'Google app authentication data missing! Please re-auth. from: <a href="%s">Google Settings</a>. ', SR_TEXTDOMAIN ),  CommonText::common_text()['base_url']. '&tab=GoogleSettings'); 
                exit;
            }
            
            $site = new \Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite();
            $site->setIdentifier($data_arr['site_url']);
            $site->setType('SITE');

            $request = new \Google_Service_SiteVerification_SiteVerificationWebResourceResource();
            $request->setSite($site);

            $service = new \Google_Service_SiteVerification($GoogleSettings->client);
            $webResource = $service->webResource;
            try{
                $result = $webResource->insert('META',$request);
            } catch (\Google_Service_Exception $ex) {
                echo __( 'Google Response: ', SR_TEXTDOMAIN ). $ex->getErrors()[0]['message'];
                exit;
            }

            //update status to success
            $get_webmaster_status = CsQuery::Cs_Get_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'json' => true,
                'json_array' => true,
            ));
            $get_webmaster_status['google'] = 'success';
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_webmaster_verify_status',
                'option_value' => $get_webmaster_status,
                'json' => true
            ));
            
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Your website has been successfully verified by Google webmaster tool.', SR_TEXTDOMAIN ),
            );
            
        }else{
             $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }

    /**
     * Site maps submission to google
     */
    public function google_sitemaps_submission(){
        
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-sitemap-submission', $_aios_nonce, true );
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        $site_url = $this->http->get('website_url', false); 
        $feed_path = $this->http->get('feed_path', false); 
        
        $GoogleSettings = new GoogleSettings( $this->http );
        if(empty($GoogleSettings->client)){
            echo sprintf( __( 'Google app authentication data missing! Please re-auth. from: <a href="%s">Google Settings</a>. ', SR_TEXTDOMAIN ),  CommonText::common_text()['base_url']. '&tab=GoogleSettings'); 
            exit;
        }
        $GoogleSettings->populate_access_token();
        $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';
        
        try{
            $webmaster->sitemaps->submit($site_url, $feed_path);
        } catch (\Google_Service_Exception $ex) {
            echo $ex->getErrors()[0]['message'];
            exit;
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Site maps has been submitted to Google successfully.', SR_TEXTDOMAIN ),
        );
        AjaxHelpers::output_ajax_response( $json_data );
        
    }
    
    /**
     * Delete Site Maps
     */
    public function google_sitemaps_manage(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-sitemap-manage', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $maps = $this->http->get('_item_id', false);
            
            $GoogleSettings = new GoogleSettings( $this->http );
            if(empty($GoogleSettings->client)){
                echo sprintf( __( 'Google app authentication data missing! Please re-auth. from: <a href="%s">Google Settings</a>. ', SR_TEXTDOMAIN ),  CommonText::common_text()['base_url']. '&tab=GoogleSettings'); 
                exit;
            }
            $GoogleSettings->populate_access_token();
            $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';

            try{
                foreach($maps as $map){
                    $webmaster->sitemaps->delete($this->site_url, $map);
                }
                
            } catch (\Google_Service_Exception $ex) {
                echo $ex->getErrors()[0]['message'];
                exit;
            }
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Site maps has been deleted successfully.', SR_TEXTDOMAIN ),
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
    }
    
    /**
     * Mark as fixed
     * 
     * @global \CsSeoMegaBundlePack\Models\SocialAnalytics\type $wpdb
     * @return type
     */
    public function google_crawlers_manage(){
        global $wpdb;
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-crawler-errors', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $_item_id = $this->http->get('_item_id', false);
            $id_string = implode(',', $_item_id); 
            $rows = CsQuery::Cs_Get_Results(array(
                'select' => '*',
                'from' => $wpdb->prefix . 'aios_crawler_errors',
                'where' => "id in({$id_string})"
            ));
                
            $GoogleSettings = new GoogleSettings( $this->http );
            if(empty($GoogleSettings->client)){
                echo 'Google APPs not set. Please go to setting section and setup the required information.'; exit;
            }

            $GoogleSettings->populate_access_token();
            $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';
            try{
                foreach($rows as $row){
                    $webmaster->urlcrawlerrorssamples->markAsFixed($this->site_url, $row->url, $row->category, $row->platform);
                    CsQuery::Cs_Delete(array(
                        'table' => 'aios_crawler_errors',
                        'where' => array(
                            'id' => $row->id
                        )
                    ));
                }
            } catch (\Google_Service_Exception $ex) {
                echo $ex->getErrors()[0]['message']; exit;
            }    
                
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Successfully marked as fixed.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }

    
    /**
     * Get Existing Site Maps
     */
    public function get_crawler_errors_list( $force_check = false){
        global $AiosGooAppToken;
        
        $last_check = CsQuery::CS_Get_Option(array(
            'option_name' => 'aios_crawler_error_lastchecked'
        ));
        
        //if already checked today and not forced to check again return 
        if( date('Y-m-d') === $last_check && $force_check === false ){ 
            return false;
        }
        
        if(empty($AiosGooAppToken['auth_status'])){
            return array( 'error' => $AiosGooAppToken['auth_error_msg']);
        }
        
        $GoogleSettings = new GoogleSettings( $this->http );
        $GoogleSettings->populate_access_token();
        $webmaster = isset($GoogleSettings->client) ?  new \Google_Service_Webmasters( $GoogleSettings->client ) : '';
        
        $data = array();
        $error = array();
        try{
            foreach($this->platform() as $platform){
                foreach($this->category() as $category){
                    $result = $webmaster->urlcrawlerrorssamples->listUrlcrawlerrorssamples($this->site_url, $category, $platform);
                    if($result){
                        foreach( $result['urlCrawlErrorSample'] as $urls){
                            $data[] = array(
                                'url' => $urls['pageUrl'],
                                'linked_from' => empty($urls['urlDetails']['linkedFromUrls']) ? '' : implode(',',$urls['urlDetails']['linkedFromUrls']),
                                'error_type' => $category,
                                'platform' => $platform,
                                'category' => $category,
                                'first_seen' => date('Y-m-d', strtotime($urls['first_detected'])),
                                'last_crawled' => date('Y-m-d', strtotime($urls['last_crawled']))
                            );
                        }
                    }
                }
            }
        } catch (\Google_Service_Exception $ex) {
            $error[] = array( 'error' => $ex->getErrors()[0]['message']);
        }
        
        CsQuery::CS_Update_Option(array(
            'option_name' => 'aios_crawler_error_lastchecked',
            'option_value' => date('Y-m-d')
        ));
        
        return empty($error) ? $data : $error;
    }
    
    /**
     * Instantly check for errors
     */
    public function crawler_errors_instant_check(){
        $result = $this->get_crawler_errors_list();
        if( !isset($result['error']) && is_array($result)){
            CsQuery::Cs_Truncate('aios_crawler_errors'); // truncate old data
            foreach($result as $item){
                CsQuery::Cs_Insert(array(
                    'table' => 'aios_crawler_errors',
                    'insert_data' => array(
                        'url' => isset($item['url']) ? $item['url'] : '',
                        'linked_from' => isset($item['linked_from']) ? $item['linked_from'] : '',
                        'error_type' => isset( $item['error_type'] ) ? $item['error_type'] : '',
                        'platform' => isset( $item['platform'] ) ? $item['platform'] : '',
                        'category' => isset( $item['category'] ) ? $item['category'] : '',
                        'first_seen' => isset($item['first_seen']) ? $item['first_seen'] : '',
                        'last_crawled' => isset($item['last_crawled']) ? $item['last_crawled']: ''
                    ),
                ));
            }
        }
    }

    /**
     * Category
     * 
     * @return type
     */
    public function category(){
        return array(
            'authPermissions', 'notFollowed', 'notFound', 'other', 'serverError', 'soft404'
        );
    }
    
    /**
     * Platform
     * @return type
     */
    public function platform(){
        return array(
            'smartphoneOnly', 'web'
        );
    }

    /**
     * Cron Job for check crawler errors
     */
    public function check_crawler_errors(){
        $result = $this->get_crawler_errors_list( true );
        $new_count = 0;
        $new_url = array();
        if($result){
            foreach($result as $item){
                $check_exists = CsQuery::Cs_Count(array(
                    'table' => 'aios_crawler_errors',
                    'where' => " url = '{$item['url']}' and error_type = '{$item['error_type']}'"
                ));
                if( $check_exists === 0 ){
                    CsQuery::Cs_Insert(array(
                        'table' => 'aios_crawler_errors',
                        'insert_data' => array(
                            'url' => $item['url'],
                            'linked_from' => $item['linked_from'],
                            'error_type' => $item['error_type'],
                            'platform' => $item['platform'],
                            'category' => $item['category'],
                            'first_seen' => $item['first_seen']),
                            'last_crawled' => $item['last_crawled']
                    ));
                    if($new_count <=10){
                        $new_url[] = $this->site_url.'/'.$item['url'];
                    }
                    $new_count++;
                }    
            }
        }
        
        //email log
//        $this->google_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );
        if(isset($this->google_settings->send_report) && $this->google_settings->send_report == 'on'){
            $current_url = admin_url('admin.php?page=cs-social-analytics&tab=GoogleCrawlerError');
            $errors_link = empty($new_url) ? '' : implode('<br>', $new_url);
            $msg = __( '%1$s Total error found: %3$s %2$s <br><br>', SR_TEXTDOMAIN );
            if(!empty($new_url)){
                $msg .= $errors_link;
                $msg .= "<br><br> <a href='".$current_url."'>".__( 'Click here to manage all errors', SR_TEXTDOMAIN )."</a>";
            }else{
                $msg .= __( '%1$s Congratulation! %2$s No new error found.', SR_TEXTDOMAIN );
            }
            
            $data = array(
                    'section_title' => __( 'Crawler Errors Report', SR_TEXTDOMAIN ),
                    'message' => sprintf($msg, '<b>', '</b>', $new_count),
                    'log_type' => 1,
                    'created_on' => date('Y-m-d H:i:s')
                );
            $check_exists = CsQuery::Cs_Count(array(
                    'table' => 'aios_email_msg',
                    'where' => " log_type = 1 "
                ));
            
            if($check_exists === 0 ){
                CsQuery::Cs_Insert(array(
                    'table' => 'aios_email_msg',
                    'insert_data'=> $data
                ));
            }else{
                CsQuery::Cs_update(array(
                    'table' => 'aios_email_msg',
                    'update_data'=> $data,
                    'update_condition'=> array('log_type' => 1)
                ));
            }
        }
    }
    
    /**
     * Get index pages
     */
    public function check_indexed_pages( $true = false ){
        $n = 0;
        $new_url = array();
//        $this->google_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );
        $modified_url = str_replace( array('http:','https:','www'), '', $this->site_url);
        try{
            $limit = isset($this->google_settings->checking_limit) ? $this->google_settings->checking_limit : 100;
            $indexed_pages = Google::getSerps("site:$modified_url", $limit ); 
        }catch (\AiosSerpException $e) {
            error_log( 'Error: '. $e->getMessage());
        }
        
        if($indexed_pages){
            foreach($indexed_pages as $page){
                if(!empty($page['url'])){
                    $check_exists = CsQuery::Cs_Count(array(
                        'table' => 'aios_se_indexed_pages',
                        'where' => " url = '{$page['url']}' and se_type = 1"
                    ));
                    if( $check_exists === 0 ){
                        CsQuery::Cs_Insert(array(
                            'table' => 'aios_se_indexed_pages',
                            'insert_data' => array(
                                'url' => $page['url'],
                                'page_title' => $page['headline'],
                                'visit_count' => 0,
                                'se_type' => 1,
                                'created_date' => date('Y-m-d')
                            )
                        ));

                        if($n <=10){
                            $new_url[] = $page['url'];
                        }
                        $n++;
                    }
                }
            }
        }
        
        //email log
        if(isset($this->google_settings->cse_email_report) && $this->google_settings->cse_email_report == 'on'){
            $current_url = admin_url('admin.php?page=cs-social-analytics&tab=GoogleIndexMonitor');
            $errors_link = empty($new_url) ? '' : implode('<br>', $new_url);
            $msg = __( '%1$s Total index found: %3$s %2$s <br><br>', SR_TEXTDOMAIN );
            if(!empty($new_url)){
                $msg .= $errors_link;
            }else{
                $msg .= __( '%1$s ops! %2$s No new index found.', SR_TEXTDOMAIN );
            }
            $msg .= "<br><br> <a href='".$current_url."'>".__( 'Click here view all', SR_TEXTDOMAIN )."</a>";
            
            $data = array(
                    'section_title' => __( 'Google Indexing Report', SR_TEXTDOMAIN ),
                    'message' => sprintf($msg, '<b>', '</b>', $n),
                    'log_type' => 2,
                    'created_on' => date('Y-m-d H:i:s')
                );
            $check_exists = CsQuery::Cs_Count(array(
                    'table' => 'aios_email_msg',
                    'where' => " log_type = 2 "
                ));
            
            if($check_exists === 0 ){
                CsQuery::Cs_Insert(array(
                    'table' => 'aios_email_msg',
                    'insert_data'=> $data
                ));
            }else{
                CsQuery::Cs_update(array(
                    'table' => 'aios_email_msg',
                    'update_data'=> $data,
                    'update_condition'=> array('log_type' => 2)
                ));
            }
        }
    }

    /**
     * Delete index row
     */
    public function google_index_delete(){
        global $wpdb;
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-manage-index-pages', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $_item_id = $this->http->get('_item_id', false);
            $id_string = implode(',', $_item_id);
            
            CsQuery::Cs_Delete_In(array(
                'table' => 'aios_se_indexed_pages',
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => $id_string
                )
            ));
            
               $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Row has been deleted successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Google URL Shortner
     */
    public function google_url_shortner(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-url-shortner', $_aios_nonce, true );
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        if($this->http->has('long_url')){
            $long_url = $this->http->get('long_url', false); 
            $GoogleSettings = new GoogleSettings( $this->http );
            if(empty($GoogleSettings->client)){
                echo 'Google APPs not set. Please go to setting section and setup the required information.';
            }
            $GoogleSettings->populate_access_token();
            
            $urlShortner = isset($GoogleSettings->client) ?  new \Google_Service_Urlshortener( $GoogleSettings->client ) : '';
            $url = new \Google_Service_Urlshortener_Url();

            try{
                  $url->longUrl = $long_url;
                  $short = $urlShortner->url->insert($url);
                  if( isset($short->id)){
                      $check_exists = CsQuery::Cs_Count(array(
                            'table' => 'aios_google_shorteners_urls',
                            'where' => " short_url = '{$short->id}' "
                        ));
                        if($check_exists === 0 ){
                            CsQuery::Cs_Insert(array(
                                'table' => 'aios_google_shorteners_urls',
                                'insert_data'=> array(
                                    'long_url' => trim($short->longUrl),
                                    'short_url' => trim($short->id),
                                    'analytics' => 0,
                                    'created_on' => date('Y-m-d H:i:s')
                                )
                            ));
                        }
                    $json_data = array(
                        'type' => 'success',
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'text' => __( 'URL has been shortened successfully.', SR_TEXTDOMAIN ),
                    );
                  }
                  
            } catch (\Google_Service_Exception $ex) {
                echo $ex->getErrors()[0]['message'];
            }
        }else{
              $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Delete index row
     */
    public function google_shortnedurl_delete(){
        global $wpdb;
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-url-shortner', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $_item_id = $this->http->get('_item_id', false);
            $id_string = implode(',', $_item_id);
            
            CsQuery::Cs_Delete_In(array(
                'table' => 'aios_google_shorteners_urls',
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => $id_string
                )
            ));
            
               $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Item has been deleted successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * 
     * @global \CsSeoMegaBundlePack\Models\SocialAnalytics\type $wpdb
     * @return typeGet pagepsed
     */
    public function googlePageSpeedTest(){
        global $wpdb;
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-pagespeed', $_aios_nonce, true );
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        if($this->http->has('test_url')){
            $test_url = $this->http->get('test_url', false); 
            
            $GoogleSettings = new GoogleSettings( $this->http );
            if(empty($GoogleSettings->client)){
                echo 'Google APPs not set. Please go to setting section and setup the required information.';
            }
            $GoogleSettings->populate_access_token();
            $PST = isset($GoogleSettings->client) ?  new \Google_Service_Pagespeedonline( $GoogleSettings->client ) : '';
            try{
               $desktop =  $PST->pagespeedapi->runpagespeed($test_url, array('strategy' => 'desktop', 'screenshot' => true));
               $mobile =  $PST->pagespeedapi->runpagespeed($test_url, array('strategy' => 'desktop', 'screenshot' => true));
               
               $desktop_data = $this->get_pagespeed_data( $desktop );
               $mobile_data = $this->get_pagespeed_data( $mobile );
               
               $data = array(
                   'url' => $test_url,
                   'desktop_score' => isset($desktop['ruleGroups']['SPEED']['score']) ? $desktop['ruleGroups']['SPEED']['score'] : 0,
                   'mobile_score' => isset($mobile['ruleGroups']['SPEED']['score']) ? $mobile['ruleGroups']['SPEED']['score'] : 0,
                   'desktop_optimized' => json_encode($desktop_data['optimized']),
                   'desktop_need_optimize' => json_encode($desktop_data['need_optimization']),
                   'mobile_optimized' => json_encode($mobile_data['optimized']),
                   'mobile_need_optimize' => json_encode($mobile_data['need_optimization']),
                   'checked_on' => date('Y-m-d H:i:s')
               );
               
               $check_exists = CsQuery::Cs_Get_Results(array(
                    'select' => 'id',
                    'from' => $wpdb->prefix . 'aios_pagespeed_insights',
                    'where' => " url = '{$test_url}' ",
                    'query_type' => 'get_row'
                ));
                if( isset($check_exists->id) && $check_exists->id > 0 ){
                    CsQuery::Cs_Update(array(
                        'table' => 'aios_pagespeed_insights',
                        'update_data' => $data,
                        'update_condition' => array( 'url' => $test_url)
                    ));
                }else{
                    $id = CsQuery::Cs_Insert(array(
                        'table' => 'aios_pagespeed_insights',
                        'insert_data' => $data
                    ));
                }
                
                $redirect_id = isset($id) ? $id : $check_exists->id;
               
            } catch (\Google_Service_Exception $ex) {
                return $ex->getErrors()[0]['message'];
            }
            
             $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Please wait a few seconds. Redirecting to detail page...', SR_TEXTDOMAIN ),
                'redirect_param' => "&id={$redirect_id}"
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
        
    }
    
    /**
     * Google PageSpeed Insights Data Calculation
     * 
     * @param type $strategy_type
     * @return type
     */
    private function get_pagespeed_data( $strategy_type ){
        $result_need_optimized = array();
        $result_optimized = array();
            
        foreach($strategy_type['formattedResults']['ruleResults'] as $key => $item){
            if( $item['ruleImpact'] > 0 ){

                if( $key == 'LeverageBrowserCaching' ){
                    $link = isset($item['urlBlocks'][0]['header']['args'][0]['value']) ? $item['urlBlocks'][0]['header']['args'][0]['value'] : '';
                    $subhed = isset($item['urlBlocks'][0]['header']['format']) ? $item['urlBlocks'][0]['header']['format']: '';

                    $opUrls = '';
                    $urls = isset($item['urlBlocks'][0]['urls']) ? $item['urlBlocks'][0]['urls'] : '';
                    if($urls){
                        $opUrls = '<ul class="list-group">';
                        foreach($urls as $url){
                            $opUrls .= "<li class='list-group-item'>".str_replace( '{{URL}}', $url['result']['args'][0]['value'], $url['result']['format']) ."</li>";
                        }
                        $opUrls .= '</ul>';
                    }
                    $result_need_optimized = array_merge($result_need_optimized, array( $key => array(
                        'header' => $item['localizedRuleName'],
                        'summary' => $item['summary']['format'],
                        'summary2' => str_replace( array('{{BEGIN_LINK}}', '{{END_LINK}}'), array( "<a href='{$link}'>", '</a>') ,$subhed),
                        'content' =>   $opUrls      
                    )));
                }
                else if( $key == 'MainResourceServerResponseTime'){
                    $response_time = isset($item['urlBlocks'][0]['header']['args'][0]['value']) ? $item['urlBlocks'][0]['header']['args'][0]['value'] : '';
                    $link = isset($item['urlBlocks'][0]['header']['args'][1]['value']) ? $item['urlBlocks'][0]['header']['args'][1]['value'] : '';

                    $result_need_optimized = array_merge($result_need_optimized, array( $key => array(
                        'header' => $item['localizedRuleName'],
                        'content' => str_replace( array('{{BEGIN_LINK}}', '{{END_LINK}}', '{{RESPONSE_TIME}}'), array( "<a href='{$link}'>", '</a>', $response_time) ,$item['urlBlocks'][0]['header']['format'])
                    )));

                }
                else if( $key == 'MinifyCss' || $key == 'OptimizeImages'){
                    $opUrls = '';
                    $urls = isset($item['urlBlocks'][0]['urls']) ? $item['urlBlocks'][0]['urls'] : '';
                    if($urls){
                        $opUrls = '<ul class="list-group">';
                        foreach($urls as $url){
                            $opUrls .= "<li class='list-group-item'>".str_replace( array('{{URL}}', '{{SIZE_IN_BYTES}}', '{{PERCENTAGE}}'), array($url['result']['args'][0]['value'], $url['result']['args'][1]['value'], $url['result']['args'][2]['value']), $url['result']['format']) ."</li>";
                        }
                        $opUrls .= '</ul>';
                    }

                    $link = isset($item['urlBlocks'][0]['header']['args'][0]['value']) ? $item['urlBlocks'][0]['header']['args'][0]['value'] : '';
                    $size_in_bytes = isset($item['urlBlocks'][0]['header']['args'][1]['value']) ? $item['urlBlocks'][0]['header']['args'][1]['value'] : '';
                    $precentage = isset($item['urlBlocks'][0]['header']['args'][2]['value']) ? $item['urlBlocks'][0]['header']['args'][2]['value'] : '';
                    $result_need_optimized = array_merge($result_need_optimized, array( $key => array(
                        'header' => $item['localizedRuleName'],
                        'summary' => $item['summary']['format'],
                        'summary2' => str_replace( array('{{BEGIN_LINK}}', '{{END_LINK}}', '{{SIZE_IN_BYTES}}', '{{PERCENTAGE}}'), array( "<a href='{$link}'>", '</a>', $size_in_bytes,$precentage) ,$item['urlBlocks'][0]['header']['format']),
                        'content' => $opUrls
                    )));

                }else if( $key == 'MinimizeRenderBlockingResources'){
                    $link = $item['urlBlocks'][1]['header']['arg'][0]['value'];

                    $opUrls = '';
                    $urls = isset($item['urlBlocks']['urls']) ? $item['urlBlocks']['urls'] : '';
                    if($urls){
                        $opUrls = '<ul class="list-group">';
                        foreach($urls as $url){
                            $opUrls .= "<li class='list-group-item'>".str_replace( '{{URL}}', $url['result']['args'][0]['value'], $url['result']['format']) ."</li>";
                        }
                        $opUrls .= '</ul>';
                    }
                    $result_need_optimized = array_merge($result_need_optimized, array( $key => array(
                        'header' => $item['localizedRuleName'],
                        'summary' => str_replace('{{NUM_CSS}}', $item['summary']['arg'][0]['value'], $item['summary']['format']),
                        'summary2' => $item['urlBlocks'][0]['header']['format'],
                        'summary3' => str_replace( array('{{BEGIN_LINK}}', '{{END_LINK}}'), array( "<a href='{$link}'>", '</a>') ,$item['urlBlocks'][1]['header']['format']),
                        'content' =>   $opUrls      
                    )));

                }
            }
            else { //optimized result
                $link = $item['summary']['args'][0]['value'];
                $result_optimized = array_merge($result_optimized, array( $key => array(
                    'header' => $item['localizedRuleName'],
                    'summary' => str_replace( array('{{BEGIN_LINK}}', '{{END_LINK}}'), array( "<a href='{$link}'>", '</a>') ,$item['summary']['format'])
                )));

            }

        }
        $result_optimized = array_merge($result_optimized, array( 'screenshot' => array(
            'summary' => $strategy_type['screenshot']['data']
        )));
        
        return array(
            'optimized' => $result_optimized,
            'need_optimization' => $result_need_optimized
        );
    }
    
    /**
     * Delete index row
     */
    public function deleteGooglePageSpeedTest(){
        global $wpdb;
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-pagespeed', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $_item_id = $this->http->get('_item_id', false);
            $id_string = implode(',', $_item_id);
            
            CsQuery::Cs_Delete_In(array(
                'table' => 'aios_pagespeed_insights',
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => $id_string
                )
            ));
            
               $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Item has been deleted successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Local Seo Listings
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function google_local_seo_listing(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-google-local-seo', $_aios_nonce, true );
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            _e( 'Your internet connection has down!', SR_TEXTDOMAIN ); exit;
        }
        
        $module = array();
        //dynamically get form values
        if( $this->http->has('inputs')){
            $inputs = explode(',',$this->http->get('inputs', false));
            foreach($inputs as $input){
                if(!empty($input)){
                    $module = array_merge( $module, array( $input => $this->http->get( $input, false)));
                }
            }
        }
        
        CsQuery::Cs_Update_Option(array(
            'option_name' => 'aios_oogle_localseo',
            'option_value' => $module,
            'json' => true
        ));

        $address = isset($module['street_address']) ? $module['street_address'] : '';
        $address .= isset($module['city_name']) ? $module['city_name'] . ',' : '';
        $address .= isset($module['state_name']) ? $module['state_name'] : '';
        $address .= isset($module['zipcode']) ? $module['zipcode'] : '';
        $address .= isset($module['country']) ? $module['country'] : '';
        
        $client = new \Goutte\Client();
        $encodeaddress = str_replace(" ", "+", $address);
        $crawler = @$client->request( 'GET', "https://maps.googleapis.com/maps/api/geocode/json?address={$encodeaddress}" ); 
        $get_latlang = empty($client->getResponse()->getContent()) ? '' : json_decode($client->getResponse()->getContent());
        
        if($get_latlang->status=='OK'){
            $lat = $get_latlang->results[0]->geometry->location->lat;
            $lng = $get_latlang->results[0]->geometry->location->lng;
        }else{
            echo 'Something went wrong! Please try again later'; exit;
        }
        
        $params = array(
            'location' => array(
                'lat' => $lat,
                'lng' => $lng
            ),
            'name' => 'Bdjobcafe',
            'phone_number' => isset($module['phone_number']) ? $module['phone_number'] : '',
            'address' => $address,
            'website' => $this->site_url,
            'language' => 'en-AU',
            'types' => $this->http->get('type', false)
        );
        
//        $this->google_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) ); 
        $key = isset($this->google_settings->browser_key) ? $this->google_settings->browser_key : '';
        if( $key ){
            $client = new \Goutte\Client();
            $crawler = $client->request('POST', "https://maps.googleapis.com/maps/api/place/add/json?key={$key}", [], [], array('HTTP_CONTENT_TYPE' => 'application/json'), json_encode($params));
            
            $response = json_decode($client->getResponse()->getContent(), true);
            if( $response['status'] == 'OK' ){
                CsQuery::Cs_Update_Option(array(
                    'option_name' => 'aios_google_placeid',
                    'option_value' => $response,
                    'json' => true
                ));
            }else{
                echo $response['status']; exit;
            }
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your business address has been added successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Remove urls from search engines
     */
    public function remove_urls(){
        global $wpdb;
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-search-url-removers', $_aios_nonce, true );
        
        //check htaccess writing permission
        $htaccess = new CsHtaccesCreator();
        if( $htaccess->check_htaccess_permission() !== 'true' ){
            echo $htaccess->check_htaccess_permission(); exit;
        }
        
        if( $this->http->has('single_url') ){
            $get_url = CsQuery::check_evil_script($this->http->get('single_url', false));
            if( GeneralHelpers::Cs_Is_Url( $get_url)){
                $this->remove_url_check_insert( $get_url );
            }
        }
        else if( $_FILES['multiple_url']['tmp_name'] !== false ){
            $get_multiple_url = $_FILES['multiple_url']['tmp_name'];
            
            $fh = fopen($get_multiple_url,'r');
            while ($url = fgets($fh)) {
                $url = GeneralHelpers::standardize_whitespace($url);
                if( GeneralHelpers::Cs_Is_Url( CsQuery::check_evil_script( $url ) ) ){
                    $this->remove_url_check_insert( $url );
                }
            }
            fclose($fh);
        }
            
        $get_urls = CsQuery::Cs_Get_Results(array(
            'select' => 'url',
            'from' => $wpdb->prefix . 'aios_remove_urls',
            'where' => 'status = 1'
        ));
        $htaccess->remove_urls( $get_urls, $this->site_url );
        if( isset($get_urls[0]->url) && !empty($get_urls[0]->url) ){
            //Cron schedule
            CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => 'daily', 'hook' => 'aios_check_urls_removed'));
        }
        
             
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your url has been added successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
            
            
    }
    
    /**
     * check and insert new url
     */
    private function remove_url_check_insert( $url ){
        global $wpdb;
        if( empty($url)) return false;
        
        $check_exists = CsQuery::Cs_Get_Results(array(
            'select' => 'id',
            'from' => $wpdb->prefix . 'aios_remove_urls',
            'where' => " url = '{$url}'",
            'query_type' => 'get_var'
        ));
        if( $check_exists === false ){
            CsQuery::Cs_Insert(array(
                'table' => 'aios_remove_urls',
                'insert_data' => array(
                    'url' => $url,
                    'status' => 1,
                    'submit_on' => date('Y-m-d H:i:s'),
                )
            ));
        }
        return true;
    }

        /**
     * Delete url from remover list
     * 
     * @global \CsSeoMegaBundlePack\Models\SocialAnalytics\type $wpdb
     */
    public function delete_remove_urls(){
        global $wpdb;
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-search-url-removers', $ajax_nonce, true );
        
        if($this->http->has('_item_id')){
            $_item_id = $this->http->get('_item_id', false);
            $id_string = implode(',', $_item_id);
            
            CsQuery::Cs_Delete_In(array(
                'table' => 'aios_remove_urls',
                'where' => array(
                    'field_name' => 'id',
                    'field_value' => $id_string
                )
            ));
            
            $get_urls = CsQuery::Cs_Get_Results(array(
                'select' => 'url',
                'from' => $wpdb->prefix . 'aios_remove_urls',
                'where' => 'status = 1'
            ));
            (new CsHtaccesCreator())->remove_urls( $get_urls, $this->site_url );
            if( count($get_urls) === 0 ){
                //Remove Cron schedule
                CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_check_urls_removed'));
            }
            
           $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Item has been deleted successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later.', SR_TEXTDOMAIN ),
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
