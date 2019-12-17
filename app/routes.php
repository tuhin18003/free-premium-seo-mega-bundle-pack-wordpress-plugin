<?php namespace CsSeoMegaBundlePack;

/** @var \Herbert\Framework\Router $router */

/***********Visitor Clink on Link Tracking***********/
$router->post([
    'as'   => 'simpleRoute',
    'uri'  => '/click-tracking',
    'uses' => __NAMESPACE__ . '\Models\BacklinkManager\FrontEndActionsRequest\LinkTracking@save_click_tracking'
]);
