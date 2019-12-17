<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\MyWebsite;
/**
 * Add New Property
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class MwsAddNewProperty {
    
    public function load_page_builder( $common_text ){
        global $wpdb;
        $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id, group_name',
            'from' => $wpdb->prefix . 'aios_blmanager_item_groups',
            'where' => 'group_type = 1'
        ));
       $data = array_merge( (new CommonText())->form_element( 'aios-add-new-property', 'property_add' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Add New Website', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Add website you owned or add website you want to compare.', SR_TEXTDOMAIN ),
           'label_add_new_property' => __( 'Add A Website URL', SR_TEXTDOMAIN ),
           'placeholder_add_new_property' => __( 'Please enter a new website url. Example: http://example.com', SR_TEXTDOMAIN ),
           'label_automatic_update' => __( 'Next Time Automatically Update SERP', SR_TEXTDOMAIN ),
           'label_automatic_backlink' => __( 'Automatically Find Backlinks', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'label_add_new_group' => __( 'Select / Create Group', SR_TEXTDOMAIN ),
           'placeholder_add_new_group' => __( 'Please enter new group name', SR_TEXTDOMAIN ),
           'select_group' => __( 'Select Group', SR_TEXTDOMAIN ),
           'select_create_new_group' => __( 'Create New Group', SR_TEXTDOMAIN ),
           'groups' => $groups,
           
           'panel_subtitle' => __( '*Before adding a new website please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'What is this?' => 'This is the process to get your website SERP. Once you add your website, you can set to automatic SEO SERP update.'
           ),
       ));
       add_action('admin_footer', [$this, '_addNewProperty_script']);
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/AddNewProperty.twig', $data );
    }
    
    function _addNewProperty_script(){
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
                            form_general_options.setup( ".card-box" );
                                
                            //keyword group
                            jQuery("#group").on("change",function(){
                                if( jQuery(this).val() === 'new'){
                                    jQuery(".create_new_group_field").addClass("animated fadeInDown").css('display','block');
                                    jQuery("#new_group_name").attr( 'required', 'required' ).removeAttr('disabled');
                                }else{
                                    jQuery(".create_new_group_field").removeClass("animated fadeInDown").css('display','none');
                                    jQuery("#new_group_name").removeAttr('required').attr('disabled','disabled');
                                }
                            });
                        } );
                } )( jQuery );
            </script>
        <?php
    }
}
