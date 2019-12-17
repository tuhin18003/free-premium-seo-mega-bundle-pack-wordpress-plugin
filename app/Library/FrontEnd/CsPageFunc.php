<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * Frontend functions
 * 
 * @package frontend
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Library\Includes\Util;

class CsPageFunc {
    /**
     * Hold options
     *
     * @var array 
     */
    public $options;
    
    /**
     * Hold page mode
     *
     * @var array 
     */
    public $page_mod;
    
    /**
     * Hold final page title
     *
     * @var type 
     */
    private $page_title;
    
    /**
     * Hold plugin data
     *
     * @var array
     */
    public $CsPlugin;
            
    function __construct( $page_title ) {
        $this->page_title = $page_title;
    }
    
    /**
     * Render page function
     * 
     * @return object
     */
    public function Cs_RenderPageData(){
        $this->page_mod['obj'] = (object)array_merge(
            (array)$this->page_mod['obj'], array(
                "{$this->CsPlugin['dataPrefix']}final_options" => (object) $this->Cs_FormatTitleDescription()
            )
        );
        
       return $this->page_mod;         
    }
    
    /**
     * Render page title
     * 
     * @return string Description
     */
    private function Cs_FormatTitleDescription(){
        $final_options = array( 'title_suffix' => '', 'title' => '', 'title_hashtag' => '', 'desc' => '', 'desc_hashtag' => '');
        if( $this->page_mod['is_home'] == 1 || $this->page_mod['is_home_page'] == 1 || $this->page_mod['is_home_index'] == 1 ){
             $final_options['title'] = $this->page_title;
            
            // just in case
            if ( empty( $final_options['title'] ) ) {
                if ( ! ( $final_options['title'] = get_bloginfo( 'name', 'display' ) ) ) {
                    $final_options['title'] = __('No Title', SR_TEXTDOMAIN);
                }
            }
            
            if( isset( $this->options->aios_web_graph_options['site_desc'] ) && !empty( $site_desc = $this->options->aios_web_graph_options['site_desc'] )){
                $final_options['desc'] = $site_desc;
            }
            
            // just in case
            if ( empty( $final_options['desc'] ) ) {
                if ( ! ( $final_options['desc'] = get_bloginfo( 'description', 'display' ) ) ) {
                    $final_options['desc'] = __('No Description', SR_TEXTDOMAIN);
                }
            }
            
        }elseif( $this->page_mod['is_post'] == 1 ){
            $final_options['title'] = $this->page_mod['obj']->wp_options->post_title;
            if ( isset( $this->options->aios_web_graph_options['og_desc_hashtags'] ) && 
                    ! empty( $this->options->aios_web_graph_options['og_desc_hashtags'] ) ) {
                $final_options['desc_hashtag'] = $this->get_hashtags();
            }
            
            // use the excerpt, if we have one
            if ( !empty($this->page_mod['id'] ) && has_excerpt( $this->page_mod['id'] ) ) {
                $final_options['desc'] = $this->page_mod['obj']->wp_options->post_excerpt;
            }else{
                $final_options['desc'] = $this->page_mod['obj']->wp_options->post_content;
            }
            
        }elseif( $this->page_mod['is_post_type_archive'] == 1 ){
            $final_options['title'] = $this->page_title;
            $final_options['desc'] = __( 'Archive Page', SR_TEXTDOMAIN);
        }elseif( $this->page_mod['is_term'] == 1 ){
            
            //get the title
            if( $this->page_mod['is_category'] ){
                $final_options['title'] = $this->get_category_title( $this->page_mod['obj']->wp_options );
            }elseif( isset( $this->page_mod['obj']->wp_options->name ) ){
                $final_options['title'] = $this->page_mod['obj']->wp_options->name;
            }
            
            //get the description
            if( $this->page_mod['is_tag']){
                if ( ! $final_options['desc'] = tag_description( $this->page_mod['id'] ) ) {
                    $term_obj = get_tag( $this->page_mod['id'] );
                    if ( ! empty( $term_obj->name ) ) {
                        $final_options['desc'] = sprintf( __( 'Tagged with %s', SR_TEXTDOMAIN), $term_obj->name );
                    }
                }
            } elseif ( $this->page_mod['is_category'] ) {
                if ( ! $final_options['desc'] = category_description( $this->page_mod['id'] ) ) {
                    $final_options['desc'] = sprintf( __( '%s Category', SR_TEXTDOMAIN), get_cat_name( $this->page_mod['id'] ) );
                }
            } else { 	// other taxonomies
                if ( ! empty( $this->page_mod['obj']->wp_options->description ) ) {
                    $final_options['desc'] = $this->page_mod['obj']->wp_options->description;
                } elseif ( ! empty( $this->page_mod['obj']->wp_options->name ) ) {
                    $final_options['desc'] = $this->page_mod['obj']->wp_options->name. __(' Archives', SR_TEXTDOMAIN);
                }
            }
            
        } elseif ( $this->page_mod['is_user'] ) {
            $final_options['title'] = $this->page_mod['obj']->wp_options->data->display_name;
            
            if ( isset($this->page_mod['obj']->wp_options->description) && ! empty( $desc =  $this->page_mod['obj']->wp_options->description ) ) {
                $final_options['desc'] = $desc;
            } elseif ( ! empty( $this->page_mod['obj']->data->display_name ) ) {
                $final_options['desc'] = sprintf( __( 'Authored by %s', SR_TEXTDOMAIN ), $this->page_mod['obj']->data->display_name );
            }

        } elseif ( is_day() ) {
            $final_options['title'] = $this->page_title;
            $final_options['desc'] = sprintf( __('Daily Archives for %s', SR_TEXTDOMAIN), get_the_date() );
        } elseif ( is_month() ) {
            $final_options['title'] = $this->page_title;
            $final_options['desc'] = sprintf( __('Monthly Archives for %s', SR_TEXTDOMAIN ), get_the_date('F Y') );
        } elseif ( is_year() ) {
            $final_options['title'] = $this->page_title;
            $final_options['desc'] = sprintf( __('Yearly Archives for %s', SR_TEXTDOMAIN ), get_the_date('Y') );
        } elseif ( $this->page_mod['is_archive'] ) {	// just in case
            $final_options['title'] = $this->page_title;
            $final_options['desc'] = __( 'Archive Page', SR_TEXTDOMAIN );
        }
        
        if( empty( $final_options['desc'] ) ){
            $final_options['desc'] = $this->options->aios_web_graph_options['site_desc'];
        }
        
        $paged = get_query_var( 'paged' );
        if ( $paged > 1 ) {
            $final_options['title_suffiz'] = sprintf( 'Page %s ', $paged );
        }
        
        if ( preg_match( '/(.*)(( #[a-z0-9\-]+)+)$/U', $final_options['title'], $match ) ) {
            $final_options['title'] = $match[1];
            $final_options['title_hashtags'] = trim( $match[2] );
        }
        
        return $final_options;
    }

