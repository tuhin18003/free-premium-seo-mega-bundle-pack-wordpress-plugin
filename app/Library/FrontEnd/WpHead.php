<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * Wp Head Actions Handler
 * 
 * @package Frontend
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Library\CsLoadedPageType;

abstract class WpHead extends CsLoadedPageType {
    
    /**
     * Construct Function
     */
    function __construct() {
        
        //load actions
        $this->load_action_hooks();
        
        //load filters
        $this->load_filter_hooks();

    }
    
    /**
     * Load All Filters
     * 
     * @version 1.0.0
     */
    private function load_filter_hooks(){
        add_filter( 'pre_get_document_title', array( $this, 'Cs_WpTitle' ), 16);
        add_filter( 'wp_title', array( $this, 'Cs_WpTitle' ), 16, 3);
        add_filter( 'thematic_doctitle', array( $this, 'Cs_WpTitle' ), 16);
        add_filter( 'language_attributes', array( $this, 'Cs_OgpnsAttributes' ), 105, 2);
    }
    
    /**
     * Load actions
     * 
     * @verson 1.0.0
     */
    private function load_action_hooks(){
        add_action( 'wp', array( $this, 'page_mod' ) );
        add_action( 'wp_head', array( $this, 'Cs_RenderPageFunc' ), 1); //should placed before meta tag render
        add_action( 'wp_head', array( $this, 'Cs_RendersMetaTags' ), 1); //priority is same to above
    }
    
    protected abstract function Cs_WpTitle( $title, $separator = '', $separator_location = '');
    protected abstract function Cs_OgpnsAttributes( $html_attr );
    protected abstract function page_mod();
    protected abstract function Cs_RenderPageFunc();
    protected abstract function Cs_RendersMetaTags();
    
}
