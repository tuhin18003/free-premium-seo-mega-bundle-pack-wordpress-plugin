<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage Website cost
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getInternalLinkData as getInternalLinkData;

class ManageLinkRulesGroups {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $getInternalLinkData = new getInternalLinkData();
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Link Rules Groups', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Manage Internal Link Builder Rules', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage Your Website Internal Link Rules', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Group Name', SR_TEXTDOMAIN ),
                   __( 'Link Count', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $getInternalLinkData->getGroups(),
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_btn_active' => __( 'Enable', SR_TEXTDOMAIN ),
           'actn_btn_inactive' => __( 'Disable', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filter by Category', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_add_new_cat_name' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'placeholder_add_new_category' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'add_new_group_box_title' => __( 'Create New Group', SR_TEXTDOMAIN ),
           'label_create_new_cat_btn' => __( 'Create Now', SR_TEXTDOMAIN ),
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           'filter_label_deactivate' => __( 'Deactivated Rules', SR_TEXTDOMAIN ),
           'manage_link_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageLinkRules'),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/internalLinkBuilder/ManageLinkRulesGroups.twig', $data );
    }
    
}
