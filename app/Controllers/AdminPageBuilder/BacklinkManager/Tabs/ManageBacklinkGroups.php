<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage BackLink Group
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getBacklinkData as getBacklinkData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class ManageBacklinkGroups {
    
    public function load_page_builder(){
        global $wpdb;
       $CommonText = new CommonText();
       $getBacklinkData = new getBacklinkData();
       
       $groups = CsQuery::Cs_Get_results(array(
            'select' => 'g.*, count(ri.id) as item_count',
            'from' => "{$wpdb->prefix}aios_blmanager_item_groups as g",
            'join' => "left join {$wpdb->prefix}aios_blmanager_items as ri on ri.item_group_id = g.id",
            'group_by' => 'g.id',
            'where' => 'g.group_type = 2'       
        ));
//       pre_print($getBacklinkData->getGroups());
       
       $edit = false; $edit_group = array(); $group_id = '';
       $label_create_edit = __( 'Enter New Group Name', SR_TEXTDOMAIN );
       $label_box_edit = __( 'Create New Group', SR_TEXTDOMAIN );
       $label_monthly_price = __( 'Enter Price', SR_TEXTDOMAIN );
       $label_description = __( 'Enter Description', SR_TEXTDOMAIN );
       $label_create_btn = __( 'Create', SR_TEXTDOMAIN );
       if(isset($_GET['edit']) && $_GET['edit'] === 'true'){
           $edit = true;
           $label_create_edit = __( 'Edit Group Name', SR_TEXTDOMAIN );
           $label_box_edit = __( 'Edit Group', SR_TEXTDOMAIN );
           $label_create_btn = __( 'Update', SR_TEXTDOMAIN );
           $label_monthly_price = __( 'Edit Monthly Price', SR_TEXTDOMAIN );
           $label_description = __( 'Update Description', SR_TEXTDOMAIN );
           $group_id = isset($_GET['group_id']) ? intval( $_GET['group_id'] ) : 0;
           
           $edit_group = CsQuery::Cs_Get_results(array(
                'select' => 'g.*, count(ri.id) as item_count',
                'from' => "{$wpdb->prefix}aios_blmanager_item_groups as g",
                'join' => "left join {$wpdb->prefix}aios_blmanager_items as ri on ri.item_group_id = g.id",
                'group_by' => 'g.id',
                'where' => "g.id = $group_id"       
            ));
//           $edit_group = $getBacklinkData->getGroups( $group_id );
       }
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Backlink Groups', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Manage Backlink Groups', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage Backlink Groups', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Group Name', SR_TEXTDOMAIN ),
                   __( 'Link Count', SR_TEXTDOMAIN ),
                   __( 'Description', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $groups,
           'actn_btns' => __( 'Change Link Status', SR_TEXTDOMAIN ),
           'actn_btn_active' => __( 'active', SR_TEXTDOMAIN ),
           'actn_btn_inactive' => __( 'Inactive', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filter by Category', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           
           'group_edit' => $edit,
           'group_edit_id' => $group_id,
           'group_edit_lable' => __( 'Editing', SR_TEXTDOMAIN ),
           'group_edit_data' => empty($edit_group) ? 0 : $edit_group[0],
           'label_add_new_cat_name' => $label_create_edit,
           'placeholder_add_new_category' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           
           'label_add_new_monthly_price' => $label_monthly_price,
           'placeholder_add_new_monthly_price' => __( 'Enter Price', SR_TEXTDOMAIN ),
           'label_description' => $label_description,
           'placeholder_description' => __( 'Enter description', SR_TEXTDOMAIN ),
           
           
           'add_new_group_box_title' => $label_box_edit,
           'label_create_new_cat_btn' => $label_create_btn,
           'label_span_edit' => __( 'Edit', SR_TEXTDOMAIN ),
           'cancel' => __( 'Cancel', SR_TEXTDOMAIN ),
            'nonce_field' => wp_create_nonce( 'aios-add-new-backlink-groups' ),
           'form_action' => admin_url( 'admin-ajax.php' ),
           
           'filter_label_deactivate' => __( 'Deactivated Rules', SR_TEXTDOMAIN ),
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageBacklinkGroups'),
           'manage_link_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageBacklinks'),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/backlinkChecker/ManageBacklinkGroups.twig', $data );
    }
   
}
