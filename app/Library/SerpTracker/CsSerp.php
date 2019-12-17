<?php namespace CsSeoMegaBundlePack\Library\SerpTracker;

use CsSeoMegaBundlePack\Library\SerpTracker\Common\AiosSerpException as E;
use CsSeoMegaBundlePack\Library\SerpTracker\Config as Config;
use CsSeoMegaBundlePack\Library\SerpTracker\Helper as Helper;
use CsSeoMegaBundlePack\Library\SerpTracker\Services as Service;

/**
 * Check required PHP settings.
 */
if (!function_exists('curl_init')) {
    throw new E('CsSeoMegaBundlePack\Library\SerpTracker requires the PHP CURL extension.');
    exit();
}

if (1 == ini_get('safe_mode') || 'on' === strtolower(ini_get('safe_mode'))) {
    throw new E('Because some CsSeoMegaBundlePack\Library\SerpTracker functions require the CURLOPT_FOLLOWLOCATION flag, ' .
        'you must not run PHP in safe mode! (This flag can not be set in safe mode.)');
    exit();
}

/**
 * Starting point for the CsSeoMegaBundlePack\Library\SerpTracker library. Example Usage:
 *
 * <code>
 * ...
 * $url = 'http://www.domain.tld';
 *
 * // Get the Google Toolbar PageRank value.
 * $result = \CsSeoMegaBundlePack\Library\SerpTracker\Services\Google::getPageRank($url);
 *
 * // Get the first 100 results for a Google search for 'query string'.
 * $result = \CsSeoMegaBundlePack\Library\SerpTracker\Services\Google::getSerps('query string');
 *
 * // Get the first 500 results for a Google search for 'query string'.
 * $result = \CsSeoMegaBundlePack\Library\SerpTracker\Services\Google::getSerps('query string', 500);
 *
 * // Check the first 500 results for a Google search for 'query string' for
 * // occurrences of the given domain name and return an array of matching
 * // URL's and their position within the serps.
 * $result = \CsSeoMegaBundlePack\Library\SerpTracker\Services\Google::getSerps('query string', 500, $url);
 * ...
 * </code>
 *
 */
class CsSerp
{
    const BUILD_NO = Config\Package::VERSION_CODE;

    protected static $_url,
                     $_host,
                     $_lastHtml,
                     $_lastLoadedUrl,
                     $_curlopt_proxy,
                     $_curlopt_proxyuserpwd,
                     $_ua
                     = false;

    public function __construct($url = false)
    {
        if (false !== $url) {
            self::setUrl($url);
        }
    }

    public function Alexa()
    {
        return new Service\Alexa;
    }

    public function Google()
    {
        return new Service\Google;
    }

    public function Mozscape()
    {
        return new Service\Mozscape;
    }

    public function OpenSiteExplorer()
    {
        return new Service\OpenSiteExplorer;
    }

    public function SEMRush()
    {
        return new Service\SemRush;
    }

    public function Sistrix()
    {
        return new Service\Sistrix;
    }

    public function Social()
    {
        return new Service\Social;
    }
    
    public function BacklinkStatus()
    {
        return new Service\Backlink_Status;
    }
    
    

    public static function getLastLoadedHtml()
    {
        return self::$_lastHtml;
    }

    public static function getLastLoadedUrl()
    {
        return self::$_lastLoadedUrl;
    }

    /**
     * Ensure the URL is set, return default otherwise
     * @return string
     */
    public static function getUrl($url = false)
    {
        $url = false !== $url ? $url : self::$_url;
        return $url;
    }

    public function setUrl($url)
    {
        if (false !== Helper\Url::isRfc($url)) {
            self::$_url  = $url;
            self::$_host = Helper\Url::parseHost($url);
        }
        else {
            throw new E('Invalid URL!');
            exit();
        }
        return true;
    }

    public static function getHost($url = false)
    {
        return Helper\Url::parseHost(self::getUrl($url));
    }
        
    public static function getDomain($url = false)
    {
        return 'http://' . self::getHost($url = false);
    }
    
    

    /**
     * @return DOMDocument
     */
    protected static function _getDOMDocument($html) {
        $doc = new \DOMDocument;
        @$doc->loadHtml($html);
        return $doc;
    }

    /**
     * @return DOMXPath
     */
    protected static function _getDOMXPath($doc) {
        $xpath = new \DOMXPath($doc);
        return $xpath;
    }

    /**
     * Get Domain Status
     * 
     * @param type $url
     * @return Int
     */
    public static function getDomainStatus( $url = false){
        $url = self::getUrl($url);
        if (self::getLastLoadedUrl() == $url) {
            return self::getLastLoadedHtml();
        }
        $status_code = Helper\HttpRequest::getHttpCode($url);
        if ( is_numeric($status_code)) {
            return $status_code;
        }
        else {
            self::noDataDefaultValue();
        }
    }
    /**
     * @return HTML string
     */
    protected static function _getPage($url) {
        $url = self::getUrl($url);
        if (self::getLastLoadedUrl() == $url) {
            return self::getLastLoadedHtml();
        }

        $html = Helper\HttpRequest::sendRequest($url);
        if ($html) {
            self::$_lastLoadedUrl = $url;
            self::_setHtml($html);
            return $html;
        }
        else {
            self::noDataDefaultValue();
        }
    }
    

    protected static function _setHtml($str)
    {
        self::$_lastHtml = $str;
    }

    protected static function noDataDefaultValue()
    {
        return Config\DefaultSettings::DEFAULT_RETURN_NO_DATA;
    }

    /**
     * @return Proxy address
     */
    public static function getCurloptProxy()
    {
        return self::$_curlopt_proxy;
    }

    /**
     * @param Proxy address $curlopt_proxy
     */
    public static function setCurloptProxy($curlopt_proxy)
    {
        self::$_curlopt_proxy = $curlopt_proxy;
    }

    /**
     * @return Proxy auth
     */
    public static function getCurloptProxyuserpwd()
    {
        return self::$_curlopt_proxyuserpwd;
    }

    /**
     * @param Proxy auth $curlopt_proxyuserpwd
     */
    public static function setCurloptProxyuserpwd($curlopt_proxyuserpwd)
    {
        self::$_curlopt_proxyuserpwd = $curlopt_proxyuserpwd;
    }

    /**
     * @return Useragent string
     */
    public static function getUserAgent()
    {
        return self::$_ua;
    }

    /**
     * @param Useragent string $ua
     */
    public static function setUserAgent($ua)
    {
        self::$_ua = $ua;
    }
}