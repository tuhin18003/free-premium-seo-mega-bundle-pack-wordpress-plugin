<?php namespace CsSeoMegaBundlePack\Library;

/**
 * Common Custom Query
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Library\Includes\Util;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;

class CsLoadedPageType {
    
    protected $mod_defaults = array(
        'id' => 0,
        'name' => false,
        'obj' => false,
        'is_post' => false,		// is post module
        'is_page' => false,		// is page module
        'is_post_type' => false,		// is custom post type
        'is_home' => false,		// home page (index or static)
        'is_home_page' => false,	// static front page
        'is_home_index' => false,	// static posts page or home index
        'is_singular' => false,
        'is_search' => false,
        'post_type' => false,
        'post_status' => false,
        'is_category' => false,
        'is_post_type_archive' => false,
        'is_archive' => false,
        'is_404' => false,
        'is_feed' => false,
        /*
         * Term
         */
        'is_term' => false,		// is term module
        'tax_slug' => '',		// empty string by default
        'is_tag' => false,
        'is_tax' => false,
        /*
         * User
         */
        'is_user' => false,		// is user module,
        'is_author' => false,		// is user module,
        'is_shop' => false,
        'is_shop_archive' => false,
        'og_type' => ''
    );
    
    /**
     * Determine page mode
     *
     * @var type 
     */
