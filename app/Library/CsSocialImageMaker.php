<?php namespace CsSeoMegaBundlePack\Library;

/**
 * Library :  CsPostMetaBoxes
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Library\FrontEnd\CsGetOptions;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;

class CsSocialImageMaker {
    
    /**
     * Hold Options
     *
     * @var type 
     */
    private $options;
    
    public function __construct(){
        //get options
        $this->get_options();
    }
    
    /**
     * Get Options
     * 
     * @since 1.0.0 
     */
    private function get_options(){
        $this->options = CsGetOptions::get_instance()->options;
    }
    
    /**
     * Singleton class instance.
     * 
     * @return CsSocialImageMaker
     */
    public static function get_instance() {

        static $instance = null;

        if ( $instance == null ) {
            $instance = new self();
        }

        return $instance;
    }
    
    /**
     * Generate custom image dimension
     * 
     * @return boolean
     */
    public function add_plugin_image_sizes(){
        $prefix = Helper::get('PLUGIN_DATA_PREFIX');
//        pre_print( $this->options );
        
        foreach( MetaTagAssets::$MTA['og_image_attr'] as $id => $img ){
//            pre_print( $this->options->aios_web_pub_options );
            if( !isset( $this->options->aios_web_pub_options[ $img[0] ]) || empty( $_width = $this->options->aios_web_pub_options[ $img[0] ] ) ){
                $_width =  MetaTagAssets::$MTA['opt']['defaults'][ $img[0] ];
            }
            
            if( !isset( $this->options->aios_web_pub_options[ $img[1] ]) || empty( $_height = $this->options->aios_web_pub_options[ $img[1] ] ) ){
                $_height =  MetaTagAssets::$MTA['opt']['defaults'][ $img[1] ];
            }
            
            if( isset( $this->options->aios_web_pub_options[ $img[2] ] ) ){
                $_crop = array( 
                    $this->options->aios_web_pub_options["{$img[2]}_x"],
                    $this->options->aios_web_pub_options["{$img[2]}_y"]
                );
            }else{
                $_crop = array( 
                    MetaTagAssets::$MTA['opt']['defaults']["{$img[2]}_x"],
                    MetaTagAssets::$MTA['opt']['defaults']["{$img[2]}_y"]
                );
            }
            
            $_crop = isset( $_crop ) ? $_crop : true;
            
            add_image_size( "{$prefix}{$id}", $_width, $_height, $_crop );
        }
        
    }
    
    /**
     * change image dimension
     * 
     * @param type $image
     * @param type $post_id
     * @return type
     */
    public function image_editor_save_pre_image_sizes( $image, $post_id = false ) {
        if ( empty( $post_id ) ) {
                return $image;
        }
        
        $this->add_plugin_image_sizes();
        return $image;
    }
}

