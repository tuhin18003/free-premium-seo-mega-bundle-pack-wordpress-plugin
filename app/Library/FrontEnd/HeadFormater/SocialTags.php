<?php namespace CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater;
/**
 * Class: SocialTags
 * Open Graph / twitter card / weibo tags
 * 
 * @package FrontEnd
 * @version 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Library\Includes\Util;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagsTabs;
use CsSeoMegaBundlePack\Library\FrontEnd\CsGetMedia;
use CsSeoMegaBundlePack\Library\FrontEnd\CsUser;

class SocialTags {
    
    /**
     * Hold page mode
     *
     * @var type 
     */
    public $page_mod;
    
     /**
     * Options
     *
     * @var array
     */
     public $options;
   
     /**
      * Hold Meta Tags
      *
      * @var type 
      */
     private $defaultMetaTagsList;
     
     /**
      * Hold Open Graph Tags
      *
      * @var type 
      */
     private $ogTags = array();
     
     /**
      * Hold Opengraph title
      *
      * @var type 
      */
     private $ogTitle;
     
     /**
      * Hold OpenGraph type
      *
      * @var type 
      */
     private $twitterCardType;
     
     /**
     * Hold CsPlugin data
     */
     public $CsPlugin;
     
     /**
      * Hold media class
      *
      * @var type 
      */
     private $CsMedia;
     
     /**
      * 
      * @var typeHold user class
      */
     private $CsUser;
     
    /**
     * Class Constructor
     */
    public function __construct() {
        //Instance of Meta Tags List
        $this->defaultMetaTagsList = new MetaTagsTabs();
        
        //instance of users class
        $this->CsUser = new CsUser();
    }

    /**
     * Add OpenGraph prefix attribute
     * 
     * @param String $html_attr
     * @version 1.0.0
     * @return String
     */
    public function Cs_OgpnsAttributes( $html_attr ){
        //generate og type
        $this->_Cs_GetOgType();
        
        $og_type = $this->page_mod['og_type']; 
        $og_ns = array(
            'og' => 'http://ogp.me/ns#',
            'fb' => 'http://ogp.me/ns/fb#',
        );
        
        $og_ns[ $og_type ] = MetaTagAssets::$MTA['head']['og_type_ns'][ $og_type ];
        
        $html_attr = ' '.$html_attr;	
        if ( strpos( $html_attr, ' prefix=' ) &&
                preg_match( '/^(.*) prefix=["\']([^"\']*)["\'](.*)$/', $html_attr, $match ) ) {
                        $html_attr = $match[1].$match[3];	// remove the prefix
                        $prefix_value = ' ';
        } else $prefix_value = '';
        
        foreach ( $og_ns as $name => $url ){
            if( ($pos = strpos( $name, '.')) !== false ){
                $name = substr( $name, 0 , $pos);
            }
            
            if ( strpos( $prefix_value, ' '.$name.': '.$url ) === false ){
                $prefix_value .= ' '.$name.': '.$url;
            }
        }
        
        $html_attr .= ' prefix="'.trim( $prefix_value ).'"';  //generate new prefix
        
        return trim( $html_attr );
    }
    
    
    /**
     * Get open graph type
     * 
     * @example http://ogp.me/ Open Graph Type
     * @version 1.0.0
     * @return string
     */
    private function _Cs_GetOgType(){
        global $wp_query;
        $og_type = 'website'; //default og type
        if ( $this->mod_defaults['is_home'] == 1 || $this->mod_defaults['is_home_page'] == 1 || $this->mod_defaults['is_home_index'] == 1 ) {
            $og_type = 'website';
        }elseif( $this->page_mod['is_author'] == 1 || $this->page_mod['is_user'] == 1 ){
            $og_type = 'profile';
        }elseif( $this->page_mod['is_post'] == 1 || $this->page_mod['is_page'] == 1 ){
            if( isset( $this->page_mod['obj']->CSMBP_options->og_post_type ) && 
               ! empty( $user_defined_og = $this->page_mod['obj']->CSMBP_options->og_post_type ) ){
                //check user defined og type
                $og_type = $user_defined_og;
            }
            elseif( isset( $wp_query->query['post_type'] ) && ! empty( $post_type = $wp_query->query['post_type'] ) && 
                    isset( MetaTagAssets::$MTA['head']['og_type_ns'][ $post_type ] ) ){
                //post similar og
                $og_type = $post_type;
            }elseif( isset( $this->options->aios_web_graph_options['og_post_type'] ) && ! empty( $og_from_common_option = $this->options->aios_web_graph_options['og_post_type'] ) ){
                //common setting og
                $og_type = $og_from_common_option;
            }else{
                //if nothing found
                $og_type = 'article';
            }
            
            //if incease none saved
            if( $og_type == '[None]' ){
                $og_type = 'article';
            }
        }
        
        $this->page_mod['og_type'] = $og_type;
    }

    /**
     * Render Open Graph Meta Tags
     * 
     * @version 1.0.0
     * @return String
     */
    public function Cs_RenderMetaTags(){
        
        //check current page type
        if ( !empty($this->page_mod['is_home']) || !empty($this->page_mod['is_home_page'] )) {
            $this->_cs_FacebookGraphsTags();
        }
        
        //generate open graph tags
        $this->_cs_OpenGraph();
        
        //get twitter card tags
        $this->_cs_TwitterGraphsTags();
        
        return $this->ogTags;
    }
     
    /**
     * Generate Facebook Graph Tags
     * 
     * @version 1.0.0
     * @return String|boolean
     */
    private function _cs_FacebookGraphsTags(){
        $common_fb_tags = array( 'property' => array());
        if( !empty( $tags = $this->defaultMetaTagsList->filter_taglist_fb() ) ){
            foreach( $tags as $tag ){
                if( isset( $this->options->aios_web_pub_options[ $tag[2] ] ) && !empty( $content = $this->options->aios_web_pub_options[ $tag[2] ] ) && !in_array( $tag[2], $this->options->aios_metas_stop_render['meta'][ $tag[1] ] ) ){
                    $common_fb_tags['property'] += array( $tag[2] => $content );
                }
            }
            $this->ogTags[] = $common_fb_tags;
        }
        return;
    }
    
    /**
     * Generate Open Graph Tags
     * 
     * @version 1.0.0
     * @return String|boolean
     */
    private function _cs_OpenGraph(){
        //get common tags generated
//        pre_print(MetaTagAssets::$MTA[ 'common_og_tags' ]);
        $common_og_tags = array( 'property' => array());
        foreach( MetaTagAssets::$MTA['common_og_tags'] as $tag ){
            if( method_exists( $this , $method = GeneralHelpers::Cs_SanitizeMetaTag( $tag ) ) && ( $method_val = $this->$method() ) !== false ){
                $common_og_tags['property'] += array( $tag => $method_val );
            }
        }
        
        $this->ogTags[] = $common_og_tags;
        
        
        //instance of media class
        $this->CsMedia = new CsGetMedia( $this->page_mod, $this->options, $this->CsPlugin );
        //get images
        $this->ogTags[] = array( 'property' => $this->CsMedia->get_all_images('og_img'));
        
        //working here - left video tag and the dymanic tag -reference - wpsso -opengraph.php
        //dynamic meta head tag - og_type_mt
        if( isset( MetaTagAssets::$MTA['head']['og_type_ns'][ $this->page_mod['og_type'] ] ) && 
                ! empty( $og_group_tags = MetaTagAssets::$MTA['head']['og_type_mt'][ $this->page_mod['og_type'] ] ) ) {
            
            $tags = array();
            foreach( $og_group_tags as $tag => $val ){
                if( isset( $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->$tag ) && 
                    ! empty( $tag_val = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->$tag ) ){
                        $tags += array( $tag => $tag_val );
                    }
            }
            $custom_tags = array_filter( $tags );
            $this->ogTags[] = array( 'property' => $custom_tags);
        }
        $extra_tags = array(); 
        $extra_name_tags = array();
        if ( ($this->page_mod['is_post'] || $this->page_mod['is_page'] ) && !empty($this->page_mod['obj'] )  ) {
            
            //tags need to check
            $extra_tags = array( 'og:updated_time' => date( 'c',strtotime($this->page_mod['obj']->wp_options->post_modified_gmt)) ); // $gmt = true
            
            if( $this->page_mod['og_type'] == 'article' ){
                if( isset( $this->options->aios_web_graph_options['og_author_field'] ) && 
                        $this->options->aios_web_graph_options['og_author_field'] != 'author' ){
                    $extra_tags += array( 
                        'article:author' => $this->CsUser->csOgProfileUrls( $this->page_mod, $this->options ),
                        'article:author:name' => $this->CsUser->CsAuthorMeta( $this->page_mod['obj']->wp_options->post_author, 
                                $this->options->aios_web_pub_options['fb_author_name'], $this->page_mod )
                    );
                } 
                $extra_tags += array(
                    'article:publisher' => $this->options->aios_web_pub_options[ $this->options->aios_web_graph_options['og_author_field'].'_publisher_url' ]
                );

                if( ! empty( $tags = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc_hashtag ) ){
                    $extra_tags += array(
                        'article:tag' => ''
                    );
                    foreach( $tags as $tag ){
                        $extra_tags['article:tag'][] = $tag;
                    }
                }

                if( ! empty( $art_section = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->og_art_section ) ){
                    $extra_tags += array(
                        'article:section' => $art_section
                    );
                }

                $extra_tags += array(
                    'article:published_time' => date( 'c', strtotime($this->page_mod['obj']->wp_options->post_date_gmt)),
                    'article:modified_time' => date( 'c', strtotime($this->page_mod['obj']->wp_options->post_modified_gmt)),
                );
                
                //weibo tags
                $extra_name_tags = array(
                    'weibo:article:create_at' => $extra_tags['article:published_time'],
                    'weibo:article:update_at' => $extra_tags['article:modified_time']
                );
            }
        }
        
        $this->ogTags[] = array( 'property' => $extra_tags);
        $this->ogTags[] = array( 'name' => $extra_name_tags);
        
        return;
    }

    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_url(){
        if( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->sharing_url) && !empty( $url = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->sharing_url ) ) {
            return $url;
        }else{
            return $this->page_mod['obj']->wp_options->url;
        }
    }
    
    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_title(){
        if( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->og_title) && !empty( $og_title = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->og_title) ){
            $this->ogTitle = substr( $og_title, 0, (int)MetaTagAssets::$MTA[ 'opt' ]['defaults']['og_title_len'] );
        }elseif( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->title) && !empty( $og_title = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->title) ){
            $title_suffix = empty( $suffix = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->title_suffix) ? '' : $suffix .' ';
            $title_hashtag = Util::Cs_HashTagFormat( $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->title_hashtag, (int)$this->options->aios_web_graph_options['og_desc_hashtags'] );
            $this->ogTitle =  $title_suffix . substr( $og_title, 0, (int)MetaTagAssets::$MTA[ 'opt' ]['defaults']['og_title_len'] ) . $title_hashtag;
        }else{
            $this->ogTitle = isset($this->page_mod['obj']->wp_options->post_title) ? substr( $this->page_mod['obj']->wp_options->post_title, 0, (int)MetaTagAssets::$MTA[ 'opt' ]['defaults']['og_title_len'] ) : false;
        }
        return $this->ogTitle;
    }
    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_description(){
        $og_desc = ''; 
        $desc_hashtag = '';
        if( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->og_desc) && !empty( $og_desc = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->og_desc) ){
            //nothing to do here
        }elseif( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc) && !empty( $og_desc = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc) ){
            $desc_hashtag = Util::Cs_HashTagFormat( $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc_hashtag, 
                    (int)$this->options->aios_web_graph_options['og_desc_hashtags'] );
            $desc_hashtag = empty( $desc_hashtag ) ? '' : ' '.$desc_hashtag;
        }else{
            if( isset($this->page_mod['obj']->wp_options->post_content) && ! empty( $og_desc = $this->page_mod['obj']->wp_options->post_content) ) {
                //nothing to do here
            }else{
                $og_desc = false;
            }
            $og_desc = isset($this->page_mod['obj']->wp_options->post_content) ? substr( $this->page_mod['obj']->wp_options->post_content, 0, (int)MetaTagAssets::$MTA[ 'opt' ]['defaults']['og_desc_len'] ) : false;
        }
        
        return Util::Cs_LimitTextLength( $og_desc, (int)MetaTagAssets::$MTA[ 'opt' ]['defaults']['og_desc_len'], $desc_hashtag );
        
    }

    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_locale(){
        return Util::Cs_GetLocale( 'default' ); 
    }
    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_site_name(){
       return Util::get_site_name( $this->options );
    }
    
    /**
     * OG Helper function
     * 
     * @return type
     */
    private function og_type(){
       return $this->page_mod['og_type'];
    }
    

    /**
     * Generate Twitter Graph Tags
     * 
     * @version 1.0.0
     * @return String|boolean
     */
    private function _cs_TwitterGraphsTags(){
        if ( !empty($this->page_mod['is_home']) || !empty($this->page_mod['is_home_page'] )) {
            $this->twitterCardType = isset($this->options->aios_web_pub_options['tc_type_default']) ? $this->options->aios_web_pub_options['tc_type_default'] : MetaTagAssets::$MTA[ 'opt' ]['defaults']['tc_type_default'];
        }else{
            $this->twitterCardType = isset($this->options->aios_web_pub_options['tc_type_post']) ? $this->options->aios_web_pub_options['tc_type_post'] : MetaTagAssets::$MTA[ 'opt' ]['defaults']['tc_type_post'];
        }
        $tc_tags = array();
        foreach( MetaTagAssets::$MTA[ 'twitter_meta' ][ $this->twitterCardType ] as $tag ){
            if( method_exists( $this , $method = GeneralHelpers::Cs_SanitizeMetaTag( $tag ) ) && ( $method_val = $this->$method() ) !== false ){
                $tc_tags += array( $tag => $method_val);
            }else if( isset( $this->options->aios_web_pub_options[ $tag ] ) && !empty( $content = $this->options->aios_web_pub_options[ $tag ] ) ){
                $tc_tags += array( $tag => $method_val);
            }
        }
        
        $this->ogTags[] = array( 'name' => $tc_tags);
    }
    
    /**
     * Generate twitter title tag
     * 
     * @version 1.0.0
     * @return String
     */
    private function twitter_title() {
        return isset($this->ogTitle) ? $this->ogTitle : '';
    }
    
    /**
     * Generate twitter description
     * 
     * @version 1.0.0
     * @return String
     */
    private function twitter_description() {
        $og_desc = ''; 
        $desc_hashtag = '';
        if( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->tc_desc) && 
            !empty( $og_desc = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}options"}->tc_desc) ){
            //do nothing here
        }elseif( isset($this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc) && !empty( $og_desc = $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc) ){
            $desc_hashtag = Util::Cs_HashTagFormat( $this->page_mod['obj']->{"{$this->CsPlugin['dataPrefix']}final_options"}->desc_hashtag, 
                    (int)$this->options->aios_web_graph_options['og_desc_hashtags'] );
            //do nothing here
            $desc_hashtag = empty( $desc_hashtag ) ? '' : ' '.$desc_hashtag;
        }else{
            $og_desc = isset($this->page_mod['obj']->wp_options->post_content) ? $this->page_mod['obj']->wp_options->post_content : false;
        }
        
        return Util::Cs_LimitTextLength( $og_desc, (int)MetaTagAssets::$MTA['opt']['defaults']['tc_desc_len'], $desc_hashtag );
    }
    
    /**
     * Get twitter card image
     * 
     * @return type
     */
    private function twitter_image(){
        $obj = isset( $this->page_mod['obj']->wp_options ) ? $this->page_mod['obj']->wp_options : $this->page_mod['obj'];
        return empty( $thumburl = get_the_post_thumbnail_url( $obj, $this->CsPlugin['dataPrefix'] . $this->twitterCardType ) ) ? false : $thumburl;
    }
    
    /**
     * get card type
     */
    private function twitter_card(){
        return isset($this->twitterCardType) ? $this->twitterCardType : '';
    }
    
    /**
     * Get twitter domain
     * 
     * @return type
     */
    private function twitter_domain(){
        return Util::get_site_url( true );
    }
    
    /**
     * Get Creator
     * 
     * @return type
     */
    private function twitter_creator(){
        if( isset( $this->page_mod['obj']->wp_options->author ) ){
            return $this->page_mod['obj']->wp_options->author->display_name;
        }
        return false;
    }

}

//view-source:http://www.codesolz-plugins.com
//view-source:http://www.codesolz-plugins.com/2017/11/12/hello-world/
//view-source:http://www.codesolz-plugins.com/shop/
//view-source:http://www.codesolz-plugins.com/product/fcm/
//view-source:http://www.codesolz-plugins.com/category/uncategorized/
//view-source:http://www.codesolz-plugins.com/tag/test/
//view-source:http://www.codesolz-plugins.com/author/admin/
//view-source:http://www.codesolz-plugins.com/books/english-today/
//
//https://developers.facebook.com/docs/reference/opengraph/object-type/business.business/
    //working on post page - Open Graph Type , socialmetatagopiions.php

//http://www.codesolz-plugins.com/product/fcm/iphone/ - here