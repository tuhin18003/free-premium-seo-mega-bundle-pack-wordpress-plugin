<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * WP Front Get Images
 * 
 * @package FrontEnd
 * @since 1.0.0
 * @version 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;

class CsGetMedia {
    
    /**
     * Hold page mode
     *
     * @var type 
     */
    private $page_mod;
    
     /**
     * Options
     *
     * @var array
     */
     private $options;
     
     /**
      * Hold plugin data
      * 
      * @var array
      */
     private $CsPlugin;


     /**
      * Hold image properties
      *
      * @var type 
      */
     private $og_single_image;

     /**
      * Hold img preg
      *
      * @var type 
      */
     private $def_img_preg = array(
        'html_tag' => 'img',
        'pid_attr' => 'data-[a-z]+-pid'
    );

     /**
      * Class Construction
      * 
      * @param type $mod
      * @param type $options
      */
    function __construct( $mod, $options, $CsPlugin ) {
        $this->page_mod = $mod;
        $this->options  = $options;
        $this->CsPlugin = $CsPlugin;
        $this->og_single_image = MetaTagAssets::$MTA['og_image_properties'];
    }
    
    /**
     * get image
     * 
     * @return type
     */
    public function get_all_images( $size_name = '' ){
        $og_tg = array(); 
        if( $this->page_mod['is_post'] ){
            if ( ( is_attachment( $this->page_mod['id'] ) || $this->page_mod['obj']->wp_options->post_type === 'attachment' ) ) {
                return $this->get_attachment_image( $size_name );
            }
            
            //if current object is not an attachment
            if( empty( $og_tg = $this->get_post_images( $size_name ) ) ){
                $og_tg = $this->get_content_images( $size_name );
            }
        }
        
        if( empty( $og_tg ) ){
            $og_tg = $this->get_default_images( $size_name );
        }
        $og_tg = array_reverse( array_filter( $og_tg ) );
        unset( $og_tg['og:image:cropped'] );
//        pre_print( $og_tg );
        return $og_tg;
    }
    
    /**0
     * Get attachment image
     * 
     * @param type $size_name
     * @return array
     */
    public function get_attachment_image( $size_name = '' ){
        $og_single_image = $this->og_single_image;
        if( wp_attachment_is_image( $this->page_mod['id'] ) ){
            list(
                $og_single_image['og:image'],
                $og_single_image['og:image:width'],
                $og_single_image['og:image:height'],
                $og_single_image['og:image:cropped']
            )  = $this->get_attachment_image_src( $size_name );
        }
        return $og_single_image;
    }
    
    /**
     * get attached images
     * 
     * @param type $size_name
     * @return array
     */
    public function get_attached_images( $size_name = '' ){
        $images = get_children( array(
                    'post_parent' => $this->page_mod['id'],
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image'
            ), OBJECT );
        
        $attach_ids = array();
        foreach ( $images as $attach ) {
                if ( ! empty( $attach->ID ) ) {
                        $attach_ids[] = $attach->ID;
                }
        }
        rsort( $attach_ids, SORT_NUMERIC );
        $og_single_image = $this->og_single_image;
        foreach ( $attach_ids as $pid ) {
                list(
                    $og_single_image['og:image'],
                    $og_single_image['og:image:width'],
                    $og_single_image['og:image:height'],
                    $og_single_image['og:image:cropped']
                ) = $this->get_attachment_image_src( $size_name, $pid );

                if ( ! empty( $og_single_image['og:image'] ) ) {
                    break;	
                }
        }
        return $og_single_image;
    }

