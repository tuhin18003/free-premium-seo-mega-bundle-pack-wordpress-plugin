<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage BackLink Cost
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getBacklinkData as getBacklinkData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class ManageWebsiteCost {
    
    private $first_day;
    private $last_day;
    private $current_url;
    
    public function load_page_builder(){
        global $wpdb;
       $CommonText = new CommonText();
       $getBacklinkData = new getBacklinkData();
       
       if( isset( $_GET['date_range']) && !empty($_GET['date_range'])){
            $date_range_raw = wp_kses( $_GET['date_range'], array());
            $date_range = explode('-',$date_range_raw);
            $this->first_day = date('Y-m-d', strtotime($date_range[0]));
            $this->last_day = date('Y-m-d', strtotime($date_range[1]));
       }else{
           $this->first_day = date('Y-m-d', strtotime('3 months ago'));
           $this->last_day = date('Y-m-d');
       }
                
       $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id,group_name',
            'from' => "{$wpdb->prefix}aios_blmanager_item_groups",
            'where' => 'group_type = 1'       
        ));
       $items = CsQuery::Cs_Get_results(array(
            'select' => '*,c.id as cid',
            'from' => "{$wpdb->prefix}aios_blmanager_item_price as c",
            'join' => "left join {$wpdb->prefix}aios_blmanager_item_groups as g on g.id = c.item_id and g.group_type = 1",
            'where' => "c.type = 1 and c.created_on between '{$this->first_day}' and '{$this->last_day}' order by c.created_on desc"
        ));
        $cost = CsQuery::Cs_Sum(array(
            'table' => 'aios_blmanager_item_price',
            'where' => "type = 1 and created_on between '{$this->first_day}' and '{$this->last_day}' ",
            'sum_of' => 'price'
        ));
            
       
       $data = array_merge( (new CommonText())->form_element( 'aios-add-blc-cost' ), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' =>  __( 'Track Website Cost', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Track Your Website Cost', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Website Group', SR_TEXTDOMAIN ),
                   __( 'Cost', SR_TEXTDOMAIN ),
                   __( 'Note', SR_TEXTDOMAIN ),
                   __( 'Paid On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $items,
           'total_cost' =>  __( 'Total Cost: ', SR_TEXTDOMAIN ),
           'cost' => $cost,
           'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
           'no_group_found' =>  __( 'Please add website group to add cost!', SR_TEXTDOMAIN ),
           'label_link_add_group' =>  __( 'Add Group', SR_TEXTDOMAIN ),
           'add_new_cost_box_title' =>  __( 'Add New Cost', SR_TEXTDOMAIN ),
           'label_group_name' =>  __( 'Select Group', SR_TEXTDOMAIN ),
           'label_cost' =>  __( 'Enter Cost', SR_TEXTDOMAIN ),
           'placeholder_cost' =>  __( 'Enter Cost', SR_TEXTDOMAIN ),
           'label_description' =>  __( 'Enter Note', SR_TEXTDOMAIN ),
           'placeholder_description' =>  __( 'Enter Note', SR_TEXTDOMAIN ),
           'select_group' =>  __( 'Select Group', SR_TEXTDOMAIN ),
           'label_date' =>  __( 'Select Date', SR_TEXTDOMAIN ),
           'groups' => $groups,
           'label_submit_btn' =>  __( 'Add Now', SR_TEXTDOMAIN ),
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageWebsiteCost'),
           'add_group_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManagePropertyGroups'),
           'manage_link_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageProperty'),
       ));
       $this->current_url = $data['current_url'];
       add_action('admin_footer', array($this, 'blc_script'));
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/ManageWebsiteCost.twig', $data );
    }
    
    /**
     * 
     * @global type $wpdb
     */
    public function blc_script(){
       ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                jQuery('#input_date').datepicker({
                    autoclose: true,
                    todayHighlight: true
                });
                
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
