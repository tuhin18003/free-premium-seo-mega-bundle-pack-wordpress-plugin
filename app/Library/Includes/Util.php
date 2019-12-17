<?php namespace CsSeoMegaBundlePack\Library\Includes;
/**
 * Utility Class
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class Util {
    
    protected static $is_mobile;			// is_mobile cached value
    protected static $mobile_obj;			// SuextMobileDetect class object
    protected static $active_plugins;		// active site and network plugins
    protected static $active_site_plugins;
    protected static $active_network_plugins;
    protected static $crawler_name;			// saved crawler name from user-agent
    protected static $filter_values = array();	// saved filter values
    protected static $user_exists = array();	// saved user_exists() values
    protected static $locales = array();		// saved get_locale() values
                
    /**
     * Determine post exists
     * 
     * @param type $post_id
     * @return type
     */
    public static function is_post_exists( $post_id ) {
          return is_string( get_post_status( $post_id ) );
    }
    
    /**
     * 
     * Admin screen base 
     * 
     * @param type $screen
     * @return boolean | string
     */
    public static function get_screen_base( $screen = false ) {
        if ( $screen === false &&
                function_exists( 'get_current_screen' ) )
                        $screen = get_current_screen();
        if ( isset( $screen->base ) )
                return $screen->base;
        else return false;
    }
    
    /**
     * determine is author page
     * 
     * @param type $user_id
     * @return type
     */
    public static function is_author_page( $user_id = 0 ) {
        return self::is_user_page( $user_id );
    }

    /**
     * Determine is user page
     * 
     * @param type $user_id
     * @return type
     */
    public static function is_user_page( $user_id = 0 ) {
        $ret = false;
        if ( is_numeric( $user_id ) && $user_id > 0 ) {
                $ret = self::user_exists( $user_id );
        } elseif ( is_author() ) {
                $ret = true;
        }
        return $ret;
    }

    /**
     * Check user exists
     * 
     * @global type $wpdb
     * @param type $user_id
     * @return boolean
     */
    public static function user_exists( $user_id ) {
        if ( is_numeric( $user_id ) && $user_id > 0 ) {	// true is not valid
                $user_id = (int) $user_id;	// cast as integer for array
                if ( isset( self::$user_exists[$user_id] ) )
                        return self::$user_exists[$user_id];
                else {
                        global $wpdb;
                        $select_sql = 'SELECT COUNT(ID) FROM '.$wpdb->users.' WHERE ID = %d';
                        return self::$user_exists[$user_id] = $wpdb->get_var( $wpdb->prepare( $select_sql, $user_id ) ) ? true : false;
                }
        } else return false;
    }
    
    
    
    /**
     * Check amp
     * 
     * @return type
     */
    public static function is_amp() {
        if ( ! defined( 'AMP_QUERY_VAR' ) ) {
                $is_amp = false;
        } else {
                $is_amp = get_query_var( AMP_QUERY_VAR, false ) ? true : false;
        }
        return $is_amp;
    }
    
    /**
     * Returns a custom site name or the default WordPress site name.
     * 
     * @param array $opts
     * @param type $mixed
     * @return type
     */
    public static function get_site_name( $opts, $mixed = 'current' ) {
        $ret = self::get_locale_opt( 'site_name', $opts, $mixed );
        if ( empty( $ret ) ) {
                return get_bloginfo( 'name', 'display' );
        } else {
                return $ret;
        }
    }
    
    /**
     * Returns a custom site description or the default WordPress site description / tagline.
     * 
     * @param array $opts
     * @param type $mixed
     * @return type
     */
    public static function get_site_description( $opts, $mixed = 'current' ) {
        $ret = self::get_locale_opt( 'site_desc', $opts, $mixed );
        if ( empty( $ret ) ) {
                return get_bloginfo( 'description', 'display' );
        } else {
                return $ret;
        }
    }
    
    /**
     * Return options
     * 
     * @param type $key
     * @param array $opts
     * @param type $mixed
     * @return type
     */
    private static function get_locale_opt( $key, $opts, $mixed = 'current' ){
        if( isset(  $opts->aios_web_graph_options ) ){
            return isset( $opts->aios_web_graph_options[ $key ] ) ? $opts->aios_web_graph_options[ $key ] : '';
        }
    }
    
    /**
     * Get admin url
     * 
     * @param type $path
     * @return type
     */
    public static function get_admin_url( $path = false ){
        if( empty( $path ) ){
            return admin_url();
        }else{
            return admin_url( $path );
        }
    }
    
    public static function get_site_url( $domain = false, $path = '', $scheme = null ){
        if( $domain ){
            return str_replace( array( 'http://', 'https://'), '', get_site_url( null, $path,  $scheme ) );
        }else{
            return get_site_url( null, $path,  $scheme );
        }
    }

    /**
     * Sanitize tag
     * 
     * @param type $tag
     * @return type
     */
    public static function sanitize_tag( $tag ) {
        $tag = sanitize_title_with_dashes( $tag, '', 'display' );
        $tag = urldecode( $tag );
        return $tag;
    }

    /**
     * Sanitize hastags
     * 
     * @param type $tags
     * @return type
     */
    public static function sanitize_hashtags( $tags = array() ) {
        // truncate tags that start with a number (not allowed)
        return preg_replace( array( '/^[0-9].*/', '/[ \[\]#!\$\?\\\\\/\*\+\.\-\^]/', '/^.+/' ),
                array( '', '', '#$0' ), $tags );
    }
    
    /**
     * Array to hastags
     * 
     * @param type $tags
     * @return type
     */
    public static function array_to_hashtags( $tags = array() ) {
        // array_filter() removes empty array values
        return trim( implode( ' ', array_filter( self::sanitize_hashtags( $tags ) ) ) );
    }
    
    /**
     * Get url
     * 
     * @return type
     */
    public static function get_url(){
        // strip out tracking query arguments by facebook, google, etc.
        return preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|'.
                'utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i',
                        '$1', GeneralHelpers::Cs_GetPort().'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] );
    }
    
    /**
     * hashtag formater
     */
    public static function Cs_HashTagFormat( $hashtag, $limit ){
        if( ! is_array( $hashtag ) ) return false;

        $tags = array_slice( $hashtag, 0, $limit );
        return self::array_to_hashtags( $tags ); //add initial space
    }
    
    /**
     * Sanitize text
     * 
     * @param type $str
     * @return type
     */
    public static function Cs_SanitizeText( $str ){
//        $str = strip_tags( $str );
        return htmlspecialchars_decode( $str );
    }
    
    

    /**
     * decode html
     * 
     * @staticvar type $charset
     * @param type $encoded
     * @return type
     */
    public static function Cs_DecodeHtml( $encoded ) {
        if ( strpos( $encoded, '&' ) === false ) {
                return $encoded;
        }

        static $charset = null;

        if ( ! isset( $charset  ) ) {
                $charset = get_bloginfo( 'charset' );
        }

        return html_entity_decode( self::Cs_DecodeUtf8( $encoded ), ENT_QUOTES, $charset );
    }

    /**
     * Encode utf8
     * 
     * @param type $decoded
     * @return type
     */
    public static function Cs_EncodeUtf8( $decoded ) {
        if ( mb_detect_encoding( $decoded, 'UTF-8') !== 'UTF-8' ) {
                $encoded = utf8_encode( $decoded );
        } else {
                $encoded = $decoded;
        }
        return $encoded;
    }
    
    /**
     * decode utf8
     * 
     * @param type $encoded
     * @return type
     */
    public static function Cs_DecodeUtf8( $encoded ) {
        if ( strpos( $encoded, '&#' ) === false ) {
                return $encoded;
        }
        $encoded = preg_replace( '/&#8230;/', '...', $encoded );
        if ( ! function_exists( 'mb_decode_numericentity' ) ) {
            return $encoded;
        }

        $decoded = preg_replace_callback( '/&#\d{2,5};/u', array( __CLASS__, 'Cs_DecodeUtf8Entity' ), $encoded );

        return $decoded;
    }

    /**
     * Decode utf8 entity
     * 
     * @param type $matches
     * @return type
     */            
    public static function Cs_DecodeUtf8Entity( $matches ) {
        $convmap = array( 0x0, 0x10000, 0, 0xfffff );
        return mb_decode_numericentity( $matches[0], $convmap, 'UTF-8' );
    }

    /**
     * String html
     * 
     * @param type $text
     * @return type
     */            
    public static function Cs_StripHtml( $text ) {
        $text = self::strip_shortcodes( $text );                                // Remove any remaining shortcodes.
        $text = preg_replace( '/[\s\n\r]+/s', ' ', $text );                     // Put everything on one line.
        $text = preg_replace( '/<\?.*\?' . '>/U', ' ', $text);                  // Remove php.
        $text = preg_replace( '/<script\b[^>]*>(.*)<\/script>/Ui', ' ', $text); // Remove javascript.
        $text = preg_replace( '/<style\b[^>]*>(.*)<\/style>/Ui', ' ', $text);   // Remove inline stylesheets.
        $text = preg_replace( '/<\/p>/i', ' ', $text);                          // Replace end of paragraph with a space.
        $text = trim( strip_tags( $text ) );                                    // Remove remaining html tags.
        $text = preg_replace( '/(\xC2\xA0|\s)+/s', ' ', $text );                // Replace 1+ spaces to a single space.
        return trim( $text );
    }
                
    /**
     * Strip shortcode
     * 
     * @param type $text
     * @return type
     */            
    public static function Cs_StripShortcodes( $text ) {
        if ( strpos( $text, '[' ) === false ) { // Stop here if no shortcodes.
            return $text;
        }
        $text = strip_shortcodes( $text );      // Remove registered shortcodes.
        if ( strpos( $text, '[' ) === false ) { // Stop here if no shortcodes.
                return $text;
        }
        $shortcodes_preg = array(
                '/\[\/?(cs_element_|mk_|rev_slider_|vc_)[^\]]+\]/',
        );
        $text = preg_replace( $shortcodes_preg, ' ', $text );
        return $text;
    }
    /**
     * Get locale
     * 
     * @global type $wp_local_package
     * @param type $mixed
     * @return type
     */            
    public static function Cs_GetLocale( $mixed = 'current' ) {
        if ( $mixed === 'default' ) {
                global $wp_local_package;
                if ( isset( $wp_local_package ) ) {
                        $locale = $wp_local_package;
                }
                if ( defined( 'WPLANG' ) ) {
                        $locale = WPLANG;
                }
                if ( is_multisite() ) {
                        if ( ( $ms_locale = get_option( 'WPLANG' ) ) === false ) {
                                $ms_locale = get_site_option( 'WPLANG' );
                        }
                        if ( $ms_locale !== false ) {
                                $locale = $ms_locale;
                        }
                } else {
                        $db_locale = get_option( 'WPLANG' );
                        if ( $db_locale !== false ) {
                                $locale = $db_locale;
                        }
                }
                if ( empty( $locale ) ) {
                        $locale = 'en_US'; // Just in case.
                }
        } else {
            if ( is_admin() && function_exists( 'get_user_locale' ) ) { // Since wp 4.7.
                    $locale = get_user_locale();
            } else {
                    $locale = get_locale();
            }
        }

        return $locale;
    }
                
    /**
     * Get text by limit
     * 
     * @param type $text
     * @param type $maxlen
     * @param type $trailing
     * @param type $cleanup_html
     * @return type
     */            
    public static function Cs_LimitTextLength( $text, $maxlen = 300, $trailing = '', $cleanup_html = true ) {
        if ( true === $cleanup_html ) {
                $text = self::Cs_CleanupHtmlTags( $text );				
        }

        
        $charset = get_bloginfo( 'charset' );
        $text = html_entity_decode( self::Cs_DecodeUtf8( $text ), ENT_QUOTES, $charset );

        if ( $maxlen > 0 ) {
                $trailing_length = (int) mb_strlen( $trailing );
                if ( $trailing_length > $maxlen ){
                    $trailing = substr( $trailing, 0, $maxlen );			
                }
                
                if ( mb_strlen( $text ) > $maxlen ) {
                    $text = mb_substr( $text, 0, ($maxlen - $trailing_length) );
                    $text = trim( preg_replace( '/[^ ]*$/', '', $text ) );		
                    $text = preg_replace( '/[,\.]*$/', '', $text );			
                }
                
                $text = $text.$trailing;						
        }
        
        $text = preg_replace( '/&nbsp;/', ' ', $text);					

        return $text;
    }

    /**
     * Cleanup html
     * 
     * @param type $text
     * @param type $strip_tags
     * @param type $use_img_alt
     * @return type
     */
    public static function Cs_CleanupHtmlTags( $text, $strip_tags = true, $use_img_alt = false ) {
        $alt_text = '';
        $alt_prefix = 'Image:';

        $text = self::Cs_StripShortcodes( $text );					
        $text = preg_replace( '/[\s\n\r]+/s', ' ', $text );				
        $text = preg_replace( '/<\?.*\?'.'>/U', ' ', $text);				
        $text = preg_replace( '/<script\b[^>]*>(.*)<\/script>/Ui', ' ', $text);		
        $text = preg_replace( '/<style\b[^>]*>(.*)<\/style>/Ui', ' ', $text);		
        $text = preg_replace( '/<!---ignore-->(.*?)<!--\/-ignore-->/Ui', ' ', $text);	
        
        if ( $strip_tags ) {
                $text = preg_replace( '/<\/p>/i', ' ', $text);				
                $text_stripped = trim( strip_tags( $text ) );				

                if ( $text_stripped === '' && $use_img_alt ) {				
                        if ( strpos( $text, '<img ' ) !== false &&
                                preg_match_all( '/<img [^>]*alt=["\']([^"\'>]*)["\']/Ui', 
                                        $text, $all_matches, PREG_PATTERN_ORDER ) ) {

                                foreach ( $all_matches[1] as $alt ) {
                                        $alt = trim( $alt );
                                        if ( ! empty( $alt ) ) {
                                                $alt = empty( $alt_prefix ) ? 
                                                        $alt : $alt_prefix.' '.$alt;

                                                $alt_text .= ( strpos( $alt, '.' ) + 1 ) === strlen( $alt ) ? 
                                                        $alt.' ' : $alt.'. ';
                                        }
                                }
                        }
                        $text = $alt_text;
                } else $text = $text_stripped;
        }

        $text = preg_replace( '/(\xC2\xA0|\s)+/s', ' ', $text );	

        return trim( $text );
    }            
    
    
}
