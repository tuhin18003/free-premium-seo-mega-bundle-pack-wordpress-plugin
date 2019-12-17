<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\BacklinkChecker;
/**
 * Add New Backlinks
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getPropertyData as getPropertyData;
use CsSeoMegaBundlePack\Models\BacklinkManager\getBacklinkData as getBacklinkData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class FindBackLinksMatrix{
    
    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
    public function __construct( $http ) {
        $this->http = $http;
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb;
        $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id, group_name',
            'from' => $wpdb->prefix . 'aios_blmanager_item_groups',
            'where' => 'group_type = 2'
        ));
//       $CommonText = new CommonText();
       $getPropertyData = new getPropertyData();
       $compare_data = array(
               'compare_by_date' => false
            );
       $getProperties = $getPropertyData->getProperty( $compare_data );
       $getBacklinkData = new getBacklinkData();
       
       $data = array_merge( CommonText::form_element( 'backlinks_findmatrix', 'aios-add-new-backlink' ), array(
//           'CommonText' => $CommonText->common_text(),
           'CommonText' => $common_text,
//           'current_tab' => __( 'Find Backlink\'s Matrix', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Find Backlink\'s Matrix', SR_TEXTDOMAIN ),
           'panel_title' =>  __( 'Backlink\'s Matrix Finder', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Add new backlink to find out it\'s matrix', SR_TEXTDOMAIN ),
           'label_backlink_to' => __( 'Backlink To', SR_TEXTDOMAIN ),
           'des_backlink_to' => __( 'Please Select your website, which backlink information you want to find.', SR_TEXTDOMAIN ),
           'label_backlink_from' => __( 'Backlink From', SR_TEXTDOMAIN ),
           'des_backlink_from' => __( 'Please enter the specific website or webpage url where the backlink added.', SR_TEXTDOMAIN ),
           'placeholder_add_new_backlink' => __( 'Please enter a website url. Example: http://example.com', SR_TEXTDOMAIN ),
           'label_automatic_backlink' => __( 'Automatically Update Backlink\'s Matrix', SR_TEXTDOMAIN ),
           'des_auto_update_status' => __( 'Check this box to update backlink\'s matrix automatically from the next time.', SR_TEXTDOMAIN ),
           'label_backlink_price' => __( 'Price', SR_TEXTDOMAIN ),
           'placeholder_backlink_price' => __( 'Enter price for this backlink', SR_TEXTDOMAIN ),
           'label_backlink_monthly_price' => __( 'Monthly Price', SR_TEXTDOMAIN ),
           'placeholder_backlink_monthly_price' => __( 'Enter monthly price', SR_TEXTDOMAIN ),
           'label_add_new_category' => __( 'Select a Group', SR_TEXTDOMAIN ),
           'select_group' => __( 'Select / Create Backlink Group', SR_TEXTDOMAIN ),
           'des_category' => __( 'Please select / create backlink group to store this backlink information groupwise.', SR_TEXTDOMAIN ),
           'select_create_new_cat' => __( 'Create New Backlink Group', SR_TEXTDOMAIN ),
           'placeholder_add_new_category' => __( 'Enter new category name', SR_TEXTDOMAIN ),
           'groups' => $groups,
           'label_new_cat_name' => __( 'New group name', SR_TEXTDOMAIN ),
           'des_new_cat_name' => __( 'Please enter new group name', SR_TEXTDOMAIN ),
           
           'label_submit_btn' => __( 'Find SERP', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Basic Hints', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'To find out your backlinks SERP fillup the following information correctly.' , SR_TEXTDOMAIN ),
               __( 'Once you add a backlink, you can track it automatically next time.' , SR_TEXTDOMAIN ),
           ),
           'panel_subtitle' => __( '*Before adding a new website please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'How many backlink i can add?' => 'There is no limitation',
               'Does the system will automatically check backlinks matrix?' => 'Yes! But you need to check on automatic update.'
           ),
           'properties' => $getProperties,
           'add_new_property_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AddNewProperty'),
           'add_new_property_btn' => __( 'Add New Property', SR_TEXTDOMAIN ),
           'no_property_found' => __( 'No Property Added. Please add property before add a backlink.', SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_FindMatrix_script']);
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/backlinkChecker/FindBacklinksMatrix.twig', $data );
    }
    
    function _FindMatrix_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            var _obj = {
                                $: $,
                                form_reset: true
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( "#backlink_finder_form" );
                                
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