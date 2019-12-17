<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\TitlesMetas;
/**
 * Social Analytics Dashboard
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\TitlesMetas\Titles_Metas\TabsFields;

class TitlesMetasTabs{

    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
    /**
     *
     * @var type Check Google App Token
     */
    public $googleAppAuth = false;
    
    private $stats;



    public function __construct(\Herbert\Framework\Http $http) {
        global $AiosGooAppToken;
        $this->http = $http;
        
        //app auth
        $this->googleAppAuth =&$AiosGooAppToken;
    }
    
    /**
     * Tab Loader
     * 
     * @return String
     */
    public function tabs_landing(){
        $current_tab = $this->http->get('tab', false);
        $current_page = $this->http->get('page', false);
        
        if( $this->http->has('tab') && 'cs-on-page-optimization' == $current_page){
            $tabTypes = $this->get_tab_type($current_tab); 
            $newClassPath = "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\onPageOptimization\\Tabs\\{$tabTypes}\\" . $current_tab;
            if(class_exists($newClassPath)){
                $newObj = new $newClassPath( $this->http );
                return $newObj->load_page_builder( CommonText::common_text( array( $current_page, $current_tab)) );
            }else{
                $data = array(
                   'current_tab' => __( '', '' ),
                   'CommonText' => CommonText::common_text(),
                   'page_title' =>  __( 'Error 404', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Error page redirection', SR_TEXTDOMAIN ),
                    
                   'oops' =>  __( 'Whoops! Page not found!', SR_TEXTDOMAIN ),
                   'not_found_msg' =>  __( 'This page cannot found or is missing.', SR_TEXTDOMAIN ),
                   'dir_msg' =>  __( 'Use the navigation left or the button below to get back and track.', SR_TEXTDOMAIN ),
                    
                   'error_page_msg' =>  __( 'Sorry! we do not find the page you are looking for.', SR_TEXTDOMAIN ),
                   'back_btn_label' =>  __( 'Back to Dashbaoard', SR_TEXTDOMAIN ),
                   'back_btn_href' => admin_url('admin.php?page=cs-social-analytics'),
                );
                return  view( '@CsSeoMegaBundlePack/error/error_404.twig', $data );
            }
            
        }else{ //load social dashboard
            
            $data = array_merge( CommonText::form_element( 'title_metas', 'aios-title-meta-optimization' ), array(
           'CommonText' => CommonText::common_text(),
           'page_title' =>  __( 'Titles & Metas', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'s you optimize your Meta title, description & keyword.', SR_TEXTDOMAIN ),
           'panel_subtitle' => __( 'Titles & Metas', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Meta Title & Description Default Settings', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
                'Create your rules for your meta title, description, keywords and for other differnt pages',
                'Default rule is - {{title}} {{sep}} {{sitename}} for the unset fields',
                'You can create title & meta description by combination of Tags and text. Or just text.'
           ),
           'settings' => $this->init_settings(),
           'tabs' => array(
              'tab1' => __( 'Titles', SR_TEXTDOMAIN ), 'tab2' => __( 'Meta Description', SR_TEXTDOMAIN ), 'tab3' => __( 'Meta Keyword', SR_TEXTDOMAIN ), __( 'Tags Format', SR_TEXTDOMAIN )
           ),
           'title_seperator' => array(
               __( 'Title Seperator', SR_TEXTDOMAIN ),
               __( 'Title Seperator will display between your post title and site name.', SR_TEXTDOMAIN ),
               array(
                   '-','&ndash;', '&mdash;', '&middot;','&bull;', '*', '&#8902;', '|', '~','&laquo;', '&raquo;', '&lt;', '&gt;'
               )
           ),
           'tab_inputs' => TabsFields::aios_get_fields(),
           'supported_tags' => TabsFields::supported_tags_format(),     
           'label_submit_btn' => __( 'Save Now', SR_TEXTDOMAIN ),
       ));
            add_action('admin_footer', [$this, '_addFooter_script']);
            return  view( '@CsSeoMegaBundlePack/onPageOptimization/tabs/OpoTitlesMeta.twig', $data );
        }
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#title_metas_settings" );
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Init Settings
     */
    private function init_settings(){
        return $getdata = CsQuery::Cs_Get_Option(array(
            'option_name' => "aios_title_meta_default",
            'json' => true,        
            'json_array' => true
        ));
    }
    
    /**
     * Get Tab Types
     * 
     * @param type $tab
     * @return boolean|string
     */
    private function get_tab_type( $current_tab ){
        if( empty($current_tab)) return false;
        
        if(strpos( $current_tab, 'Robots') !== false){
            return 'Robots';
        }
        else if(strpos( $current_tab, 'Meta') !== false){
            return 'Meta';
        }
        else if(strpos( $current_tab, 'Webmaster') !== false){
            return 'SearchEngine';
        }
        else if(strpos( $current_tab, 'Seo') !== false){
            return 'Seo';
        }
    }
    
    
}
