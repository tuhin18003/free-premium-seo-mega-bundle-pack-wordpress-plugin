<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Tabs\RankingChecker;
/**
 * Google Site Maps
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Models\KeywordManager\KeywordQuery\KeywordQuery;

class RnkChkManageKeywords {
    
    protected $http;
    private $rows;
    private $groups;
    private $first_day;
    private $last_day;
    private $filter_msg_title;
    private $filter_msg_subtitle;
    private $data;


    public function __construct( $Http ) {
        $this->http = $Http;
        //get groups
        $this->get_groups();
        
        // get the selected keywords
        $this->get_listed_keywords();
        
    }
    
    public function load_page_builder( $common_text ){
        
        $this->data = array_merge( CommonText::form_element( 'export_keywords', 'aios-manage-all-keywords' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Keyword Ranking Monitor', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Lets you monitor keywords ranking, check instantly or automatically.', SR_TEXTDOMAIN ),
            
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'actn_btn_export' =>  __( 'Export', SR_TEXTDOMAIN ),
           'groups' => $this->groups->rows,
           'label_filter_btn' =>  __( 'Filters', SR_TEXTDOMAIN ),
           'label_actn_btn_compare' =>  __( 'Compare', SR_TEXTDOMAIN ),
           'label_actn_btn_compare_by_date' =>  __( 'Compare / Detail by Date', SR_TEXTDOMAIN ),
           'label_actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'label_actn_btns' =>  __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_btn_compare_by_date' =>  __( 'Compare / Detail by Date', SR_TEXTDOMAIN ),
            'actn_btn_update' =>  __( 'Instantly Update', SR_TEXTDOMAIN ),
           'actn_btn_set_auto_update' =>  __( 'Monitor Keyword(s)', SR_TEXTDOMAIN ),
           'actn_btn_remove_auto_update' =>  __( 'Don\'t Monitor Keyword(s)', SR_TEXTDOMAIN ),
           'auto_update' =>  __( 'Auto Update', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
               __( 'Keyword', SR_TEXTDOMAIN ),
               __( 'Position', SR_TEXTDOMAIN ),
               __( 'Position Changed', SR_TEXTDOMAIN ),
               __( 'Domain', SR_TEXTDOMAIN ),
               __( 'Last Updated', SR_TEXTDOMAIN ),
           ),
           'tbl_rows' => isset($this->rows->rows) ? $this->rows->rows : '',
            'domain' =>  __( 'Domain:', SR_TEXTDOMAIN ),
            'link_to' =>  __( 'Keyword From:', SR_TEXTDOMAIN ),
            'lable_row_monitoring' =>  __( 'This keyword is monitoring automaticlly.', SR_TEXTDOMAIN ),
            'filter_title' => $this->filter_msg_title,
            'filter_subtitle' => $this->filter_msg_subtitle,
            'label_back_to_btn' => __( 'Back to All', SR_TEXTDOMAIN ),
            'no_item_found' => __( 'No Item Found!', SR_TEXTDOMAIN ),
       ));
       
       if( $this->http->has( 'compare_by_date' ) ){
            add_action( 'admin_footer', array( $this, '_compare_date_script') );
            return view( '@CsSeoMegaBundlePack/KeywordManager/tabs/RankingChecker/CompareByDate.twig', $this->data );
       }else{
           add_action('admin_footer', [$this, '_addFooter_script']);
           return  view( '@CsSeoMegaBundlePack/KeywordManager/tabs/RankingChecker/ManageKeywords.twig', $this->data );
       } 
    }
    
    /**
     * Page script
     */
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
                            
                            //status change
                            delete _obj.action_type;
                            var newObj = Object.assign({
                                action_type: 'update_settings',
                                swal_confirm_text: '<?php echo __( 'Keyword ranking will be monitor automatically.', SR_TEXTDOMAIN ); ?>',
                                swal_confirm_btn_text: '<?php echo __( 'Yep! Do It!', SR_TEXTDOMAIN ); ?>',
                                redirect: window.location.href
                            }, _obj);
                            var action_handler = new Aios_Action_Handler( newObj );
                            action_handler.setup( "#set_auto_update" ); 
                            
                            var newObj = Object.assign({
                                action_type: 'update_settings',
                                swal_confirm_text: '<?php echo __( 'Keyword will be no longer monitor automatically.', SR_TEXTDOMAIN ); ?>',
                                swal_confirm_btn_text: '<?php echo __( 'Yep! Do It!', SR_TEXTDOMAIN ); ?>'
                            }, _obj);
                            var action_handler = new Aios_Action_Handler( newObj );
                            action_handler.setup( "#remove_auto_update" ); 

                           var ARDP = new Aios_Redirect_Detail_Page({$:$, table_id: '#demo-custom-toolbar', current_location: window.location.href});
                           ARDP.setup("#btn_compare_date");
                            
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
    public function _compare_date_script(){
//        $this->get_compared_data();
        ?>
            <script type="text/javascript">
                /********************overview********************/
                var LineChartData = [<?php $r=0; foreach($this->get_compared_data() as $label => $value) { if($r>0) {echo ",";} ?> 
                        <?php echo "{pointInterval:   24 * 3600 * 1000, name:'{$label}', data:[ {$value} ] }";   ?>
                    <?php $r++; } ?>];        

                var _obj = {
                    graph_load_in: 'graph_keywords_position',
                    title_text: '<?php _e( 'Keyword Ranking Monitor', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: Google', SR_TEXTDOMAIN )?>',
                    reverse_y_axis: true,
                    y_axis_text: '<?php _e( 'Keyword Rank', SR_TEXTDOMAIN )?>',
                    data: LineChartData
                };
                AiosNasChartsLine = new Aios_NasCharts_Line( _obj );
                AiosNasChartsLine.init();
                /********************overview********************/
                
                //get date
                (function($){

                //Date range picker
                $('#reportrange span').html(moment("<?php echo date("Ymd",strtotime($this->first_day));?>", "YYYYMMDD").format('MMMM D, YYYY') + ' - ' + moment("<?php echo date("Ymd",strtotime($this->last_day));?>", "YYYYMMDD").format('MMMM D, YYYY'));
                $('#reportrange').daterangepicker({
                    format: 'MM/DD/YYYY',
                    startDate: moment("<?php echo date("Ymd",strtotime($this->first_day));?>", "YYYYMMDD"),
                    endDate: moment("<?php echo date("Ymd",strtotime($this->last_day));?>", "YYYYMMDD"),
                    minDate: '01/01/2015',
                    maxDate: moment(),
                    dateLimit: {
                        months: 12
                    },
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                        'Last 6 Months': [moment().subtract(6, 'months'), moment()],
                        'Last 12 Months': [moment().subtract(12, 'months'), moment()],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    opens: 'left',
                    drops: 'down',
                    buttonClasses: ['btn', 'btn-sm'],
                    applyClass: 'btn-default',
                    cancelClass: 'btn-white',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Submit',
                        cancelLabel: 'Cancel',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                }, function( start, end, label ){
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                    var $date_range = start.format('M/D/YYYY') + ' - ' + end.format('M/D/YYYY');
                    var $keyword_ids = '<?php echo $this->http->has( 'item_ids' ) ? "&item_ids=".$this->http->get( 'item_ids' ) : ''; ?>';
                    var $compare_by_date = '<?php echo $this->http->has( 'compare_by_date' ) ? "&compare_by_date=".$this->http->get( 'compare_by_date' ) : ''; ?>';
                    window.location ='<?php echo isset($this->data['CommonText']) ? $this->data['CommonText']['current_url'] : ''; ?>&date_range='+$date_range+$keyword_ids+$compare_by_date;
                });
            })(jQuery);
            
            </script>
        <?php
    }

        /**
     * Get selected Keywrods
     * 
     * @since 1.0.0
     * @return array Description
     */
    private function get_listed_keywords(){
        $condition = array();
        if( $this->http->has( 'group_filter' )){
            $filter_by_group_id = GeneralHelpers::Cs_Clean_Data( $this->http->get( 'group_filter', false ));
            $condition = array( 'group_id' => $filter_by_group_id);
        }   
        else if( $this->http->has( 'compare_by_date' )){
            date_default_timezone_set(get_option('timezone_string'));
            if( $this->http->has( 'date_range' )){
                $date_range = explode( '-', $this->http->get( 'date_range', false));
                $this->first_day = date('Y-m-d', strtotime($date_range[0]));
                $this->last_day = date('Y-m-d', strtotime($date_range[1]));
            }else{
                $this->first_day = date('Y-m-d', strtotime('2 weeks ago'));
                $this->last_day = date('Y-m-d', strtotime( '+2 days'));
            }
            $keyword_ids = GeneralHelpers::Cs_Clean_Data( $this->http->get( 'item_ids', false ) );
            $condition = array(
                'first_date' => $this->first_day,
                'last_date' => $this->last_day,
                'k_id' => $keyword_ids
            );
        }   
        
        
        $this->rows = KeywordQuery::get_Key_Words( $condition );
        
        if( isset( $filter_by_group_id ) ){
            $this->filter_msg_title =  __( 'Filtered by group', SR_TEXTDOMAIN );
            $this->filter_msg_subtitle = __( 'Total: <b class="text-white">'. (isset( $this->rows->num_rows ) ? $this->rows->num_rows : 0) .'</b> properties found by group : <b class="text-white">' . $this->get_group_name_id( $filter_by_group_id ) .'</b>', SR_TEXTDOMAIN );
        }
        
    }
    
    /**
     * Get Groups
     * 
     * @return type
     */
    private function get_groups(){
        $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id, name',
            'from' => 'groups',
            'where' => array( 'type' => 5),
            'query_var' => 101
        ));
        $this->groups = isset( $groups->rows ) ? $groups : '';
    }
    
    /**
     * Get group name
     * 
     * @param type $group_id
     * @return type
     */
    private function get_group_name_id( $group_id ){
        $group_name = '';
        if( isset($this->groups->rows ) ){
            foreach($this->groups->rows as $group){
                if( $group->id === $group_id ){
                    $group_name = $group->name; break;
                }
            }
        }
        return $group_name;
    }
    
    private function get_compared_data(){
        $compare_data = array();
        if( isset($this->rows->rows )){
            foreach( $this->rows->rows as $row){
                $update_date = date( 'Y-m-d', strtotime( $row->created_on ));
                $date = mktime( 0,0,0, date('n', strtotime($update_date)), date('j', strtotime($update_date)), date('Y', strtotime($update_date)) ) * 1000;
                $keyword = trim($row->keyword);
                if( ! array_key_exists( $keyword, $compare_data ) ){
                    $compare_data = array_merge( $compare_data, array(
                        $keyword => "[ {$date}, {$row->current_position} ]"
                    ));
                }else{
                    $compare_data[ $keyword ] =  $compare_data[ $keyword ] .', '."[ {$date}, {$row->current_position} ]";
                }
            }
        }
        return $compare_data;
    }
    
}
