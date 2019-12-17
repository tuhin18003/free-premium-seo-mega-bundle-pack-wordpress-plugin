<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage Link Rules Groups
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getPropertyData as getPropertyData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class ManagePropertyGroups {
    
    public function load_page_builder(){
        global $wpdb;
       $CommonText = new CommonText();
       $getPropertyData = new getPropertyData();
       $groups = CsQuery::Cs_Get_results(array(
            'select' => 'g.*, count(ri.id) as item_count',
            'from' => "{$wpdb->prefix}aios_blmanager_item_groups as g",
            'join' => "left join {$wpdb->prefix}aios_blmanager_items as ri on ri.item_group_id = g.id",
            'group_by' => 'g.id',
             'where' => 'g.group_type = 1'       
        ));
           
       $data = array_merge( $CommonText->form_element(), array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Property Groups', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Property Groups', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage Your Property Groups', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Group Name', SR_TEXTDOMAIN ),
                   __( 'Property Count', SR_TEXTDOMAIN ),
                   __( 'Description', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $groups,
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filter by Category', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_add_new_cat_name' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'placeholder_add_new_category' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'label_add_new_cat_des' => __( 'Enter Group Description', SR_TEXTDOMAIN ),
           'placeholder_add_new_des' => __( 'Enter New Group Description', SR_TEXTDOMAIN ),
           'add_new_group_box_title' => __( 'Create New Group', SR_TEXTDOMAIN ),
           'label_create_new_cat_btn' => __( 'Create Now', SR_TEXTDOMAIN ),
            'nonce_field' => wp_create_nonce( 'aios-add-new-property-groups' ),
           'form_action' => admin_url( 'admin-ajax.php' ),
           'filter_label_deactivate' => __( 'Deactivated Rules', SR_TEXTDOMAIN ),
           'manage_link_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageProperty'),
       ));
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/ManagePropertyGroups.twig', $data );
    }
    
}
