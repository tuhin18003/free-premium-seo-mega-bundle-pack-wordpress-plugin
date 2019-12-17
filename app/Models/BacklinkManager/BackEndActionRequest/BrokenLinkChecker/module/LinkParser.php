<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module;

/**
 * Broken Link - parsers
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module\PatternMatching;

class LinkParser {
    
    public static function get_links( $post_id,$content, $options ){
        $links = array();
        if( $options->link_types ){
            $link = '';
            $content = htmlspecialchars_decode( $content );
            foreach($options->link_types as $type){
                if(method_exists(__CLASS__, $type)){
                    $link = self::$type($content);
                    if( is_array( $link )){
                        $link = array(
                            'id_'.$post_id.'_'.$type => $link
                        );
                        $links = array_merge($links, $link);
                    }
                }
            }
        }
        return is_array($links) ? array_filter($links) : false;
    }
    
    private static function link( $content ){
        $links = self::extract_tags($content, 'a', false, true);
        $ret_link = array();
        if( $links ){
            foreach($links as $link){
                $anchor = $link['contents'];
                $rel = isset($link['attributes']['rel']) ? $link['attributes']['rel'] : '';
                $ret_link[] = array( "a___{$anchor}___r___{$rel}" => $link['attributes']['href']); 
            }
        }
        return empty($ret_link) ? false : $ret_link;
    }
    
    
    private static function image( $content ){
        $ret_link = array();
        if(preg_match_all('/(<img[\s]+[^>]*src\s*=\s*)([\"\'])([^>]+?)\2([^<>]*>)/i', $content, $matches, PREG_SET_ORDER)){
            foreach($matches as $link){
                $ret_link[] = $link[3]; 
            }
        }
        return empty($ret_link) ? false : $ret_link;
    }

    private static function youtube_iframe( $content ){
        $results = array();
	if(strpos($content, PatternMatching::youtube_iframe() ) && strpos($content, PatternMatching::youtube_playlist_embed()) === false && strpos($content, 'playlist') === false && strpos($content, 'list') === false){
            return self::get_iframe_src($content);
        }
    }
    
    private static function youtube_embed( $content ){
        $results = array();
	if(strpos($content, PatternMatching::youtube_embed())){
            return self::get_iframe_src($content);
        }
    }
    
    private static function googlevideo_embed( $content ){
        $results = array();
	if( strpos($content, PatternMatching::googlevideo_embed()) ){
            $links =  self::get_iframe_src($content);
            return self::get_iframe_src($content);
        }
    }
    
    private static function vimeo_embed( $content ){
        $results = array();
	if( strpos($content, PatternMatching::vimeo_embed()) ){
            $links =  self::get_iframe_src($content);
            return self::get_iframe_src($content);
        }
    }
    
    private static function dailymotion_embed( $content ){
        $results = array();
	if( strpos($content, PatternMatching::dailymotion_embed()) ){
            $links =  self::get_iframe_src($content);
            return self::get_iframe_src($content);
        }
    }
    
    private static function youtube_playlist_embed( $content ){
        $results = array();
	if( strpos($content, 'youtube.com') && (strpos($content, PatternMatching::youtube_playlist_embed()) || strpos($content, 'playlist') || strpos($content, 'list'))){
            $links =  self::get_iframe_src($content);
            return self::get_iframe_src($content);
        }
    }
    
    private static function get_iframe_src( $content ){
        $ret_link = array();
        if(preg_match_all('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $content, $matches, PREG_SET_ORDER)){
            foreach ($matches as $match){
                $ret_link[] = isset($match[1]) ? $match[1] : '';
            }
        }
        return empty( $ret_link ) ? false : $ret_link;
    }

    
    public static function extract_tags( $html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'ISO-8859-1' ){
	 
		if ( is_array($tag) ){
			$tag = implode('|', $tag);
		}
	 
		//If the user didn't specify if $tag is a self-closing tag we try to auto-detect it
		//by checking against a list of known self-closing tags.
		$selfclosing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param' );
		if ( is_null($selfclosing) ){
			$selfclosing = in_array( $tag, $selfclosing_tags );
		}
	 
		//The regexp is different for normal and self-closing tags because I can't figure out 
		//how to make a sufficiently robust unified one.
		if ( $selfclosing ){
			$tag_pattern = 
				'@<(?P<tag>'.$tag.')			# <tag
				(?P<attributes>\s[^>]+)?		# attributes, if any
				\s*/?>							# /> or just >, being lenient here 
				@xsi';
		} else {
			$tag_pattern = 
				'@<(?P<tag>'.$tag.')			# <tag
				(?P<attributes>\s[^>]+)?		# attributes, if any
				\s*>							# >
				(?P<contents>.*?)				# tag contents
				</(?P=tag)>						# the closing </tag>
				@xsi';
		}
	 
		$attribute_pattern = 
			'@
			(?P<name>\w+)											# attribute name
			\s*=\s*
			(
				(?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)	# a quoted value
				|							# or
				(?P<value_unquoted>[^\s"\']+?)(?:\s+|$)				# an unquoted value (terminated by whitespace or EOF) 
			)
			@xsi';
	 
		//Find all tags 
		if ( !preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ){
			//Return an empty array if we didn't find anything
			return array();
		}
	 
		$tags = array();
		foreach ($matches as $match){
	 
			//Parse tag attributes, if any
			$attributes = array();
			if ( !empty($match['attributes'][0]) ){ 
	 
				if ( preg_match_all( $attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER ) ){
					//Turn the attribute data into a name->value array
					foreach($attribute_data as $attr){
						if( !empty($attr['value_quoted']) ){
							$value = $attr['value_quoted'];
						} else if( !empty($attr['value_unquoted']) ){
							$value = $attr['value_unquoted'];
						} else {
							$value = '';
						}
	 
						//Passing the value through html_entity_decode is handy when you want
						//to extract link URLs or something like that. You might want to remove
						//or modify this call if it doesn't fit your situation.
						$value = html_entity_decode( $value, ENT_QUOTES, $charset );
	 
						$attributes[$attr['name']] = $value;
					}
				}
	 
			}
	 
			$tag = array(
				'tag_name' => $match['tag'][0],
				'offset' => $match[0][1], 
				'contents' => !empty($match['contents'])?$match['contents'][0]:'', //empty for self-closing tags
				'attributes' => $attributes, 
			);
			if ( $return_the_entire_tag ){
				$tag['full_tag'] = $match[0][0]; 			
			}
	 
			$tags[] = $tag;
		}
	 
		return $tags;
	}
}
