<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Site Maps
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Models\SocialAnalytics\GoogleActionHandler;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleCrawlerError {
    
    protected $http;
    private $internet_status;
    
    public function __construct( $Http ) {
        $this->http = $Http;
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb, $AiosGooAppToken;
        $where = []; $filter_data= [];
        if($this->http->has('filter_cat')){
            $filter_cat = $this->http->get('filter_cat', false);
            $where = array('where' => "category = '" . $filter_cat . "'");
            $filter_data = array(
                'filter_title' =>  __( 'Filters Result', SR_TEXTDOMAIN ),
                'filter_subtitle' => __( 'Total: %1$s %3$s %2$s crawler errors found by category : %1$s %4$s %2$s', SR_TEXTDOMAIN )
            );
        }
        
        //check internet connection
        if( GeneralHelpers::check_internet_status() === false ){
            $this->internet_status = __( 'Your internet connection has down!', SR_TEXTDOMAIN ); 
        }else{
            $GAH = new GoogleActionHandler( $this->http );
            // Check errors
            $GAH->crawler_errors_instant_check();
        }
        
        
        $rows = CsQuery::Cs_Get_Results(array_merge(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_crawler_errors',
            'order_by' => 'first_seen DESC',
            'num_rows' => true,
        ),$where));
        
        if($this->http->has('filter_cat')){
            $filter_data['filter_subtitle'] = sprintf( $filter_data['filter_subtitle'], '<b>', '</b>', isset($rows->num_rows) ? $rows->num_rows : 0, $filter_cat );
        }
        
        // check for token
        $data = array_merge( CommonText::form_element( 'crawler_manage', 'aios-crawler-errors' ), $filter_data, array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Google Crawler Errors', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you manage Google crawler errors remotely from this section.', SR_TEXTDOMAIN ),
           'actn_fix_btn' =>  __( 'Mark as fixed', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'URL', SR_TEXTDOMAIN ),
               __( 'Type', SR_TEXTDOMAIN ),
               __( 'Linked From', SR_TEXTDOMAIN ),
               __( 'Detected', SR_TEXTDOMAIN )
           ),
           'tbl_rows' => $rows->rows,
           'label_filter_platform' =>  __( 'Platform', SR_TEXTDOMAIN ),
           'platforms' => empty($this->internet_status) ? $GAH->platform() : '',
           'label_filter_cat' =>  __( 'Filter by', SR_TEXTDOMAIN ),
            'categories' => empty($this->internet_status) ? $GAH->category() : '',
           'site_url' => empty($this->internet_status) ? $GAH->site_url : '',
           'label_back_to_btn' =>  __( 'Back To All', SR_TEXTDOMAIN ),
            'aios_goo_app_token' => $AiosGooAppToken,
            'no_internet' => $this->internet_status
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/Google/GoogleCrawlerError.twig', $data );
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
                                swal_confirm_text: '<?php _e( 'Do you want to mark selected urls as fixed?', SR_TEXTDOMAIN ); ?>',
                                swal_confirm_btn_text: '<?php _e( 'Yes! Mark as Fixed', SR_TEXTDOMAIN ); ?>',
                                redirect: window.location.href
                            };
                            var action_handler = new Aios_Action_Handler( _obj );
                            action_handler.setup( "#btn_delete" ); 
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    
}
