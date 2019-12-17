<?php namespace CsSeoMegaBundlePack\Library;

/**
 * Global Meta Handler
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

class CsGlobalTaxonomyMeta {
    
    /**
     * Class Constructor
     */
    function __construct() {
    }

    /**
     * Get taxonomy meta
     * 
     * @param type $term
     * @param type $taxonomy
     * @param type $meta
     * @return boolean
     */
    public static function get_term_meta( $term, $taxonomy, $meta = null ) {
        /* Figure out the term id */
        if ( is_int( $term ) ) {
                $term = get_term_by( 'id', $term, $taxonomy );
        }
        elseif ( is_string( $term ) ) {
                $term = get_term_by( 'slug', $term, $taxonomy );
        }

        if ( is_object( $term ) && isset( $term->term_id ) ) {
                $term_id = $term->term_id;
        }
        else {
                return false;
        }

        $tax_meta = self::get_term_tax_meta( $term_id, $taxonomy );

        /*
        Either return the complete array or a single value from it or false if the value does not exist
                   (shouldn't happen after merge with defaults, indicates typo in request)
        */
        if ( ! isset( $meta ) ) {
                return $tax_meta;
        }


        if ( isset( $tax_meta[ 'aios_' . $meta ] ) ) {
                return $tax_meta[ 'aios_' . $meta ];
        }

        return false;
    }
    
    private static function get_term_tax_meta( $term_id, $taxonomy ){
        
    }
    
    /**
     * Check mutitple terms query
     * 
     * @global type $wp_query
     * @return boolean
     */
    public static function is_multiple_terms_query() {
            global $wp_query;

            if ( ! is_tax() && ! is_tag() && ! is_category() ) {
                    return false;
            }

            $term          = get_queried_object();
            $queried_terms = $wp_query->tax_query->queried_terms;

            if ( empty( $queried_terms[ $term->taxonomy ]['terms'] ) ) {
                    return false;
            }

            return count( $queried_terms[ $term->taxonomy ]['terms'] ) > 1;
    }

}
