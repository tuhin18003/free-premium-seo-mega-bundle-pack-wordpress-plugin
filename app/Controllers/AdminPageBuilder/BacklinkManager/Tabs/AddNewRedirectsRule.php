<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Add new redirection rule
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Match as Aios_Match;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Redirect as Aios_Redirect;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Group as Aios_Group;

use CsSeoMegaBundlePack\Helper;


class AddNewRedirectsRule {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $page_title = __( 'Add new redirection', SR_TEXTDOMAIN );
       $page_subtitle = __( 'Add Redirection Rule for Automatic Rediracts', SR_TEXTDOMAIN );
       $submit_btn_text = __( 'Add Redirection', SR_TEXTDOMAIN );
       $edit_item = array();
       if( isset($_GET['rule_id']) && !empty($_GET['rule_id'])){
           $rule_id = (int) trim( $_GET['rule_id'] );
           $get_rule = Aios_Redirect::get_by_id( $rule_id );
           $edit_item = array(
                   $get_rule->get_id(),
                   $get_rule->get_url(),
                   $get_rule->get_match_type(),
                   $get_rule->get_action_type(),
                   $get_rule->is_regex(),
                   $get_rule->get_action_data(),
                   $get_rule->get_group_id(),
                   $get_rule->get_description(),
                   $get_rule->get_action_code(),
               );
           $page_title = __( 'Edit redirection', SR_TEXTDOMAIN );
           $page_subtitle = __( 'Edit Redirection Rule for Automatic Rediracts', SR_TEXTDOMAIN );
           $submit_btn_text = __( 'Update Redirection', SR_TEXTDOMAIN );
       }
       
       if( isset($_GET['source_url']) && !empty($_GET['source_url'])){
           $edit_item = array(
                   '',
                  trim($_GET['source_url']) ,
               );
       }
       
       
//       pre_print($edit_item);
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Add New Redirects Rule', SR_TEXTDOMAIN ),
           'page_title' =>  $page_title,
           'page_subtitle' =>  $page_subtitle,
           
           
           'label_source_url' => __( 'Request URL', SR_TEXTDOMAIN ),
           'placeholder_source_url' => __( 'Example: /about', SR_TEXTDOMAIN ),
           'label_destination_url' => __( 'Destination URL', SR_TEXTDOMAIN ),
           'placeholder_destination_url' => __( 'Example: '.site_url().'/about-us/', SR_TEXTDOMAIN ),
           'label_description' => __( 'Description', SR_TEXTDOMAIN ),
           'placeholder_description' => __( 'Enter description', SR_TEXTDOMAIN ),
           
           'label_match' => __( 'Matching Type', SR_TEXTDOMAIN ),
           'label_action' => __( 'Action Type', SR_TEXTDOMAIN ),
           'label_group' => __( 'Group', SR_TEXTDOMAIN ),
           'placeholder_source_url' => __( 'Please enter request url', SR_TEXTDOMAIN ),
           'label_user_regular_expression' => __( 'Use Regular Expression', SR_TEXTDOMAIN ),
           
           
           'select_match' => Aios_Match::get_match_select(),
           'select_action' => Aios_Redirect::actions(),
           'select_group' => Aios_Group::get_for_select(),
           'nonce_field' => wp_create_nonce( 'aios-redirection-redirect_add' ),
           
           'label_submit_btn' => $submit_btn_text,
           'label_back_btn' => __( 'Back', SR_TEXTDOMAIN ),
           'url_all_redirections' => admin_url('admin.php?page=cs-backlink-manager&tab=AllRedirectsRules'),
           'panel_subtitle' => __( '*Before adding a new rule please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'What is "Request URL"?' =>  __( 'The URL you want to redirect to other URL.', SR_TEXTDOMAIN ),
               'What is "Matching Type"?' =>  __( 'How you want to redirect the URL to other URL.', SR_TEXTDOMAIN ),
               'What is "Action Type"?' =>  __( 'Destination URL behaviour.', SR_TEXTDOMAIN ),
               'What is "Destination URL"?' =>  __( 'The URL where you want to redirect.', SR_TEXTDOMAIN ),
           ),
           'loading_gif' => LOADING_GIF_URL,
           'form_action' => admin_url( 'admin-ajax.php' ),
           'edit_item' => $edit_item           
       );
       
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/301_redirection/AddNewRedirectionRule.twig', $data );
    }
    
}
