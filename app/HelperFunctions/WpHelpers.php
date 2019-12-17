<?php namespace CsSeoMegaBundlePack\HelperFunctions;
/**
 * Wordpress Core Helper Functions
 * 
 * @package All in One Seo 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

if ( ! defined( 'CSMBP_VERSION' ) ) {
	exit;
}

class WpHelpers {
    
    /**
     * Check Installed Plugins
     * 
     * @param type $plugin_base
     * @return boolean
     */
    public static function installed_plugins( $plugin_base = false ) {
        if ( ! empty( $plugin_base ) && validate_file( $plugin_base ) > 0 ) {	// contains invalid characters
                return false;
        } elseif ( ! file_exists( WP_PLUGIN_DIR.'/'.$plugin_base) ) {	// check existence of plugin folder
                return false;
        }
        $installed_plugins = get_plugins();
        if ( ! isset( $installed_plugins[$plugin_base] ) ) {	// check for a valid plugin header
                return false;
        }
        return true;
    }
                
    public static function plugin_has_update( $plugin_base = false ) {
        if ( ! self::installed_plugins( $plugin_base ) ) {
                return false;
        }
        $update_plugins = get_site_transient( 'update_plugins' );
        if ( isset( $update_plugins->response ) && is_array( $update_plugins->response ) ) {
                if ( isset( $update_plugins->response[$plugin_base] ) ) {
                        return true;
                }
        }
        return false;
    }
                
}
