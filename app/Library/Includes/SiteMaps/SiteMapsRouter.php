<?php namespace CsSeoMegaBundlePack\Library\Includes\SiteMaps;
/**
 * Sitemap router
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

class SiteMapsRouter {
    //put your code here
    
    public static function get_base_url( $page ) {
        global $wp_rewrite;

        $base = $wp_rewrite->using_index_permalinks() ? 'index.php/' : '/';

        // Get the scheme from the configured home url instead of letting WordPress determine the scheme based on the requested URI.
        return home_url( $base . $page, parse_url( get_option( 'home' ), PHP_URL_SCHEME ) );
    }

}
