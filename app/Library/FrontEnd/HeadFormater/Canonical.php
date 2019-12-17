<?php namespace CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater;
/**
 * Canonical
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalMeta;
use CsSeoMegaBundlePack\Library\CsGlobalTaxonomyMeta;
use CsSeoMegaBundlePack\Library\CsLoadedPageType;
use CsSeoMegaBundlePack\Library\CsReplaceVars;
use CsSeoMegaBundlePack\Library\Includes\SiteMaps\SiteMapsRouter;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class Canonical {
    
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
    public function generate_canonical() {
        $canonical          = false;
        $canonical_override = false;
        
        // Set decent canonicals for homepage, singulars and taxonomy pages.
        if ( is_singular() ) {
                $obj       = get_queried_object();
                $canonical = get_permalink( $obj->ID );

                $canonical_unpaged = $canonical;

                $canonical_override = CsGlobalMeta::getMetaValue( 'canonical' );

                // Fix paginated pages canonical, but only if the page is truly paginated.
                if ( get_query_var( 'page' ) > 1 ) {
                    $num_pages = ( substr_count( $obj->post_content, '<!--nextpage-->' ) + 1 );
                    if ( $num_pages && get_query_var( 'page' ) <= $num_pages ) {
                        if ( ! $GLOBALS['wp_rewrite']->using_permalinks() ) {
                                $canonical = add_query_arg( 'page', get_query_var( 'page' ), $canonical );
                        }
                        else {
                                $canonical = user_trailingslashit( trailingslashit( $canonical ) . get_query_var( 'page' ) );
                        }
                    }
                }
        }
        else {
                if ( is_search() ) {
                        $search_query = get_search_query();

                        // Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
                        if ( ! empty( $search_query ) && ! preg_match( '|^page/\d+$|', $search_query ) ) {
                                $canonical = get_search_link();
                        }
                }
                elseif ( is_front_page() ) {
                        $canonical = GeneralHelpers::home_url();
                }
                elseif ( CsLoadedPageType::is_posts_page() ) {

                        $posts_page_id = get_option( 'page_for_posts' );
                        $canonical     = CsGlobalMeta::getMetaValue( 'canonical', $posts_page_id );

                        if ( empty( $canonical ) ) {
                                $canonical = get_permalink( $posts_page_id );
                        }
                }
                elseif ( is_tax() || is_tag() || is_category() ) {

                        $term = get_queried_object();

                        if ( ! empty( $term ) && ! CsGlobalTaxonomyMeta::is_multiple_terms_query() ) {

                                $canonical_override = CsGlobalTaxonomyMeta::get_term_meta( $term, $term->taxonomy, 'canonical' );
                                $term_link          = get_term_link( $term, $term->taxonomy );

                                if ( ! is_wp_error( $term_link ) ) {
                                        $canonical = $term_link;
                                }
                        }
                }
                elseif ( is_post_type_archive() ) {
                        $post_type = get_query_var( 'post_type' );
                        if ( is_array( $post_type ) ) {
                                $post_type = reset( $post_type );
                        }
                        $canonical = get_post_type_archive_link( $post_type );
                }
                elseif ( is_author() ) {
                        $canonical = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
                }
                elseif ( is_archive() ) {
                        if ( is_date() ) {
                                if ( is_day() ) {
                                        $canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
                                }
                                elseif ( is_month() ) {
                                        $canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
                                }
                                elseif ( is_year() ) {
                                        $canonical = get_year_link( get_query_var( 'year' ) );
                                }
                        }
                }

                $canonical_unpaged = $canonical;

                if ( $canonical && get_query_var( 'paged' ) > 1 ) {
                        global $wp_rewrite;
                        if ( ! $wp_rewrite->using_permalinks() ) {
                                if ( is_front_page() ) {
                                        $canonical = trailingslashit( $canonical );
                                }
                                $canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
                        }
                        else {
                                if ( is_front_page() ) {
                                        $canonical = SiteMapsRouter::get_base_url( '' );
                                }
                                $canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
                        }
                }
        }

        $canonical_no_override = $canonical;

        if ( is_string( $canonical ) && $canonical !== '' ) {
                // Force canonical links to be absolute, relative is NOT an option.
                if (GeneralHelpers::is_url_relative( $canonical ) === true ) {
                        $canonical = GeneralHelpers::base_url( $canonical );
                }
        }

        if ( is_string( $canonical_override ) && $canonical_override !== '' ) {
                $canonical = $canonical_override;
        }

        return $canonical;
    }

    
}
