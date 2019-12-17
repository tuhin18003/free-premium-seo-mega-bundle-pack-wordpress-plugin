<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\TitlesMetas\Titles_Metas;
/**
 * Tabs Fields
 * 
 * @package On Page Optimization
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class TabsFields {
    
    /**
     * Holds Tabs inputs
     *
     * @var type 
     */
    private static $tabs = array();
    
    /**
     * Holds fields prefix
     *
     * @var type array
     */
    public static $post_types_fields_prefix = array(
        'title-', 'metadesc-', 'metakey-'
    );
    
    /**
     * Holds fields prefix for archive
     *
     * @var type array
     */
    public static $field_prefix_cpta = array(
        'title-ptarchive-', 'metadesc-ptarchive-', 'metakey-ptarchive-'
    );
    
    /**
     * Holds fields prefix
     *
     * @var type array
     */
    public static $tax_fields_prefix = array(
        'title-tax-', 'metadesc-tax-', 'metakey-tax-'
    );
    
    /** Holds fields prefix
     *
     * @var type array
     */
    public static $archive_author_fields_prefix = array(
        'title-author-aios', 'metadesc-author-aios', 'metakey-author-aios'
    );
    
    /** Holds fields prefix
     *
     * @var type array
     */
    public static $data_archive_fields_prefix = array(
        'title-archive-aios', 'metadesc-archive-aios', 'metakey-author-aios'
    );
    
    /** Holds fields prefix
     *
     * @var type array
     */
    public static $archive_fields_prefix = array(
        'title-archive-aios', 'metadesc-archive-aios', 'metakey-author-aios'
    );
    
    /** Holds fields prefix
     *
     * @var type array
     */
    public static $search_fields_prefix = array(
        'title-search-aios'
    );
    
    /** Holds fields prefix
     *
     * @var type array
     */
    public static $_404_prefix = array(
        'title-404-aios'
    );

    /**
     * holds Current field prefix
     *
     * @var type 
     */
    public static $current_field_prefix;
    
    private static $field_types = array(
        
    );

    /**
     * Combine Tabs fields
     * 
     * @return array
     */
    public static function aios_get_fields(){
        self::get_field_types();
        self::set_home_fields();
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        $post_types2 = get_post_types( array( '_builtin' => false, 'has_archive' => true ), 'objects' );
        self::get_post_types_fields($post_types);
        self::get_post_types_fields($post_types2, 'cpta'); // custom post types archive
        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
        self::get_taxonomies_fields( $taxonomies );
        self::archives();
        return self::$tabs;
    }
    
    /**
     * Set Home fields and init tabs array
     */
    private static function set_home_fields(){
        for($t=1; $t<=3; $t++){
            $fields_arr = self::$field_types[$t-1];
            if( isset(self::$tabs["tab{$t}"]['inputs']) ){
                self::$tabs["tab{$t}"]['inputs'] = array_merge( (array)self::$tabs["tab{$t}"]['inputs'], array( 
                        self::$post_types_fields_prefix[$t-1]."home-aios" => array_merge($fields_arr,  array( 'label' => __( 'Home', SR_TEXTDOMAIN ) ))
                    )
                );
            }else{ // if not not set // first loop
                self::$tabs = array_merge(self::$tabs, array(
                    "tab{$t}" => array(
                        'inputs' => array( 
                                self::$post_types_fields_prefix[$t-1]."home-aios" => array_merge($fields_arr,  array( 'label' => __( 'Home', SR_TEXTDOMAIN ) ))
                            )
                        )
                    )
                );
            }
        }
    }
    
    /**
     * Generate Tabs Inputs for post types
     * 
     * @param type $post_types
     * @param type $cpta
     */
    private static function get_post_types_fields($post_types, $cpta = false){
        $total = count( $post_types );
        if(is_array($post_types) && !empty( $post_types)){
            foreach($post_types as $post_type){
                $name = $post_type->labels->name;
                $id = $post_type->name;
                self::$current_field_prefix = self::$post_types_fields_prefix;
                if( $cpta ){
                    self::$current_field_prefix = self::$field_prefix_cpta;
                    $name = sprintf( __( 'Custom Post Type Archives(%s)', SR_TEXTDOMAIN ), $name);
                }
                for($t=1; $t<=3; $t++){
                    $fields_arr = self::$field_types[$t-1];
                    self::$tabs["tab{$t}"]['inputs'] = array_merge( (array)self::$tabs["tab{$t}"]['inputs'], array( 
                            self::$current_field_prefix[$t-1]."{$id}" => array_merge($fields_arr,  array( 'label' => __( $name, SR_TEXTDOMAIN ) ))
                        )
                    );
                }
            }
        }
    }
    
    /**
     * Generate Tabs Inputs for post types
     * 
     * @param type $post_types
     * @param type $cpta
     */
    private static function get_taxonomies_fields($taxonomies){
        if ( is_array( $taxonomies ) && $taxonomies !== array() ) {
	foreach ( $taxonomies as $tax ) {
		if ( in_array( $tax->name, array( 'link_category', 'nav_menu' ) ) ) {
                    continue;
		}
                $name = $tax->labels->name;
                $id = $tax->name;
                for($t=1; $t<=3; $t++){
                    $fields_arr = self::$field_types[$t-1]; // input types, tabs types
                    self::$tabs["tab{$t}"]['inputs'] = array_merge( (array)self::$tabs["tab{$t}"]['inputs'], array( 
                            self::$tax_fields_prefix[$t-1]."{$id}" => array_merge($fields_arr,  array( 'label' => __( $name, SR_TEXTDOMAIN ) ))
                        )
                    );
                }
            }
        }
    }
    
    /**
     * Get Archives
     */
    private static function archives(){
        for($t=1; $t<=3; $t++){
            
            self::$tabs["tab{$t}"]['inputs'] = array_merge(self::$tabs["tab{$t}"]['inputs'],
                    array( 
                        self::$archive_author_fields_prefix[$t-1] => array_merge(
                            self::$field_types[$t-1], array( 'label' => __( 'Author archives', SR_TEXTDOMAIN ) )
                    )
                )
            );
            
            self::$tabs["tab{$t}"]['inputs'] = array_merge(self::$tabs["tab{$t}"]['inputs'],
                    array( 
                        self::$archive_fields_prefix[$t-1] => array_merge(
                            self::$field_types[$t-1], array( 'label' => __( 'Date archives', SR_TEXTDOMAIN ) )
                    )
                )
            );
            
            if( isset( self::$search_fields_prefix[$t-1] )){
                self::$tabs["tab{$t}"]['inputs'] = array_merge(self::$tabs["tab{$t}"]['inputs'],
                        array( 
                            self::$search_fields_prefix[$t-1] => array_merge(
                                self::$field_types[$t-1], array( 'label' => __( 'Search pages', SR_TEXTDOMAIN ) )
                        )
                    )
                );
            }
            
            if( isset(self::$_404_prefix[$t-1])){
                self::$tabs["tab{$t}"]['inputs'] = array_merge(self::$tabs["tab{$t}"]['inputs'],
                        array( 
                            self::$_404_prefix[$t-1] => array_merge(
                                self::$field_types[$t-1], array( 'label' => __( '404 pages', SR_TEXTDOMAIN ) )
                        )
                    )
                );
            }
        }
    }

     

        /**
     * Get fields types
     * 
     * @return type
     */
    private static function get_field_types(){
        return self::$field_types = array(
            array(
                'placeholder' => __( "Enter title format", SR_TEXTDOMAIN ),
                'help_text' => __( "Enter title format. Example: {{sitename}} {{sep}} {{sitedesc}}", SR_TEXTDOMAIN ),
                'input_type' => 'text',
                'type' => 'input'
            ),
            array(
                'placeholder' => __( "Enter meta description", SR_TEXTDOMAIN ),
                'help_text' => __( "Enter meta description. Example: some text some text {{catname}}", SR_TEXTDOMAIN ),
                'type' => 'textarea',
            ),
            array(
                'placeholder' => __( "Enter meta keyword ", SR_TEXTDOMAIN ),
                'help_text' => __( "Enter meta keyword. Example: myKeyword", SR_TEXTDOMAIN ),
                'type' => 'textarea',
            )
        );
    }
    
    /**
     * Supported Tags format
     */
    public static function supported_tags_format(){
        return  array(
            'date'                 => __( 'The date of the post/page', SR_TEXTDOMAIN ),
            'title'                => __( 'The title of the post/page', SR_TEXTDOMAIN ),
            'parent_title'         => __( 'The title of the parent page of the current page', SR_TEXTDOMAIN ),
            'sitename'             => __( 'The site\'s name', SR_TEXTDOMAIN ),
            'sitedesc'             => __( 'The site\'s tag line / description', SR_TEXTDOMAIN ),
            'excerpt'              => __( 'The post/page excerpt (or auto-generated if it does not exist)', SR_TEXTDOMAIN ),
            'excerpt_only'         => __( 'The post/page excerpt (without auto-generation)', SR_TEXTDOMAIN ),
            'tag'                  => __( 'The current tag/tags', SR_TEXTDOMAIN ),
            'category'             => __( 'The post categories (comma separated)', SR_TEXTDOMAIN ),
            'primary_category'     => __( 'The primary category of the post/page', SR_TEXTDOMAIN ),
            'category_description' => __( 'The category description', SR_TEXTDOMAIN ),
            'tag_description'      => __( 'The tag description', SR_TEXTDOMAIN ),
            'term_description'     => __( 'The term description', SR_TEXTDOMAIN ),
            'term_title'           => __( 'The term name', SR_TEXTDOMAIN ),
            'searchphrase'         => __( 'The current search phrase', SR_TEXTDOMAIN ),
            'sep'                  => sprintf(
                    __( 'The separator defined in your theme\'s %s tag.', SR_TEXTDOMAIN ),
                    '<code>wp_title()</code>'
            ),
            'pt_single'                 => __( 'The post type single label', SR_TEXTDOMAIN ),
            'pt_plural'                 => __( 'The post type plural label', SR_TEXTDOMAIN ),
            'modified'                  => __( 'The post/page modified time', SR_TEXTDOMAIN ),
            'id'                        => __( 'The post/page ID', SR_TEXTDOMAIN ),
            'name'                      => __( 'The post/page author\'s \'nicename\'', SR_TEXTDOMAIN ),
            'user_description'          => __( 'The post/page author\'s \'Biographical Info\'', SR_TEXTDOMAIN ),
            'userid'                    => __( 'The post/page author\'s userid', SR_TEXTDOMAIN ),
            'currenttime'               => __( 'The current time', SR_TEXTDOMAIN ),
            'currentdate'               => __( 'The current date', SR_TEXTDOMAIN ),
            'currentday'                => __( 'The current day', SR_TEXTDOMAIN ),
            'currentmonth'              => __( 'The current month', SR_TEXTDOMAIN ),
            'currentyear'               => __( 'The current year', SR_TEXTDOMAIN ),
            'page'                      => __( 'The current page number with context (i.e. page 2 of 4)', SR_TEXTDOMAIN ),
            'pagetotal'                 => __( 'The current page total', SR_TEXTDOMAIN ),
            'pagenumber'                => __( 'The current page number', SR_TEXTDOMAIN ),
            'caption'                   => __( 'Attachment caption', SR_TEXTDOMAIN ),
            'focuskw'                   => __( 'The posts focus keyword', SR_TEXTDOMAIN ),
            'term404'                   => __( 'The slug which caused the 404', SR_TEXTDOMAIN ),
            'cf_<custom-field-name>'    => __( 'Replaced with a posts custom field value', SR_TEXTDOMAIN ),
            'ct_<custom-tax-name>'      => __( 'Replaced with a posts custom taxonomies, comma separated.', SR_TEXTDOMAIN ),
            'ct_desc_<custom-tax-name>' => __( 'Replaced with a custom taxonomies description', SR_TEXTDOMAIN ),
    );
    }
    
}
