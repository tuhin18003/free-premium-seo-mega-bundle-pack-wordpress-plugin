<?php namespace CsSeoMegaBundlePack\Library\Includes\HtAccess;

/**
 * Htaccess Handler
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

class CsHtaccesCreator {
    public $htaccess_root;
    public $htaccess_admin;

    function __construct(){
        $this->htaccess_root = ABSPATH . '.htaccess';
        $this->htaccess_admin = ABSPATH . 'wp-admin/.htaccess';
    }
    
    public  function remove_urls( $urls = array(), $site_url ){
        if ( ! function_exists( 'insert_with_markers' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/misc.php' );
        }
            $insertion = '# Stop Apache from serving .ht* files
<Files ~ "^\.ht">
Order allow,deny
Deny from all
</Files>' ."\n";
            if( !empty($urls) && is_array($urls)){
                foreach($urls as $url){
                    $remove_url = str_replace( $site_url, '', $url->url);
                    $insertion .= "\n"."RedirectMatch gone '{$remove_url}$'";
                }
                $insertion .= "\nErrorDocument 410 default";
                
            }
                
            
        //Since it has to be an array, explode
        $insertion = explode( "\n", $insertion );
        insert_with_markers( $this->htaccess_root, 'CS SEO MEGA BUNDLE PACK - Remove URLs', $insertion );
    }
    
    /**
     * Check permission
     * 
     * @return boolean
     */
    public function check_htaccess_permission(){
        if( !file_exists( $this->htaccess_root) && !is_writable( $this->htaccess_root)){
            return __( 'Please make sure your .htaccess file is writable.', SR_TEXTDOMAIN);
        }
        return 'true';
    }
    
}
