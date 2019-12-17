<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes;

/**
 * Library :  Term loader
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes\SocialMetaTagsOptions;
use CsSeoMegaBundlePack\Library\FrontEnd\CsGetOptions;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class CsTerm {
    protected $query_term_id = 0;
    protected $query_tax_slug = '';
    protected $query_tax_obj = false;
    private $meta_id;
    private $auth;
            
    function __construct() {
//        $this->get_options();
    }

    /**
     * Get Options
     * 
     * @since 1.0.0 
     */
    private function get_options(){
        $this->options = CsGetOptions::get_instance()->options;
        $this->meta_id = Helper::get('PLUGIN_SECTION_ID');
        $this->auth = Helper::get('PLUGIN_SHORT_NAME');
        $post = CsQuery::Cs_GetTermCustom( array( 'term_id' => $this->query_term_id, 'taxonomy' => $this->query_tax_slug) );
        $this->options = (object)array_merge( (array)$this->options, array( 'post' => $post ) );
        
        /**load custom script for this section**/
        add_action( 'admin_footer', array( $this, 'load_custom_script' ) );
    }
    
    public function add_actions(){
        if ( ( $this->query_tax_slug = GeneralHelpers::Cs_GetRequestValue( 'taxonomy' ) ) === '' ){
            return false;
        }
        if ( ( $this->query_term_id = GeneralHelpers::Cs_GetRequestValue( 'tag_ID' ) ) === '' )	{
            return false;
        }
        //init the meta tabs
        add_action( 'admin_init', array( $this, 'add_metaboxes' ) );
        
        //load the meta tabs
        add_action( $this->query_tax_slug.'_edit_form', array( &$this, 'show_metaboxes' ), 100, 1 );
        
//        add_action( 'created_'.$this->query_tax_slug, array( &$this, 'save_options' ), WPSSO_META_SAVE_PRIORITY, 2 );
        add_action( 'edited_'.$this->query_tax_slug, array( new SocialMetaTagsOptions(), 'on_save' ) );
        
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_metaboxes(){
        //get options
        $this->get_options();
        $this->query_tax_obj = get_taxonomy( $this->query_tax_slug );
//         pre_print( $this->query_tax_obj);
        
        $_meta_box = SocialMetaTagsOptions::meta_box();
        if( is_array($_meta_box) ){
            if( isset( $_meta_box[0] ) ){
                $metabox = array_filter($_meta_box[0]);
                add_meta_box( $this->meta_id . 'meta_boxes' , sprintf( $metabox['title'], $this->auth ), array( (new $metabox['callback'][0]), $metabox['callback'][1] ), "{$this->meta_id}term", $metabox['context'], $metabox['priority'], $this->options ); 
            }
        }
    }

    /**
     * Show MetaBoxes
     * 
     * @param type $term
     * @return type
     */
    public function show_metaboxes( $term ) {
        if ( ! current_user_can( $this->query_tax_obj->cap->edit_terms ) ){
            return;
        }
        echo "\n<!-- {$this->auth} term metabox section begin -->\n";
        echo "<div id=\"poststuff\">\n";
        do_meta_boxes( "{$this->meta_id}term", 'normal', $term );
        echo "\n</div><!-- .poststuff -->\n";
        echo "\n<!-- {$this->auth} term metabox section end -->\n";
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
