<?php namespace CsSeoMegaBundlePack\Models\CommonQuery;

/**
 * Global Options Handler
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class CsGlobalOptions {
    
    /**
     * Global Options
     *
     * @var type array
     */
    public static $options;
    
    /**
     * Default Options
     *
     * @var type array
     */
    private static $default_options = array(
        'webmaster_verify_code' => 'aios_webmaster_verify_meta',
        'webmaster_verify_status'=> 'aios_webmaster_verify_status',
        'google_settings' => 'aios_google_settings'
    );
        
    /**
     * Class Constructor
     */
    function __construct() {
    }

    /**
     * Get Options
     * 
     * @param type $options
     * @return object
     */
    public static function get_options( $options = array(), $json_array = false ){
        $get_options = [];
        foreach( $options as $option ){
            $get_options = array_merge( $get_options, (array)self::get_option($option, $json_array));
        }
        return (object)$get_options;
    }
    
    /**
     * Get option
     * 
     * @param type $option_name
     * @return boolean|array
     */
    private static function get_option( $option_name, $json_array = false ){
        if( isset( self::$default_options[$option_name])){ // json array option
            return array( $option_name => CsQuery::Cs_Get_Option(array(
                'option_name' => self::$default_options[$option_name],
                'json' => true,
            )));
        }else if($json_array){ // single option
            return array(
                $option_name => CsQuery::Cs_Get_Option(array(
                    'option_name' => $option_name,
                    'json' => true,        
                    'json_array' => true
                ))
            );
        }else{ // single option
            return array(
                $option_name => CsQuery::Cs_Get_Option(array(
                    'option_name' => $option_name,
                ))
            );
        }
        return false;
    }
}
