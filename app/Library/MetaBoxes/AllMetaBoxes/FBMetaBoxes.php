<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes;

/**
 * MetaBoxes
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use Herbert\Framework\Enqueue;
use Herbert\Framework\Application;
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class FBMetaBoxes{
    
    private $facebook_settings;
    private $setting_url;
    protected $http;

    public function __construct(){
        $this->facebook_settings = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_facebook_settings', 'json' => true ) );
        $this->setting_url = admin_url('admin.php?page=cs-social-analytics&tab=FbSettings');
        
        //add scripts
        $this->add_scripts();
    }
    
    /**
     * Add scripts
     */
    private function add_scripts(){
        if( isset( $this->facebook_settings->fb_publish_to ) && !empty( $this->facebook_settings->fb_publish_to )){
            //enqueue script
            $this->load_library_script();
            
            //add footer script
            add_action('admin_footer', array($this, 'load_custom_script'));
            
        }
    }

    /**
     * Register Meta box
     * 
     * @return type
     */
    public static function meta_box(){
        return array( array(
            'id' => 'meta_box',
            'title' => __( '%s - Social Publisher & Scheduler', SR_TEXTDOMAIN ),
            'callback' => array( 'CsSeoMegaBundlePack\\Library\\MetaBoxes\\AllMetaBoxes\\FBMetaBoxes', 'FacebookSection' ),
            'screen' => '',
            'context' => 'normal',
            'priority' => 'core'
        ));
    }
    
    /**
     * Add Script 
     */
    public function load_library_script(){
        $app = new Application();
        $enqueue = new Enqueue($app);
        
        $enqueue->admin([
            'as'  => 'csmbp-bootstrap-datepicker.min.css',
            'src' => Helper::assetUrl('/default/plugins/jquery-date-time-picker/jquery.datetimepicker.min.css'),
        ]);
        $enqueue->admin([
            'as'  => 'csmbp-bootstrap-datepicker.min.js',
            'src' => Helper::assetUrl('/default/plugins/jquery-date-time-picker/jquery.datetimepicker.full.min.js'),
        ]);
        
    }
    
    /**
     * Facebook helper script
     */
    public function load_custom_script(){
        
        ?>
        <!--Aios Facebook Helper Script-->
        <script type="text/javascript">
            jQuery('#date_time_picker').datetimepicker({
                startDate:'+1971/05/01',//or 1986/12/08
                format: 'Y/m/d h:i A',
//                format:'unixtime'
            });
            jQuery(document).ready(function(){
//                var $tabs = jQuery('horizontalTabs');
//                $tabs.responsiveTabs({
//                    rotate: false,
//                    startCollapsed: 'accordion',
//                    collapsible: 'accordion',
//                    setHash: true,
//                });
                
                jQuery("#aios_fb_instant_post").on("click", function(){
                    if(jQuery(this).prop('checked')) {
                        jQuery("#date_time_picker").attr('readonly', 'readonly');
                    }else{
                        jQuery("#date_time_picker").removeAttr('readonly');
                    }
                });
            });
        </script>
        <!--Aios Facebook Helper Script-->
            <?php
    }

    /**
     * Publish to facebook
     * 
     * @global \CsSeoMegaBundlePack\Models\SocialAnalytics\type $wpdb
     * @param type $post
     */
    public function FacebookSection( $post ){
        global $wpdb;
            
        wp_nonce_field( 'aios_facebook_publish_action', 'aios_facebook_publish' );
        ?>
        <div class="<?php echo Helper::get('PLUGIN_PREFIX'); ?>-section">
            <div class="horizontalTabs">
                <ul>
                    <li><a href="#tab-Facebook"><?php _e( 'Facebook', SR_TEXTDOMAIN);?></a></li>
                </ul>
                <div id="tab-Facebook">
                    <?php if( isset( $this->facebook_settings->fb_app_id ) && !empty( $this->facebook_settings->fb_app_id ) ){
                    $custom_title = CsQuery::Cs_Get_postmeta( $post->ID, 'aios_fb_custom_title');
                    $custom_content = CsQuery::Cs_Get_postmeta( $post->ID, 'aios_fb_custom_content');
                ?>
              <div class="<?php echo Helper::get('PLUGIN_PREFIX'); ?>-form-group form-group-border">
                  <label for="test"><?php _e('Custom Title For Facebook', SR_TEXTDOMAIN); ?></label>
                  <div class="input-group">
                      <input type="text" name="fb_custom_title" id="fb_custom_title" class="form-control" value="<?php echo $custom_title; ?>" placeholder="<?php _e('Enter custom title', SR_TEXTDOMAIN); ?>"/>
                      <p class="description"><?php _e('You can create a specific title for Facebook or leave it blank to publish the original post title.', SR_TEXTDOMAIN) ?></p>
                  </div>
              </div>
              <div class="<?php echo Helper::get('PLUGIN_PREFIX'); ?>-form-group form-group-border">
                  <label for="test"><?php _e('Custom Content For Facebook', SR_TEXTDOMAIN); ?></label>
                  <div class="input-group">
                      <textarea class="" cols="77" rows="5" name="fb_custom_content" placeholder="<?php _e('Enter your custom content', SR_TEXTDOMAIN); ?>" ><?php echo $custom_content; ?></textarea>
                      <p class="description"><?php _e('You can use this field to publish custom content to Facebook.', SR_TEXTDOMAIN); ?></p>
                  </div>
              </div>
              <div class="<?php echo Helper::get('PLUGIN_PREFIX'); ?>-form-group form-group-border">
                  <label for="test">Publish Intantly</label>
                  <div class="input-group">
                      <input type="checkbox" name="aios_fb_instant_post" id="aios_fb_instant_post" value="1" />
                      <p class="description"><?php _e('Click this checkbox to publish current post instantly to Facebook.', SR_TEXTDOMAIN) ?></p>
                  </div>
              </div>
              <div class="<?php echo Helper::get('PLUGIN_PREFIX'); ?>-form-group">
                  <label for="test">Create Schedule</label>
                  <div class="input-group">
                      <input type="text" id="date_time_picker" name="aios_fb_post_schedule_val" placeholder="<?php _e('Select a date time for schedule', SR_TEXTDOMAIN) ?>" />
                      <p class="description"><?php _e('Create a schedule for publish this post later.', SR_TEXTDOMAIN) ?></p>
                  </div>
              </div>
                <?php }else{ ?>
                      <div class="csmbp-notice csmbp-warning">
                          <div class="notice-label">
                              <i class="fa fa-warning"></i> API Settings Required
                          </div>
                          <div class="notice-message">
                              <?php _e( "You need to <a href='{$this->setting_url}'>setup facebook app </a> to publish post to facebook.", SR_TEXTDOMAIN);?>
                          </div>
                      </div>
                <?php } ?>
                </div>
                
            </div>
                
        </div>
        <?php
    }
    
}
