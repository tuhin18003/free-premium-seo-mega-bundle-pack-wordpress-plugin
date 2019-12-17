<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module;

/**
 * Broken Link - Pattern matching
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

class PatternMatching {
    
    /**
     * url match
     * 
     * @return string
     */
    public static function link(){
        return "(https?:\/\/|www\.)[\.A-Za-z0-9\-]+\.[a-zA-Z]{2,4}";
    }
    
    /**
     * url match
     * 
     * @return string
     */
    public static function any_link(){
        return "(https?:\/\/|www\.)[\.A-Za-z0-9\-]";
    }
    
    /**
     * Match Images
     * 
     * @return string
     */
    public static function image(){
        return "\.(jpe?g|gif|png|ani|bmp|cal|img|jbg|jpg|mac|pbm|pcd|pcx|pct|pgm|psd|ras|tga|tiff|wmf)";
    }
    
    /**
     * Match plaintext url
     * 
     * @return string
     */
    public static function plaintext_url(){
        return "#(?<=[\s>\]]|^)(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is";
    }

    /**
     * Match youtube iframe
     * 
     * @return string
     */
    public static function youtube_iframe(){
        return 'youtube.com/embed/';
    }
    
    /**
     * Match youtube embed
     * 
     * @return string
     */
    public static function youtube_embed(){
        return 'youtube.com/v/';
    }
    
    /**
     * Match youtube playlist embed
     * 
     * @return string
     */
    public static function youtube_playlist_embed(){
        return 'youtube.com/p/';
    }
    
    /**
     * Match youtube playlist embed
     * 
     * @return string
     */
    public static function smart_youtube_embed(){
        return 'youtube.com/p/';
    }
    
    /**
     * Match googe video embed
     * 
     * @return string
     */
    public static function googlevideo_embed(){
        return 'video.google.com/';
    }
    
    /**
     * Match vimeo video embed
     * 
     * @return string
     */
    public static function vimeo_embed(){
        return 'vimeo.com/moogaloop.swf?';
    }
    
    /**
     * Match dailymotion embed
     * 
     * @return string
     */
    public static function dailymotion_embed(){
        return 'dailymotion.com/swf/video/';
    }
    
    
    public static function get_abv( $arg ){
        if( $arg === 'links'){
            return 'Blogroll';
        }
        else if( $arg === 'posts'){
            return 'Posts / Pages';
        }
        else if( $arg === 'image'){
            return 'Image';
        }
    }
    
}
