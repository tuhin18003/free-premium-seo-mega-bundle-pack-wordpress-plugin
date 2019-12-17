<?php namespace CsSeoMegaBundlePack\Library\Includes\Builders;

/**
 * Library : Tab Generator
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsForm;

class CsMetaTabGenerator {
    
    /**
     * Hold Class Variables
     *
     * @var type Array
     */
    private $_data = array();

    /**
     * Property Setter
     * 
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        $this->_data[ $name ] = $value;
    }
    
    /**
     * Get Meta Tabs Content
     * 
     * @param type $echo
     * @return string
     */
    public function get_meta_contents( $echo = true ){
        $prefix = Helper::get('PLUGIN_PREFIX');
        $prefix1 = Helper::get('PLUGIN_DATA_PREFIX');
        $content = "<div class='{$prefix}-section'>";
        if( isset( $this->_data[ 'nonce'] )){
            $content .= wp_nonce_field( $prefix1 . $this->_data[ 'nonce'][ 'action' ],  $prefix1 . $this->_data[ 'nonce'][ 'name' ], false, false );
        }
        if( isset($this->_data['tabID']) ){
            $tabID = $this->_data['tabID'];
            $content .= "<div class='horizontalTabs'>";
            $t = 0; 
            if( isset( $this->_data[ 'tabsName' ] ) ){
                $content .= '<ul>'; 
                foreach($this->_data[ 'tabsName' ] as $tabName){
                    $content .= "<li><a href='#{$prefix}_{$tabID}_{$t}'>{$tabName}</a></li>";
                    $t++;
                }
                $content .= '</ul>';
            }
            $CsForm = new CsForm(); 
            $j = 0;
            if( isset( $this->_data[ 'tabsContent' ] ) ){
                foreach($this->_data[ 'tabsContent' ] as $tabContent ){
                    $content .= "<div id='{$prefix}_{$tabID}_{$j}'>";
                    $content .= $CsForm->get_input_fields( $tabContent );
                    $content .= "</div>";
                    $j++;
                }
            }
            $content .= '</div>';
        }else{
              $content .= '<div class="csmbp-notice csmbp-warning">'.
                  '<div class="notice-label">'.
                      '<i class="fa fa-warning"></i> Tab ID Required'.
                  '</div>'.
                  '<div class="notice-message">'.
                      __( "You need to add a tab id to generate tab.", SR_TEXTDOMAIN) 
                . '</div>' .
              '</div>';
        }
        
        //end metabox
        $content .= '</div>';
        
        if( $echo === true ){
            echo $content;
        }else {
            return $content;
        }
    }
    
    
}
