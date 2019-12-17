<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common;
/**
 * Common Text
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Library\Backend\Messages\BackendMessages;

class CommonText {
    
    /**
     * Common Text
     * 
     * @since 1.0.0
     * @return array
     */
    public static function common_text( $current_page = array() ){
        $tab_name = isset( $current_page[1] ) ? stripslashes( $current_page[1] ) : '';
        return array_merge( array(
            'menu' => array(
               __( 'Dashboard', '' ) => array( 'home', '' ),
//               __( 'My Websites', SR_TEXTDOMAIN ) => array(
//                   'has_sub', 'bullseye',
//                   'MwsAddNewProperty' => array( 'plus', __( 'Add New Website', SR_TEXTDOMAIN ) ),
//                   'ManageProperty' => array( 'plus', __( 'All Websites', SR_TEXTDOMAIN ) ),
//                   'ManagePropertyGroups' => array( 'plus', __( 'Groups', SR_TEXTDOMAIN ) ),
//                   'ManageWebsiteCost' => array( 'plus', __( 'Cost Calculation', SR_TEXTDOMAIN ) ),
//                   'PropertyOptions' => array( 'plus', __( 'Options', SR_TEXTDOMAIN ) ),
//               ),
               __( 'Backlink Checker', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'search',
                   'blc-find-backLinks-matrix' => array( 'plus', __( 'Find Backlink\'s Matrix', SR_TEXTDOMAIN ) ),
                   'ManageBacklinks' => array( 'plus', __( 'All Backlinks', SR_TEXTDOMAIN ) ),
                   'ManageBacklinkGroups' => array( 'plus', __( 'Groups', SR_TEXTDOMAIN ) ),
                   'ManageBacklinkCost' => array( 'plus', __( 'Cost Calculation', SR_TEXTDOMAIN ) ),
                   'BacklinkOptions' => array( 'plus', __( 'Options', SR_TEXTDOMAIN ) ),
                ),
               __( 'Interlink Builder', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'link',
                   'AddNewLinkRule' => array( 'plus', __( 'Add New Internal Link', SR_TEXTDOMAIN ) ),
                   'ManageLinkRules' => array( 'plus', __( 'All Internal Links', SR_TEXTDOMAIN ) ),
                   'ManageLinkRulesGroups' => array( 'plus', __( 'Groups', SR_TEXTDOMAIN ) ),
                ),
                __( 'Backlink Builder', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'gavel',
                   'SubmitNewWebsite' => array( 'plus', __( 'Create Backlinks', SR_TEXTDOMAIN ) ),
                   'AllPingedWebsite' => array( 'plus', __( 'All Pinged Website', SR_TEXTDOMAIN ) ),
                ),
                 __( 'Link Click Tracking', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'hand-pointer-o',
                   'VisitorAllClickedLink' => array( 'plus', __( 'All Clicked Links', SR_TEXTDOMAIN ) ),
                   'VisitorAllClickedLinkCount' => array( 'plus', __( 'Click Counter', SR_TEXTDOMAIN ) ),
                ),
                 __( 'Internal Link Checker', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'times',
                   'AllDetectedLink' => array( 'plus', __( 'All Detected Links', SR_TEXTDOMAIN ) ),
                   'AllBrokenLink' => array( 'plus', __( 'All Broken Links', SR_TEXTDOMAIN ) ),
                   'BrokenLinkOptions' => array( 'plus', __( 'General Options', SR_TEXTDOMAIN ) ),
                ),
               __( '301 Redirections', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'exclamation-triangle',
                   'AddNewRedirectsRule' => array( 'plus', __( 'Add New Redirects', SR_TEXTDOMAIN ) ),
                   'AllRedirectsRules' => array( 'plus', __( 'All Redirects', SR_TEXTDOMAIN ) ),
                   'All404Error' => array( 'plus', __( 'Monitor 404 Errors', SR_TEXTDOMAIN ) ),
                   'RedirectionGroups' => array( 'plus', __( 'Groups', SR_TEXTDOMAIN ) ),
                   'Options301Redirection' => array( 'plus', __( 'Options', SR_TEXTDOMAIN ) ),
//                   __( 'Redirection Modules', SR_TEXTDOMAIN ) => array( 'link', 'RedirectionModules' ),
                ),
               
                __( 'Global Options', '' ) => array( 'cogs', 'GeneralOptions' ),
            ),
            
//            'current_tab' => $tab_name,
//            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            'current_tab' => $tab_name,
            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            
            'plugin_name' => 'Backlink Manager',
            'plugin_version' => '1.0.0',
            'base_url' => admin_url("admin.php?page=cs-backlink-manager"),
            'current_url' => admin_url("admin.php?page=cs-backlink-manager&tab={$tab_name}"),
            'site_url' => site_url('/')
            
        ), BackendMessages::get_instance()->Cs_Get_Notices( array( 'notice_no_item', 'notice_inet_down') ) );
    }
    
    /**
     * Common Form Elements
     * 
     * @param type $nonce_string
     * @return boolean
     */
    public static function form_element( $action_hook, $nonce_string, $multiple_actions = false ){
//        return array(
//            'nonce_field' => wp_create_nonce( $nonce_string ),
//            'form_action' => empty($action_hook) ? admin_url( 'admin-ajax.php') : admin_url( "admin.php?page=cs-backlink-manager&action={$action_hook}" ),
//            'loading_gif' => Helper::assetUrl('default/images/loader/loading.gif'),
//            'base_url' => admin_url("admin.php?page=cs-backlink-manager")        
//        );
            
        if(empty($action_hook) || empty($nonce_string)) return false;
        
        $mult_actions = array();
        if($multiple_actions){
            foreach($multiple_actions as $key=>$hook){
                $mult_actions = array_merge( $mult_actions, array( $key => admin_url( "admin.php?page=cs-backlink-manager&action={$hook}" ) ));
            }
        }
        
        return array_merge($mult_actions, array(
            'nonce_field' => wp_create_nonce( $nonce_string ),
            'form_action' => admin_url( "admin.php?page=cs-backlink-manager&action={$action_hook}" ),
            'base_url' => admin_url("admin.php?page=cs-backlink-manager"),
        ));
    }
    
}
