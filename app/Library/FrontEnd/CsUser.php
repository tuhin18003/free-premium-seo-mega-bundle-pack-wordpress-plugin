<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * WP Users
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

class CsUser {
    
    /**
     * Generate og author profile url
     * 
     * @param type $page_mod
     * @param type $options
     * @return type
     */
    public function csOgProfileUrls( $page_mod, $options ){
        $users  = array( $page_mod['obj']->wp_options->post_author );
        $crawler_name = GeneralHelpers::get_crawler_name();
        $ret = array();
        foreach ( $users as $user_id ){
            if( isset( $options->aios_web_graph_options['og_author_field'] ) && ( $author = $options->aios_web_graph_options['og_author_field']) != 'author' ){
                switch( $crawler_name ){
                    case 'pinterest':
                        $value = $this->CsAuthorMeta( $user_id, $options->aios_web_pub_options['p_author_name'], $page_mod );
                        break;
                    default:
                        $value = $this->CsAuthorWebsite( $user_id, $author, $options );
                        break;
                }
            }else{
                $field_id = empty( $author ) ? 'none' : $author;
                $value = $this->CsAuthorWebsite( $user_id, $field_id, $options );
            }
            
            if ( ! empty( $value ) ) {
                $ret[] = $value;
            }
        }
        return $ret;
    }
    
    /**
     * Get website author url
     * 
     * @param type $user_id
     * @param type $field_id
     * @param type $options
     * @return type
     */
    public function CsAuthorWebsite(  $user_id, $field_id = 'url', $options = false ){
        $url = '';
        switch ( $field_id ) {
            case 'none':
                    break;
            case 'index':
                    $url = get_author_posts_url( $user_id );
                    break;
            default:
                    $url = get_the_author_meta( $field_id, $user_id );
                    if ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) {
                            if ( $options->aios_web_graph_options['og_author_fallback'] && 
                                    ( $field_id === $options->aios_web_graph_options['og_author_field'] || 
                                            $field_id === $options->aios_web_pub_options['seo_author_field'] ) ) {
                                $url = get_author_posts_url( $user_id );
                            }
                    }
                    break;
        }
        return trim( $url );
    }
    
    /**
     * Generate author meta
     * 
     * @param type $user_id
     * @param type $field_id
     * @param type $page_mod
     * @return type
     */
    public function CsAuthorMeta( $user_id, $field_id, $page_mod ){
        $value = '';
        switch ( $field_id ) {
            case 'none':
                break;
            case 'fullname':
                $value = $page_mod['obj']->wp_options->author->first_name.' '.
                        $page_mod['obj']->wp_options->author->last_name;
                break;
            case 'description':
                $value = preg_replace( '/[\s\n\r]+/s', ' ', 
                        get_the_author_meta( $field_id, $user_id ) );
                break;
            case 'display_name':
                $value = $page_mod['obj']->wp_options->author->display_name;
                break;
            case 'nickname':
                $value = $page_mod['obj']->wp_options->author->nickname;
                break;
            default:
                $value = get_the_author_meta( $field_id, $user_id );
                break;
        }
        
        return trim( $value );
    }
    
}
