<?php namespace CsSeoMegaBundlePack;
/**
 * Functions
 * 
 * @package Aios
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


use Herbert\Framework\Http;

/***********fielters***********/
include( CSMBP_BASE_DIR_PATH . '/BackEndActions/filters/BackEndFilters.php');
    
/***********Broken link checker Cron Job***********/
add_action( 'aios_check_links_cron', array( __NAMESPACE__  .  '\\Models\\BacklinkManager\\BackEndActionRequest\\BrokenLinkChecker\\BrokenLinkChecker', 'link_status_checker') );
add_action( 'aios_check_links_entire_db_cron', array( __NAMESPACE__  .  '\\Models\\BacklinkManager\\BackEndActionRequest\\BrokenLinkChecker\\BrokenLinkChecker', 'entire_db_sync') );

/***********Social Analytics***********/
add_action('aios_facebook_stats_cron', array( __NAMESPACE__  .  '\\Models\\SocialAnalytics\\FacebookActionHandler', 'get_facebook_stats'));
add_action('add_meta_boxes', function(){
    global $post_type;
    $http = new Http();
    $obj = new Library\MetaBoxes\CsPostMetaBoxes();
    return $obj->get_all_meta_boxes( $post_type );
});
add_action('save_post', function($post_type){
    $http = new Http();
    $obj = new Models\SocialAnalytics\FacebookActionHandler($http);
    $obj->facebook_settings_on_save_post();
    $sso = new Library\MetaBoxes\AllMetaBoxes\SocialMetaTagsOptions();
    $sso->on_save( $post_type );
    $seo = new Library\MetaBoxes\AllMetaBoxes\SeoMetaBoxes();
    $seo->on_save( $post_type );
    return true;
});

add_action('aiosFbAutoPubCron', function( $publish_on ){
    $http = new Http();
    $obj = new Models\SocialAnalytics\FacebookActionHandler($http);
    return $obj->auto_publish_queue( $publish_on );
});

add_action('aios_google_index_search_cron', function(){
    $http = new Http();
    $obj = new Models\SocialAnalytics\GoogleActionHandler($http);
    return $obj->check_indexed_pages();
});

add_action('aios_google_cron', function(){
    $http = new Http();
    $obj = new Models\SocialAnalytics\GoogleActionHandler($http);
    return $obj->check_crawler_errors();
});




/***********Keyword Cron Job***********/
add_action( 'aios_keyword_manager_cron', array( __NAMESPACE__  .  '\\Models\\KeywordManager\\KeywordHandler', 'aios_autoupdate_positions') );
add_action('csmbp_cron_getNewKeywordPosition', function( $k_ID ){
    $http = new Http();
    $obj = new Models\KeywordManager\KeywordHandler($http);
    return $obj->csmbp_get_new_keyword_pos( $k_ID );
});


use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\MetaBoxes\CsTerm;
class CsActionLoader{
    
    function __construct() {
        //load if admin
        if( is_admin() ){
            $this->loadTermMeta();
        }
        
        //cron jobs loader
        $this->loadCronJobs();
        
        //load function 
        $this->loadWpFunctions();
        
        //load filters
        $this->loadWpFilters();
        
        //custom actions
        $this->loadCustomAjaxCalls();
    }
    
    /**
     * Load Term Meta
     * 
     * @return type
     */
    private function loadTermMeta(){
        //get term name
        if( GeneralHelpers::Cs_GetRequestValue('taxonomy') !== '' ){
            (new CsTerm())->add_actions();
        }
        return;
    }
    
    /**
     * Load System Cron Jobs
     * 
     * @version 1.0.0
     */
    private function loadCronJobs(){
        //remove urls
        add_action('aios_check_urls_removed', array( __NAMESPACE__  .  '\\Library\\CronJobs\\AutoCronJobs', 'cs_check_urls_removed'));
        add_action('csmbp_cron_monitorKeyword', array( __NAMESPACE__  .  '\\Library\\CronJobs\\AutoCronJobs', 'monitor_keyword'));
    }
    
    /**
     * laod functions
     */
    private function loadWpFunctions(){
        //image dimension changer
        add_action( 'wp', array( Library\CsSocialImageMaker::get_instance(), 'add_plugin_image_sizes' ), -100 );
        add_action( 'current_screen', array( Library\CsSocialImageMaker::get_instance(), 'add_plugin_image_sizes' ), -100 );
        
        add_action( 'admin_footer', function(){
            ?>
                <!-- Start of Async Drift Code -->
<!--                <script>
                !function() {
                  var t;
                  if (t = window.driftt = window.drift = window.driftt || [], !t.init) return t.invoked ? void (window.console && console.error && console.error("Drift snippet included twice.")) : (t.invoked = !0, 
                  t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ], 
                  t.factory = function(e) {
                    return function() {
                      var n;
                      return n = Array.prototype.slice.call(arguments), n.unshift(e), t.push(n), t;
                    };
                  }, t.methods.forEach(function(e) {
                    t[e] = t.factory(e);
                  }), t.load = function(t) {
                    var e, n, o, i;
                    e = 3e5, i = Math.ceil(new Date() / e) * e, o = document.createElement("script"), 
                    o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + i + "/" + t + ".js", 
                    n = document.getElementsByTagName("script")[0], n.parentNode.insertBefore(o, n);
                  });
                }();
                drift.SNIPPET_VERSION = '0.3.1';
                drift.load('39zttppgyf5v');
                </script>-->
                <!-- End of Async Drift Code -->
            <?php
        });
    }
    
    /**
     * Load wp filters
     */
    private function loadWpFilters(){
        //image dimension changer
        add_filter( 'image_editor_save_pre', array( Library\CsSocialImageMaker::get_instance(), 'image_editor_save_pre_image_sizes' ), -100, 2 );
    }
    
    /**
     * Load dynamic custom calls by wp default ajax hook
     * 
     * @return array|boolean|string
     */
    private function loadCustomAjaxCalls(){
        add_action( 'wp_ajax_cs_custom_call', array( $this, 'cs_custom_call') );
        add_action( 'wp_ajax_nopriv_cs_custom_call', array( $this, 'cs_custom_call') );
    }
    
    /**
     * Custom Ajax calling
     */
    public function cs_custom_call(){
        $data = $_REQUEST['data'];
        if( empty($method = $data['method'] ) || strpos( $method, '@') === false ){
            _e( 'Method parameter missing / invalid! ', SR_TEXTDOMAIN );
            die();
        }
        $method = explode( '@', $method );
        $class_path = str_replace( '\\\\', '\\', '\\'. __NAMESPACE__ .'\\'.$method[0]);
        if( !class_exists( $class_path ) ){
            echo sprintf( __( 'Library Class "%s" not found! ', SR_TEXTDOMAIN ), $class_path );
            die();
        }
        echo (new $class_path())->{$method[1]}( $data );
        exit;
    }
    
}
new CsActionLoader();