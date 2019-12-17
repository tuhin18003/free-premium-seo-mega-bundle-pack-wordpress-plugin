<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\KeywordResearch;
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

class KresKeywordSuggestions {
    
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
        $this->data = array_merge( CommonText::form_element( 'save_keyword_suggestion', 'aios-save-keyword-suggestion' ), array(
           'CommonText' => $common_text,
           'webs' => ConstantText::get_instance()->property_groups( 'keyword_sugg_group' ) [ 'keyword_sugg_group' ],
           'is_inet_down' => GeneralHelpers::check_internet_status(),
           'page_title' =>  __( 'Keyword Suggestions', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Generate long tail keywords from popular website\'s.', SR_TEXTDOMAIN ),
           'label_add_new_keyword' => __( 'Enter keyword(s)*', SR_TEXTDOMAIN ),
           'placeholder_search' => __( 'Type a keyword', SR_TEXTDOMAIN ),
           'label_search_results' => __( 'Kewyword Results', SR_TEXTDOMAIN ),
           'kr_panel_title' => __( 'Kewyword Results', SR_TEXTDOMAIN ),
           'kr_before_submit' => __( 'Please enter your desired keyword and hit the button.', SR_TEXTDOMAIN ),
           'label_btn_gen_keyword' => __( 'Generate Keywords', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'label_stop_btn' => __( 'Stop', SR_TEXTDOMAIN ),
           'label_btn_save' => __( 'Save Suggestions', SR_TEXTDOMAIN ),
           'label_btn_export' => __( 'Export', SR_TEXTDOMAIN ),
           'no_keyword_found' => __( 'No Keyword Found Yet!', SR_TEXTDOMAIN ),
           'check_all' => __( 'Check All', SR_TEXTDOMAIN ),
           'hints_title'=> __( 'Some Useful Information', SR_TEXTDOMAIN ),
           'app_create_hints' => array(
               __( 'To stop suggestion or for new keyword search please click the stop button & try again by Generate Keywords button.', SR_TEXTDOMAIN ),
               __( 'Select keywords and save it to use it later.', SR_TEXTDOMAIN ),
               __( 'You can use these keywords to re-write / spin article.', SR_TEXTDOMAIN ),
           ),
           'export_url' =>  $common_text['base_url'] .'&action=export_keywords',
           'error_title' => __( 'Nothing Selected!!', SR_TEXTDOMAIN ),
           'error_text' => __( 'You need to select at least one keyword to perform this action!', SR_TEXTDOMAIN ),
           'search_finished' => __( 'Search Finished! All possible keyword has been searched!', SR_TEXTDOMAIN ),
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/KeywordResearch/SearchKeywords.twig', $this->data );
    }
    
    function _addFooter_script(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            //save keyword
                            $(".btn-submit").on( "click", function(){
                                var _obj = {
                                    $: $,
                                    form_reset: true,
                                    additional_data: {
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
                              
                        } );
                } )( jQuery );
            </script>
        <?php
    }
}
