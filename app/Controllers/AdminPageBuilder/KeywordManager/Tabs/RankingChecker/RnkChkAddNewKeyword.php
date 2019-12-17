<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\RankingChecker;
/**
 * Add New Keywords
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class RnkChkAddNewKeyword {
    
    
    public function load_page_builder( $common_text ){
       $data = array_merge( CommonText::form_element( 'keyword_add', 'aios-add-new-keywords' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Keyword Ranking Checker', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Let\'t you monitor your keywords or your compititor keywords', SR_TEXTDOMAIN ),
           'inputs' => $this->get_input_fields(),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'placeholder_add_new_group' => __( 'Please enter new group name', SR_TEXTDOMAIN ),
           'select_group' => __( 'Select Group', SR_TEXTDOMAIN ),
           'select_create_new_group' => __( 'Create New Group', SR_TEXTDOMAIN ),
           'panel_title' => __( 'Keyword Setup', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Keyword Ranking Checker', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'Simply put the keywords and website to check it\'s rank automatically.' , SR_TEXTDOMAIN ),
           ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/RankingChecker/AddNewKeywords.twig', $data );
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
                            form_general_options.setup( "#keywordRankingChecker" );
                                
                            //keyword group
                            jQuery("#group").on("change",function(){
                                if( jQuery(this).val() === 'new'){
                                    jQuery(".input-line-break").show( 'slow' );
                                    jQuery("#new_group_name").show( 'slow' ).attr( 'required', 'required' ).removeAttr('disabled');
                                }else{
                                    jQuery(".input-line-break").hide( 'slow' );
                                    jQuery("#new_group_name").hide( 'slow' ).removeAttr('required').attr('disabled','disabled');
                                }
                            });
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Generate inputs
     * 
     * @global type $wpdb
     * @return type
     */
    private function get_input_fields(){
        global $wpdb;
        $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id, name',
            'from' => 'groups',
            'where' => array( 'type' => 5),
            'query_var' => 101
        ));
        $existing_group = array();
        
        if($groups){
            foreach($groups->rows as $group){
                $existing_group = array_merge( $existing_group, array(
                    $group->id => $group->name
                ));
            }
        }
        
        return array(
                'new_keywords' => array(
                    'label' => __( 'Enter Keyword(s)*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter single keyword or comma seperated multiple keywords', SR_TEXTDOMAIN ),
                    'help_text' => __( "Enter single keyword or comma seperated multiple keywords", SR_TEXTDOMAIN ),
                    'type' => 'textarea',
                    'required' => true
                ),
                'keyword_domain' => array(
                    'label' => __( 'Add A Website URL*', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Enter A Website URL', SR_TEXTDOMAIN ),
                    'help_text' => __( "Please enter a website url. Example: http://example.com", SR_TEXTDOMAIN ),
                    'input_type' => 'url',
                    'type' => 'input',
                    'required' => true
                ),
                'group' => array(
                    'label' => __( 'Select / Create Group', SR_TEXTDOMAIN ),
                    'placeholder' => __( 'Select / Create Group', SR_TEXTDOMAIN ),
                    'help_text' => __( 'Create a group or select from existing group.', SR_TEXTDOMAIN ),
                    'options' => array(
                        'group' => array(
                            'sub_options' => array_merge(array(
                                    '[None]' => __( '========== None ==========', SR_TEXTDOMAIN ),
                                    'new' => __( 'Create New Group', SR_TEXTDOMAIN ),
                                ),$existing_group ),
                            'class' => 'form-control',
                            'type' => 'select',
                        ),
                        'new_group_name' => array(
                            'line_break' => true,
                            'line_break_class' => 'display-none',
                            'placeholder' => __( 'Please enter new group name', SR_TEXTDOMAIN ),
                            'class' => 'form-control display-none',
                            'input_type' => 'text',
                            'type' => 'input'
                        ),
                        
                    ),
                    'type' => 'miscellaneous',
                ),
                'auto_update_status' => array(
                    'label' => __( 'Automatically Update', SR_TEXTDOMAIN ),
                    'help_text' => __( 'Check this box to update ranking time to time.', SR_TEXTDOMAIN ),
                    'type' => 'checkbox'
                ),
        );
    }
}
