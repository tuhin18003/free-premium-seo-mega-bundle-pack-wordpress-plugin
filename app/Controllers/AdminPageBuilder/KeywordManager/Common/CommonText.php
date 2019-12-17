<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common;
/**
 * Common Text
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
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
               __( 'Keyword Ranking', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'google',
                   'RnkChkAddNewKeyword' => array( 'plus', __( 'Add Keywords', SR_TEXTDOMAIN ) ),
                   'RnkChkManageKeywords' => array( 'plus', __( 'Ranking Monitor', SR_TEXTDOMAIN ) ),
                   'RnkChkManageKeywordGroups' => array( 'plus', __( 'Groups', SR_TEXTDOMAIN ) ),
                ),
               __( 'Keyword Research', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'search',
                   'KresManageSelectedKeywords' => array( 'plus', __( 'All Selected Keywords', SR_TEXTDOMAIN ) ),
                   'KresKeywordSuggestions' => array( 'plus', __( 'Keyword Suggestion', SR_TEXTDOMAIN ) ),
                   'KresGAKWrap' => array( 'plus', __( 'Adwords Wrapper', SR_TEXTDOMAIN ) ),
               ),
               __( 'Easy Bulk Tags', SR_TEXTDOMAIN ) => array(
                   'has_sub', 'tags',
                   'BulkTags' => array( 'plus', __( 'Add Bulk Tags', SR_TEXTDOMAIN ) )
               ),
            ),
            'current_tab' => $tab_name,
            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            'plugin_name' => 'Keyword Manager',
            'plugin_version' => '1.0.0',
            'base_url' => admin_url("admin.php?page=cs-keyword-manager"),
            'current_url' => admin_url("admin.php?page=cs-keyword-manager&tab={$tab_name}"),
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
        if(empty($action_hook) || empty($nonce_string)) return false;
        
        $mult_actions = array();
        if($multiple_actions){
            foreach($multiple_actions as $key=>$hook){
                $mult_actions = array_merge( $mult_actions, array( $key => admin_url( "admin.php?page=cs-keyword-manager&action={$hook}" ) ));
            }
        }
        
        return array_merge($mult_actions, array(
            'nonce_field' => wp_create_nonce( $nonce_string ),
            'form_action' => admin_url( "admin.php?page=cs-keyword-manager&action={$action_hook}" ),
            'base_url' => admin_url("admin.php?page=cs-keyword-manager"),
        ));
    }
    
}
