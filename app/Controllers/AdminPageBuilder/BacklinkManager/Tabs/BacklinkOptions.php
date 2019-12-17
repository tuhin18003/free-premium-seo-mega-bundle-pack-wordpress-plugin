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

class BacklinkOptions {
    
    public function load_page_builder(){
        $CommonText = new CommonText();
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_backlink_options', 'json' => true ) );   
       
       $data = array_merge( $CommonText->form_element( 'aios-backlink-options' ), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' => __( 'Backlink Options', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Backlink Option Management', SR_TEXTDOMAIN ),
           
           'label_cron_schedule' => __( 'Automatic Update SERP', SR_TEXTDOMAIN ),
           'get_options' => $get_options,
           'label_select_default' => '==============Select Cron Job Schedule==============',
           'select_expire' => wp_get_schedules(),
           'label_submit_btn' => 'Update Options',
           'panel_subtitle' => __( '*Please read FAQ get more information.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'Automatic Update SERP' =>  __( 'Schedule for the system to automatically update your backlink information. We recomonded for "Weekly" schedule.', SR_TEXTDOMAIN ),
           ),
       ));
       
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/BacklinkChecker/BacklinkOptions.twig', $data );
    }
}
