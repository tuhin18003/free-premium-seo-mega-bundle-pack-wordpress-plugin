<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\Dashboard\Common;
/**
 * Common Text
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class CommonText {
    
    /**
     * Common Text
     * 
     * @since 1.0.0
     * @return array
     */
    public static function common_text( $current_page = array()){
        return array(
            'menu' => array(
               __( 'Dashboard', '' ) => array( 'home', '' ),
            ),
            'current_tab' => isset( $current_page[1] ) ? stripslashes( $current_page[1] ) : '',
            'current_page' => isset( $current_page[0] ) ? stripslashes( $current_page[0] ) : '',
            'plugin_name' => 'Premium Seo Mega Bundle Pack',
            'plugin_version' => '1.0.0',
            'base_url' => admin_url("admin.php?page=cs-aiost-dashboard"),
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
        if( $multiple_actions ){
            foreach( $multiple_actions as $key=>$hook ){
//                $mult_actions = array_merge( $mult_actions, array( $key => admin_url( "admin.php?page=cs-social-analytics&action={$hook}" ) ));
                $mult_actions = array_merge( $mult_actions, array( $key => $hook ));
            }
        }
        
        return array_merge($mult_actions, array(
            'nonce_field' => wp_create_nonce( $nonce_string ),
            'form_action' => admin_url( "admin.php?page=cs-social-analytics&action={$action_hook}" ),
            'base_url' => admin_url("admin.php?page=cs-social-analytics"),
            'post_edit_url' => admin_url("post.php?action=edit&post="),
        ));
    }
    
}