//    public $page_mod;


    /**
     * Determine whether this is the homepage and shows posts.
     *
     * @return bool
     */
    public  function is_home_posts_page() {
        return ( is_home() && 'posts' == get_option( 'show_on_front' ) );
    }

    /**
     * Determine whether the this is the static frontpage.
     *
     * @return bool
     */
    public function is_home_static_page() {
            return ( is_front_page() && 'page' == get_option( 'show_on_front' ) && is_page( get_option( 'page_on_front' ) ) );
    }

    /**
     * Determine whether this is the posts page, when it's not the frontpage.
     *
     * @return bool
     */
    public function is_posts_page() {
        return ( is_home() && 'page' == get_option( 'show_on_front' ) );
    }
    
    
    public function fCs_Get_Page_Mod(){
        $this->sget_page_type();
        return $this->mod_defaults;
    }

        /**
     * Determine loaded page type
     * 
     * @param type $use_post
     * @param type $mod
     * @param type $wp_obj
     * @return array
     */
    public function Cs_Get_Page_Mod( $use_post = false, $mod = false, $wp_obj = false ) {
        //get page type
        $this->get_page_type();
        
        if ($this->is_post_page() ) {
            $mod['name'] = 'post';
        } elseif ( $this->mod_defaults['is_page'] == 1 ) {
                $mod['name'] = 'page';
        } elseif ( $this->mod_defaults['is_home'] == 1 ) {
                $mod['name'] = 'home';
        } elseif ($this->is_term_page() ) {
                $mod['name'] = 'term';
        } elseif ( $this->mod_defaults['is_author'] ) {
                $mod['name'] = 'user';
        }else {
                $mod['name'] = false;
        }
        
        if(method_exists( __CLASS__ , $function = $mod['name'].'_get_mod' )){
            $mod = array_merge( $this->mod_defaults, self::$function() );
        }else{
            $mod = array_merge( $this->mod_defaults, $this->post_get_mod_orphan() );
        }
        
        return array_merge( $this->mod_defaults, $mod );
    }

    
    /**
     * Get Rendered page types
     * 
     * @since 1.0.0
     * @return boolean 
     */
    private function get_page_type(){
         global $wp_query;
        
        //get the conditional values 
        foreach( $this->mod_defaults as $key => $val ){
            $this->mod_defaults[ $key ] = $wp_query->{$key};
        }
        
        //check home page type
        if( $this->mod_defaults['is_home'] == 1 ){
            //reset home type
            $this->mod_defaults['is_home'] = false;
                    
            if ( $this->is_home_static_page() ) {
                $this->mod_defaults[ 'is_home' ] = true;   
            }
            elseif ( $this->is_home_posts_page() ) {
                $this->mod_defaults[ 'is_home_page' ] = true;   
            }
            elseif ( $this->is_posts_page() ) {
                $this->mod_defaults[ 'is_home_index' ] = true;   
            }
        }
        
        return true;
    }


    /**
     * Get Rendered page types
     * 
     * @since 1.0.0
     * @return boolean 
     */
    private function x_get_page_type(){
        global $wp_query;
        
        
        $og_type = 'website';
        
        if ( self::is_home_static_page() ) {
            $this->mod_defaults[ 'is_home' ] = true;   
            $og_type = 'website';
        }
        elseif ( self::is_home_posts_page() ) {
            $this->mod_defaults[ 'is_home_page' ] = true;   
            $og_type = 'website';
        }
        elseif ( self::is_posts_page() ) {
            $this->mod_defaults[ 'is_home_index' ] = true;   
            $og_type = 'website';
        }
        elseif ( is_singular() ) {
            $this->mod_defaults[ 'is_singular' ] = true;   
        }
        elseif ( is_search() ) {
            $this->mod_defaults[ 'is_search' ] = true;   
        }
        elseif ( is_category() ) {
            $this->mod_defaults[ 'is_category' ] = true;   
        }
        elseif ( is_tag() ) {
            $this->mod_defaults[ 'is_tag' ] = true;   
        }
        elseif ( is_tax() ) {
            $this->mod_defaults[ 'is_tax' ] = true;   
        }
//        elseif ( is_term() ) {
//            $this->mod_defaults[ 'is_term' ] = true;   
//        }
        elseif ( is_author() ) {
            $og_type = 'profile';
            $this->mod_defaults[ 'is_author' ] = true;   
        }
        elseif ( function_exists ('is_shop') && is_shop() ) {
            $this->mod_defaults[ 'is_shop' ] = true;  
            $this->mod_defaults[ 'is_shop_archive' ] = true;   
        }
        elseif ( is_post_type_archive() ) {
            $this->mod_defaults[ 'is_post_type_archive' ] = true;   
        }
        elseif ( is_archive() ) {
            $this->mod_defaults[ 'is_archive' ] = true;   
        }
        elseif ( is_404() ) {
            $this->mod_defaults[ 'is_404' ] = true;   
        }
        
        $this->mod_defaults[ 'is_post_type_archive' ] = isset($wp_query->is_post_type_archive) ? $wp_query->is_post_type_archive : false;
        
        if( $this->is_post_page() ){
            if( ! empty( $post_type = get_post_type() ) && isset( MetaTagAssets::$MTA[ 'head' ][ 'og_type_ns' ][ $post_type ] )){
                $og_type = $post_type;
            }else {
                $og_type = !isset($this->options->aios_web_graph_options['og_post_type']) || empty( $this->options->aios_web_graph_options['og_post_type'] ) ? 'article' : $this->options->aios_web_graph_options['og_post_type'];
            }
        }
        
        $this->mod_defaults[ 'og_type' ] = $og_type;
        
    }
    
    /**
     * Get post mod
     * 
     * @return type
     */
    public function post_get_mod() {
        global $wp_query;
        $mod['id'] = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : get_the_id();
        $mod['name'] = 'post';
        $mod['is_post'] = true;
        $mod['is_post_type'] = $this->is_custom_post_type();
        $mod['obj'] = CsQuery::Cs_Get_Post_Custom( $mod['id'] );
        return $mod;
    }
    
    /**
     * Get post mod
     * 
     * @return type
     */
    public function home_get_mod() {
        global $wp_query;
        $mod['id'] = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : get_the_id();
        $mod['name'] = 'home';
        $mod['obj'] = CsQuery::Cs_Get_Post_Custom( $mod['id'] );
        return $mod;
    }
    
    /**
     * Get page mod
     * 
     * @return type
     */
    public function page_get_mod() {
        global $wp_query;
        $mod['id'] = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : '';
        $mod['name'] = 'page';
        $mod['is_post'] = false;
        $mod['obj'] = CsQuery::Cs_Get_Post_Custom( $mod['id'] );
        return $mod;
    }
    
    /**
     * Get  post archive mod
     * 
     * @return type
     */
    public function post_get_mod_orphan() {
        global $wp_query;
        
        $mod['id'] = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : '';
        $mod['obj'] = CsQuery::Cs_Get_Post_Custom( $mod['id'] );
        return $mod;
    }
    
    /**
     * Get term mod
     * 
     * @param type $mod_id
     * @return type
     */
    public function term_get_mod() {
            $mod['id'] = $GLOBALS['wp_query']->get_queried_object_id();
            $mod['name'] = 'term';
            $mod['is_term'] = true;
            $mod['obj'] = CsQuery::Cs_GetTermCustom();
            return $mod;
    }
    
    /**
     * User object
     * 
     */
    public function user_get_mod(){
        $mod['id'] = $GLOBALS['wp_query']->get_queried_object_id();
        $mod['name'] = 'user';
        $mod['is_user'] = true;
        
        $optn = array_merge_recursive(
            (array)$GLOBALS['wp_query']->get_queried_object(),
            array(
                'url' => Util::get_url(), 'description' => $GLOBALS['wp_query']->get_queried_object()->description
            )
        ); 
        $mod['obj'] = (object)array(
            'wp_options' =>  (object)$optn
        );
        return $mod;
    }

    /**
     * Check page type
     * 
     * @return boolean
     */
    public function is_post_page(){
        if ( $this->mod_defaults[ 'is_singular' ] || $this->mod_defaults[ 'is_single' ] ) {
            return true;
        }
        return false;
    }
    
    /**
     * Determine page is term
     * 
     * @return type
     */
    public function is_term_page() {
        $ret = false;
        if ( $this->mod_defaults['is_tax'] || $this->mod_defaults['is_category'] || $this->mod_defaults['is_tag'] ) {
            $ret = true;
        }
        return $ret;
    }
    
    /**
     * Get post id
     * 
     * @return type
     */
    public function get_post_id() {
        global $post;
        return isset($post->ID) ? $post->ID : false;
    }
    
    /**
     * Get Term ID
     * 
     * @return type
     */
    public function get_term_id(){
        $term_obj = get_queried_object();
        return isset($term_obj->term_id) ? $term_obj->term_id : false;
    }
    
    /**
     * Check if a post is a custom post type.
     * @param  mixed $post Post object or ID
     * @return boolean
     */
    function is_custom_post_type( $post = NULL ){
        $all_custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );

        // there are no custom post types
        if ( empty ( $all_custom_post_types ) )
            return FALSE;

        $custom_types      = array_keys( $all_custom_post_types );
        $current_post_type = get_post_type( $post );

        // could not detect current type
        if ( ! $current_post_type )
            return FALSE;

        return in_array( $current_post_type, $custom_types );
    }
    
    /**
     * Check home
     * 
     * @return boolean
     */
    public function is_home(){
        if ( $this->mod_defaults['is_home'] == 1 || $this->mod_defaults['is_home_page'] == 1 || $this->mod_defaults['is_home_index'] == 1 ) {
            return true;
        }else{
            return false;
        }
    }
}


