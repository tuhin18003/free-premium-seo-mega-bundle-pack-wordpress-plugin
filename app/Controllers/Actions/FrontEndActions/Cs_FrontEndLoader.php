<?php 
/**
 * Front End Loader
 * 
 * @package FontEnd Loader
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Library\FrontEnd\AiosWpHead;

class Cs_FrontEndLoader{
    
    function __construct() {
        //load actions
        add_action( 'init', array( __CLASS__, 'add_action' ) );
    }
    
    /**
     * Load Actions
     * 
     * @global type $pagenow
     */
    public static function add_action(){
        global $pagenow;
        if( isset( $pagenow ) && $pagenow != 'wp-login.php' && $pagenow != 'wp-signup.php' && !is_admin()){
            
            //load filters
            include( CSMBP_BASE_DIR_PATH . '/BacklinkManager/internalLinkFilter.php');
            
            //load redirections
            include( CSMBP_BASE_DIR_PATH . '/BacklinkManager/301_redirection/301_redirection.php');
            
            //load headers
            new AiosWpHead();
        }
        
    }
    
}