<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Backlink options
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
class GeneralOptions {
    
    
    public function load_page_builder(){
        $CommonText = new CommonText();
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_general_option', 'json' => true ) );   
        
       $data = array_merge( $CommonText->form_element( 'aios-general-options', 'general_options' ), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' => __( 'Global Options', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Options Management', SR_TEXTDOMAIN ),
           
           'label_self_pinging' => __( 'Wordpress Self Pingback', SR_TEXTDOMAIN ),
           'label_click_tracking' => __( 'Visitor Click Tracking', SR_TEXTDOMAIN ),
           'label_on' => __( 'On', SR_TEXTDOMAIN ),
           'label_off' => __( 'Off', SR_TEXTDOMAIN ),
           'label_cron_schedule' => __( 'Automatic Update SERP', SR_TEXTDOMAIN ),
           
           'get_options' => $get_options,
           'label_select_default' => '==============Select Cron Job Schedule==============',
           'select_expire' => wp_get_schedules(),
           'label_submit_btn' => 'Update Options',
           
           'panel_subtitle' => __( '*Please read FAQ get more information.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'Wordpress Self Pingback' =>  __( 'A pingback is a special type of comment that’s created when you link to another blog post, as long as the other blog is set to accept pingbacks. You can trun it on or off from here.', SR_TEXTDOMAIN ),
               'Visitor Click Tracking' =>  __( 'If you like to track your visitor click you can trun it on.', SR_TEXTDOMAIN ),
           ),
       ));
       
       add_action('admin_footer', [$this, '_general_optoins_script']);
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/general_options/GeneralOptions.twig', $data );
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
