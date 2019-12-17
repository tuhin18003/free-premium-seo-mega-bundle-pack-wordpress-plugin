<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Settings
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleSettings {
    
    /**
     * Hold Google Client
     *
     * @var ojbect
     */
    public $client;
    
    /**
     * Hold Access Token
     *
     * @var ojbect
     */
    public $access_token;

    /**
     * Hold Options Data
     *
     * @var obj 
     */
    private $settings;
    
    /**
     * Hold Http request
     *
     * @var type object
     */
    protected $http;
    
    public function __construct( $Http ) {
        $this->http = $Http;
        /**
         * init google
         */
        $this->int_google();
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb;
        
        $notificatoin = '';
        if( $this->http->has('code')){
            $this->populate_access_token();
            $profiles = $this->get_google_profiles();
            $notificatoin =  __( '%s Success! %s Please select the profile ID from bellow and save settings.', SR_TEXTDOMAIN );
            $notificatoin = sprintf($notificatoin, '<strong>', '</strong>' );
        }else {
            $profiles = (array)CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_anlytics_profiles', 'json' => true ) );   
        }
        
        $data = array_merge( CommonText::form_element( 'google_settings', 'aios-google-settings' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Settings', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Google app authorization & settings', SR_TEXTDOMAIN ),
           'notificatoin' => $notificatoin,
           
           'label_app_id' => __( 'APP Client ID*', SR_TEXTDOMAIN ),
           'placeholder_app_id' => __( 'Enter google app client id', SR_TEXTDOMAIN ),
           'des_app_id' => __( 'From the Google API console', SR_TEXTDOMAIN ),
           'label_app_secret' => __( 'APP Client Secret*', SR_TEXTDOMAIN ),
           'placeholder_app_secret' => __( 'Enter google app client secret', SR_TEXTDOMAIN ),
           'des_app_secret' => __( 'From the Google API console', SR_TEXTDOMAIN ),
           'label_app_redirect_url' => __( 'Authorized redirect URI', SR_TEXTDOMAIN ),
           'des_app_redirect_url' => __( 'Use This URL In Authorized redirect URIs Section In Your APP.', SR_TEXTDOMAIN ),
           'des_app_authorize' => __( 'Clik this button to authorize your app & get profile ids.', SR_TEXTDOMAIN ),
           'app_redirect_url' => $common_text['base_url'].'&tab=GoogleSettings',
           'dialog_url' => $this->get_auth_url(),
           'label_profile_id' => __( 'Analytics Profile ID*', SR_TEXTDOMAIN ),
           'label_select_default' => '==============Select Website Profile ID==============',
           'get_options' => $this->settings,
           'des_profile_id' => __( 'Select your website profile ID. If you don\'t see anything, please click the authorize button to get profile ID\'s.', SR_TEXTDOMAIN ),
           'profiles' => $profiles,
            
            
           'label_section_title' => __( 'Following setting will be applied when the plugin will automatically add Google analytics javascript code on all pages.', SR_TEXTDOMAIN ),
           'label_app_authorize' => __( 'Authorize The App', SR_TEXTDOMAIN ),
           'label_annon_ip' => __( 'Anonymize Analytics IP', SR_TEXTDOMAIN ),
           'des_annon_ip' => __( 'if you select yes, the visitor tracking script will use Google Analytics _anonymizelp function.', SR_TEXTDOMAIN ),
           'label_annon_ip_options' => array(
               __( 'No', SR_TEXTDOMAIN ),
               __( 'Yes', SR_TEXTDOMAIN ),
           ),
           'label_google_analytics_id' => __( 'Google Analytics ID', SR_TEXTDOMAIN ),
           'placeholder_google_analytics_id' => __( 'Enter Google Analytics ID', SR_TEXTDOMAIN ),
           'des_google_analytics_id' => __( 'This Google analytics ID will be use in the tracking script.', SR_TEXTDOMAIN ),
           'label_auto_add_tracking_code' => __( 'Automatically add tracking code', SR_TEXTDOMAIN ),
           'des_auto_add_tracking_code' => __( 'Check this to automatically add google user tracking code to all of your website pages.', SR_TEXTDOMAIN ),
            
           'label_webmaster_section_title' => __( 'Google Webmaster Tool Settings', SR_TEXTDOMAIN ),
           'label_cron_schedule' => __( 'Check Google Crawler Error', SR_TEXTDOMAIN ),
           'des_cron_schedule' => __( 'Automatically check crawler error in this selected time period', SR_TEXTDOMAIN ),
           'label_stop_cron' => __( 'Stop Automatic Checkup', SR_TEXTDOMAIN ),
           'label_select_default' => '==============Select Cron Job Schedule==============',
           'select_schedule' => wp_get_schedules(), 
           'label_google_webmaster_tool' => __( 'Google Website Verificatoin Code', SR_TEXTDOMAIN ),
           'placeholder_google_webmaster_tool' => __( 'Enter google webmaster verificatoin code', SR_TEXTDOMAIN ),
           'label_email_send' => __( 'Send Report', SR_TEXTDOMAIN ),
           'des_email_send' => __( 'Check this box to get an email with crawler report.', SR_TEXTDOMAIN ),
            
           'label_cse_section_title' => __( 'Google Custom Search Settings', SR_TEXTDOMAIN ),
           'google_domains' => GeneralHelpers::google_domains(),
           'label_google_country' => __( 'Google Location', SR_TEXTDOMAIN ),
           'label_all_location' => __( 'All Location', SR_TEXTDOMAIN ),
           'des_google_country' => __( 'Select Google Location', SR_TEXTDOMAIN ),
           'label_custom_search_id' => __( 'Google Custom Search ID', SR_TEXTDOMAIN ),
           'placeholder_custom_search_id' => __( 'Enter Google custom search id', SR_TEXTDOMAIN ),
           'des_custom_search_id' => __( 'Google Custom Search ID. Create and get your CSE ID from here <a href="https://cse.google.com/cse/" target="_blank">https://cse.google.com/cse/</a>', SR_TEXTDOMAIN ),
           'label_index_cron_schedule' => __( 'Check Google Indexed Pages', SR_TEXTDOMAIN ),
           'des_index_cron_schedule' => __( 'Automatically check Google indexed pages in this selected time period', SR_TEXTDOMAIN ),
           'des_cse_email_report' => __( 'Check this box to get an email with index pages report.', SR_TEXTDOMAIN ),
           'label_checking_limit' => __( 'Check Number Of Index', SR_TEXTDOMAIN ),
           'placeholder_checking_limit' => __( 'Enter the limit of search', SR_TEXTDOMAIN ),
           'des_checking_limit' => __( 'Enter The Limit of The Google Index Results you want to check. Not more than 1000.', SR_TEXTDOMAIN ),
           'label_local_seo_section_title' => __( 'Google Local Seo Settings.', SR_TEXTDOMAIN ),
           'label_browser_key' => __( 'Enter API Key', SR_TEXTDOMAIN ),
           'placeholder_browser_key' => __( 'Enter your api key', SR_TEXTDOMAIN ),
           'des_browser_key' => __( 'From the Google API console. If you don\'t have one, please go here <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a> to create one.', SR_TEXTDOMAIN ),
            
            
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           
           'panel_subtitle' => __( 'Basic Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Basic Google Application Creation Hints', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
                'Create a project in the Google APIs console from here <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>.',
                'Enable the APIs from the <a href="https://console.developers.google.com/apis/library" target="_blank">APIs library</a>.',
                'Following APIs Needs to be Enabled : <br> i. Analytics API <br> ii. Google Search Console API <br>iii. Google Site Verification API v1 <br> iv. Custom Search API <br> v. PageSpeed Insights API<br> vi. URL Shortener API <br> vii. Google Places API Web Service',
                'After enabling the api go to <a href="https://console.developers.google.com/apis/credentials" target="_blank">Credentials</a> > Credentials > Create credentials > Oauth Client ID',
                'Select "Web application" from application type',
                'Enter your application name',
                'On Authorized redirect URIs please enter the url from bellow of this page - "Authorized redirect URI" and save it.',
           )
           
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/Settings.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: '<?php echo commonText::common_text()['base_url'] . '&tab=GoogleSettings'; ?>'
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#google_settings" );
                           
                           $( "#profile_id").on("change", function(){
                               $("#analytics_id_for_track").val( $(this).val());
                           });
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    public function int_google(){
        $this->client = new \Google_Client();
        $this->settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );  
        
        if( isset($this->settings->app_id) && ! empty($this->settings->app_id)){
            $this->client->setClientId($this->settings->app_id);
            $this->client->setClientSecret($this->settings->app_secret);
            $scopes =" https://www.googleapis.com/auth/analytics https://www.googleapis.com/auth/webmasters https://www.googleapis.com/auth/webmasters.readonly https://www.googleapis.com/auth/siteverification https://www.googleapis.com/auth/siteverification.verify_only https://www.googleapis.com/auth/cse https://www.googleapis.com/auth/urlshortener https://www.googleapis.com/auth/plus.business.manage";
            $this->client->setScopes($scopes);
            $this->client->setAccessType('offline');
            $redirect = filter_var( commonText::common_text()['base_url'] . '&tab=GoogleSettings', FILTER_SANITIZE_URL);
            $this->client->setRedirectUri($redirect);
            $state = mt_rand();
            $this->client->setState($state);
            $this->client->setPrompt('consent');
            return $this->client;
        }
        return false;
    }

    /**
     * 
     * @return typeGet Authorize url
     */
    public function get_auth_url(){
        return isset($this->settings->app_id) && ! empty($this->settings->app_id) ? $this->client->createAuthUrl() : false;
    }

    /**
     * Populate Access Token
     * 
     * @return type
     */
    public function populate_access_token(){
        $token_data = '';
        if( $this->http->has('code')){
            $this->client->authenticate($this->http->get('code', false));
            $tokenSessionKey = $this->client->getAccessToken(); 
            $token_data = $tokenSessionKey;
            
            //
        }else{
            $get_token = (array)CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_token', 'json' => true ) ); 
            $this->client->setAccessToken($get_token);
            $token_data = $get_token;
            if( $this->client->isAccessTokenExpired() ){
                $this->client->refreshToken($get_token['refresh_token']);
                $token_data = $this->client->getAccessToken();
            }
        }
        
        $this->access_token = isset($token_data['access_token']) ? $token_data['access_token'] : '';
        
        CsQuery::Cs_Update_Option(array(
            'option_name' => 'aios_google_token',
            'option_value' => $token_data,
            'json' => true
        ));
        
        return empty($token_data) ? '' : $this->client->setAccessToken($token_data);
    }

    
    public function get_google_account(){
        $analytics = isset($this->client) ?  new \Google_Service_Analytics( $this->client ) : '';
        $man_accounts = $analytics->management_accounts->listManagementAccounts();
        $accounts = [];
        if( $man_accounts ){
            foreach ($man_accounts['items'] as $account) {
                $accounts[] = [ 'id' => $account['id'], 'name' => $account['name'] ];
            }
        }
        return $accounts;
    }
    
    public function get_google_properties( $account_id ){
        $analytics = isset($this->client) ?  new \Google_Service_Analytics( $this->client ) : '';
        $man_properties = $analytics->management_webproperties->listManagementWebproperties($account_id);
        $properties = [];
        if( $man_properties ){
            foreach ($man_properties['items'] as $property) {
                    $properties[] = [ 'id' => $property['id'], 'name' => $property['name'] ];
            }//foreach
        }
        return $properties;
    }
    
    public function get_google_profiles(){
        $analytics = isset($this->client) ?  new \Google_Service_Analytics( $this->client ) : '';
        $accounts = $this->get_google_account();
        $profiles = [];
        if( $accounts ){
            foreach($accounts as $account){
                $properties = $this->get_google_properties( $account['id'] );
                if($properties){
                    foreach($properties as $property){
                        $man_views = $analytics->management_profiles->listManagementProfiles( $account['id'], $property['id'] );
                        foreach ($man_views['items'] as $view) {
                                $profiles[] = [ 'id' => $view['id'], 'name' => $view['name'], 'webPropertyId' => $view['webPropertyId'], 'websiteUrl' => $view['websiteUrl'] ];
                        }//foreach
                    }
                }
            }
            
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_google_anlytics_profiles',
                'option_value' => $profiles,
                'json' => true
            ));
        }

        return $profiles;
    }

}
