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
use CsSeoMegaBundlePack\Library\CsLoadedPageType;
use CsSeoMegaBundlePack\Library\CsReplaceVars;
use CsSeoMegaBundlePack\Library\CsGlobalTaxonomyMeta;


class MetaKeyword {
    
    private $replace;
    private $options;
    
    function __construct( $options ) {
        
        //get options
        $this->init_settings( $options );
        
        //instance replace
        $this->replace = new CsReplaceVars( $this->options );
    }

    /**
     * Init settings
     */
    private function init_settings( $options ){
        $this->options = $options;
    }

    /**
     * Generate meta description
     * 
     * @global type $post
     * @global type $wp_query
     */
    public function generate_metakeyword() {
        global $post, $wp_query;

        $keywords = '';

        if ( is_singular() ) {
                $keywords = CsGlobalMeta::getMetaValue( 'metakeywords' );
                if ( $keywords === '' && ( is_object( $post ) && ( ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) ) {
                        $keywords = $this->replace->ReplaceVars( $this->options[ 'metakey-' . $post->post_type ], $post );
                }
        }
        else {
                if ( CsLoadedPageType::is_home_posts_page() && isset($this->options['metakey-home-aios']) && $this->options['metakey-home-aios'] !== '' ) {
                        $keywords = $this->replace->ReplaceVars( $this->options['metakey-home-aios'], array() );
                }
                elseif ( CsLoadedPageType::is_home_static_page() ) {
                        $keywords = CsGlobalMeta::getMetaValu( 'metakeywords' );
                        if ( $keywords === '' && ( is_object( $post ) && ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) {
                                $keywords = $this->replace->ReplaceVars( $this->options[ 'metakey-' . $post->post_type ], $post );
                        }
                }
                elseif ( CsLoadedPageType::is_posts_page() ) {
                        $keywords = $this->get_keywords( get_post( get_option( 'page_for_posts' ) ) );
                }
                elseif ( is_category() || is_tag() || is_tax() ) {
                        $term = $wp_query->get_queried_object();

                        if ( is_object( $term ) ) {
                                $keywords = CsGlobalTaxonomyMeta::get_term_meta( $term, $term->taxonomy, 'metakey' );
                                if ( ( ! is_string( $keywords ) || $keywords === '' ) && ( isset( $this->options[ 'metakey-tax-' . $term->taxonomy ] ) && $this->options[ 'metakey-tax-' . $term->taxonomy ] !== '' ) ) {
                                        $keywords = $this->replace->ReplaceVars( $this->options[ 'metakey-tax-' . $term->taxonomy ], $term );
                                }
                        }
                }
                elseif ( is_author() ) {
                        $author_id = get_query_var( 'author' );
                        $keywords  = get_the_author_meta( 'metakey', $author_id );
                        if ( ! $keywords && $this->options['metakey-author-aios'] !== '' ) {
                                $keywords = $this->replace->ReplaceVars( $this->options['metakey-author-aios'], $wp_query->get_queried_object() );
                        }
                }
                elseif ( is_post_type_archive() ) {
                        $post_type = get_query_var( 'post_type' );
                        if ( is_array( $post_type ) ) {
                                $post_type = reset( $post_type );
                        }
                        if ( isset( $this->options[ 'metakey-ptarchive-' . $post_type ] ) && $this->options[ 'metakey-ptarchive-' . $post_type ] !== '' ) {
                                $keywords = $this->replace->ReplaceVars( $this->options[ 'metakey-ptarchive-' . $post_type ], $wp_query->get_queried_object() );
                        }
                }
        }

        if ( is_string( $keywords ) && $keywords !== '' ) {
                echo "\n", '<meta name="keywords" content="', esc_attr( strip_tags( stripslashes( $keywords ) ) ), '"/>';
        }
    }
    
    
    /**
     * Getting the keywords
     *
     * @param WP_Post $post The post object with the values.
     *
     * @return string
     */
    private function get_keywords( $post ) {
            $keywords        = CsGlobalMeta::getMetaValue( 'metakeywords', $post->ID );
            $option_meta_key = 'metakey-' . $post->post_type;

            if ( $keywords === '' && ( is_object( $post ) && ( isset( $this->options[ $option_meta_key ] ) && $this->options[ $option_meta_key ] !== '' ) ) ) {
                    $keywords = $keywords = $this->replace->ReplaceVars( $this->options[ $option_meta_key ], $post );
            }

            return $keywords;
    }

    
}
