<?php namespace CsSeoMegaBundlePack\Library\Backend\Messages;

/**
 * All Constant Text
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class ConstantText {
    
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
     * Get property groups
     * 
     * @param type $group_id
     * @return type
     */
    public function property_groups( $group_id = false ){
        $properties = array(
            'keyword_sugg_group' => array(
               __( 'Google', SR_TEXTDOMAIN ),
               __( 'Bing', SR_TEXTDOMAIN ),
               __( 'Yahoo', SR_TEXTDOMAIN ),
               __( 'youtube', SR_TEXTDOMAIN ),
               __( 'Ebay', SR_TEXTDOMAIN ),
               __( 'Amazon', SR_TEXTDOMAIN ),
           ),
        );
        
        
        return $group_id === true ? ( in_array( $group_id, $properties) ? $properties[ $group_id ] :  '' ) : $properties;
    }
    
}
