<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module;

/**
 * Broken Link - Modules
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module\PatternMatching;
use CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module\LinkParser;

class modules {
    
    public static function blogroll(){
        global $wpdb;
        $links = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'links'
        ));
        if( $links ){
            $link_arr = array();
            foreach( $links as $link){
                self::insert_link_to_db(array(
                    'url' => $link->link_url,
                    'anchor' => $link->link_name,
                    'link_rel' => $link->link_rel,
                    'link_type' => 'blogroll',
                    'ref_id' => $link->link_id,
                    'ref_container' => 'links'
                ));
            }
        }
        return false;
    }
    
    public static function comment($options = array()){
        return self::any_post( $options, 'comment', 'comments');
    }
    
    public static function custom_posts( $options = array() ){
        return false;
    }
    
    public static function custom_css( $options = array() ){
        return false;
    }

    public static function posts($options = array()){
        return self::any_post( $options, 'post', 'posts');
    }
    
    public static function page($options = array()){
        return self::any_post( $options, 'page', 'posts');
    }

    private static function any_post( $options = array(), $type, $ref_container ){
        global $wpdb;
        if( empty( $options ) && empty($type)) return false;
        
        $where = '';
        
        if($type === 'comment'){
            $select = 'comment_ID as ID,comment_content as post_content';
            $table =  $wpdb->prefix . 'comments';
            $where = ' comment_content REGEXP "' . PatternMatching::any_link() .'"';  
        }else{
            $where = " post_type = '{$type}' ";
            if( !empty( $options->statuses ) ){
                $statuses = implode( ",", array_map( array( __CLASS__, 'add_quotes' ),  (array)$options->statuses ) );
                $where .= " and post_status in ({$statuses}) ";
            }
            $where .= ' and post_content REGEXP "' . PatternMatching::any_link() .'"';  
            $table =  $wpdb->prefix . 'posts';
            $select = 'ID, post_content';
        }
        
        $get_content = CsQuery::Cs_Get_Results(array(
            'select' => $select,
            'from' => $table,
            'where' => $where
        ));
        
        
        if( $get_content ){
            $links = array();
            foreach($get_content as $item){
                $link  = LinkParser::get_links( $item->ID, $item->post_content, $options );
                if( $link !== false ){
                    $links = array_merge( $links, $link);
                }
            }
            self::add_post_link_db($links, $ref_container);
        }else{
            return false;
        }
    }
    
    public function add_quotes( $str ){
        return sprintf( "'%s'", $str);
    }
    
    private static function add_post_link_db( $array, $ref_container ){
        global $wpdb;
        if( empty( $array)) return false;
        foreach($array as $key => $item){
            $key_arr = explode( "_", $key );
            
            foreach($item as  $subitem){
                $anchor = ''; $url = ''; $rel = '';
                if(is_array($subitem)){
                    $an_rel = explode( '___', array_keys($subitem)[0]);
                    $anchor = isset($an_rel[1]) ? $an_rel[1] : '';
                    $rel = isset($an_rel[3]) ? $an_rel[3] : '';
                    $url = array_values($subitem)[0];
                }
                $url = empty($url) ? $subitem : $url;
                $anchor = empty($anchor) ? $key_arr[2] : $anchor;
                
                self::insert_link_to_db(array(
                    'url' => $url,
                    'anchor' => $anchor,
                    'link_type' => $key_arr[2],
                    'ref_id' => $key_arr[1],
                    'link_rel' => $rel,
                    'ref_container' => $ref_container
                ));
            }
        }
    }
    
    private static function insert_link_to_db( $insert_data ){
        if( !is_array($insert_data)) return false;
        $insert_data = (object)$insert_data;
        
        $check_exists = CsQuery::Cs_Count(array(
            'table' => 'aios_all_internal_links',
            'where' => "url = '{$insert_data->url}' and anchor_type = '{$insert_data->anchor}'",
        ));
        if( 0 === $check_exists ){
            CsQuery::Cs_Insert(array(
                'table'=> 'aios_all_internal_links',
                'insert_data' => array(
                    'url' => $insert_data->url,
                    'anchor_type' => $insert_data->anchor,
                    'link_type' => $insert_data->link_type,
                    'ref_container' => $insert_data->ref_container,
                    'ref_id' => $insert_data->ref_id,
                    'link_rel' => $insert_data->link_rel,
                    'broken' => 0,
                    'dismissed'=> 0,
                    'check_count' => 0,
                    'link_found' => date('Y-m-d H:i:s')
                )
            ));
        }
        
    }
}