    /**
     * Get content images
     * 
     * @param type $size_name
     * @return array
     */
    public function get_content_images( $size_name = '' ){
        $og_ret = array();
        pre_print( $this->page_mod );
        if( !isset($this->page_mod['obj']->wp_options->post_content ) || empty( $content = $this->page_mod['obj']->wp_options->post_content )){
            return $og_ret;
        }
        $og_single_image = $this->og_single_image;
        $size_info = GeneralHelpers::Cs_GetSizeInfo( $size_name );
        $img_preg = $this->def_img_preg;
        foreach( array( 'html_tag', 'pid_attr' ) as $type ) {
            $filter_name = 'content_image_preg_' . $type;
            if ( has_filter( $filter_name ) ) {
                $img_preg[$type] = apply_filters( $filter_name, $this->def_img_preg[$type] );
            }
        }
        
        if ( preg_match_all( '/<(('.$img_preg['html_tag'].')[^>]*? ('.$img_preg['pid_attr'].')=[\'"]([0-9]+)[\'"]|'.
                '(img)[^>]*? (data-share-src|data-lazy-src|data-src|src)=[\'"]([^\'"]+)[\'"])[^>]*>/s',
                        $content, $all_matches, PREG_SET_ORDER ) ) {
            
            foreach ( $all_matches as $img_num => $img_arr ) {
                $tag_value = $img_arr[0];

                if ( empty( $img_arr[5] ) ) {
                    $tag_name = $img_arr[2];	// img
                    $attr_name = $img_arr[3];	// data-wp-pid
                    $attr_value = $img_arr[4];	// id
                } else {
                    $tag_name = $img_arr[5];	// img
                    $attr_name = $img_arr[6];	// data-share-src|data-lazy-src|data-src|src
                    $attr_value = $img_arr[7];	// url
                }
                
                switch ( $attr_name ) {
                    // WordPress media library image id
                    case 'data-wp-pid':
                        list(
                                $og_single_image['og:image'],
                                $og_single_image['og:image:width'],
                                $og_single_image['og:image:height'],
                                $og_single_image['og:image:cropped'],
                                $og_single_image['og:image:id']
                        ) = $this->get_attachment_image_src( $size_name, $attr_value );
                        break;
                    default:
                        // recognize gravatar images in the content
                        if ( preg_match( '/^(https?:)?(\/\/([^\.]+\.)?gravatar\.com\/avatar\/[a-zA-Z0-9]+)/', $attr_value, $match ) ) {
                            $og_single_image['og:image'] = GeneralHelpers::Cs_GetPort().':'.$match[2].'?s='.$size_info['width'].'&d=404&r=G';
                            $og_single_image['og:image:width'] = $size_info['width'];
                            $og_single_image['og:image:height'] = $size_info['width'];	// square image
                            break;	
                        }
                        
                        if ( preg_match( '/class="[^"]+ wp-image-([0-9]+)/', $tag_value, $match ) ) {
                            list(
                                    $og_single_image['og:image'],
                                    $og_single_image['og:image:width'],
                                    $og_single_image['og:image:height'],
                                    $og_single_image['og:image:cropped']
                            ) = $this->get_attachment_image_src( $size_name, $match[1] );
                            break;	
                        } else {
                            $og_single_image = array(
                                'og:image' => $attr_value,
                                'og:image:width' => '',
                                'og:image:height' => ''
                            );
                        }
                        
                        if ( empty( $og_single_image['og:image'] ) ) {
                            break;
                        }
                        
                        if ( empty( $og_single_image['og:image:width'] ) || $og_single_image['og:image:width'] < 0 ||
                            empty( $og_single_image['og:image:height'] ) || $og_single_image['og:image:height'] < 0 ) {
                            GeneralHelpers::Cs_AddImageUrlSize( 'og:image', $og_single_image );
                        }
                        
                        break;
                    }
                }
                return $og_ret;
            }
            
        return $og_ret;
    }

    /**
     * Get default images
     * 
     * @param type $size_name
     * @return array
     */
    public function get_default_images( $size_name = '' ){
        $og_ret = array();
        $og_single_image = $this->og_single_image;
        
        foreach ( array( 'id', 'id_pre', 'url', 'url:width', 'url:height' ) as $key ) {
            $img[$key] = empty( $this->options->aios_web_graph_options['og_def_img_'.$key] ) ?
                        '' : $this->options->aios_web_graph_options['og_def_img_'.$key];
        }
        
        if ( empty( $img['id'] ) && empty( $img['url'] ) ) {
            return $og_ret;
        }
        
        if ( ! empty( $img['id'] ) ) {
            list(
                    $og_single_image['og:image'],
                    $og_single_image['og:image:width'],
                    $og_single_image['og:image:height'],
                    $og_single_image['og:image:cropped'],
                    $og_single_image['og:image:id']
            ) = $this->get_attachment_image_src( $size_name, $img['id'] );
        }
        
        if ( empty( $og_single_image['og:image'] ) && ! empty( $img['url'] ) ) {
            $og_single_image = array(
                    'og:image' => $img['url'],
                    'og:image:width' => $img['url:width'],
                    'og:image:height' => $img['url:height'],
            );
        }
        
        if ( ! empty( $og_single_image['og:image'] ) ) {
            return $og_ret;
        }
        return $og_ret;
    }

    /**
     * Get post images
     * 
     * @param type $size_name
     * @return type
     */
    public function get_post_images( $size_name = '' ){
        if( empty($img_property = $this->get_featured( $size_name ) ) ) {
            $img_property = $this->get_attached_images( $size_name );
        }
        return $img_property;
    }

    /**
     * Get featured image
     * 
     * @param type $size_name
     * @return array
     */
    public function get_featured( $size_name = '' ){
        if ( has_post_thumbnail( $this->page_mod['id'] ) ) {
            $attachment_id = get_post_thumbnail_id( $this->page_mod['id'] );
        } else {
            $attachment_id = false;
        }
        
        $og_single_image = $this->og_single_image;
        if ( ! empty( $attachment_id ) ) {
            list(
                $og_single_image['og:image'],
                $og_single_image['og:image:width'],
                $og_single_image['og:image:height'],
                $og_single_image['og:image:cropped']
            ) = $this->get_attachment_image_src( $size_name, $attachment_id );
        }
        return $og_single_image;
    }


    /**
     * Get attachment image src
     * 
     * @param type $size_name
     * @return type
     */
    public function get_attachment_image_src( $size_name = '', $attachment_id = false ){
        $size_name = empty( $size_name ) ? 'thumbnail' : $this->CsPlugin['dataPrefix'].$size_name;
//        $size_info = GeneralHelpers::Cs_GetSizeInfo( $size_name );
//        $img_meta = wp_get_attachment_metadata( $this->page_mod['id'] );

        //get attachment id
        $attachment_id = empty( $attachment_id ) ? $this->page_mod['id'] : $attachment_id; 
        
        return $img = image_downsize( $attachment_id, $size_name );
//        pre_print( $img );
    }
    
}
