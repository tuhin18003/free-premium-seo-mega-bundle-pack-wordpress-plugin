<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\aboutUs\Common;

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
        return array(
            'menu' => array(
               __( 'Welcome', '' ) => array( 'home', '' ),
            ),
            'current_tab' => $tab_name,
            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            'plugin_name' => 'Premium Seo Mega Bundle Pack',
            'plugin_version' => '1.0.0',
            'base_url' => admin_url("admin.php?page=about-us"),
            'current_url' => admin_url("admin.php?page=about-us&tab={$tab_name}"),
            'site_url' => site_url('/')
        );
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
