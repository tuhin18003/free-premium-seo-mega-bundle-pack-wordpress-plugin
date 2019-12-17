<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\EasyTags;
/**
 * Search New Keywords
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\Backend\Messages\ConstantText;

class BulkTags {
    
    /**
     * Hold superglobal variables
     *
     * @var type 
     */
    protected $http;

    /**
     * hold data
     *
     * @var type 
     */
    private $data;


    /**
     * Construct function
     * 
     * @param type $Http
     */
    public function __construct( $Http ) {
        $this->http = $Http;
        
        //init settings
//        $this->init_settings();
        
    }
    
    /**
     * Load Page
     * 
     * @global type $wpdb
     * @return type
     */
    public function load_page_builder( $common_text ){
        $this->data = array_merge( CommonText::form_element( 'add_tags', 'add-remove-custom-tags' ), array(
           'CommonText' => $common_text,
           'webs' => ConstantText::get_instance()->property_groups( 'keyword_sugg_group' ) [ 'keyword_sugg_group' ],
           'is_inet_down' => GeneralHelpers::check_internet_status(),
           'page_title' =>  __( "Easy Tag Adder", SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Easily add tags to your post, pages, products from one place', SR_TEXTDOMAIN ),
           'panel_title_type_selector' =>  __( 'Basic Settings', SR_TEXTDOMAIN ),
           'inputs' => $this->get_inputs(),
           'label_add_new_keyword' => __( 'Enter keyword(s)*', SR_TEXTDOMAIN ),
           'placeholder_search' => __( 'Type a keyword', SR_TEXTDOMAIN ),
           'label_search_results' => __( 'Tags Results', SR_TEXTDOMAIN ),
           'kr_panel_title' => __( 'Tags Results', SR_TEXTDOMAIN ),
           'kr_before_submit' => __( 'Please enter your desired keyword and hit the button.', SR_TEXTDOMAIN ),
           'label_btn_gen_keyword' => __( 'Generate Tags', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'label_stop_btn' => __( 'Stop', SR_TEXTDOMAIN ),
           'label_btn_save' => __( 'Save Tags', SR_TEXTDOMAIN ),
           'label_btn_export' => __( 'Export', SR_TEXTDOMAIN ),
           'no_keyword_found' => __( 'No Keyword Found Yet!', SR_TEXTDOMAIN ),
           'check_all' => __( 'Check All', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Basic Hints:', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'Please select your post from the list.', SR_TEXTDOMAIN ),
               __( 'Choose If you want to remove your old tags or add new tags beside your old tags.', SR_TEXTDOMAIN ),
               __( 'Enter a keyword related to your post.', SR_TEXTDOMAIN ),
               __( 'When your tags generation complete click the hit stop button and choose your tags.', SR_TEXTDOMAIN ),
               __( 'Then hit the add tags button from bottom of the page to add your tags', SR_TEXTDOMAIN ),
           ),
           'app_create_hints_2nd' => array(
               __( 'To stop suggestion or for new tag search please click the stop button & try again by Generate Keywords button.', SR_TEXTDOMAIN )
           ),
           'error_title' => __( 'Nothing Selected!!', SR_TEXTDOMAIN ),
           'error_text' => __( 'You need to select at least one keyword to perform this action!', SR_TEXTDOMAIN ),
           'error_global_title' => __( 'Error!!', SR_TEXTDOMAIN ),
           'error_type_text' => __( 'Type Not Selected!!', SR_TEXTDOMAIN ),
           'error_item_type_text' => __( 'Type Not Selected!!', SR_TEXTDOMAIN ),
           'search_finished' => __( 'Search Finished! All possible keyword has been searched!', SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/EasyTags/BulkTags.twig', $this->data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            
                            //save keyword
                            $( ".btn-submit" ).on( "click", function(){
                                var _obj = {
                                    $               : $,
                                    form_reset      : true,
                                    additional_data : {
                                        name: 'save_data',
                                        value: 'yes'
                                    },
                                    destroy_after_success: true
                                };
                                
                                var form_general_options = new Aios_Form_Handler( _obj );
                                form_general_options.setup( ".portlet-body" );
                            });
                            
                            //normal form submit
                             $( "form" ).on( "submit", function() {
                                var item_types = $('#types').val();
                                if( item_types == 0 || item_types.length === 0 ){
                                    swal( '<?php echo $this->data['error_global_title']; ?>', '<?php echo $this->data['error_type_text']; ?>', 'error' );
                                    return false; 
                                }
                              $("#item_types").val( item_types );
                                var item_id = $('#item_id').val();
                                if( item_id == 0 || item_id.length === 0 ){
                                    var item_type = $("label span.label_all_items").text();
                                    swal( '<?php echo $this->data['error_global_title']; ?>', '<?php echo $this->data['error_item_type_text']; ?>', 'error' );
                                    return false; 
                                }
                                $("#item_types_id").val( item_id );
                                var old_tags = [];
                                $('[name="old_tags[]"]:checked').each(function( i, e){
                                    old_tags +=  $(this).val() + '_';
                                });
                                $("#old_tags_remove").val( old_tags );
                              
                                var has_empty = true;
                                $(this).find( 'input[type="checkbox"]' ).each(function () {
                                    if ( $(this).is(":checked") ) {
                                        has_empty = false; 
                                    }
                                });
                                if ( has_empty ) { 
                                    swal( '<?php echo $this->data['error_title']; ?>', '<?php echo $this->data['error_text']; ?>', 'error' );
                                    return false; 
                                }
                            });
                            
                            
                            //search keyword
                            var _obj = {
                              $ : $,
                              startSearch: true,
                              searchTypes: '.type_selection',
                              query: '#search',
                              btnStart: '.btn-generate-keywords',
                              btnStop: '.btn-stop',
                              beforeResultsContainer : '#beforeResult',
                              resultsContainer : '.KeySuggList',
                              defaultResultNotice: '.kr-alert-before-submit',
                              counter : '.count_',
                              progress : '.progress',
                              checkAll : '.check-all',
                              searchFinishedNotice: '.search-finished'
                            };
                            var AKS = new Aios_Keyword_Suggestion( _obj );
                            AKS.init();
                              
                           $("#types").on( 'change', function(){
                                var $type = $(this).val();
                                if( $type.length === 0 ){
                                    return;
                                }
                                $(".all_items").html( 'Please wait a while... Getting your all '+ $type +'.' ).slideDown('slow');
                                var data = {
                                    action: 'get_items', 
                                    type : $type
                                };

                                $.post( AIOS.base_url, data, function( res ){
                                    $(".label_all_items").text( data.type );
                                    $(".all_items").html( res ).slideDown('slow');
                                });
                           });   
                           
                           $("body").on( 'change', '#item_id', function(){
                               var $_this = $(this);
                               var post_data = {
                                   id: $_this.val(),
                                   type: $('#types').val(),
                                   action: 'get_old_tags'
                               };
                                $(".old_tag_status").html( 'Please wait a while...' ).slideDown('slow');
                               $.post( AIOS.base_url, post_data, function( res ){
                                    $(".old_tag_status").html( res ).slideDown('slow');
                                });
                           });
                              
                        });
                } )( jQuery );
            </script>
        <?php
    }
    
    /**
     * Get Inputs for types
     * 
     * @return type
     */
    private function get_inputs(){
        return array(
                'types' => array(
                    'label' => __( 'Select Type*', SR_TEXTDOMAIN ),
                    'help_text' => __( "Select type to get the all list", SR_TEXTDOMAIN ),
                    'type' => 'select',
                    'options' => array(
                        '0' => __( "=============== Select Type ===============", SR_TEXTDOMAIN ),
                        'post' => __( "Posts", SR_TEXTDOMAIN ),
                        'product' => __( "Products", SR_TEXTDOMAIN ),
                    ),
                    'required' => true
                ),
                'all_items' => array(
                    'label' => sprintf( __( 'Select %s', SR_TEXTDOMAIN ), '<span class="label_all_items"></span>*' ),
                    'help_text' => sprintf(  __( "Please select your %s ", SR_TEXTDOMAIN ) , '<span class="label_all_items"></span>' ),
                    'placeholder_text' => __( "Please select above item to get the list.", SR_TEXTDOMAIN ),
                    'type' => 'input_placeholder'
                ),
                'old_tag_status' => array(
                    'label' => __( 'All Old Tags', SR_TEXTDOMAIN ),
                    'help_text' => __( "if you want to delete old tags, please select from above list.", SR_TEXTDOMAIN ),
                    'placeholder_text' => __( "Old tags list.", SR_TEXTDOMAIN ),
                    'type' => 'input_placeholder'
                )
            );
    }
}
