<?php
/**
 * Backend Filters
 * 
 * @package Aios
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use Herbert\Framework\Http;

add_filter('user_contactmethods', function(){
    $http = new Http();
    $obj = new CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagsTabs($http);
    return $obj->filter_author_profile_page_contacts();
});