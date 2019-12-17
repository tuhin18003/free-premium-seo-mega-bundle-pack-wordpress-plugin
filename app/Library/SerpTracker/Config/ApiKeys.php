<?php
namespace CsSeoMegaBundlePack\Library\SerpTracker\Config;

/**
 * Configuration constants for the CsSeoMegaBundlePack\Library\CsSerp library.
 *
 * @package    CsSeoMegaBundlePack\Library\CsSerp
 * @author CodeSolz <customer-service@codesolz.net>
 * 
 * 
 * @updated    2013/12/11
 */

/**
 * Client API keys
 * @package    CsSeoMegaBundlePack\Library\CsSerp
 */
class ApiKeys
{
    // To acquire an API key, visit Google's APIs Console here:
    //      https://code.google.com/apis/console
    // In the Services pane, activate the "PageSpeed Insights API" (not the service!).
    // Next, go to the API Access pane. The API key is near the bottom of that pane,
    // in the section titled "Simple API Access.".
    const GOOGLE_SIMPLE_API_ACCESS_KEY = '';

    // To acquire a Mozscape (f.k.a. SEOmoz) API key, visit:
    //      https://moz.com/products/api/keys
    const MOZSCAPE_ACCESS_ID  = '';
    const MOZSCAPE_SECRET_KEY = '';

    // To acquire a SISTRIX API key, visit:
    //      http://www.sistrix.de
    const SISTRIX_API_ACCESS_KEY = '';

    public static function getGoogleSimpleApiAccessKey() {
        return env('GOOGLE_SIMPLE_API_ACCESS_KEY', self::GOOGLE_SIMPLE_API_ACCESS_KEY);
    }

    public static function getMozscapeAccessId() {
        return env('MOZSCAPE_ACCESS_ID', self::MOZSCAPE_ACCESS_ID);
    }

    public static function getMozscapeSecretKey()
    {
        return env('MOZSCAPE_SECRET_KEY', self::MOZSCAPE_SECRET_KEY);
    }

    public static function getSistrixApiAccessKey()
    {
        return env('SISTRIX_API_ACCESS_KEY', self::SISTRIX_API_ACCESS_KEY);
    }
}
