<?php namespace CsSeoMegaBundlePack\Library\Backend\Builder;

/**
 * Class: Option page loader
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class AdminOptoinPageBuilder {
    
    /**
     * Http Instance
     *
     * @var type 
     */
    private $http;
    
    public function __construct( $http ) {
        $this->http = $http;
    }
    
    /**
     * admin All option pages 
     */
    private function admin_option_pages( $package_name ){
        $segments = array( 
            'BacklinkManager' => array(
                'blc' => 'BacklinkChecker'
            )
        );
        
        return isset( $segments[ $package_name ] ) ? $segments[ $package_name ] : false; 
    }
    
    /**
     * 
     * @param type $block_name
     * @return booleanGet segment name
     */
    public function cs_option_page_loader( $package_name ){
        $current_tab = $this->http->get( 'tab', false );
        $current_page = $this->http->get( 'page', false );
        
        if( !empty( $current_tab ) ) {
            if( ( $sub_package = $this->get_subpackage_name( $package_name, $current_tab ) ) === false ){
                return false;
            }
            return "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\{$package_name}\\Tabs\\{$sub_package}\\" . $this->Cs_SanitizeTabToFunction( $current_tab );
        }
        return false;
    }
    
    /**
     * Get segment name
     */
    private function get_subpackage_name( $package_name, $current_tab ){
        if( !empty( $package_group = $this->admin_option_pages( $package_name ) ) ){
            foreach( $package_group as $key => $val ){
                if( strpos( $current_tab, $key ) !== false){
                    return $val;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Sanitize tab name to class name
     */
    private function Cs_SanitizeTabToFunction( $tab_name ){
        if( ( $pos = strpos( $tab_name, '-' )) !== false ){
            return str_replace( ' ', '', ucwords( str_replace( '-', ' ', substr( $tab_name, (int)($pos+1) ))));
        }
        return false;
    }
    
}
