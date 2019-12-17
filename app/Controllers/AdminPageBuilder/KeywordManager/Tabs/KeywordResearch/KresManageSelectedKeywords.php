<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\KeywordResearch;
/**
 * Google Site Maps
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\Backend\Messages\ConstantText;
use CsSeoMegaBundlePack\Helper;

class KresManageSelectedKeywords {
    
    protected $http;
    private $rows;
    
    public function __construct( $Http ) {
        $this->http = $Http;
        
        // get the selected keywords
        $this->get_selected_keywords();
    }
    
    public function load_page_builder( $common_text ){
        
        $data = array_merge( CommonText::form_element( 'export_keywords', 'aios-selected-keywords' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'All Selected Keywords', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you mange your all selected keywords.', SR_TEXTDOMAIN ),
            
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'actn_btn_export' =>  __( 'Export', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'Keyword', SR_TEXTDOMAIN ),
               __( 'Keyword Channel', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => $this->rows,
       ));
        
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/KeywordResearch/ManageSelectedKeywords.twig', $data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            //delete group
                            var _obj = {
                                $: $,
                                data_section_id: '#demo-custom-toolbar',
                                row_id : "#item_id_",
                                action_type: 'delete',
                                redirect: window.location.href
                            };
                            var action_handler = new Aios_Action_Handler( _obj );
                            action_handler.setup( "#btn_delete" ); 
                            
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Get selected Keywrods
     * 
     * @since 1.0.0
     * @return array Description
     */
    private function get_selected_keywords(){
        if( ($groups = ConstantText::get_instance()->property_groups( 'keyword_sugg_group' ) [ 'keyword_sugg_group' ]) !== false ){
            foreach( $groups as $group){
                $keywords = CsQuery::Cs_Get_Option(array(
                    'option_name' => Helper::get( 'PLUGIN_DATA_PREFIX' ) . "keyword_sugg_{$group}",
                    'json' => true,
                    'json_array' => true
                ));
                if( is_array( $keywords ) ){
                    foreach( $keywords as $keyword ){
                        $this->rows[] = array(
                            'keyword' => $keyword, 'group' =>  $group
                        );
                    }
                }    
            }
            return empty($this->rows) ? false : asort($this->rows);
        }        
        return false;
    }
    
}
