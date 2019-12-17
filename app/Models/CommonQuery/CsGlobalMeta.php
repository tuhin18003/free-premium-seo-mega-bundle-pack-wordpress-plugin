<?php namespace CsSeoMegaBundlePack\Models\CommonQuery;

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

class CsGlobalMeta {
    
    /**
     * Global Options
     *
     * @var type array
     */
    public static $options;
    
    /**
     * Hold the prefix
     *
     * @var type string
     */
    public static $meta_prefix = 'aios_seo_';
    
    /**
     * Meta fields
     *
     * @var type 
     */
    public static $meta_fields = array(
        'general'  => array(
                'snippetpreview' => array(
                        'type'         => 'snippetpreview',
                        'title'        => '', // Translation added later.
                        'help'         => '', // Translation added later.
                        'help-button'  => '', // Translation added later.
                ),
                'focuskw_text_input' => array(
                        'type'          => 'focuskeyword',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'autocomplete'  => false,
                        'help'          => '', // Translation added later.
                        'description'   => '<div id="focuskwresults"></div>',
                        'help-button'   => '', // Translation added later.
                ),
                'focuskw' => array(
                        'type'  => 'hidden',
                        'title' => '',
                ),
                'title'          => array(
                        'type'          => 'hidden',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'description'   => '', // Translation added later.
                        'help'          => '', // Translation added later.
                ),
                'metadesc'       => array(
                        'type'          => 'hidden',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'class'         => 'metadesc',
                        'rows'          => 2,
                        'description'   => '', // Translation added later.
                        'help'          => '', // Translation added later.
                ),
                'linkdex'        => array(
                        'type'          => 'hidden',
                        'title'         => 'linkdex',
                        'default_value' => '0',
                        'description'   => '',
                ),
                'content_score'  => array(
                        'type'          => 'hidden',
                        'title'         => 'content_score',
                        'default_value' => '0',
                        'description'   => '',
                ),
                'metakeywords'   => array(
                        'type'          => 'metakeywords',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'class'         => 'metakeywords',
                        'description'   => '', // Translation added later.
                ),
                'pageanalysis'   => array(
                        'type'         => 'pageanalysis',
                        'title'        => '', // Translation added later.
                        'help'         => '', // Translation added later.
                        'help-button'  => '', // Translation added later.
                ),
        ),
        'advanced' => array(
                'meta-robots-noindex'  => array(
                        'type'          => 'select',
                        'title'         => '', // Translation added later.
                        'default_value' => '0', // = post-type default.
                        'options'       => array(
                                '0' => '', // Post type default - translation added later.
                                '2' => '', // Index - translation added later.
                                '1' => '', // No-index - translation added later.
                        ),
                ),
                'meta-robots-nofollow' => array(
                        'type'          => 'radio',
                        'title'         => '', // Translation added later.
                        'default_value' => '0', // = follow.
                        'options'       => array(
                                '0' => '', // Follow - translation added later.
                                '1' => '', // No-follow - translation added later.
                        ),
                ),
                'meta-robots-adv'      => array(
                        'type'          => 'multiselect',
                        'title'         => '', // Translation added later.
                        'default_value' => '-', // = site-wide default.
                        'description'   => '', // Translation added later.
                        'options'       => array(
                                '-'            => '', // Site-wide default - translation added later.
                                'none'         => '', // Translation added later.
                                'noodp'        => '', // Translation added later.
                                'noimageindex' => '', // Translation added later.
                                'noarchive'    => '', // Translation added later.
                                'nosnippet'    => '', // Translation added later.
                        ),
                ),
                'bctitle'              => array(
                        'type'          => 'text',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'description'   => '', // Translation added later.
                ),
                'canonical'            => array(
                        'type'          => 'text',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'description'   => '', // Translation added later.
                ),
                'redirect'             => array(
                        'type'          => 'text',
                        'title'         => '', // Translation added later.
                        'default_value' => '',
                        'description'   => '', // Translation added later.
                ),
        ),
        'social'   => array(),
        /* Fields we should validate & save, but not show on any form */
        'non_form' => array(
                'linkdex' => array(
                        'type'          => null,
                        'default_value' => '0',
                ),
        ),
    );
    
    public static $fields_index = array();
    public static $defaults = array();
    
    /**
     * Class Constructor
     */
    function __construct() {
    }

    /**
     * Get meta value
     * 
     * @global type $post
     * @param type $key
     * @param type $postid
     * @return string
     */
    public static function getMetaValue( $key, $postid = 0 ){
        global $post;
        $postid = absint( $postid );
        if ( $postid === 0 ) {
                if ( ( isset( $post ) && is_object( $post ) ) && ( isset( $post->post_status ) && $post->post_status !== 'auto-draft' ) ) {
                        $postid = $post->ID;
                }
                else {
                        return '';
                }
        }
        
        $custom = get_post_custom( $postid ); // Array of strings or empty array.
        
        if ( isset( $custom[ self::$meta_prefix . $key ][0] ) ) {
            $unserialized = maybe_unserialize( $custom[ self::$meta_prefix . $key ][0] );
            if ( $custom[ self::$meta_prefix . $key ][0] === $unserialized ) {
                    return $custom[ self::$meta_prefix . $key ][0];
            }
            else {
                    $field_def = self::$meta_fields[ self::$fields_index[ self::$meta_prefix . $key ]['subset'] ][ self::$fields_index[ self::$meta_prefix . $key ]['key'] ];
                    if ( isset( $field_def['serialized'] ) && $field_def['serialized'] === true ) {
                            return $unserialized;
                    }
            }
        }
        
        if ( isset( self::$defaults[ self::$meta_prefix . $key ] ) ) {
            return self::$defaults[ self::$meta_prefix . $key ];
        }
        else {
            return '';
        }
    }
    
    
}
