<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Facebook;
/**
 * Settings
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class FbSettings {
    
    protected $http;
    protected $settings;
    protected $redirect_url;
    protected $page_list = [];


    public function __construct( $Http ) {
        $this->http = $Http;
        
        //initiate settings
        $this->init_settings();
    }
    
    public function load_page_builder( $common_text ){
        $notificatoin = '';
        if( $this->http->has('code')){
            // get the page name
            $this->get_the_page_list();
            $notificatoin =  __( '%s Success! %s Please select the page from bellow and save settings.', SR_TEXTDOMAIN );
            $notificatoin = sprintf($notificatoin, '<strong>', '</strong>' );
        }
        
        
        $data = array_merge( CommonText::form_element( 'fb_settings', 'aios-facebook-settings' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Settings', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Facebook app and other settings', SR_TEXTDOMAIN ),
           'notificatoin' => $notificatoin,
           'label_app_id' => __( 'Facebook APP ID*', SR_TEXTDOMAIN ),
           'placeholder_app_id' => __( 'Enter facebook app id', SR_TEXTDOMAIN ),
           'des_fb_app_id' => __( 'From the Facebook APP', SR_TEXTDOMAIN ),
           'label_app_secret' => __( 'Facebook APP Secret*', SR_TEXTDOMAIN ),
           'placeholder_app_secret' => __( 'Enter facebook app secret', SR_TEXTDOMAIN ),
           'des_fb_app_secret' => __( 'From the Facebook APP', SR_TEXTDOMAIN ),
           'fb_dialog_url' => $this->get_auth_url(),
           'des_app_authorize' => __( 'Clik this button to authorize your app & get Facebook pages list.', SR_TEXTDOMAIN ),
           
           'label_publish_to' => __( 'Post Publish to', SR_TEXTDOMAIN ),
           'des_publish_to' => __( 'Select your page. If you don\'t see anything, please click the authorize button to get your Facebook pages.', SR_TEXTDOMAIN ),
           'label_select_default' => '==============Select Facebook Page ==============',
            
           'label_cron_schedule' => __( 'Facebook Statistics Update', SR_TEXTDOMAIN ),
           'des_cron_schedule' => __( 'Automatically get update Facebook statistics in this selected time period', SR_TEXTDOMAIN ),
           'label_stop_cron' => __( 'Stop Automatic Update', SR_TEXTDOMAIN ),
           'label_cron_select_default' => '==============Select Cron Job Schedule==============',
           'select_schedule' => wp_get_schedules(), 
            
           'get_options' => $this->settings,
           
           'label_app_auth' => __( 'APP Authorization', SR_TEXTDOMAIN ),
           'label_app_token' => __( 'Authorize The App', SR_TEXTDOMAIN ),
            
           'fb_pages' => isset($this->page_list) && !empty($this->page_list) ? $this->page_list : (isset($this->settings->all_fb_pages) && !empty($this->settings->all_fb_pages) ? json_decode($this->settings->all_fb_pages, true) : ''),
           'all_fb_pages' => empty($this->page_list) ? '' : json_encode($this->page_list),
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           
           'panel_subtitle' => __( 'Basic Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Basic Facebook Application Creation Hints', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               'Create a app in the Facebook APP developers area from here <a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a>.',
               'Select <b>My Apps -> Add a New App</b>. Enter your App display name, Contact email then click the button "Create App ID"',
               'Click the "Dashboard" from left menu. Then from the page click button Choose Platform -> website',
               'On new page scroll down to the bottom.',
               "Use <b>'{$this->redirect_url}'</b> as site url. Click next. You are done. ",
               'From the top menu, select My App -> your app. You will redirect to your dashboard.',
               'Select your app ID & secret from there and enter that into this page & click the button "Save Settings"',
               'After saving you will see the "Authorise The App" button. Click that and authorize your app. Then follow the others settings and save it'
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/facebook/Settings.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: '<?php echo $this->redirect_url; ?>'
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#facebook_settings" );
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Init Settings
     * 
     * @return type
     */
    private function init_settings(){
        $this->redirect_url = CommonText::common_text()['base_url'] .'&tab=FbSettings'; 
        $this->settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_facebook_settings', 'json' => true ) );
    }

    /**
     * 
     * @return booleanGet the auth url
     */
    private function get_auth_url(){
        if( isset( $this->settings->fb_app_id ) && !empty( $this->settings->fb_app_id ) ){
            $encoded_url = urlencode($this->redirect_url);
            return "https://www.facebook.com/dialog/oauth?client_id={$this->settings->fb_app_id}&redirect_uri={$encoded_url}&scope=manage_pages%2Cpublish_pages%2Cread_insights&state=re-fb-0";
        }
        return false;
    }
    
    /**
     * 
     */
    private function get_the_page_list(){
        $page_list = array();
        if( $this->http->has('code')){
            $encoded_url = urlencode($this->redirect_url);
            $code = $this->http->get('code', false);
            $UserAccessToken = (object) wp_remote_get("https://graph.facebook.com/oauth/access_token?client_id={$this->settings->fb_app_id}&redirect_uri={$encoded_url}&client_secret={$this->settings->fb_app_secret}&code=$code");
            $response = $UserAccessToken->response['code'];
            if ($response == 200) {
                $shortUserTokenarr = json_decode($UserAccessToken->body);
                $shortToken = $shortUserTokenarr->access_token;
                $UserAccessTokens = (object) wp_remote_get("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id={$this->settings->fb_app_id}&client_secret={$this->settings->fb_app_secret}&fb_exchange_token=$shortToken");
                $longUserTokenarr = json_decode($UserAccessTokens->body);
                $longToken = $longUserTokenarr->access_token;
                $page_acces_tokens = (object) wp_remote_get("https://graph.facebook.com/me/accounts?access_token=$longToken");
                $page_token_json = json_decode($page_acces_tokens->body);
                if (count($page_token_json->data) > 0) {
                    for ($i = 0; $i < count($page_token_json->data); $i++) {
                        $data = $page_token_json->data[$i];
                        $this->page_list[$data->id] = array(
                            $data->name, $data->access_token
                        );
                    }
                }
            }
        }
//        pre_print($this->page_list);
    }
}
