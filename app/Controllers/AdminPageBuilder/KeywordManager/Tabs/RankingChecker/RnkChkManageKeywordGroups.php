<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\RankingChecker;
/**
 * Manage Keyword Groups
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class RnkChkManageKeywordGroups {
    
    public function load_page_builder( $common_text ){
       $data = array_merge( CommonText::form_element( 'add_groups', 'Aios-manage-keywords-groups'), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Keyword Groups', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Add / Manage Your Keywords Groups', SR_TEXTDOMAIN ),
           'inputs' => $this->get_input_fields(),
           'tbl_headers' => array(
                   __( 'Group Name', SR_TEXTDOMAIN ),
                   __( 'Keyword Count', SR_TEXTDOMAIN ),
                   __( 'Description', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $this->get_groups(),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'filter_actn_btns' => __( 'Filter by Category', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_add_new_cat_name' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'placeholder_add_new_category' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
           'label_add_new_cat_des' => __( 'Enter Group Description', SR_TEXTDOMAIN ),
           'placeholder_add_new_des' => __( 'Enter New Group Description', SR_TEXTDOMAIN ),
           'add_new_group_box_title' => __( 'Create New Group', SR_TEXTDOMAIN ),
           'label_create_new_cat_btn' => __( 'Create Now', SR_TEXTDOMAIN ),
           'filter_label_deactivate' => __( 'Deactivated Rules', SR_TEXTDOMAIN ),
           'manage_link_url' => $common_text['base_url'].'&tab=RnkChkManageKeywords',
       ));
       
       add_action( 'admin_footer', array( $this, '_scriptManageKeywordGroups') );
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/RankingChecker/ManageKeywordGroups.twig', $data );
    }
    
    public function _scriptManageKeywordGroups(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            
                            //create new group
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#new_group_form" );
                            
                            //delete group
                            var _obj = {
                                $: $,
                                data_section_id: '#demo-custom-toolbar',
                                row_id : "#item_id_",
                                action_type: 'delete'
                            };
                            var action_handler = new Aios_Action_Handler( _obj );
                            action_handler.setup( "#btn_delete" ); 
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * 
     */
    private function get_groups(){
        $data = CsQuery::Cs_Get_Results(array(
            'select' => 'g.*, count(k.id) as total_keyword',
            'from' => array( 'g' => 'groups'),
            'join' => array(
                array( array( 'd' => 'domains'), 'd.url_group_id = g.id', 'LEFT'),
                array( array( 'k' => 'keywords'), ' k.domain_id = d.id', 'LEFT'),
            ),
            'where' => array( 'g.type' => 5 ),
            'group_by' => 'g.name',
            'query_var' => 101
        ));
        return empty( $data ) ? '' : $data->rows;
    }

        /**
     * Input fields
     * 
     * @since 1.0.0
     * @return array
     */
    private function get_input_fields(){
        return array(
                'new_cat_name' => array(
                    'label' => __( 'Enter New Group Name*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter New Group Name', SR_TEXTDOMAIN ),
                    'help_text' => __( "Please enter a group name. Example: premium keyword", SR_TEXTDOMAIN ),
                    'input_type' => 'text',
                    'type' => 'input',
                    'required' => true
                ),
                'new_cat_des' => array(
                    'label' => __( 'Enter Group Description', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter New Group Description', SR_TEXTDOMAIN ),
                    'help_text' => __( "Group Description to remember anything.", SR_TEXTDOMAIN ),
                    'type' => 'textarea'
                ),
            );
    }
    
}
