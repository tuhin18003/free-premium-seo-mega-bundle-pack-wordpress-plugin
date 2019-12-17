<?php
namespace CsSeoMegaBundlePack\Library\SerpTracker\Services;

/**
 * CsSeoMegaBundlePack\Library\CsSerp extension for Sistrix data.
 *
 * @package    CsSeoMegaBundlePack\Library\CsSerp
 * @author CodeSolz <customer-service@codesolz.net>
 * 
 * 
 * @updated    2013/08/14
 */

use CsSeoMegaBundlePack\Library\SerpTracker\Common\AiosSerpException as E;
use CsSeoMegaBundlePack\Library\SerpTracker\CsSerp;
use CsSeoMegaBundlePack\Library\SerpTracker\Config;
use CsSeoMegaBundlePack\Library\SerpTracker\Helper;

class Sistrix extends CsSerp
{
    public static function getDBs()
    {
        return array(
            'de', //   de – Germany
            'at', //   at – Austria
            'ch', //   ch – Switzerland
            'us', //   us – USA
            'uk', //   uk – England
            'es', //   es – Spain
            'fr', //   fr – France
            'it', //   it – Italy
        );
    }

    /**
     * Returns the Sistrix visibility index
     *
     * @access        public
     * @param   url   string     The URL to check.
     * @return        integer    Returns the Sistrix visibility index.
     * @link    http://www.sistrix.com/blog/870-sistrix-visibilityindex.html
     */
    public static function getVisibilityIndex($url = false)
    {
        $url     = parent::getUrl($url);
        $domain  = Helper\Url::parseHost($url);
        $dataUrl = sprintf(Config\Services::SISTRIX_VI_URL, urlencode($domain));

        $html = static::_getPage($dataUrl);
        @preg_match_all('#<h3>(.*?)<\/h3>#si', $html, $matches);

        return isset($matches[1][0]) ? $matches[1][0] : parent::noDataDefaultValue();
    }

    /**
     * Returns the Sistrix visibility index by using the SISTRIX API
     *
     * @access        public
     * @param   url   string     The URL to check.
     * @return        integer    Returns the Sistrix visibility index.
     * @link    http://www.sistrix.com/blog/870-sistrix-visibilityindex.html
     */
    public static function getVisibilityIndexByApi($url = false, $db = false)
    {
        self::guardApiKey();
        self::guardApiCredits();

        $url = parent::getUrl($url);
        $domain = static::getDomainFromUrl($url);
        $database = static::getValidDatabase($db);

        $dataUrl = sprintf(Config\Services::SISTRIX_API_VI_URL, Config\ApiKeys::getSistrixApiAccessKey(), urlencode($domain), $database);

        $json = static::_getPage($dataUrl);

        if(empty($json)) {
            return parent::noDataDefaultValue();
        }

        $json_decoded = (Helper\Json::decode($json, true));
        if (!isset($json_decoded['answer'][0]['sichtbarkeitsindex'][0]['value'])) {
            return parent::noDataDefaultValue();
        }
        return $json_decoded['answer'][0]['sichtbarkeitsindex'][0]['value'];
    }

    public static function getApiCredits()
    {
        self::guardApiKey();

        $dataUrl = sprintf(Config\Services::SISTRIX_API_CREDITS_URL, Config\ApiKeys::getSistrixApiAccessKey());
        $json = static::_getPage($dataUrl);

        if(empty($json)) {
            return parent::noDataDefaultValue();
        }

        $json_decoded = (Helper\Json::decode($json, true));
        if (!isset($json_decoded['answer'][0]['credits'][0]['value'])) {
            return parent::noDataDefaultValue();
        }
        return $json_decoded['answer'][0]['credits'][0]['value'];
    }

    public static function checkApiCredits()
    {
        return static::getApiCredits() > 0;
    }

    protected static function guardApiKey()
    {
        if(!static::hasApiKey()) {
            self::exc('In order to use the SISTRIX API, you must obtain and set an ' .
                      'API key first (see CsSeoMegaBundlePack\Library\SerpTracker\Config\ApiKeys.php).' . PHP_EOL);
        }
    }

    protected static function hasApiKey()
    {
        if ('' == Config\ApiKeys::getSistrixApiAccessKey()) {
            return false;
        }

        return true;
    }

    protected static function guardApiCredits()
    {
        if(!static::checkApiCredits()) {
            self::exc('Not enough API credits.'.PHP_EOL);
        }
    }

    private static function checkDatabase($db)
    {
        return !in_array($db, self::getDBs()) ? false : $db;
    }

    protected static function getDomainFromUrl($url)
    {
        $url      = parent::getUrl($url);
        $domain   = Helper\Url::parseHost($url);
        static::guardDomainIsValid($domain);

        return $domain;
    }

    protected static function getValidDatabase($db)
    {
        $db = ($db == false) ? Config\DefaultSettings::SISTRIX_DB : $db;

        $database = self::checkDatabase($db);
        static::guardDatabaseIsValid($database);

        return $database;
    }

    protected static function guardDatabaseIsValid($database)
    {
        if (false === $database) {
            self::exc('db');
        }
    }

    protected static function guardDomainIsValid($domain)
    {
        if (false == $domain) {
            self::exc('Invalid domain name.');
        }
    }

    private static function exc($err)
    {
        $e = ($err == 'db')
            ? "Invalid database. Choose one of: " .
               substr( implode(", ", self::getDBs()), 0, -2)
            : $err;
        throw new E($e);
    }
}
