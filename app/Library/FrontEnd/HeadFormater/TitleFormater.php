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

class TitleFormater {
    
    /**
     * Holds the generated title
     *
     * @var string
     */
    private $title = null;
    
     /**
     * Options
     *
     * @var array
     */
     public $options;
    
    /**
     * Instance of replace vars
     *
     * @var type 
     */
    private $replace;
    
    /**
     * Hold page mode
     *
     * @var type 
     */
    public $page_mod;
    
            
    function __construct( $options ) {
        $this->options = $options;
        //instance replace
        $this->replace = new CsReplaceVars();
        $this->replace->options = $this->options;
    }

    /**
     * Get titles from options
     */
    private function get_title_rule_from_option( $index, $var_source = array() ){
        if ( ! isset( $this->options->aios_title_meta_default[ $index ] ) || $this->options->aios_title_meta_default[ $index ] === '' ) {
            if ( is_singular() ) {
                return $this->replace->ReplaceVars( '{{title}} {{sep}} {{sitename}}', $var_source );
            }
            else {
                    return '';
            }
        }
        else {
            return $this->replace->ReplaceVars( $this->options->aios_title_meta_default[ $index ], $var_source);
        }
    }

    /**
     * Title
     * 
     * @param type $title
     * @param type $separator
     * @param type $separator_location
     * @return type
     */
    public function title( $title, $separator = '', $separator_location = '' ) {
        if ( is_null( $this->title ) ) {
            $this->title = $this->generate_title( $title, $separator_location );
        }
        return $this->title;
    }
    
    /**
     * Generate Title
     * 
     * @param type $title
     * @param type $separator_location
     * @return type
     */
    private function generate_title( $title, $separator_location ){
        if ( is_feed() ) {
            return $title;
        }
        $separator = $this->replace->ReplaceVars( '{{sep}}', array() );
        $separator = ' ' . trim( $separator ) . ' ';
        
        if ( '' === trim( $separator_location ) ) {
            $separator_location = ( is_rtl() ) ? 'left' : 'right';
        }
        
        $original_title = $title;
        $modified_title = true;
        $title_part = '';
        
        if ( $this->page_mod['is_home'] ) {
            $title = $this->get_content_title();
        }
        elseif ( $this->page_mod[ 'is_home_page' ] ) {
           $title = $this->get_title_rule_from_option( 'title-home-aios' );  
        }
        elseif ( $this->page_mod[ 'is_home_index' ] ) {
               $title = $this->get_content_title( get_post( get_option( 'page_for_posts' ) ) ); 
        }
        elseif ( $this->page_mod[ 'is_singular' ] ) {
            $title = $this->get_content_title(); 
            if ( ! is_string( $title ) || '' === $title ) {
                    $title_part = $original_title;
            }
        }
        elseif ( $this->page_mod[ 'is_search' ] ) {
                $title = $this->get_title_rule_from_option( 'title-search-aios' );

                if ( ! is_string( $title ) || '' === $title ) {
                        $title_part = sprintf(  __( 'Search for "%s"', SR_TEXTDOMAIN ), esc_html( get_search_query() ) );
                }
        }
        elseif ( $this->page_mod[ 'is_category' ] || $this->page_mod[ 'is_tag' ] || $this->page_mod[ 'is_tax' ] ) {
                $title = $this->get_taxonomy_title();

                if ( ! is_string( $title ) || '' === $title ) {
                        if ( $this->page_mod[ 'is_category' ] ) {
                                $title_part = single_cat_title( '', false );
                        }
                        elseif ( $this->page_mod[ 'is_tag' ] ) {
                                $title_part = single_tag_title( '', false );
                        }
                        else {
                                $title_part = single_term_title( '', false );
                                if ( $title_part === '' ) {
                                        $term       = $GLOBALS['wp_query']->get_queried_object();
                                        $title_part = $term->name;
                                }
                        }
                }
        }
        elseif ( $this->page_mod[ 'is_author' ] ) {
                $title = $this->get_author_title();

                if ( ! is_string( $title ) || '' === $title ) {
                        $title_part = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
                }
        }
        elseif ( $this->page_mod[ 'is_post_type_archive' ] ) {
            $post_type = get_query_var( 'post_type' );

            if ( is_array( $post_type ) ) {
                $post_type = reset( $post_type );
            }

            $title = $this->get_title_rule_from_option( 'title-ptarchive-' . $post_type );

            if ( ! is_string( $title ) || '' === $title || strpos( $title, '{{' ) !== false ) {
                $post_type_obj = get_post_type_object( $post_type );
                if ( isset( $post_type_obj->labels->menu_name ) ) {
                        $title_part = $post_type_obj->labels->menu_name;
                }
                elseif ( isset( $post_type_obj->name ) ) {
                        $title_part = $post_type_obj->name;
                }
                else {
                        $title_part = ''; // To be determined what this should be.
                }
                
                //if somehow title replacement missing
                if( strpos( $title, '{{' ) !== false ){
                    $title = str_replace( '{{title}}', $title_part, $title );
                    $title_part = '';
                }
            }

        }
        elseif ( $this->page_mod[ 'is_archive' ] ) {
                $title = $this->get_title_rule_from_option( 'title-archive-aios' );
                if ( empty( $title ) ) {
                        if ( is_month() ) {
                                $title_part = sprintf( __( '%s Archives', SR_TEXTDOMAIN ), single_month_title( ' ', false ) );
                        }
                        elseif ( is_year() ) {
                                $title_part = sprintf( __( '%s Archives', SR_TEXTDOMAIN ), get_query_var( 'year' ) );
                        }
                        elseif ( is_day() ) {
                                $title_part = sprintf( __( '%s Archives', SR_TEXTDOMAIN ), get_the_date() );
                        }
                        else {
                                $title_part = __( 'Archives', SR_TEXTDOMAIN );
                        }
                }
        }
        elseif ( $this->page_mod[ 'is_404' ] ) {
                $title = $this->get_title_rule_from_option( 'title-404-aios' );
                if ( empty( $title ) ) {
                    $title_part = __( 'Page not found', SR_TEXTDOMAIN );
                }
        }
        
        else {
            $modified_title = false;
        }
        
        if ( ( $modified_title && empty( $title ) ) || ! empty( $title_part ) ) {
            $title = $this->get_default_title( $separator, $separator_location, $title_part );
        }

        if ( defined( 'ICL_LANGUAGE_CODE' ) && false !== strpos( $title, ICL_LANGUAGE_CODE ) ) {
            $title = str_replace( ' @' . ICL_LANGUAGE_CODE, '', $title );
        }

        
        
        return esc_html( strip_tags( stripslashes( $title ) ) );
    }
    
    
    
