<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * All  Redirects Rules
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getRedirectionData as getRedirectionData;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Group as Aios_Group;
use CsSeoMegaBundlePack\Helper;

class AllRedirectsRules {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $getRedirectionData = new getRedirectionData();
       
       $filter_group =  isset($_GET['filter_group']) ? (int) sanitize_text_field( $_GET['filter_group'] ) : '';
       $data_item = $getRedirectionData->getRedirectionRules( $filter_group );
       $filter_msg_title = $filter_msg_subtitle = '';
       if(!empty($filter_group)){
           $row_count = $data_item === false ? 0 : count($data_item);
           
           $group_name = Aios_Group::get($filter_group)->get_name();
          
           $filter_msg_title =  __( 'Filtered by group', SR_TEXTDOMAIN );
           $filter_msg_subtitle = __( 'Total: <b>'. $row_count .'</b> rules found by group : <b>' . $group_name .'</b>', SR_TEXTDOMAIN );
       }
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'All Redirects Rules', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'All Redirections Rules', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Manage Your Redirections Rules', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Type', SR_TEXTDOMAIN ),
                   __( 'Match URL', SR_TEXTDOMAIN ),
                   __( 'Destination URL', SR_TEXTDOMAIN ),
                   __( 'Hits', SR_TEXTDOMAIN ),
                   __( 'Status', SR_TEXTDOMAIN ),
                   __( 'Last Access', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $data_item,
           'redirect_group' => Aios_Group::get_for_select(),
           'filter_title' => $filter_msg_title,
           'filter_subtitle' => $filter_msg_subtitle,
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_btn_active' => __( 'Enable', SR_TEXTDOMAIN ),
           'actn_btn_inactive' => __( 'Disable', SR_TEXTDOMAIN ),
           'actn_btn_reset_hits' => __( 'Reset Hits', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filter by Group', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           
           'label_span_edit' => __( 'Edit', SR_TEXTDOMAIN ),
           
           
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AllRedirectsRules'),
           'edit_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AddNewRedirectsRule'),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/301_redirection/AllRedirectsRules.twig', $data );
    }
    
}
