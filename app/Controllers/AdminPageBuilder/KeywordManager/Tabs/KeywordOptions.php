<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs;
/**
 * Backlink options
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
class KeywordOptions {
    
    
    public function load_page_builder(){
        $CommonText = new CommonText();
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_keyword_options', 'json' => true ) );   
        
       $data = array_merge( CommonText::form_element( 'keyword_options', 'aios-keyword-general-options' ), array(
           'CommonText' => CommonText::common_text(),
           'page_title' => __( 'General Options', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Options Management', SR_TEXTDOMAIN ),
           
           'label_self_pinging' => __( 'Wordpress Self Pingback', SR_TEXTDOMAIN ),
           'label_click_tracking' => __( 'Visitor Click Tracking', SR_TEXTDOMAIN ),
           'label_on' => __( 'On', SR_TEXTDOMAIN ),
           'label_off' => __( 'Off', SR_TEXTDOMAIN ),
           'label_cron_schedule' => __( 'Keyword Automatic Update', SR_TEXTDOMAIN ),
           
           'get_options' => $get_options,
           'label_select_default' => '==============Select Cron Job Schedule==============',
           'select_expire' => wp_get_schedules(),
           'label_submit_btn' => 'Update Options',
           
           'panel_subtitle' => __( '*Please read FAQ get more information.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'Keyword Automatic Update' =>  __( 'Schedule for the system to automatically update your keyword information. We recomonded for "Weekly" schedule.', SR_TEXTDOMAIN ),
           ),
       ));
       
       add_action('admin_footer', [$this, '_general_optoins_script']);
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/keyword_options/KeywordOptions.twig', $data );
    }
    
    function _general_optoins_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                                var _obj = {
                                    $: $,
                                    redirect: window.location.href
                                };
                                var form_general_options = new Aios_Form_Handler( _obj );
                                form_general_options.setup( "#general_options" );
                        } );
                } )( jQuery );
            </script>
        <?php
    }
}
