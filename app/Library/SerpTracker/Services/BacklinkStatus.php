<?php namespace CsSeoMegaBundlePack\Library\SerpTracker\Services;
/**
 * Link Status Information
 *
 * @package CSMonitorBacklinks
 * @since 1.0.0
 */

use CsSeoMegaBundlePack\Library\SerpTracker\CsSerp;
use CsSeoMegaBundlePack\Library\SerpTracker\Config;
use CsSeoMegaBundlePack\Library\SerpTracker\Helper;
use Goutte\Client;

class BacklinkStatus extends CsSerp {
    
    /**
     * Get Backlink information
     * 
     * @since 1.0.0
     * @param string $link_to
     * @return boolean|string
     */
    public static function getBacklinkStatus( $link_to ){
        
        $site_url = Helper\Url::getDomainName($link_to );
        $site_url = rtrim( $site_url, '/' );
        
        $resources = self::get_page_resource();
        if( !empty( $resources ) ) {
            $blc = $resources->filter("a")->each(function ($node) use ($site_url) {
                $anchor = $node->attr('href');
                if( strpos( $anchor, $site_url) > 0){
                    return array(
                        'link_text' => $node->text(),
                        'link_to' => $anchor,
                        'backlink_type' => empty($node->attr('rel')) ? '' : $node->attr('rel')
                    );
                }
            });
            
            $blc = array_values(array_filter($blc));
            return empty( $blc ) ? '' : $blc;
        
        }else{
            return false;
        }
    }

    /**
     * Get resources
     * 
     * @since 1.0.0
     * @return mixed
     */
    private static function get_resource_data() {
        
        $url = parent::getUrl($url);
        
        $crawler = $this->get_page_resource( $url );
        $this->resources = $crawler;
        
        return $this->resources;
    }

    
    /**
     * Get page resources
     * 
     * @since 1.0.0
     * @param $url
     * @return mixed
     */
    public static function get_page_resource( $url = false ) {
        $dataUrl = parent::getUrl($url);
        $client = new Client();
        $crawler = @$client->request( 'GET', $dataUrl ); 
        return empty( $crawler ) ? '' : $crawler;
    }
    
}
