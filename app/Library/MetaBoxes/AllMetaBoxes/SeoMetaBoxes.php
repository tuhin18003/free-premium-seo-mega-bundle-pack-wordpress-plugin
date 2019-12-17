<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes;

/**
 * Library :  Social Meta Tags Options 
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsMetaTabGenerator;

class SeoMetaBoxes {
    
    /**
     * Hold Options
     *
     * @var type 
     */
    private $options;

    /**
     * Hold Nonce ID
     *
     * @var type 
     */
    private $_nonce_id = '_seo_meta_tag_nonce';
    
    /**
     * Hold Nonce Action Value
     *
     * @var type 
     */
    private $_nonce = '_seo_meta_tag_options';

    /**
     * Hold Tab Generator instance
     *
     * @var type object
     */
    private $_tabGenerator;
    
    /**
     * Hold data prefix
     *
     * @var type 
     */
    private $dataPrefix;

    public function __construct() {
        $this->dataPrefix = Helper::get('PLUGIN_DATA_PREFIX');
        
        /**create instance of tab generator**/
        $this->_tabGenerator = new CsMetaTabGenerator();
        
        /**load custom script for this section**/
        add_action( 'admin_footer', array( $this, 'load_custom_script' ) );
    }
    
    /**
     * Register Meta box
     * 
     * @return array
     */
    public static function meta_box(){
        return array( array(
            'id' => 'meta_box',
            'title' => __( '%s - SEO options', SR_TEXTDOMAIN ),
            'callback' => array( 'CsSeoMegaBundlePack\\Library\\MetaBoxes\\AllMetaBoxes\\SeoMetaBoxes', 'meta_box_content' ),
            'screen' => '',
            'context' => 'normal',
            'priority' => 'core'
        ));
    }
    
        
    /**
     * Custom Script
     * 
     * @return string 
     */
    public function load_custom_script(){
        ?>
        <script type="text/javascript">
            /**
             * Tabs Script
             * 
             * @version 1.0.0
             * @param {type} $
             */
            jQuery(document).ready(function( $ ) {
               
                
            });
            
        </script>
        <?php
    }
    
    /**
     * Meta Box Content
     * 
     * @param object $post
     * @param object $options
     */
    public function meta_box_content( $post, $options ){
        $this->_tabGenerator->tabID = 'seo_options';
        $this->_tabGenerator->nonce = array(
          'action' => $this->_nonce,
          'name' => $this->_nonce_id
        );
        $this->_tabGenerator->tabsName = array(
          __( 'Miscellaneous', SR_TEXTDOMAIN),
          __( 'SEO Test', SR_TEXTDOMAIN)
        );
        
        $this->_tabGenerator->tabsContent = array(
                array(
                    'st1' => array(
                        'helptext' => __( '%s Robot / Bot options', SR_TEXTDOMAIN ),
                        'type' => 'section_title'
                    ),
                    'schema_img_dimensions' => array(
                        'wrapper' => true,
                        'label' => __( 'Robot / Bot Meta', SR_TEXTDOMAIN ),
                        'options' => array(
                            'meta-robots-nofollow' => array(
                                'after_text' => __( 'Follow(Default)', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'on',
                                'value' => $this->__get_value( $options, 'meta-robots-nofollow' ),
                            ),
                            'meta-robots-nofollow2' => array(
                                'checkbox_same_id' => 'meta-robots-nofollow',
                                'after_text' => __( 'No Follow', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'off',
                                'value' => $this->__get_value( $options, 'meta-robots-nofollow' ),
                            ),
                            'meta-robots-noindex' => array(
                                'line_break' => true,
                                'after_text' => __( 'Index (Default)', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'on',
                                'value' => $this->__get_value( $options, 'meta-robots-noindex' ),
                            ),
                            'meta-robots-noindex2' => array(
                                'checkbox_same_id' => 'meta-robots-noindex',
                                'after_text' => __( 'No Index', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'off',
                                'value' => $this->__get_value( $options, 'meta-robots-noindex' ),
                            ),
                            'meta-robots-archive' => array(
                                'line_break' => true,
                                'after_text' => __( 'Archive (Default)', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'on',
                                'value' => $this->__get_value( $options, 'meta-robots-archive' ),
                            ),
                            'meta-robots-Archive2' => array(
                                'checkbox_same_id' => 'meta-robots-archive',
                                'after_text' => __( 'No Archive', SR_TEXTDOMAIN ),
                                'type' => 'radio',
                                'default_value' => 'off',
                                'value' => $this->__get_value( $options, 'meta-robots-archive' ),
                            ),
                            'meta-robots-noodp' => array(
                                'line_break' => true,
                                'after_text' => __( 'Noodp', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                                'value' => $this->__get_value( $options, 'meta-robots-noodp' ),
                            ),
                            'meta-robots-noodir' => array(
                                'after_text' => __( 'Noydir (for yahoo)', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                                'value' => $this->__get_value( $options, 'meta-robots-noodir' ),
                            )
                        ),
                        'type' => 'miscellaneous',
                    )
                ),
                array(
                    'st2' => array(
                        'helptext' => __( '%s All Social Websites / Open Graph', SR_TEXTDOMAIN ),
                        'type' => 'section_title'
                    )
                )
          );
          $this->_tabGenerator->get_meta_contents();
    }
    
    /**
     * Get Values
     * 
     * @param type $options
     * @param type $id
     * @param type $wp_id
     * @return type
     */
    private function __get_value( $options, $id, $wp_id = '' ){
        if( isset( $options[ 'args' ]->post->wp_options->$id ) && !empty( $value = $options[ 'args' ]->post->wp_options->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) && !empty( $value = $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_graph_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_graph_options[ $id ] )){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_pub_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_pub_options[ $id ] )){
            return $value;
        }
    }

    /**
     * Save custom data
     * 
     * @param type $post
     * @return boolean
     */
    public function on_save( $post ) {
        if ( 
            ! isset($_POST[ $this->dataPrefix . $this->_nonce_id  ]) 
            || ! wp_verify_nonce( $_POST[ $this->dataPrefix . $this->_nonce_id ], $this->dataPrefix . $this->_nonce ) 
        ) return false;
            
        unset( $_POST[ $this->dataPrefix . $this->_nonce_id ]);
       
        if( !empty( $_POST ) ){
            if( ! isset( $_POST['CSMBP_meta-robots-noodir']) ){
                delete_post_meta( $_POST['post_ID'], 'CSMBP_meta-robots-noodir' );
            }
            if( ! isset( $_POST['CSMBP_meta-robots-noodp']) ){
                delete_post_meta( $_POST['post_ID'], 'CSMBP_meta-robots-noodp' );
            }
        }    
        
        return true;
    }
   
}