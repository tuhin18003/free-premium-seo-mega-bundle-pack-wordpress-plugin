<?php

/** @var  \Herbert\Framework\Application $container */
/** @var  \Herbert\Framework\Http $http */
/** @var  \Herbert\Framework\Router $router */
/** @var  \Herbert\Framework\Enqueue $enqueue */
/** @var  \Herbert\Framework\Panel $panel */
/** @var  \Herbert\Framework\Shortcode $shortcode */
/** @var  \Herbert\Framework\Widget $widget */


if( !function_exists('aios_db_uninstall')) {
    /**
     * Uninstall DB
     * 
     * @since 1.0.0
     * @global obj $wpdb
     */
    function aios_db_uninstall(){
        global $wpdb;
        $tbl_name = array(
            $wpdb->prefix . 'aios_properties',
            $wpdb->prefix . 'aios_property_groups',
            $wpdb->prefix . 'aios_properties_assets',
            $wpdb->prefix . 'aios_backlinks',
            $wpdb->prefix . 'aios_backlinks_assets',
            $wpdb->prefix . 'aios_backlink_groups',
            $wpdb->prefix . 'aios_internal_link_items',
            $wpdb->prefix . 'aios_internal_link_groups',
            $wpdb->prefix . 'aios_redirection_items',
            $wpdb->prefix . 'aios_redirection_groups',
            $wpdb->prefix . 'aios_redirection_404',
            $wpdb->prefix . 'aios_redirection_logs',
        );
        foreach( $tbl_name as $tbl_name){
            $sql = "DROP TABLE IF EXISTS $tbl_name";
            $wpdb->query($sql);
        }
        delete_option( 'aios_db_version' );
        delete_option( 'aios_installed_on' );
        delete_option( 'aios_redirection_options' );
        
        //social apps
        delete_option( 'aios_google_settings' );
        delete_option( 'aios_google_token' );
        delete_option( 'aios_gwebmaster_verify_meta' );
        delete_option( 'aios_total_url_shorted' );
        delete_option( 'aios_facebook_settings' );
        delete_option( 'aios_oogle_localseo' );
        delete_option( 'aios_google_placeid' );
        delete_option( 'aios_sitemap_count' );
        
        //on page optmization
        delete_option( 'aios_metas_stop_render' );
        delete_option( 'aios_web_pub_options' );
        delete_option( 'aios_web_graph_options' );
    }
}
add_action( 'aios_db_uninstall', 'aios_db_uninstall' );
do_action( 'aios_db_uninstall' );