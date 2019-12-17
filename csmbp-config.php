<?php
global $wpdb;
return [
    
    /**
     * Plugin's Config & 
     * Herbert version constraint
     */
    'constraint'            => '~0.9.9',
    'db_version'            => '1.0.0',
    'PLUGIN_VERSION'        => '1.0.0',
    'PLUGIN_NAME'           => 'Premium Seo Mega Bundle Pack',
    'PLUGIN_SHORT_NAME'     => 'SEO Mega Pack',
    'PLUGIN_PREFIX'         => 'CSMBP',
    'PLUGIN_DATA_PREFIX'    => 'CSMBP_',
    'PLUGIN_SECTION_ID'     => 'section_csmbp_',
    'PLUGIN_TBL_PREFIX'     => $wpdb->prefix . 'csmbp_',
    'PLUGIN_AUTHOR_URL'     => 'http://codesolz.com/',
    'PLUGIN_STORE_URL'      => 'http://codecanyon.net/item/',
    
    /**
     * Auto-load all required files.
     */
    'requires' => [
        CSMBP_BASE_DIR_PATH . '/app/Library/WpCore/MultitpleActions.php',
        CSMBP_BASE_DIR_PATH . '/app/Library/SerpTracker/CsBacklinkSerp.php',
        CSMBP_BASE_DIR_PATH . '/app/Controllers/AdminPageBuilder/BacklinkManager/actions/backlinkManagerAjaxActionsRequire.php',
        CSMBP_BASE_DIR_PATH . '/app/Controllers/Actions/FrontEndActions/Cs_FrontEndLoader.php',
        CSMBP_BASE_DIR_PATH . '/app/Controllers/Actions/BackEndActions/BackEndActions.php',
    ],
    
    /**
     * The tables to manage.
     */
    'tables' => [
    ],


    /**
     * Activate
     */
    'activators' => [
        CSMBP_BASE_DIR_PATH . '/app/activate.php'
    ],

    /**
     * Deactivate
     */
    'deactivators' => [
        CSMBP_BASE_DIR_PATH . '/app/deactivate.php'
    ],

    /**
     * The shortcodes to auto-load.
     */
    'shortcodes' => [
        CSMBP_BASE_DIR_PATH . '/app/shortcodes.php'
    ],

    /**
     * The widgets to auto-load.
     */
    'widgets' => [
        CSMBP_BASE_DIR_PATH . '/app/widgets.php'
    ],

    /**
     * The styles and scripts to auto-load.
     */
    'enqueue' => [
        CSMBP_BASE_DIR_PATH . '/app/Enqueue/admin/themes/default/enqueue.php',
        CSMBP_BASE_DIR_PATH . '/app/Enqueue/admin/themes/default-actions/enqueue.php',
    ],

    /**
     * The routes to auto-load.
     */
    'routes' => [
        'CsSeoMegaBundlePack' => CSMBP_BASE_DIR_PATH . '/app/routes.php'
    ],

    /**
     * The panels to auto-load.
     */
    'panels' => [
        'CsSeoMegaBundlePack' => CSMBP_BASE_DIR_PATH . '/app/panels.php'
    ],

    /**
     * The APIs to auto-load.
     */
    'apis' => [
        'CsSeoMegaBundlePack' => CSMBP_BASE_DIR_PATH . '/app/api.php'
    ],

    /**
     * The view paths to register.
     *
     * E.G: 'CsSeoMegaBundlePack' => CSMBP_BASE_DIR_PATH . '/views'
     * can be referenced via @CsSeoMegaBundlePack/
     * when rendering a view in twig.
     */
    'views' => [
        'CsSeoMegaBundlePack' => CSMBP_BASE_DIR_PATH . '/resources/views'
    ],

    /**
     * The view globals.
     */
    'viewGlobals' => [

    ],

    /**
     * The asset path.
     */
    'assets' => '/resources/assets/'

];
