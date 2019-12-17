<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Broken Link options
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
class BrokenLinkOptions {
    
    
    public function load_page_builder(){
        $CommonText = new CommonText();
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_broken_link_options', 'json' => true ) );   
        
//        pre_print($get_options);
        
       $data = array_merge( $CommonText->form_element( 'aios-brokenlink-general-options', 'bl_general_options' ), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' => __( 'Internal Link Checking Options', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'You can set automatic option to find out internal links and broken links and it will send you email if any broken link detect.', SR_TEXTDOMAIN ),
           
           'label_post_statuses' => __( 'Post Statuses', SR_TEXTDOMAIN ),
           'post_statuses_options' => get_post_stati(array('internal' => false), 'objects'),
           'label_where_to_look' => __( 'Search in', SR_TEXTDOMAIN ),
           'where_to_look_options' => array(
               'blogroll' => __( 'Blogroll items', SR_TEXTDOMAIN ),
               'comment' => __( 'Comments', SR_TEXTDOMAIN ),
               'custom_css' => __( 'Custom CSS', SR_TEXTDOMAIN ),
               'page' => __( 'Pages', SR_TEXTDOMAIN ),
               'posts' => __( 'Posts', SR_TEXTDOMAIN ),
               'custom_posts' => __( 'Custom Posts', SR_TEXTDOMAIN ),
           ),
           'label_link_types' => __( 'Link Types', SR_TEXTDOMAIN ),
           'link_types_options' => array(
                'link' => __("HTML links", SR_TEXTDOMAIN ),
                'image'=>__("HTML images", SR_TEXTDOMAIN ),
                'plaintext_url' => __("Plaintext URLs", SR_TEXTDOMAIN ),
                'youtube_iframe' => __("Embedded YouTube videos", SR_TEXTDOMAIN ),
                'youtube_embed' => __("Embedded YouTube videos (old embed code)", SR_TEXTDOMAIN ),
                'youtube_playlist_embed' => __("Embedded YouTube playlists (old embed code)", SR_TEXTDOMAIN ),
                'smart_youtube_embed' => __("Smart YouTube httpv:// URLs", SR_TEXTDOMAIN ),
                'googlevideo_embed' => __("Embedded GoogleVideo videos", SR_TEXTDOMAIN ),
                'vimeo_embed' => __("Embedded Vimeo videos", SR_TEXTDOMAIN ),
                'dailymotion_embed'=> __("Embedded DailyMotion videos", SR_TEXTDOMAIN ),
           ),
           'label_protocols_apis' => __( 'Protocols & APIS', SR_TEXTDOMAIN ),
           'protocols_apis_options' => array(
                'http' => __("Basic HTTP", SR_TEXTDOMAIN ),
                'mediafire_checker' => __("MediaFire API", SR_TEXTDOMAIN ),
                'rapidshare_checker' => __("RapidShare API", SR_TEXTDOMAIN ),
                'youtube_checker' => __("YouTube API", SR_TEXTDOMAIN ),
           ),
           
           
           'label_db_sync_schedule' => __( 'Automatic Sync Entire Database', SR_TEXTDOMAIN ),
           'label_monitor_schedule' => __( 'Automatic Link Monitor', SR_TEXTDOMAIN ),
           'label_select_default' => '==============Select Cron Job Schedule==============',
           'select_expire' => wp_get_schedules(),
           'label_timeout' => __( 'Link Monitoring Timeout', SR_TEXTDOMAIN ),
           'placeholder_timeout' => __( 'Links that take longer than this to load will be marked as broken', SR_TEXTDOMAIN ),
           'label_no_autocheck' => __( 'Stop CronJob', SR_TEXTDOMAIN ),
           'label_submit_btn' => 'Update Options',
           'get_options' => $get_options,
           'section_1' => __( 'Where To Look', SR_TEXTDOMAIN ),
           'section_2' => __( 'Link Types, Protocls & APIS ', SR_TEXTDOMAIN ),
           'section_3' => __( 'General Settings', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'Wordpress Self Pingback' =>  __( 'A pingback is a special type of comment that’s created when you link to another blog post, as long as the other blog is set to accept pingbacks. You can trun it on or off from here.', SR_TEXTDOMAIN ),
               'Visitor Click Tracking' =>  __( 'If you like to track your visitor click you can trun it on.', SR_TEXTDOMAIN ),
           ),
       ));
       
       add_action('admin_footer', [$this, '_general_optoins_script']);
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/broken_links/BrokenLinkOptions.twig', $data );
    }
    
    function _general_optoins_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                                var _obj = {
                                    $: $
                                };
                                var form_general_options = new Aios_Form_Handler( _obj );
                                form_general_options.setup( "#general_options" );
                        } );
                } )( jQuery );
            </script>
        <?php
    }
}
