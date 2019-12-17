<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Premium Seo Mega Bundle Pack
 * Plugin URI:        http://codesolz.com/plugin/premium-seo-mega-bungle-pack
 * Description:       A plugin.
 * Version:           1.0.0
 * Author:            CodeSolz
 * Author URI:        http://codesolz.com
 * License:           MIT
 * Text Domain:       cs-seo-mega-bundle-pack
 * Domain Path:       ./lang
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

if ( ! class_exists( 'Csmbp_Seo_Tool' ) ){
    
    class Csmbp_Seo_Tool{
        
        /**
         * Hold actions hooks
         *
         * @var type 
         */
        private static $csmbp_hooks = [];
        
        function __construct(){
            
            //load plugins constant
            self::set_constant();
            
            //load core files
            self::load_core_framework();
            
            //load init
            self::load_action_files();
            
            /**
             * load textdomain
             */
            if ( is_admin() ) {
                add_action( 'init', array( __CLASS__, 'csmbp_init_textdomain' ) );
            }
        }
        
        /**
         * Set plugins constant
         */
        private static function set_constant(){
            /**
             * Define MB current version
             */
            define( 'CSMBP_VERSION', '1.0.0' );
            
            /**
            * Define MB current version
            */
            define( 'SR_TEXTDOMAIN', 'cs-seo-mega-bundle-pack' );

            /**
            * Plugin Base DIR URI
            */
            define( 'CSMBP_ROOT_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

            /**
            * Hold plugins base dir path
            */
            define( 'CSMBP_BASE_DIR_PATH', untrailingslashit(plugin_dir_path( __FILE__ ) ));
            
            /**
             * hold plubing base dir name
             */
            define( 'CSMBP_BASE_DIR_NAME', basename( dirname( __FILE__ ) ) );
            
            /**
             * should be remove
             */
            define( 'LOADING_GIF_URL', CSMBP_ROOT_URI . '/resources/assets/default/images/loader/loading.gif' );
            
            /**
             * trademark branding
             */
            define( 'CSMPB_BRAND_SIG', CSMBP_ROOT_URI . '/resources/assets/default/images/branding/trade-mark-logo.png' );
            
            /**
             * plubing branding
             */
            define( 'CSMBP_NAME_SIG', CSMBP_ROOT_URI . '/resources/assets/default/images/branding/plugin-name-signature.png' );
            
            //set plugins time zone to wordpress timezone
            if ( empty( $default_timeZone = get_option('timezone_string') ) ){
                date_default_timezone_set( 'UTC' );
            }else{
                date_default_timezone_set( $default_timeZone );
            }
        }
        
        /**
         * load core framework
         */
        private static function load_core_framework(){
            require_once CSMBP_BASE_DIR_PATH . '/vendor/autoload.php';
            require_once CSMBP_BASE_DIR_PATH . '/vendor/getherbert/framework/bootstrap/autoload.php';
        }
        
        /**
         * load actions
         */
        private static function load_action_files(){
            if( ! empty( $requires = \CsSeoMegaBundlePack\Helper::get( 'requires' ) ) ){
                foreach( $requires as $require){
                    $class_name = basename( $require, '.php' );
                    if( class_exists( $class_name ) ){
                        if ( ! array_key_exists( $class, self::$csmbp_hooks ) ) {
                            //create class for plain class files, not those classes with namespace
                            self::$csmbp_hooks[ $class_name ] = new $class_name();
                        }
                    }
                }
            }
        }
        
        /**
         * load textdomain
         */
        public static function csmbp_init_textdomain(){
            load_plugin_textdomain( SR_TEXTDOMAIN, false, CSMBP_BASE_DIR_NAME . '/lang' );
        }
    
    }
    
    global $Csmbp_Seo_Tool;
    $Csmbp_Seo_Tool = new Csmbp_Seo_Tool();
}