<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes;

/**
 * Library :  CsPostMetaBoxes
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use Herbert\Framework\Application;
use CsSeoMegaBundlePack\Helper;
use Herbert\Framework\Enqueue;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\FrontEnd\CsGetOptions;
use CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes\FBMetaBoxes;
use CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes\SocialMetaTagsOptions;
use CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes\SeoMetaBoxes;

class CsPostMetaBoxes{
    
    
    /**
     * Hold Options
     *
     * @var type 
     */
    protected $options;

    /**
     * Hold Meta Boxes
     *
     * @var type 
     */
    protected $metaBoxes;

    public function __construct(){
        //get options
        $this->get_options();
        
        add_action( 'CSMBP_meta_boxes', array( $this, 'add_social_scheduler' ),  1); 
        add_action( 'CSMBP_meta_boxes', array( $this, 'add_social_meta_options' ),  2); 
        add_action( 'CSMBP_meta_boxes', array( $this, 'add_seo_meta_options' ),  3); 
        
        /**load library script for this section**/
        $this->load_library_script();
        
        /**load custom script for this section**/
        add_action( 'admin_footer', array( $this, 'load_custom_script' ) );
    }
    
    /**
     * Get Options
     * 
     * @since 1.0.0 
     */
    private function get_options(){
        $this->options = CsGetOptions::get_instance()->options;
        if( !empty( $post_id = get_the_id() )){
            $post = CsQuery::Cs_Get_Post_Custom( $post_id );
            $this->options = isset( $this->options ) ? (object)array_merge( (array)$this->options, array( 'post' => $post )) : $this->options;
        }
    }

    /**
     * Generate all meta
     * 
     * @param type $post_type
     */
    public function get_all_meta_boxes( $post_type ){
        do_action( 'CSMBP_meta_boxes' );
        if( $this->metaBoxes ){
            $i = 0;
            foreach( $this->metaBoxes as $metabox ){
                add_meta_box( Helper::get('PLUGIN_SECTION_ID') . $metabox['id'] .'_'. $i , sprintf( $metabox['title'], Helper::get('PLUGIN_SHORT_NAME')), array( (new $metabox['callback'][0]), $metabox['callback'][1] ), $post_type, $metabox['context'], $metabox['priority'], $this->options ); 
                $i++;
            }
        }
    }
    
    /**
     * Social Scheduler
     * 
     * @since 1.0.0
     */
    public function add_social_scheduler(){
        $this->metaBoxes = FBMetaBoxes::meta_box();
    }
    
    /**
     * Social Meta Tags 
     * 
     * @since 1.0.0
     */
    public function add_social_meta_options(){
        $this->metaBoxes = array_merge( $this->metaBoxes, SocialMetaTagsOptions::meta_box() );
    }
    
    /**
     * Social Meta Tags 
     * 
     * @since 1.0.0
     */
    public function add_seo_meta_options(){
        $this->metaBoxes = array_merge( $this->metaBoxes, SeoMetaBoxes::meta_box() );
    }
    
    /**
     * Custom style & scripts for metaboxes
     */
    public function load_library_script(){
        
    }

    /**
     * Load Common Scripts
     */
    public function load_custom_script(){
        ?>
        <script type="text/javascript">
            /**
             * Tabs Script
             * 
             * @version 1.0.0
             * @param {type} $
             */
            jQuery(document).ready(function( $ ) {
                var $tabs = jQuery('.horizontalTabs');
                $tabs.responsiveTabs({
                    rotate: false,
                    startCollapsed: 'accordion',
                    collapsible: 'accordion',
                    setHash: true
                });
            });
            
        </script>
        <?php
    }
    
}
