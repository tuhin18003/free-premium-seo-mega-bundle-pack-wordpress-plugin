<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * All 404 Error
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class VisitorAllClickedLinkCount {
    private $first_day;
    private $last_day;
    private $current_url;
    
    public function load_page_builder(){
        global $wpdb;
       $CommonText = new CommonText();
      
       if( isset( $_GET['date_range']) && !empty($_GET['date_range'])){
            $date_range_raw = wp_kses( $_GET['date_range'], array());
            $date_range = explode('-',$date_range_raw);
            $this->first_day = date('Y-m-d', strtotime($date_range[0]));
            $this->last_day = date('Y-m-d', strtotime($date_range[1]));
       }else{
           $this->first_day = date('Y-m-d', strtotime('5 months ago'));
           $this->last_day = date('Y-m-d');
       }
       
       $data_item = CsQuery::Cs_Get_Results(array(
           'select' => '*',
           'from' => $wpdb->prefix . 'aios_visitor_link_click_count',
           'where' => "last_clicked between '{$this->first_day}' and '{$this->last_day}'",
           'order_by' => 'id desc'
       ));
        $log_count = empty($data_item) ? 0 : count( $data_item );   
           
       $data = array_merge($CommonText->form_element('aios-visitor-clicked-link'), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' =>  __( 'Click Count', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Monitor & Manage All Links Clicked By Your Visitor', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Link', SR_TEXTDOMAIN ),
                   __( 'Click Count', SR_TEXTDOMAIN ),
                   __( 'Last Click', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $data_item,
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_span_add_redirect' => __( 'Change Link', SR_TEXTDOMAIN ),
           'label_ip' => __( 'IP', SR_TEXTDOMAIN ),
           'label_log_found' => __( 'Total Log Found', SR_TEXTDOMAIN ),
           'log_count' => $log_count,
           
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=VisitorAllClickedLinkCount'),
       ));
       
       $this->current_url = $data['current_url'];
       
       add_action('admin_footer', array( $this, 'vct_script'));
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/visitorClickTracking/VisitorAllClickedLinkCount.twig', $data );
    }
    
    /**
     * 
     * @global type $wpdb
     */
    public function vct_script(){
       ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                
                //Date range picker
                $('#reportrange span').html(moment("<?php echo date("Ymd",strtotime($this->first_day));?>", "YYYYMMDD").format('MMMM D, YYYY') + ' - ' + moment("<?php echo date("Ymd",strtotime($this->last_day));?>", "YYYYMMDD").format('MMMM D, YYYY'));
                $('#reportrange').daterangepicker({
                    format: 'MM/DD/YYYY',
                    startDate: moment("<?php echo date("Ymd",strtotime($this->first_day));?>", "YYYYMMDD"),
                    endDate: moment("<?php echo date("Ymd",strtotime($this->last_day));?>", "YYYYMMDD"),
                    minDate: '01/01/2017',
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
                    window.location ='<?php echo isset($this->current_url) ? $this->current_url : ''; ?>&date_range='+$date_range;
                });
            });
        </script>
       <?php
    }
}
