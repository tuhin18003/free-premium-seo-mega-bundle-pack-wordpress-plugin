<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * Manage Property
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\getPropertyData as getPropertyData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class ManageProperty {
    
    private $items;
    private $cbdLabel = array();
    private $cbdDate = array();
    private $compareData;
    private $compareDataLabel;
    private $colors = array("#6CF", "#EA8422", "#365EBF", "#51A351", "#000", "#644183","#3b5998","#dd4b39","#394B65","#F5900E", "#55a32d", "#174558", "#E24547","#C82D28","#3d999e","#C1D900","#5E5E5E","#af0606","#7BC0FF");
    private $alexaData;
    private $domainAuthData;
    private $siteTrustData;
    private $spamData;
    private $backlinkData;
    private $first_day;
    private $last_day;
    private $current_base_url;


    /**
     * Load Page Builder
     * 
     * @since 1.0.0
     * @return String
     */
    public function load_page_builder(){
        global $wpdb;
       $CommonText = new CommonText();
       $getPropertyData = new getPropertyData();
       
       $groups = CsQuery::Cs_Get_results(array(
            'select' => 'id, group_name',
            'from' => $wpdb->prefix . 'aios_blmanager_item_groups',
            'where' => ' group_type = 1'
        ));
       
       $propertyIds = '';
       $url ='';
       if( isset( $_GET['item_ids'] ) && ! empty( $_GET['item_ids'] )){
           $compare_by_date = isset($_GET['compare_by_date']) ? trim($_GET['compare_by_date']) : '';
           $propertyIds = wp_kses( stripslashes($_GET['item_ids']), array() );
       }
       
       if(empty($compare_by_date)){ //compare
           $compare_data = array(
               'compare_by_date' => false
            );
       }else{ // compare by date
           if(isset($_GET['date_range']) && !empty($_GET['date_range'])){
                $date_range = explode('-',$_GET['date_range']);
                $this->first_day = date('Y-m-d', strtotime($date_range[0]));
                $this->last_day = date('Y-m-d', strtotime($date_range[1]));
                
            }else{
                $this->first_day = date('Y-m-d', strtotime('2 weeks ago'));
                $this->last_day = date('Y-m-d');
            }
            $compare_data = array(
                'compare_by_date' => true,
                'first_date' => $this->first_day,
                'last_date' => $this->last_day,
                'p_id' => $propertyIds
            );
       }
       
       $filter_msg_title = $filter_msg_subtitle = '';
       if(isset($_GET['group_filter']) && !empty(isset($_GET['group_filter']))){
            $group_filter = wp_kses( stripslashes($_GET['group_filter']), array() );
            $compare_data = array_merge($compare_data, array('group_id' => $group_filter));
            $getProperties = $getPropertyData->getProperty( $compare_data );
            if($groups){
                foreach($groups as $group){
                    if($group->id === $group_filter){
                        $group_row = $group->group_name; break;
                    }
                }
            }
            $filter_msg_title =  __( 'Filtered by group', SR_TEXTDOMAIN );
            $filter_msg_subtitle = __( 'Total: <b>'. count($getProperties) .'</b> properties found by group : <b>' . $group_row .'</b>', SR_TEXTDOMAIN );
       }else{
            $getProperties = $getPropertyData->getProperty( $compare_data );
       }
       
//       pre_print($compare_data);
//       pre_print($getProperties);
       
       
       
       $items = array(); 
       if($getProperties){
           foreach( $getProperties as $item ){
               $siteStatus = '';
               if(isset($item->item_domain_status)){
                    $siteStatus = domain_status_abv( $item->item_domain_status) === 200 ? '<span class="label label-table label-success">' . __( 'Good & Live', SR_TEXTDOMAIN ). '</span>' : '<span class="label label-table label-warning">' . domain_status_abv( $item->item_domain_status) .'</span>';
               }
                
                $google_rank = 0;
                $mozData = empty($item->moz_data) ? '' : json_decode($item->moz_data);
                $moz_rank = empty( $mozData ) ? 0 : $mozData->moz_rank;
                $alexaData = empty($item->alexa_data) ? '' : json_decode($item->alexa_data);
                $alexa_rank = empty( $alexaData ) ? 0 : $alexaData->alexa_global_rank;

                $site_seo_score = column_link_quality($google_rank, $moz_rank, $alexa_rank);
                
//                pre_print($alexaData->alexa_country);
                
                $this->items[] = $items[] = array(
                   $item->item_domain,
                   $siteStatus,
                   $site_seo_score,
                   empty( $mozData ) ? 0 : $mozData->spam_score,
                   empty( $mozData ) ? 0 : round($mozData->domain_authority, 2),
                   empty( $mozData ) ? 0 : round($mozData->page_authority, 2),
                   empty($alexaData->alexa_global_rank) ? 0 : $alexaData->alexa_global_rank,
                   empty($alexaData->alexa_country) ? 0 : $alexaData->alexa_country,
                   empty($alexaData->alexa_country_rank) ? 0 : $alexaData->alexa_country_rank->rank,
                   empty($mozData->total_external_links) ? 0 : $mozData->total_external_links,
                   $item->p_id,
                   empty($mozData->moz_rank) ? 0 : round($mozData->moz_rank,2),
                   empty($mozData->moz_trust) ? 0 : round($mozData->moz_trust,2),
                   isset($item->auto_find_backlink) ? $item->auto_find_backlink : '',
                   isset($item->item_auto_update) ? $item->item_auto_update : '',
                   isset($item->created_on) ? $item->created_on : ''
                );
                
                if(isset($item->created_on) && !empty($item->created_on)){
//                    $dateIndex = date('Y-m-d', strtotime($item->created_on));
                    $dateIndex = $item->item_domain;
                    
                    $this->alexaData[$dateIndex] = array_merge( isset($this->alexaData[$dateIndex]) ? (array)$this->alexaData[$dateIndex] : array(), array( empty($alexaData->alexa_global_rank) ? 0 : $alexaData->alexa_global_rank ));
                    $this->domainAuthData[$dateIndex] = array_merge(isset($this->domainAuthData[$dateIndex]) ? (array)$this->domainAuthData[$dateIndex] : array(), array( empty($mozData->domain_authority) ? 0 : round($mozData->domain_authority, 2) ));
                    $this->siteTrustData[$dateIndex] = array_merge(isset($this->siteTrustData[$dateIndex]) ? (array)$this->siteTrustData[$dateIndex]  : array(), array( empty($mozData->moz_trust) ? 0 : round($mozData->moz_trust, 2) ));
                    $this->spamData[$dateIndex] = array_merge(isset($this->spamData[$dateIndex]) ? (array)$this->spamData[$dateIndex] : array(), array( empty($mozData->spam_score) ? 0 : round($mozData->spam_score, 2) ));
                    $this->backlinkData[$dateIndex] = array_merge(isset($this->backlinkData[$dateIndex]) ? (array)$this->backlinkData[$dateIndex] : array(), array( empty($mozData->total_external_links) ? 0 : round($mozData->total_external_links, 2) ));
                    
                    if( ! in_array( $item->created_on, $this->cbdDate)){
                        $this->cbdDate = array_merge((array)$this->cbdDate, array($item->created_on));
                    }
                    
                    if( ! in_array( $item->item_domain, $this->cbdLabel)){
                        $this->cbdLabel = array_merge((array)$this->cbdLabel, array($item->item_domain));
                    }                   
                }
           }
       }
       
       
       
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Manage Property', SR_TEXTDOMAIN ),
           'data_items' => $getProperties,
           'loading_gif' => $CommonText->form_element()['loading_gif'],
           'no_data_found'=> __( 'Sorry No Data Found!', SR_TEXTDOMAIN ),
       );
       
       add_action("admin_footer", [$this, 'mp_action_script']);
       
       if(empty($propertyIds)){ // list properties
           $data = array_merge( $data, array(
               'page_title' =>  __( 'Manage Property', SR_TEXTDOMAIN ),
               'page_subtitle' =>  __( 'Manage all of your properties.', SR_TEXTDOMAIN ),
               'tbl_headers' => array(
                   __( 'Property / Website url', SR_TEXTDOMAIN ),
                   __( 'Health Status', SR_TEXTDOMAIN ),
                   __( 'Reputation', SR_TEXTDOMAIN ),
                   __( 'Authority', SR_TEXTDOMAIN ),
                   __( 'Alexa Ranking', SR_TEXTDOMAIN ),
                   __( 'Alexa Backlinks', SR_TEXTDOMAIN ),
               ),
               'tbl_data_array' => $items,
               'filter_title' => $filter_msg_title,
               'filter_subtitle' => $filter_msg_subtitle,
               'out_of_10' =>  __( 'Out of 10', SR_TEXTDOMAIN ),
               'out_of_100' =>  __( 'Out of 100', SR_TEXTDOMAIN ),
               'domain_auth' =>  __( 'Domain athority ', SR_TEXTDOMAIN ),
               'page_auth' =>  __( 'Page athority ', SR_TEXTDOMAIN ),
               'properties' => $getProperties,
               'groups' => $groups,
               'label_filter_btn' =>  __( 'Filters', SR_TEXTDOMAIN ),
               'actn_btn_compare' =>  __( 'Compare', SR_TEXTDOMAIN ),
               'actn_btn_compare_by_date' =>  __( 'Compare / Detail by Date', SR_TEXTDOMAIN ),
               'actn_delete_btn' =>  __( 'Delete', SR_TEXTDOMAIN ),
               'actn_btns' =>  __( 'Bulk Actions', SR_TEXTDOMAIN ),
               'actn_btn_update' =>  __( 'Instantly Update SERP', SR_TEXTDOMAIN ),
               'actn_btn_set_auto_update' =>  __( 'Automatically Update', SR_TEXTDOMAIN ),
               'actn_btn_remove_auto_update' =>  __( 'Don\'t Update Automatic', SR_TEXTDOMAIN ),
               'actn_btn_set_auto_update_backlink' =>  __( 'Add To Automatic Backlink Finder', SR_TEXTDOMAIN ),
               'actn_btn_remove_auto_update_backlink' =>  __( 'Remove From Automatic Backlink Finder', SR_TEXTDOMAIN ),
               'auto_update' =>  __( 'Automatic Update ', SR_TEXTDOMAIN ),
               'auto_update_backlink' =>  __( 'Backlink will update automatically', SR_TEXTDOMAIN ),
               'auto_update_property' =>  __( 'SERP will Update automatically', SR_TEXTDOMAIN ),
               'base_url' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageProperty'),
               'label_back_to_btn' => __( 'Back to All', SR_TEXTDOMAIN ),
            ));
           return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/ManageProperty.twig', $data );
       }else{ // compare properties
            if(empty($compare) && empty($compare_by_date)){ 
                $data = array_merge( $data, array(
                   'page_title' =>  __( 'Property Detail', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Detail information about your property', SR_TEXTDOMAIN ),
                ));
            }else{
                
                $this->compareDataLabel = array(
                   __( 'Alexa Global Ranking', SR_TEXTDOMAIN ),
                   __( 'Alexa Country Ranking', SR_TEXTDOMAIN ),
                   __( 'Moz Ranking', SR_TEXTDOMAIN ),
                   __( 'Spam Score', SR_TEXTDOMAIN ),
                   __( 'Domain Auth.', SR_TEXTDOMAIN ),
                   __( 'Page Auth.', SR_TEXTDOMAIN ),
                   __( 'Site Trust', SR_TEXTDOMAIN ),
                   __( 'Reputation', SR_TEXTDOMAIN ),
                   __( 'Total Backlink', SR_TEXTDOMAIN ),
                    
                );
                $data = array_merge( $data, array(
                   'back_btn_label' =>  __( 'Back', SR_TEXTDOMAIN ),
                   'back_btn_link' => admin_url('admin.php?page=cs-backlink-manager&tab=ManageProperty')
                ));
            }
            
            if(empty($compare_by_date)){
                $data = array_merge( $data, array(
                   'page_title' =>  __( 'Property Compare', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Detail information comparing of your properties', SR_TEXTDOMAIN ),
                   'site_ranking_trust_title' =>  __( 'Website Ranking & Spam Score', SR_TEXTDOMAIN ),
                   'site_ranking_trust_subtitle' =>  __( 'The lower graph line is better then the higher graph line', SR_TEXTDOMAIN ),
                   'site_auth_trust_title' =>  __( 'Website Authoriry & Trust & Popularity Score', SR_TEXTDOMAIN ),
                   'site_auth_trust_subtitle' =>  __( 'The higher graph line is better then the lower graph line', SR_TEXTDOMAIN ),
                ));
                add_action( 'admin_footer', array( $this, 'property_compare_script') );
                return view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/PropertyCompareOrDetails.twig', $data );
            }else{
                $data = array_merge( $data, array(
                   'page_title' =>  __( 'Property Compare By Date', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Detail information comparing of your properties', SR_TEXTDOMAIN ),
                   
                   'site_ranking_trust_title' =>  __( 'Alexa Website Ranking', SR_TEXTDOMAIN ),
                   'site_ranking_trust_subtitle' =>  __( 'Alexa global website ranking. The lower graph line is better then the higher graph line.', SR_TEXTDOMAIN ),
                   'site_auth_trust_title' =>  __( 'Website Domain Authoriry', SR_TEXTDOMAIN ),
                   'site_auth_trust_subtitle' =>  __( 'The higher graph line is better then the lower graph line', SR_TEXTDOMAIN ),
                   'site_trust_title' =>  __( 'Website Domain Trust', SR_TEXTDOMAIN ),
                   'site_trust_subtitle' =>  __( 'The higher graph line is better then the lower graph line', SR_TEXTDOMAIN ),
                   'site_spam_title' =>  __( 'Website Spam Score', SR_TEXTDOMAIN ),
                   'site_spam_subtitle' =>  __( 'The lower graph line is better then the higher graph line', SR_TEXTDOMAIN ),
                   'site_backlink_title' =>  __( 'Website Backlinks Count', SR_TEXTDOMAIN ),
                   'site_backlink_subtitle' =>  __( 'The higher graph line is better then the lower graph line', SR_TEXTDOMAIN ),
                    'date_range_title' => __( 'Data by Date Range', SR_TEXTDOMAIN ),
                    'date_range_subtitle' => __( 'Data showing from '. date('d M Y',strtotime($this->first_day)) . ' to ' . date('d M Y',strtotime($this->last_day)), SR_TEXTDOMAIN ),
                ));
                $this->current_base_url = admin_url('admin.php?page=cs-backlink-manager&tab=ManageProperty');
                add_action( 'admin_footer', array( $this, 'property_compare_date_script') );
                return view( '@CsSeoMegaBundlePack/backlinkManager/tabs/property/PropertyCompareByDate.twig', $data );
            }
            
       }
       
    }
    
    /**
     * Add Script to property compare page
     * 
     * @since 1.0.0
     */
    public function property_compare_script(){
        
        ?>
        <script type="text/javascript">
            /************Review by site************/
            var $c3_charts_colors = {
                <?php for($i=0; $i<count($this->items); $i++){ $item = $this->items[$i]; if($i>0) {echo ",\n"; } ?>
                        '<?php echo $item[0]; ?>' : "<?php echo $this->colors[$i]; ?>"
                <?php }?>
                };
            var $c3_categories = [ <?php echo "'".$this->compareDataLabel[0] ."',"."'".$this->compareDataLabel[1] ."'," . "'".$this->compareDataLabel[3] ."'";?>];    
            var $c3_columns_data = [
               <?php for($d=0; $d<count($this->items); $d++){ $item = $this->items[$d]; if($d>0) {echo ",\n";} ?>
                        ['<?php echo $item[0]; ?>',<?php echo $item[6]; ?>, <?php echo $item[8]; ?>, <?php echo $item[3]; ?>]
                <?php } ?>
               ]; 

            var $c3_categories1 = [ <?php echo "'".$this->compareDataLabel[2] ."',"."'".$this->compareDataLabel[4] ."'," . "'".$this->compareDataLabel[5] ."'," . "'".$this->compareDataLabel[6] ."'," . "'".$this->compareDataLabel[7] ."'";?>];    
            var $c3_columns_data1 = [
               <?php for($d=0; $d<count($this->items); $d++){ $item = $this->items[$d]; if($d>0) {echo ",\n";} ?>
                        ['<?php echo $item[0]; ?>',<?php echo $item[11]; ?>, <?php echo $item[4]; ?>, <?php echo $item[5]; ?>, <?php echo $item[12]; ?>, <?php echo $item[2]; ?>]
                <?php } ?>
               ]; 

            /************Review by site************/
        </script>
        <?php
    }
    
    public function mp_action_script(){
        ?>
        <script type="text/javascript">    
            ( function( $ ) {
                $( document ).ready( function() {
                     var _obj = {
                        $: $,
                        form_reset: true,
                        redirect: window.location.href
                    };
                    var action_handler = new Aios_Action_Handler( _obj );
                    action_handler.setup( "#update_now" );   

                });
            
            });
        </script>    
            
        <?php
        
    }
    
    /**
     * property compare by date script
     * 
     * @since 1.0.0
     */
    public function property_compare_date_script(){
        
        ?>
            <script type="text/javascript">
                <?php if( ! empty($this->items)) { ?>
                var $c3_charts_colors = {
                    <?php for($i=0; $i<count($this->cbdLabel); $i++){ if($i>0) {echo ",\n"; } ?>'<?php echo $this->cbdLabel[$i]; ?>' : "<?php echo $this->colors[$i]; ?>"
                    <?php } ?>
                };
                /********************alexa ranking********************/
                    //labels(
                    var $c3_categories = [<?php $i =0; foreach($this->cbdDate as $date ){ if($i>0) {echo ",\n"; } ?>'<?php echo $date; ?>'<?php $i++; } ?>];
                    var $c3_alexa_columns_data = [
                        <?php $r=0; foreach($this->cbdLabel as $label) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $label;?>',<?php echo implode(",",$this->alexaData[$label]); ?>]    
                        <?php $r++; } ?>
                    ];
                /********************alexa ranking********************/
                
                /********************Domain Authority********************/
                var $c3_domAuth_columns_data = [
                        <?php $r=0; foreach($this->cbdLabel as $label) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $label;?>',<?php echo implode(",",$this->domainAuthData[$label]); ?>]    
                        <?php $r++; } ?>
                    ];
                
                /********************Domain Authority********************/
                /********************site trust********************/
                var $c3_sitetrust_columns_data = [
                        <?php $r=0; foreach($this->cbdLabel as $label) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $label;?>',<?php echo implode(",",$this->siteTrustData[$label]); ?>]    
                        <?php $r++; } ?>
                    ];
                
                /********************site trust********************/
                
                /********************Spam Data********************/
                var $c3_spam_columns_data = [
                        <?php $r=0; foreach($this->cbdLabel as $label) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $label;?>',<?php echo implode(",",$this->spamData[$label]); ?>]    
                        <?php $r++; } ?>
                    ];
                
                /********************Spam Data********************/
                
                /********************Backlink Data********************/
                var $c3_backlink_columns_data = [
                        <?php $r=0; foreach($this->cbdLabel as $label) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $label;?>',<?php echo implode(",",$this->backlinkData[$label]); ?>]    
                        <?php $r++; } ?>
                    ];
                
                /********************Domain Authority********************/
        <?php } ?>
//            date range picker
            
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
                    var $propertyIds = '<?php echo isset($_GET['item_ids']) ? "&item_ids=".$_GET['item_ids'] : ''; ?>';
                    var $compare_by_date = '<?php echo isset($_GET['compare_by_date']) ? "&compare_by_date=".$_GET['compare_by_date'] : ''; ?>';
                    window.location ='<?php echo isset($this->current_base_url) ? $this->current_base_url : ''; ?>&date_range='+$date_range+$propertyIds+$compare_by_date;
                });
            })(jQuery);
        </script>
        <?php
    }                                                            
    
}
