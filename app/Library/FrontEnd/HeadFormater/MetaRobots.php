<?php namespace CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater;
/**
 * MetaRobot Tag
 * 
 * @package FrontEnd
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalMeta;
use CsSeoMegaBundlePack\Library\CsGlobalTaxonomyMeta;

class MetaRobots {
    /**
     * Hold options
     *
     * @var type 
     */
    public $options;
    
    /**
     * Hold page mode
     *
     * @var type 
     */
    public $page_mod;
    
    /**
     * Construc funciton
     * 
     * @param type $options
     * @param type $page_mod
     */
    function __construct( ) {
        //
    }


    /**
     * Generate meta description
     * 
     * @global type $wp_query
     */
    public function generate_meta_robots() {
        global $wp_query;
        
        $ret              = array( 'name' => array() );
        $robots           = array();
        $robots['index']  = 'index';
        $robots['follow'] = 'follow';
        $robots['other']  = array();
        
        if ( $this->page_mod['is_singular'] ) {
            $option_name = 'noindex-' . $this->page_mod['obj']->wp_options->post_type;
            $noindex     = isset( $this->options[ $option_name ] ) && $this->options[ $option_name ] ===  'off';
            $private     = 'private' === $this->page_mod['obj']->wp_options->post_status;

            if ( $noindex || $private ) {
                $robots['index'] = 'noindex';
            }
            $robots = $this->robots_for_single_post( $robots );
        }
        else {
                if ( $this->page_mod['is_search'] || $this->page_mod['is_404'] ) {
                        $robots['index'] = 'noindex';
                }
                elseif ( $this->page_mod['is_tax'] || $this->page_mod['is_tag'] || $this->page_mod['is_category'] ) {
                        $term = $wp_query->get_queried_object();
                        if ( is_object( $term ) && ( isset( $this->options[ 'noindex-tax-' . $term->taxonomy ] ) && $this->options[ 'noindex-tax-' . $term->taxonomy ] === 'off' ) ) {
                                $robots['index'] = 'noindex';
                        }

                        // Three possible values, index, noindex and default, do nothing for default.
                        $term_meta = CsGlobalTaxonomyMeta::get_term_meta( $term, $term->taxonomy, 'noindex' );
                        if ( is_string( $term_meta ) && 'default' !== $term_meta ) {
                                $robots['index'] = $term_meta;
                        }

                        if ( CsGlobalTaxonomyMeta::is_multiple_terms_query() ) {
                                $robots['index'] = 'noindex';
                        }
                } elseif ( 
                            ( is_author() && isset($this->options['noindex-author-aios']) && $this->options['noindex-author-aios'] === 'off' ) ||
                            ( is_date() && isset($this->options['noindex-archive-aios']) && $this->options['noindex-archive-aios'] === 'off' ) 
                        ) {
                        $robots['index'] = 'noindex';
                }
        elseif ( $this->page_mod['is_home'] || $this->page_mod['is_home_page'] || $this->page_mod['is_home_index'] ) {
                    
                    if( isset($this->options['noindex-home-aios']) && $this->options['noindex-home-aios'] === 'off'){
                        $robots['index'] = 'noindex';
                    }
                    
                    if( isset($this->options['nofollow-home-aios']) && $this->options['nofollow-home-aios'] === 'off'){
                        $robots['follow'] = 'nofollow';
                    }
                    

                    if ( isset($this->options['archive-home-aios']) && $this->options['archive-home-aios'] === 'off' ) {
                        $robots['other'][] = 'noarchive';
                    }
                    
                    if ( isset($this->options['noodp-home-aios']) && $this->options['noodp-home-aios'] === 'on' ) {
                        $robots['other'][] = 'noodp';
                    }

                    if ( isset($this->options['noydir-home-aios']) && $this->options['noydir-home-aios'] === 'on' ) {
                        $robots['other'][] = 'noydir';
                    }
                    
                    if ( get_query_var( 'paged' ) > 1 && $this->options['noindex-subpages-aios'] === 'off' ) {
                            $robots['index'] = 'noindex';
                    }
                    
                    $page_for_posts = get_option( 'page_for_posts' );
                    if ( $page_for_posts ) {
                            $robots = $this->robots_for_single_post( $robots, $page_for_posts );
                    }
                    
                    unset( $page_for_posts );

                }
                elseif ( $this->page_mod['is_post_type_archive'] ) {
                        $post_type = get_query_var( 'post_type' );

                        if ( is_array( $post_type ) ) {
                                $post_type = reset( $post_type );
                        }

                        if ( isset( $this->options[ 'noindex-ptarchive-' . $post_type ] ) && $this->options[ 'noindex-ptarchive-' . $post_type ] === 'off' ) {
                                $robots['index'] = 'noindex';
                        }
                }

                $is_paged         = isset( $wp_query->query_vars['paged'] ) && ( $wp_query->query_vars['paged'] && $wp_query->query_vars['paged'] > 1 );
                $noindex_subpages = isset($this->options['noindex-subpages-aios']) ? $this->options['noindex-subpages-aios'] === 'off' : '';
                if ( $is_paged && $noindex_subpages ) {
                        $robots['index'] = 'noindex';
                }

                if ( isset($this->options['noodp']) && $this->options['noodp'] === 'off' ) {
                        $robots['other'][] = 'noodp';
                }
//                unset( $robot );
        }

        // Force override to respect the WP settings.
        if ( '0' == get_option( 'blog_public' ) || isset( $_GET['replytocom'] ) ) {
                $robots['index'] = 'noindex';
        }

        $robotsstr = $robots['index'] . ',' . $robots['follow'];

        if ( $robots['other'] !== array() ) {
                $robots['other'] = array_unique( $robots['other'] ); // TODO Most likely no longer needed, needs testing.
                $robotsstr .= ',' . implode( ',', $robots['other'] );
        }

        $robotsstr = preg_replace( '`^index,follow,?`', '', $robotsstr );

        if ( is_string( $robotsstr ) && $robotsstr !== '' ) {
            $ret['name'] = array( 'robots' => esc_attr( $robotsstr ) );
        }
        
        return $ret;
    }
    
    /**
     * Robots for single post
     * 
     * @param type $robots
     * @param type $post_id
     * @return string
     */
    private function robots_for_single_post( $robots, $post_id = 0 ) {
            
                if( isset($this->page_mod['obj']->CSMBP_options->{'meta-robots-noindex'}) && $this->page_mod['obj']->CSMBP_options->{'meta-robots-noindex'} == 'off' ){
                    $robots['index'] = 'noindex';
                }else{
                    $robots['index'] = 'index';
                }
                
                if( isset($this->page_mod['obj']->CSMBP_options->{'meta-robots-nofollow'}) && $this->page_mod['obj']->CSMBP_options->{'meta-robots-nofollow'} == 'off' ){
                    $robots['follow'] = 'nofollow';
                }else{
                    $robots['follow'] = 'follow';
                }
                
                if( isset($this->page_mod['obj']->CSMBP_options->{'meta-robots-archive'}) && $this->page_mod['obj']->CSMBP_options->{'meta-robots-archive'} == 'off' ){
                    $robots['other'][] = 'noarchive';
                }
                
                if( isset($this->page_mod['obj']->CSMBP_options->{'meta-robots-noodp'}) && $this->page_mod['obj']->CSMBP_options->{'meta-robots-noodp'} == 'on' ){
                    $robots['other'][] = 'noodp';
                }
                
                if( isset($this->page_mod['obj']->CSMBP_options->{'meta-robots-noodir'}) && $this->page_mod['obj']->CSMBP_options->{'meta-robots-noodir'} == 'on' ){
                    $robots['other'][] = 'noydir';
                }
                
		return $robots;
	}
        
}