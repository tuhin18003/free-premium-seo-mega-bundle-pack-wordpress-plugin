<?php
namespace CsSeoMegaBundlePack\Library\SerpTracker\Services;

/**
 * CsSeoMegaBundlePack\Library\CsSerp extension for Mozscape (f.k.a. Seomoz) metrics.
 *
 * @package    CsSeoMegaBundlePack\Library\CsSerp
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Library\SerpTracker\Common\AiosSerpException as E;
use CsSeoMegaBundlePack\Library\SerpTracker\CsSerp;
use CsSeoMegaBundlePack\Library\SerpTracker\Config as Config;
use CsSeoMegaBundlePack\Library\SerpTracker\Helper as Helper;

class Mozscape extends CsSerp
{
    // A normalized 100-point score representing the likelihood
    // of the URL to rank well in search engine results.
    public static function getPageAuthority($url = false)
    {
        $data = static::getCols('34359738368', $url);
        return (parent::noDataDefaultValue() == $data) ? $data :
            $data['upa'];
    }

    // A normalized 100-point score representing the likelihood
    // of the domain of the URL to rank well in search engine results.
    public static function getDomainAuthority($url = false)
    {
        $data = static::getCols('68719476736', Helper\Url::parseHost($url));
        return (parent::noDataDefaultValue() == $data) ? $data :
            $data['pda'];
    }

    // The number of external equity links to the URL.
    // http://apiwiki.moz.com/glossary#equity
    public static function getEquityLinkCount($url = false)
    {
        $data = static::getCols('2048', $url);
        return (parent::noDataDefaultValue() == $data) ? $data :
            $data['uid'];
    }

    // The number of links (equity or nonequity or not, internal or external) to the URL.
    public static function getLinkCount($url = false)
    {
        $data = static::getCols('2048', $url);
        return (parent::noDataDefaultValue() == $data) ? $data :
            $data['uid'];
    }

    // The normalized 10-point MozRank score of the URL.
    public static function getMozRank($url = false)
    {
        $data = static::getCols('16384', $url);
        return (parent::noDataDefaultValue() == $data) ? $data :
            $data['umrp'];
    }

    // The raw MozRank score of the URL.
    public static function getMozRankRaw($url = false)
    {
        $data = static::getCols('16384', $url);
        return (parent::noDataDefaultValue() == $data) ? $data :
            number_format($data['umrr'], 16);
    }
    
    /**
     * Get Moz comparison
     * 
     * @param type $url
     * @return string | array
     */
    public static function getMozComparison( $url = false){
        $resources = static::getComparison($url);
        
        if(empty($resources)){
            return parent::noDataDefaultValue();
        }
        
        $resources = (object) $resources;
//        return $resources;
        
        $page_auth = isset($resources->data['page'][0]['page_authority']) ? $resources->data['page'][0]['page_authority'] : '';
        $moz_rank = isset($resources->data['page'][0]['mozrank']) ? $resources->data['page'][0]['mozrank'] : '';
        $domain_authority = isset($resources->data['root_domain'][0]['domain_authority']) ? $resources->data['root_domain'][0]['domain_authority'] : '';
        $total_external_links = isset($resources->data['root_domain'][0]['total_external_links']) ? $resources->data['root_domain'][0]['total_external_links'] : '';
        $total_links = isset($resources->data['root_domain'][0]['total_links']) ? $resources->data['root_domain'][0]['total_links'] : '';
        $moztrust = isset($resources->data['root_domain'][0]['moztrust']) ? $resources->data['root_domain'][0]['moztrust'] : '';
        $spam_score = isset($resources->data['subdomain'][0]['spam_score']) ? $resources->data['subdomain'][0]['spam_score'] : '';
        $spam_flags = isset($resources->data['subdomain'][0]['spam_flags']) ? $resources->data['subdomain'][0]['spam_flags'] : '';
        
        return array(
            'moz_rank' => $moz_rank,
            'moz_trust' => $moztrust,
            'spam_score' => $spam_score,
            'spam_flags' => $spam_flags,
            'domain_authority' => $domain_authority,
            'page_authority' => $page_auth,
            'total_links' => $total_links,
            'total_external_links' => $total_external_links
        );
        
    }

    /**
     * Return Link metrics from the (free) Mozscape (f.k.a. Seomoz) API.
     *
     * @access        public
     * @param   cols  string     The bit flags you want returned.
     * @param   url   string     The URL to get metrics for.
     */
    public static function getComparison($cols, $url = false)
    {

        $apiEndpoint = sprintf(Config\Services::MOZSCAPE_SERP_URL,
            urlencode(Helper\Url::parseHost(parent::getUrl($url)))
        );

        $ret = static::_getPage($apiEndpoint);

        return (!$ret || empty($ret) || '{}' == (string)$ret)
                ? parent::noDataDefaultValue()
                : Helper\Json::decode($ret, true);
    }
    
    
    
    /**
     * Return Link metrics from the (free) Mozscape (f.k.a. Seomoz) API.
     *
     * @access        public
     * @param   cols  string     The bit flags you want returned.
     * @param   url   string     The URL to get metrics for.
     */
    public static function getCols($cols, $url = false)
    {
        if ('' == Config\ApiKeys::getMozscapeAccessId() ||
            '' == Config\ApiKeys::getMozscapeSecretKey()) {
            throw new E('In order to use the Mozscape API, you must obtain
                and set an API key first (see CsSeoMegaBundlePack\Library\SerpTracker\Config\ApiKeys.php).');
            exit(0);
        }

        $expires = time() + 300;

        $apiEndpoint = sprintf(Config\Services::MOZSCAPE_API_URL,
            urlencode(Helper\Url::parseHost(parent::getUrl($url))),
            $cols,
            Config\ApiKeys::getMozscapeAccessId(),
            $expires,
            urlencode(self::_getUrlSafeSignature($expires))
        );

        $ret = static::_getPage($apiEndpoint);

        return (!$ret || empty($ret) || '{}' == (string)$ret)
                ? parent::noDataDefaultValue()
                : Helper\Json::decode($ret, true);
    }

    private static function _getUrlSafeSignature($expires)
    {
        $data = Config\ApiKeys::getMozscapeAccessId() . "\n{$expires}";
        $sig  = self::_hmacsha1($data, Config\ApiKeys::getMozscapeSecretKey());

        return base64_encode($sig);
    }

    private static function _hmacsha1($data, $key)
    {
        // Use PHP's built in functionality if available
        // (~20% faster than the custom implementation below).
        if (function_exists('hash_hmac')) {
            return hash_hmac('sha1', $data, $key, true);
        }

        return self::_hmacsha1Rebuild($data, $key);
    }

    private static function _hmacsha1Rebuild($data, $key)
    {
        $blocksize = 64;
        $hashfunc  = 'sha1';

        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }

        $key  = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key^$opad) .
                    pack('H*', $hashfunc(($key^$ipad) . $data))));
        return $hmac;
    }
}
