<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage Link Rules
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getInternalLinkData as getInternalLinkData;
use CsSeoMegaBundlePack\Helper;

class ManageLinkRules {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $getInternalLinkData = new getInternalLinkData();
       
//       pre_print($getInternalLinkData->getCategory());
       
       $filter_cat_id = isset($_GET['filter_cat']) ? sanitize_text_field($_GET['filter_cat']) : '';
       $data = $getInternalLinkData->getInternalLinkRules( $filter_cat_id );
//       pre_print($data);
       $filter_msg_title = $filter_msg_subtitle = '';
       if(!empty($filter_cat_id)){
           if($filter_cat_id == 'inactive'){
               $catName = 'inactive';
           }else{
               $filter_cat_id = 'where g.id = '. $filter_cat_id;
               $catName = $getInternalLinkData->getGroups( $filter_cat_id );
               $catName = empty($catName) ? '' : $catName[0]->name;
           }
           $filter_msg_title =  __( 'Filtered by group', SR_TEXTDOMAIN );
           $filter_msg_subtitle = __( 'Total: <b>'. count($data) .'</b> rules found by group : <b>' . $catName .'</b>', SR_TEXTDOMAIN );
       }
       
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Manage Link Rules', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Manage Internal Link Builder Rules', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage Your Website Internal Link Rules', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Target Keywords', SR_TEXTDOMAIN ),
                   __( 'Target URL', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $data,
           'tbl_group' => $getInternalLinkData->getGroups(),
           'filter_title' => $filter_msg_title,
           'filter_subtitle' => $filter_msg_subtitle,
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_btn_active' => __( 'Enable', SR_TEXTDOMAIN ),
           'actn_btn_inactive' => __( 'Disable', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filters', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           'filter_label_deactivate' => __( 'Deactivated Rules', SR_TEXTDOMAIN ),
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageLinkRules'),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/internalLinkBuilder/ManageLinkRules.twig', $data );
    }
  
}
