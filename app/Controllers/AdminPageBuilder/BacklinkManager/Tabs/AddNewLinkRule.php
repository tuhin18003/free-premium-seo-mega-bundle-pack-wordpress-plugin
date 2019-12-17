<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Add New Internal Link Rule
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getInternalLinkData as getInternalLinkData;

class AddNewLinkRule {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $getInternalLinkData = new getInternalLinkData();
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Add New Link Rule', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Add New Internal Link Builder Rule', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Automatically Build Your Website Internal Link', SR_TEXTDOMAIN ),
           'label_add_new_keyword' => __( 'Find keyword(s)*', SR_TEXTDOMAIN ),
           'placeholder_add_new_keyword' => __( 'Enter single keyword or comma seperated multiple keywords', SR_TEXTDOMAIN ),
           'label_add_new_property' => __( 'Create Link by this URL*', SR_TEXTDOMAIN ),
           'placeholder_add_new_property' => __( 'Please enter a url. Example: http://example.com', SR_TEXTDOMAIN ),
           'label_add_new_category' => __( 'Select / create category', SR_TEXTDOMAIN ),
           'placeholder_add_new_category' => __( 'Please enter new category name', SR_TEXTDOMAIN ),
           'select_group' => __( 'Select Category', SR_TEXTDOMAIN ),
           'select_create_new_cat' => __( 'Create New Category', SR_TEXTDOMAIN ),
           
           'link_category' => $getInternalLinkData->getGroups(),
           'nonce_field' => wp_create_nonce( 'aios-add-new-internal-link' ),
           'form_action' => admin_url( 'admin-ajax.php' ),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'panel_subtitle' => __( '*Before adding a new rule please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'How does it works?' =>  __( 'It will automatically find the keyword from your post and will create link automatically.', SR_TEXTDOMAIN ),
               'How to add a keyword?' =>  __( 'You can add single keyword in the input field or multiple keyword by comma seperated.', SR_TEXTDOMAIN ),
           ),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/internalLinkBuilder/AddNewLinkRule.twig', $data );
    }
    
}
