<?php namespace CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater;
/**
 * Title Format
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalMeta;
use CsSeoMegaBundlePack\Library\CsGlobalTaxonomyMeta;
use CsSeoMegaBundlePack\Library\CsReplaceVars;


class MetaDescription {
    
    private $replace;
    public $options;
    public $page_mod;
    
    function __construct() {
        
        //get options
        $this->init_settings();
        
        //instance replace
        $this->replace = new CsReplaceVars( $this->options );
    }

    /**
     * Init settings
     */
    private function init_settings(){
        $this->options = json_decode($this->options, true);
    }

    /**
     * Generate meta description
     * 
     * @global type $post
     * @global type $wp_query
     */
    public function generate_metadesc() {
        global $post, $wp_query;

        $metadesc          = '';
        $metadesc_override = false;
        $post_type         = '';
        $template          = '';

        if ( is_object( $post ) && ( isset( $post->post_type ) && $post->post_type !== '' ) ) {
                $post_type = $post->post_type;
        }

        if ($this->page_mod[ 'is_singular' ] ) {
                if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
                        $template = $this->options[ 'metadesc-' . $post_type ];
                        $term     = $post;
                }
                $metadesc_override = CsGlobalMeta::getMetaValue( 'metadesc' );
        }
        else {
                if ( $this->page_mod[ 'is_search'] ) {
                        $metadesc = '';
                }
                elseif (  $this->page_mod[ 'is_home_page'] ) {
                        $template = isset($this->options['metadesc-home-aios']) ? $this->options['metadesc-home-aios'] : '';
                        $term     = array();

                        if ( empty( $template ) ) {
                                $template = get_bloginfo( 'description' );
                        }
                }
                elseif ( $this->page_mod[ 'is_home_index'] ) {
                        $metadesc = CsGlobalMeta::getMetaValue( 'metadesc', get_option( 'page_for_posts' ) );
                        if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
                                $page     = get_post( get_option( 'page_for_posts' ) );
                                $template = $this->options[ 'metadesc-' . $post_type ];
                                $term     = $page;
                        }
                }
                elseif ( $this->page_mod[ 'is_home'] ) {
                        $metadesc = CsGlobalMeta::getMetaValue( 'metadesc' );
                        if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
                                $template = $this->options[ 'metadesc-' . $post_type ];
                        }
                }
                elseif ( $this->page_mod[ 'is_category'] || $this->page_mod['is_tag'] || $this->page_mod['is_tax'] ) {
                        $term              = $wp_query->get_queried_object();
                        $metadesc_override = CsGlobalTaxonomyMeta::get_term_meta( $term, $term->taxonomy, 'desc' );
                        if ( is_object( $term ) && isset( $term->taxonomy, $this->options[ 'metadesc-tax-' . $term->taxonomy ] ) ) {
                                $template = $this->options[ 'metadesc-tax-' . $term->taxonomy ];
                        }
                }
                elseif ( $this->page_mod[ 'is_author' ] ) {
                        $author_id = get_query_var( 'author' );
                        $metadesc  = get_the_author_meta( 'aios_metadesc', $author_id );
                        if ( ( ! is_string( $metadesc ) || $metadesc === '' ) && '' !== $this->options['metadesc-author-aios'] ) {
                                $template = $this->options['metadesc-author-wpseo'];
                        }
                }
                elseif ( $this->page_mod[ 'is_post_type_archive' ] ) {
                        $post_type = get_query_var( 'post_type' );
                        if ( is_array( $post_type ) ) {
                                $post_type = reset( $post_type );
                        }
                        if ( isset( $this->options[ 'metadesc-ptarchive-' . $post_type ] ) ) {
                                $template = $this->options[ 'metadesc-ptarchive-' . $post_type ];
                        }
                }
                elseif ( $this->page_mod[ 'is_archive' ] ) {
                        $template = $this->options['metadesc-archive-wpseo'];
                }

                // If we're on a paginated page, and the template doesn't change for paginated pages, bail.
                if ( ( ! is_string( $metadesc ) || $metadesc === '' ) && get_query_var( 'paged' ) && get_query_var( 'paged' ) > 1 && $template !== '' ) {
                        if ( strpos( $template, '%%page' ) === false ) {
                                $metadesc = '';
                        }
                }
        }

        $post_data = $post;

        if ( is_string( $metadesc_override ) && '' !== $metadesc_override ) {
                $metadesc = $metadesc_override;
                if ( isset( $term ) ) {
                        $post_data = $term;
                }
        }
        else if ( ( ! is_string( $metadesc ) || '' === $metadesc ) && '' !== $template ) {
                if ( ! isset( $term ) ) {
                        $term = $wp_query->get_queried_object();
                }

                $metadesc  = $template;
                $post_data = $term;
        }

        $metadesc = $this->replace->ReplaceVars( $metadesc, $post_data );

        return trim( $metadesc );
    }
    
}
