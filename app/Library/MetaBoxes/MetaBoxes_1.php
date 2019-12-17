<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes;

/**
 * Library :  MetaBoxes
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Models\SocialAnalytics\FBMetaBoxes;
use CsSeoMegaBundlePack\Library\MetaBoxes\SocialMetaTagsOptions;

class MetaBoxes1 {
    
    /**
     * Hold Meta Boxes
     *
     * @var type 
     */
    private $metaBoxes;

    public function __construct(){
        add_action( 'CSMBP_meta_boxes', array( $this, 'add_social_scheduler' ),  1); 
        add_action( 'CSMBP_meta_boxes', array( $this, 'add_social_meta_tags_opsions' ),  2); 
    }

    /**
     * Generate all meta
     * 
     * @param type $post_type
     */
    public function get_all_meta_boxes( $post_type ){
        do_action( 'CSMBP_meta_boxes' );
        if( $this->metaBoxes ){
            foreach( $this->metaBoxes as $metabox ){
                add_meta_box( $metabox['id'], sprintf( $metabox['title'], Helper::get('PLUGIN_SHORT_NAME')), array( (new $metabox['callback'][0]), $metabox['callback'][1] ), $post_type, $metabox['context'], $metabox['priority']); 
            }
        }
    }
    
    /**
     * Generate Metaboxes
     */
    public function add_social_scheduler(){
        $this->metaBoxes = FBMetaBoxes::meta_boxes();
    }
    
    /**
     * Social Meta Tags 
     * 
     * @since 1.0.0
     */
    public function add_social_meta_tags_opsions(){
        $this->metaBoxes = array_merge( $this->metaBoxes, SocialMetaTagsOptions::meta_boxes() );
    }
    
}
