<?php namespace CsSeoMegaBundlePack\Library\Includes;
/**
 * FrontEnd : 
 * 
 * @category Frontend
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Helper;

class CsFilters {
    
    /**
     * instance
     *
     * @var type 
     */
    private static $instance;
    
    /**
     * Generate instance
     * 
     * @return type
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
                self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * All filters
     * 
     * @since 1.0.0
     * @version 1.0.0
     * @return array
     */
    public static function head_attr_filters(){
        $prefix = Helper::get('PLUGIN_DATA_PREFIX');
        return array(
            "{$prefix}head_attr_filter" => 'language_attributes'
        );
    }
    
    
}