    /**
     * Get content title
     * 
     * @param type $object
     * @return type
     */
    public function get_content_title( $object = null ) {
        global $wp_query;
        if ( is_null( $object ) ) {
            $object = $GLOBALS['wp_query']->get_queried_object();
        }
        
        if ( is_object( $object ) ) {
//            $title = CsGlobalMeta::getMetaValue( 'title', $object->ID );
//            if ( $title !== '' ) {
//                    return $this->replace->ReplaceVars( $title, $object );
//            }
//            $format = ''; 
            if( empty( $post_type = get_post_type() ) ){
                $post_type = ( isset( $object->post_type ) ? $object->post_type : $object->query_var );
            }
            
            return $this->get_title_rule_from_option( "title-{$post_type}", $object );
        }
        return $this->get_title_rule_from_option( 'title-404-aios' );
    }
    
    /**
     * Used for category, tag, and tax titles.
     *
     * @return string
     */
    public function get_taxonomy_title() {
            $object = $GLOBALS['wp_query']->get_queried_object();

            $title = CsGlobalTaxonomyMeta::get_term_meta( $object, $object->taxonomy, 'title' );

            if ( is_string( $title ) && $title !== '' ) {
                    return $this->replace->ReplaceVars( $title, $object );
            }
            else {
                    return $this->get_title_rule_from_option( 'title-tax-' . $object->taxonomy, $object );
            }
    }

    /**
     * Used for author titles.
     *
     * @return string
     */
    public function get_author_title() {
            $author_id = get_query_var( 'author' );
            $title     = trim( get_the_author_meta( 'aios_title', $author_id ) );

            if ( $title !== '' ) {
                    return $this->replace->ReplaceVars( $title, array() );
            }

            return $this->get_title_rule_from_option( 'title-author-wpseo' );
    }
    
    /**
     * Get the default title
     * 
     * @param type $sep
     * @param type $seplocation
     * @param type $title
     * @return type
     */
    private function get_default_title( $sep, $seplocation, $title = '' ) {
		if ( 'right' == $seplocation ) {
			$regex = '`\s*' . preg_quote( trim( $sep ), '`' ) . '\s*`u';
		}
		else {
			$regex = '`^\s*' . preg_quote( trim( $sep ), '`' ) . '\s*`u';
		}
		$title = preg_replace( $regex, '', $title );

		if ( ! is_string( $title ) || ( is_string( $title ) && $title === '' ) ) {
			$title = get_bloginfo( 'name' );
			$title = $this->add_paging_to_title( $sep, $seplocation, $title );
			$title = $this->add_to_title( $sep, $seplocation, $title, strip_tags( get_bloginfo( 'description' ) ) );

			return $title;
		}

		$title = $this->add_paging_to_title( $sep, $seplocation, $title );
		$title = $this->add_to_title( $sep, $seplocation, $title, strip_tags( get_bloginfo( 'name' ) ) );

		return $title;
	}
        
        /**
         * Adding page to title
         * 
         * @global type $wp_query
         * @param type $sep
         * @param type $seplocation
         * @param type $title
         * @return type
         */
        private function add_paging_to_title( $sep, $seplocation, $title ) {
		global $wp_query;

		if ( ! empty( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ) {
			return $this->add_to_title( $sep, $seplocation, $title, $wp_query->query_vars['paged'] . '/' . $wp_query->max_num_pages );
		}

		return $title;
	}
        
        /**
         * Add to title
         * 
         * @param type $sep
         * @param type $seplocation
         * @param type $title
         * @param type $title_part
         * @return type
         */
        private function add_to_title( $sep, $seplocation, $title, $title_part ) {
		if ( 'right' === $seplocation ) {
			return $title . $sep . $title_part;
		}

		return $title_part . $sep . $title;
	}
    
}