    /**
     * Get hastag
     * 
     * @return boolean
     */
    private function get_hashtags(){
        $tags = array();
        if ( $this->page_mod['is_singular'] == 1 && ! empty( $this->page_mod['id'] ) ) {
            $post_ids = array ( $this->page_mod['id'] );	// array of one
            // add the parent tags if option is enabled
            if ( $this->options->aios_web_graph_options['og_page_parent_tags'] && is_page( $this->page_mod['id'] ) ) {
                $post_ids = array_merge( $post_ids, get_post_ancestors( $this->page_mod['id'] ) );
            }
            
            foreach ( $post_ids as $id ) {
                if ( $this->options->aios_web_graph_options['og_page_title_tag'] && is_page( $id ) ) {
                        $tags[] = Util::sanitize_tag( get_the_title( $id ) );
                }
                foreach ( wp_get_post_tags( $id, array( 'fields' => 'names') ) as $tag_name ) {
                    $tags[] = $tag_name;
                }
            }
            
        }elseif($this->page_mod['is_search'] == 1 ){
            $tags = preg_split( '/ *, */', get_search_query( false ) );
        }
        
        $tags = array_unique( array_map( array( 'CsSeoMegaBundlePack\\Library\\Includes\\Util', 'sanitize_tag' ), $tags ) );
        
//        $tags = array_slice( $tags, 0, (int)$this->options->aios_web_graph_options['og_desc_hashtags'] );
        
        if ( ! empty( $tags ) ) {
            return $tags;
            // remove special character incompatible with Twitter
//            return Util::array_to_hashtags( $tags );
        }
        
        return false;
    }
    
    /**
     * Get parent title
     * 
     * @param type $term_obj
     * @return boolean
     */
    private function get_category_title( $term_obj ){
        $cat = get_category( $term_obj->term_id );
        if ( isset( $term_obj->name ) ) {
            $title = $term_obj->name.' Archives ';	// default value
        }
        
        if ( ! empty( $cat->category_parent ) ) {
            $title = get_category_parents( $term_obj->term_id );
        }
        return $title;
    }
    
}
