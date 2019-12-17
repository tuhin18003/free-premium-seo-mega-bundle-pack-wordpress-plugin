<?php namespace CsSeoMegaBundlePack\Library;

/**
 * Replace Custom Vars
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Models\CommonQuery\CsGlobalMeta;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\Includes\Util;
use CsSeoMegaBundlePack\Library\CsPrimaryTerm;

class CsReplaceVars {
    
    public $options;
    private  $args;
    
    /**
     * @var    array    Default post/page/cpt information
     */
    protected  $defaults = array(
            'ID'            => '',
            'name'          => '',
            'post_author'   => '',
            'post_content'  => '',
            'post_date'     => '',
            'post_excerpt'  => '',
            'post_modified' => '',
            'post_title'    => '',
            'taxonomy'      => '',
            'term_id'       => '',
            'term404'       => '',
    );
            
    function __construct() {
        //set options
//        $this->options = $options;
    }
    

    /**
     * Replace vars
     */
    public function ReplaceVars( $string, $args, $omit = array() ){
//        pre_print( $args );
        
        $string = strip_tags( $string );
        
        $args = (array) $args;
        if ( isset( $args['post_content'] ) && ! empty( $args['post_content'] ) ) {
                $args['post_content'] = Util::Cs_StripShortcodes( $args['post_content'] );
        }
        if ( isset( $args['post_excerpt'] ) && ! empty( $args['post_excerpt'] ) ) {
                $args['post_excerpt'] = Util::Cs_StripShortcodes( $args['post_excerpt'] );
        }

        $this->args = (object) wp_parse_args( $args, $this->defaults );
        
        $replacements = array(); $matches = array();
        if ( preg_match_all( '`{{([^{]+({{single)?)}}}?`iu', $string, $matches ) ) {
            $replacements =  $this->getReplacedValues( $matches, $omit);
        }
        
        if ( is_array( $replacements ) && $replacements !== array() ) {
            $string = str_replace( array_keys( $replacements ), array_values( $replacements ), $string );
        }
        
        if ( isset( $replacements['{{sep}}'] ) && ( is_string( $replacements['{{sep}}'] ) && $replacements['{{sep}}'] !== '' ) ) {
            $q_sep  = preg_quote( $replacements['{{sep}}'], '`' );
            $string = preg_replace( '`' . $q_sep . '(?:\s*' . $q_sep . ')*`u', $replacements['{{sep}}'], $string );
        }
        
        // Remove superfluous whitespace.
        $string = GeneralHelpers::standardize_whitespace( $string );
        
        return trim( $string );
    }
    
    private  function getReplacedValues( $matches, $omit ){
        $replacements = array(); $replacement = '';
        foreach ( $matches[1] as $k => $var ) {
            if ( in_array( $var, $omit, true ) ) {
                continue;
            }
            
            if ( strpos( $var, 'cf_' ) === 0 ) {
                $replacement = $this->aios_retrieve_cf_custom_field_name( $var );
            }
            elseif ( strpos( $var, 'ct_desc_' ) === 0 ) {
                $replacement = $this->aios_retrieve_ct_desc_custom_tax_name( $var );
            }
            elseif ( strpos( $var, 'ct_' ) === 0 ) {
                $single      = ( isset( $matches[2][ $k ] ) && $matches[2][ $k ] !== '' ) ? true : false;
                $replacement = $this->aios_retrieve_ct_custom_tax_name( $var, $single );
            } 
            elseif (  method_exists( __CLASS__, ( $method_name = 'aios_retrieve_' . $var ) ) ) {
                $replacement = $this->$method_name();
            } 
            
            if ( isset( $replacement ) ) {
                $var                  = $this->add_var_delimiter( $var );
                $replacements[ $var ] = $replacement;
            }
            unset( $replacement, $single, $method_name );
        }
        
        return $replacements;
    }
    
    
    private  function aios_retrieve_sep(){
        return $this->options->aios_title_meta_default['seperator'];
    }
    
    /**
     * Retrieve sitename
     * 
     * @staticvar type $replacement
     * @return type
     */
    private  function aios_retrieve_sitename(){
        static $replacement;
        if ( ! isset( $replacement ) ) {
            $sitename = Util::get_site_name( $this->options );
            if ( $sitename !== '' ) {
                    $replacement = $sitename;
            }
        }
        return $replacement;
    }
    /**
     * Retrieve site desc
     * 
     * @staticvar type $replacement
     * @return type
     */
    private  function aios_retrieve_sitedesc(){
        static $replacement;
        if ( ! isset( $replacement ) ) {
            $description = Util::get_site_description( $this->options );
            if ( $description !== '' ) {
                    $replacement = $description;
            }
        }
        return $replacement;
    }

    /**
     * Add var delimiter
     * 
     * @param type $string
     * @return type
     */
    private  function add_var_delimiter( $string ) {
        return '{{' . $string . '}}';
    }
    
    /**
     * Remove var delimiter
     * 
     * @param type $string
     * @return type
     */
    private static function remove_var_delimiter( $string ) {
        return trim( $string, '}' );
    }
    
    /**
     * Retive category
     * 
     * @return type  string|null
     */
    private function aios_retrieve_category() {
            $replacement = null;

            if ( ! empty( $this->args->ID ) ) {
                    $cat = $this->aios_get_terms( $this->args->ID, 'category' );
                    if ( $cat !== '' ) {
                            $replacement = $cat;
                    }
            }

            if ( ( ! isset( $replacement ) || $replacement === '' ) && ( isset( $this->args->cat_name ) && ! empty( $this->args->cat_name ) ) ) {
                    $replacement = $this->args->cat_name;
            }

            return $replacement;
    }
    
    /**
     * Retrieve the category description for use as replacement string.
     *
     * @return string|null
     */
    private function aios_retrieve_category_description() {
            return $this->aios_retrieve_term_description();
    }
    
    /**
     * Retrieve the date of the post/page/cpt for use as replacement string.
     *
     * @return string|null
     */
    private function aios_retrieve_date() {
            $replacement = null;

            if ( $this->args->post_date !== '' ) {
                    $replacement = mysql2date( get_option( 'date_format' ), $this->args->post_date, true );
            }
            else {
                    if ( get_query_var( 'day' ) && get_query_var( 'day' ) !== '' ) {
                            $replacement = get_the_date();
                    }
                    else {
                            if ( single_month_title( ' ', false ) && single_month_title( ' ', false ) !== '' ) {
                                    $replacement = single_month_title( ' ', false );
                            }
                            elseif ( get_query_var( 'year' ) !== '' ) {
                                    $replacement = get_query_var( 'year' );
                            }
                    }
            }

            return $replacement;
    }
    
    /**
     * Retrive excerpt
     * 
     * @return type
     */
    private function aios_retrieve_excerpt() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			if ( $this->args->post_excerpt !== '' ) {
				$replacement = strip_tags( $this->args->post_excerpt );
			}
			elseif ( $this->args->post_content !== '' ) {
				$replacement = wp_html_excerpt( strip_shortcodes( $this->args->post_content ), 155 );
			}
		}

		return $replacement;
	}
        
    /**
     * Excerpt only
     * 
     * @return type
     */    
    private function aios_retrieve_excerpt_only() {
            $replacement = null;

            if ( ! empty( $this->args->ID ) && $this->args->post_excerpt !== '' ) {
                    $replacement = strip_tags( $this->args->post_excerpt );
            }

            return $replacement;
    }    
    
    /**
     * Parent title
     * 
     * @return type
     */
    private function aios_retrieve_parent_title() {
        $replacement = null;

        if ( ! isset( $replacement ) && ( ( is_singular() || is_admin() ) && isset( $GLOBALS['post'] ) ) ) {
                if ( isset( $GLOBALS['post']->post_parent ) && 0 !== $GLOBALS['post']->post_parent ) {
                        $replacement = get_the_title( $GLOBALS['post']->post_parent );
                }
        }

        return $replacement;
    }
    
    /**
     * Retrieve the current search phrase for use as replacement string.
     *
     * @return string|null
     */
    private function aios_retrieve_searchphrase() {
            $replacement = null;

            if ( ! isset( $replacement ) ) {
                    $search = get_query_var( 's' );
                    if ( $search !== '' ) {
                            $replacement = esc_html( $search );
                    }
            }

            return $replacement;
    }
    
    /**
     * Reteieve tag
     * 
     * @return type
     */
    private function aios_retrieve_tag() {
        $replacement = null;
        if ( isset( $this->args->ID ) ) {
                $tags = $this->aios_get_terms( $this->args->ID, 'post_tag' );
                if ( $tags !== '' ) {
                        $replacement = $tags;
                }
        }
        return $replacement;
    }
    
    /**
     * Retrieve tag des
     * 
     * @return type
     */
    private function aios_retrieve_tag_description() {
        return $this->aios_retrieve_term_description();
    }
    
    /**
     * Retrieve term desc
     * 
     * @return type
     */
    private function aios_retrieve_term_description() {
        $replacement = null;

        if ( isset( $this->args->term_id ) && ! empty( $this->args->taxonomy ) ) {
                $term_desc = get_term_field( 'description', $this->args->term_id, $this->args->taxonomy );
                if ( $term_desc !== '' ) {
                        $replacement = trim( strip_tags( $term_desc ) );
                }
        }

        return $replacement;
    }
    
    /**
     * Retrieve term title
     * 
     * @return type
     */
    private function aios_retrieve_term_title() {
        $replacement = null;
        if ( ! empty( $this->args->taxonomy ) && ! empty( $this->args->name ) ) {
                $replacement = $this->args->name;
        }
        return $replacement;
    }
    
    /**
     * Retrieve title
     * 
     * @return type
     */
    private function aios_retrieve_title() {
        $replacement = null;

        if ( is_string( $this->args->post_title ) && $this->args->post_title !== '' ) {
            $replacement = stripslashes( $this->args->post_title );
        }
        
        return $replacement;
    }
    
    /**
     * Retrieve primary category
     *
     * @return bool|int|null
     */
    private function aios_retrieve_primary_category() {
            $primary_category = null;
            if ( ! empty( $this->args->ID ) ) {
                    $primary_category_obj = new CsPrimaryTerm( 'category', $this->args->ID );

                    $term_id = $primary_category_obj->get_primary_term();
                    $term    = get_term( $term_id );

                    if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                            $primary_category = $term->name;
                    }
            }

            return $primary_category;
    }
    
    /**
     * Page numbering
     * 
     * @global type $wp_query
     * @global type $post
     * @param type $request
     * @return type
     */
    private function aios_determine_pagenumbering( $request = 'nr' ) {
		global $wp_query, $post;
		$max_num_pages = null;
		$page_number   = null;

		$max_num_pages = 1;

		if ( ! is_singular() ) {
			$page_number = get_query_var( 'paged' );
			if ( $page_number === 0 || $page_number === '' ) {
				$page_number = 1;
			}

			if ( isset( $wp_query->max_num_pages ) && ( $wp_query->max_num_pages != '' && $wp_query->max_num_pages != 0 ) ) {
				$max_num_pages = $wp_query->max_num_pages;
			}
		}
		else {
			$page_number = get_query_var( 'page' );
			if ( $page_number === 0 || $page_number === '' ) {
				$page_number = 1;
			}

			if ( isset( $post->post_content ) ) {
				$max_num_pages = ( substr_count( $post->post_content, '<!--nextpage-->' ) + 1 );
			}
		}

		$return = null;

		switch ( $request ) {
			case 'nr':
				$return = $page_number;
				break;
			case 'max':
				$return = $max_num_pages;
				break;
		}

		return $return;
	}
        
        /**
	 * Determine the post type names for the current post/page/cpt
	 *
	 * @param string $request 'single'|'plural' - whether to return the single or plural form.
	 * @return string|null
	 */
	private function aios_determine_pt_names( $request = 'single' ) {
		global $wp_query;
		$pt_single = null;
		$pt_plural = null;

		if ( isset( $wp_query->query_vars['post_type'] ) && ( ( is_string( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] !== '' ) || ( is_array( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] !== array() ) ) ) {
			$post_type = $wp_query->query_vars['post_type'];
		}
		else {
			// Make it work in preview mode.
			$post_type = $wp_query->get_queried_object()->post_type;
		}

		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		if ( $post_type !== '' ) {
			$pt        = get_post_type_object( $post_type );
			$pt_plural = $pt_single = $pt->name;
			if ( isset( $pt->labels->singular_name ) ) {
				$pt_single = $pt->labels->singular_name;
			}
			if ( isset( $pt->labels->name ) ) {
				$pt_plural = $pt->labels->name;
			}
		}

		$return = null;

		switch ( $request ) {
			case 'single':
				$return = $pt_single;
				break;
			case 'plural':
				$return = $pt_plural;
				break;
		}

		return $return;
	}
    
    /**
     * Retrieve the attachment caption
     *
     * @return string|null
     */
    private function aios_retrieve_caption() {
        return $this->aios_retrieve_excerpt_only();
    }
    
    /**
     * Retrieve custom field name
     * 
     * @global \CsSeoMegaBundlePack\Models\CommonQuery\type $post
     * @param type $var
     * @return type
     */
    private function aios_retrieve_cf_custom_field_name( $var ) {
        global $post;
        $replacement = null;

        if ( is_string( $var ) && $var !== '' ) {
                $field = substr( $var, 3 );
                if ( ( is_singular() || is_admin() ) && ( is_object( $post ) && isset( $post->ID ) ) ) {
                        $name = get_post_meta( $post->ID, $field, true );
                        if ( $name !== '' ) {
                                $replacement = $name;
                        }
                }
        }

        return $replacement;
    }
    
    /**
     * Custom tax name
     * 
     * @param type $var
     * @param type $single
     * @return type
     */
    private function aios_retrieve_ct_custom_tax_name( $var, $single = false ) {
            $replacement = null;
            if ( ( is_string( $var ) && $var !== '' ) && ! empty( $this->args->ID ) ) {
                    $tax  = substr( $var, 3 );
                    $name = $this->aios_get_terms( $this->args->ID, $tax, $single );
                    if ( $name !== '' ) {
                            $replacement = $name;
                    }
            }

            return $replacement;
	}
    
    /**
     * Custom tax name
     * 
     * @global \CsSeoMegaBundlePack\Models\CommonQuery\type $post
     * @param type $var
     * @return type
     */    
    private function aios_retrieve_ct_desc_custom_tax_name( $var ) {
        global $post;
        $replacement = null;

        if ( is_string( $var ) && $var !== '' ) {
                $tax = substr( $var, 8 );
                if ( is_object( $post ) && isset( $post->ID ) ) {
                        $terms = get_the_terms( $post->ID, $tax );
                        if ( is_array( $terms ) && $terms !== array() ) {
                                $term      = current( $terms );
                                $term_desc = get_term_field( 'description', $term->term_id, $tax );
                                if ( $term_desc !== '' ) {
                                        $replacement = trim( strip_tags( $term_desc ) );
                                }
                        }
                }
        }

        return $replacement;
	}
     
    /**
     * current date
     * 
     * @staticvar type $replacement
     * @return type
     */
     private function aios_retrieve_currentdate() {
            static $replacement;

            if ( ! isset( $replacement ) ) {
                    $replacement = date_i18n( get_option( 'date_format' ) );
            }

            return $replacement;
    }   
    
    /**
     * Retrieve the current day
     *
     * @return string
     */
    private function aios_retrieve_currentday() {
            static $replacement;

            if ( ! isset( $replacement ) ) {
                    $replacement = date_i18n( 'j' );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the current month
     *
     * @return string
     */
    private function aios_retrieve_currentmonth() {
            static $replacement;

            if ( ! isset( $replacement ) ) {
                    $replacement = date_i18n( 'F' );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the current time 
     *
     * @return string
     */
    private function aios_retrieve_currenttime() {
            static $replacement;

            if ( ! isset( $replacement ) ) {
                    $replacement = date_i18n( get_option( 'time_format' ) );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the current year for use as replacement string.
     *
     * @return string
     */
    private function aios_retrieve_currentyear() {
            static $replacement;

            if ( ! isset( $replacement ) ) {
                    $replacement = date_i18n( 'Y' );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the post/page/cpt's focus keyword for use as replacement string.
     *
     * @return string|null
     */
    private function aios_retrieve_focuskw() {
            $replacement = null;

            if ( ! empty( $this->args->ID ) ) {
                    $focus_kw = CsGlobalMeta::getMetaValue( 'focuskw', $this->args->ID );
                    if ( $focus_kw !== '' ) {
                            $replacement = $focus_kw;
                    }
            }

            return $replacement;
    }
    
    /**
     * Retrieve the post/page/cpt ID 
     *
     * @return string|null
     */
    private function aios_retrieve_id() {
            $replacement = null;

            if ( ! empty( $this->args->ID ) ) {
                    $replacement = $this->args->ID;
            }

            return $replacement;
    }
    
    /**
     * Retrieve modified date
     * 
     * @return type
     */
    private function aios_retrieve_modified() {
            $replacement = null;

            if ( ! empty( $this->args->post_modified ) ) {
                    $replacement = mysql2date( get_option( 'date_format' ), $this->args->post_modified, true );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the post/page/cpt author's "nice name" 
     *
     * @return string|null
     */
    private function aios_retrieve_name() {
            $replacement = null;

            $user_id = $this->aios_retrieve_userid();
            $name    = get_the_author_meta( 'display_name', $user_id );
            if ( $name !== '' ) {
                    $replacement = $name;
            }

            return $replacement;
    }
    
    /**
     * Retrieve description
     * 
     * @return type
     */
    private function aios_retrieve_user_description() {
        $replacement = null;

        $user_id     = $this->aios_retrieve_userid();
        $description = get_the_author_meta( 'description', $user_id );
        if ( $description != '' ) {
                $replacement = $description;
        }

        return $replacement;
    }
        
    
    /**
     * Retrieve the current page number with context (i.e. 'page 2 of 4')
     *
     * @return string
     */
    private function aios_retrieve_page() {
            $replacement = null;

            $max = $this->aios_determine_pagenumbering( 'max' );
            $nr  = $this->aios_determine_pagenumbering( 'nr' );
            $sep = $this->aios_retrieve_sep();

            if ( $max > 1 && $nr > 1 ) {
                    $replacement = sprintf( $sep . ' ' . __( 'Page %1$d of %2$d', SR_TEXTDOMAIN ), $nr, $max );
            }

            return $replacement;
    }
    
    /**
     * Retrieve the current page number
     *
     * @return string|null
     */
    private function aios_retrieve_pagenumber() {
            $replacement = null;

            $nr = $this->aios_determine_pagenumbering( 'nr' );
            if ( isset( $nr ) && $nr > 0 ) {
                    $replacement = (string) $nr;
            }

            return $replacement;
    }
    
    /**
     * Retrieve the current page total 
     *
     * @return string|null
     */
    private function aios_retrieve_pagetotal() {
            $replacement = null;

            $max = $this->aios_determine_pagenumbering( 'max' );
            if ( isset( $max ) && $max > 0 ) {
                    $replacement = (string) $max;
            }

            return $replacement;
    }
    
    /**
     * Retrieve plural
     * 
     * @return type
     */
    private function aios_retrieve_pt_plural() {
        $replacement = null;

        $name = $this->aios_determine_pt_names( 'plural' );
        if ( isset( $name ) && $name !== '' ) {
                $replacement = $name;
        }

        return $replacement;
    }
    
    /**
     * Retrieve pt single
     * 
     * @return type
     */
    private function aios_retrieve_pt_single() {
            $replacement = null;

            $name = $this->aios_determine_pt_names( 'single' );
            if ( isset( $name ) && $name !== '' ) {
                    $replacement = $name;
            }

            return $replacement;
    }
    
    /**
     * Retrieve term 404
     * 
     * @return type
     */
    private function aios_retrieve_term404() {
		$replacement = null;

		if ( $this->args->term404 !== '' ) {
			$replacement = sanitize_text_field( str_replace( '-', ' ', $this->args->term404 ) );
		}
		else {
			$error_request = get_query_var( 'pagename' );
			if ( $error_request !== '' ) {
				$replacement = sanitize_text_field( str_replace( '-', ' ', $error_request ) );
			}
			else {
				$error_request = get_query_var( 'name' );
				if ( $error_request !== '' ) {
					$replacement = sanitize_text_field( str_replace( '-', ' ', $error_request ) );
				}
			}
		}

		return $replacement;
	}
        
     /**
     * Retrieve the post/page/cpt author's user id 
     *
     * @return string
     */
    private function aios_retrieve_userid() {
            $replacement = ! empty( $this->args->post_author ) ? $this->args->post_author : get_query_var( 'author' );
            return $replacement;
    }   
    
    /**
     * Get terms
     * 
     * @param type $id
     * @param type $taxonomy
     * @param type $return_single
     * @return type
     */
    public function aios_get_terms( $id, $taxonomy, $return_single = false ) {

        $output = '';

        // If we're on a specific tag, category or taxonomy page, use that.
        if ( is_category() || is_tag() || is_tax() ) {
                $term   = $GLOBALS['wp_query']->get_queried_object();
                $output = $term->name;
        }
        elseif ( ! empty( $id ) && ! empty( $taxonomy ) ) {
                $terms = get_the_terms( $id, $taxonomy );
                if ( is_array( $terms ) && $terms !== array() ) {
                        foreach ( $terms as $term ) {
                                if ( $return_single ) {
                                        $output = $term->name;
                                        break;
                                }
                                else {
                                        $output .= $term->name . ', ';
                                }
                        }
                        $output = rtrim( trim( $output ), ',' );
                }
        }
        unset( $terms, $term );

        return $output;
    }

}
