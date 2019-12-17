<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\Helpers;
/**
 * Redirection Options
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class Redirection_Helper {
    
    /**
     * Get Redirection Options
     * 
     * @return array
     */
    public static function aios_get_options() {
	$options = get_option( 'aios_redirection_options' );
	if ( $options === false )
		$options = array();

	$defaults = apply_filters( 'aios_default_options', array(
		'monitor_post'    => 0,
		'expire_redirect' => 7,
		'expire_404'      => 7,
		'modules'         => array(),
	) );

	foreach ( $defaults as $key => $value ) {
		if ( ! isset( $options[ $key ] ) )
			$options[ $key ] = $value;
	}

	$options['lookup'] = apply_filters( 'aios_lookup_ip', 'http://urbangiraffe.com/map/?ip=' );
	return $options;
    }
    
    /**
     * Get Auto Generate target
     * 
     * @return string
     */
    public static function auto_generate() {
        $options = self::aios_get_options();
        $id = time();

        $url = $options['auto_target'];
        $url = str_replace( '$dec$', $id, $url );
        $url = str_replace( '$hex$', sprintf( '%x', $id ), $url );
        return $url;
    }
    
    /**
     * Sanitize Url
     * 
     * @param type $url
     * @param type $regex
     * @return string
     */
    public static function sanitize_url( $url, $regex = false ) {
            // Make sure that the old URL is relative
            $url = preg_replace( '@^https?://(.*?)/@', '/', $url );
            $url = preg_replace( '@^https?://(.*?)$@', '/', $url );

            // No hash
            $url = preg_replace( '/#.*$/', '', $url );

            // No new lines
            $url = preg_replace( "/[\r\n\t].*?$/s", '', $url );

            // Clean control codes
            $url = preg_replace( '/[^\PC\s]/u', '', $url );

            // Ensure a slash at start
            if ( substr( $url, 0, 1 ) !== '/' && $regex === false )
                    $url = '/'.$url;

            return $url;
    }
    
    
}
