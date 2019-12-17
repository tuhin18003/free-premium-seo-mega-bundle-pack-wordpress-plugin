<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * All 404 Error
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getRedirectionData as getRedirectionData;

class All404Error {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $getRedirectionData = new getRedirectionData();
       
       $data_item = $getRedirectionData->get404Errors();
      
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Monitor 404 Errors', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'All 404 Error', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Monitor & Manage All of Your 404 Errors', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( '404 URL', SR_TEXTDOMAIN ),
                   __( 'User Agent', SR_TEXTDOMAIN ),
                   __( 'User IP', SR_TEXTDOMAIN ),
                   __( 'Hits On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $data_item,
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           
           'label_span_add_redirect' => __( 'Add Redirect', SR_TEXTDOMAIN ),
           
           
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AllRedirectsRules'),
           'edit_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AddNewRedirectsRule'),
           'loading_gif' => LOADING_GIF_URL
       );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/301_redirection/All404Errors.twig', $data );
    }
}
