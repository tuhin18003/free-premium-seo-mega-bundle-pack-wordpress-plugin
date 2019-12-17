<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * Optons Handler
 * 
 * @package Frontend
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalOptions;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class CsGetOptions {
    
    /**
     * @var type array
     */
    private $required_options = [ 
        'aios_webmaster_verify_meta', 
        'aios_google_settings', 
        'aios_title_meta_default', 
        'aios_meta_robots_status', 
        'aios_metas_stop_render', 
        'aios_web_pub_options', 
        'aios_web_graph_options' 
        ];
    
    /**
     * instance
     *
     * @var type 
     */
    private static $instance;
    
    
    /**
     * Options
     *
     * @var array
     */
    public $options;
    
    public function __construct() {
        //load options
//        $this->get_options();
        $this->Cs_GetOptions();
    }

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
     * Get Options & cache it
     * 
     * @since 1.0.0
     */
    public function get_options(){
        $cache_id = Helper::get('PLUGIN_DATA_PREFIX') . GeneralHelpers::Cs_Md5_Hash( 'head_optons' );
//        if( ( $this->options = get_transient( $cache_id ) ) === false ){
            $this->options = CsGlobalOptions::get_options( $this->required_options, true );
            set_transient( $cache_id, $this->options, 6 * MONTH_IN_SECONDS );
//        }
    }
    
    /**
     * Get Options
     * 
     * @param type $options
     * @return type
     */
    public function Cs_GetOptions(){
        $get_options = [];
        foreach( $this->required_options as $option ){
            $get_options = array_merge( $get_options, (array) $this->_json_array( $option ) );
        }
        $this->options = (object)$get_options;
        return;
    }
    
    /**
     * Get json array option
     * 
     * @param type $option_name
     * @return type
     */
    private function _json_array( $option_name ){
        return array(
            $option_name => CsQuery::Cs_Get_Option(array(
                'option_name' => $option_name,
                'json' => true,        
                'json_array' => true
            ))
        );
    }
    
}
