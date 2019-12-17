<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Redirection rule options
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Group as Aios_Group;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\Helpers\Redirection_Helper as Redirection_Helper;

class Options301Redirection {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       
       $options = Redirection_Helper::aios_get_options();
       
       $expiry = array(
            -1 => __( 'No log', SR_TEXTDOMAIN ),
            1  => __( 'A day', SR_TEXTDOMAIN ),
            7  => __( 'A week', SR_TEXTDOMAIN ),
            30 => __( 'A month', SR_TEXTDOMAIN ),
            60 => __( 'Two months', SR_TEXTDOMAIN ),
            0  => __( 'Keep forever', SR_TEXTDOMAIN ),
        );
       
//       pre_print($edit_item);
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( '301 Options', SR_TEXTDOMAIN ),
           'page_title' => __( '301 Options', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( '301 Option Management', SR_TEXTDOMAIN ),
           
           
           'label_no_logs' => __( 'No Log', SR_TEXTDOMAIN ),
           'label_redirect_logs' => __( 'Keep Redirect Logs', SR_TEXTDOMAIN ),
           'label_404_logs' => __( 'Keep 404 Logs', SR_TEXTDOMAIN ),
           'label_monitor_posts' => __( 'Monitor changes to posts', SR_TEXTDOMAIN ),
           'placeholder_monitor_posts' => __( 'Don\'t Monitor', SR_TEXTDOMAIN ),
           
           'select_expire' => $expiry,
           'select_group' => Aios_Group::get_for_select(),
           'nonce_field' => wp_create_nonce( 'aios-301_options' ),
           'get_options' => $options,
           'label_submit_btn' => 'Update Options',
           'label_back_btn' => __( 'Back', SR_TEXTDOMAIN ),
           'url_all_redirections' => admin_url('admin.php?page=cs-backlink-manager&tab=AllRedirectsRules'),
           'panel_subtitle' => __( '*Before adding a new rule please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'Redirects Logs' =>  __( 'How long you want to keep the redirect log data. Keep empty for automatic.', SR_TEXTDOMAIN ),
               '404 Logs' =>  __( 'How long you want to keep the redirect log data. Keep empty for automatic.', SR_TEXTDOMAIN ),
               'Monitor changes to post' =>  __( 'Automatically motnior your post for change the permanlink. Select group for where you want to engage it.', SR_TEXTDOMAIN ),
           ),
           'loading_gif' => LOADING_GIF_URL,
           'form_action' => admin_url( 'admin-ajax.php' ),
       );
       
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/301_redirection/Options301Redirection.twig', $data );
    }
    
}
