<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Google;
/**
 * Google Analytics
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class GoogleStatistics extends GoogleSettings {
    
    public $result;
    public $result2;
    private $from_date;
    private $to_date;
    private $country;
    private $browsers;
    private $languages;
    private $visitor_type;
    private $source;
    private $device_type;
    private $totalResults;
    private $no_internet;


    public function __construct($Http) {
        parent::__construct($Http);
    }
    
    public function load_page_builder( $common_text ){
        global $wpdb, $AiosGooAppToken;
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_settings', 'json' => true ) );   
        if( isset( $get_options->profile_id ) && !empty($get_options->profile_id)){
            $this->get_report( $get_options->selected_profile_id );
        }
        $data = array_merge( CommonText::form_element( 'google_analytics', 'aios-google-analytics' ), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Analytics Overview', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Views your Google Analytics data.', SR_TEXTDOMAIN ),
           'panel_title' =>  __( 'Audience Overview', SR_TEXTDOMAIN ),
           'no_internet' => $this->no_internet,
           'result' => $this->result,
           'totalResults' => isset($this->totalResults) ? $this->totalResults : '',
           'data_showing' => __( 'Data Showing : ', SR_TEXTDOMAIN ) . date('d M, Y' ,strtotime($this->from_date)) . __( ' to ', SR_TEXTDOMAIN ) . date('d M, Y' ,strtotime($this->to_date)),
           'pie_chart_status' => isset($this->result2['rows']) ? 'true' : 'false',
           'label_submit_btn' => __( 'Save Settings', SR_TEXTDOMAIN ),
           'aios_goo_app_token' => $AiosGooAppToken
       ));
       add_action('admin_footer', [$this, '_addFooter_script']);
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/google/GoogleStatistics.twig', $data );
    }
    
    public function _addFooter_script(){
        ?>
        <script type="text/javascript">
            <?php if(isset($this->result['rows'])) { ?>
            /********************audience overview********************/
                var LinehighChartData = [<?php $r=0; foreach($this->metrics_labels() as $key=>$label) { if($r>0) {echo ",";} ?> 
                        <?php echo "{pointInterval: 24*3600*1000, name:'{$label}', data:[  {$this->get_metrics_values($key)}]}";   ?>
                    <?php $r++; } ?>];        

                var _obj = {
                    graph_load_in: 'graph_audience_overview',
                    title_text: '<?php _e( 'Visitors Overview ', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: LinehighChartData
                };
                AiosNasChartsLine = new Aios_NasCharts_Line( _obj );
                AiosNasChartsLine.init();
            /********************audience overview********************/
            <?php } ?>

            <?php if(isset($this->country)) { ?>
            /********************Country audience overview********************/
                var highChartData = [<?php $i=0;
                    foreach($this->country as $country => $visits){
                        if($i>0) echo ',';
                        echo "{name: '{$country}', y: {$visits[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_country',
                    title_text: '<?php _e( 'Visitor by country. ', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: highChartData,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Country', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************Country audience overview********************/
            <?php } ?>

            <?php if(isset($this->browsers)) { ?>
            /********************audience browsers overview********************/
                var browserData = [<?php $i=0;
                    foreach($this->browsers as $name => $precen){
                        if($i>0) echo ',';
                        echo "{name: '{$name}', y: {$precen[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_browser',
                    title_text: '<?php _e( 'Visitor by browsers. ', SR_TEXTDOMAIN );?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: browserData,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Browser', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************audience  browsers overview********************/
            <?php } ?>
            <?php if(isset($this->visitor_type)) { ?>
            /********************audience types overview********************/
                var trafficTypesData = [<?php $i=0;
                    foreach($this->visitor_type as $name => $precen){
                        if($i>0) echo ',';
                        echo "{name: '{$name}', y: {$precen[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_type',
                    title_text: '<?php _e( 'Visitor types. ', SR_TEXTDOMAIN );?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: trafficTypesData,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Types', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************audience  types overview********************/
            <?php } ?>
            <?php if(isset($this->languages)) { ?>
            /********************audience types overview********************/
                var trafficLangData = [<?php $i=0;
                    foreach($this->languages as $name => $precen){
                        if($i>0) echo ',';
                        echo "{name: '{$name}', y: {$precen[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_lang',
                    title_text: '<?php _e( 'Visitor language. ', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: trafficLangData,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Language', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************audience  types overview********************/
            <?php } ?>
            <?php if(isset($this->source)) { ?>
            /********************audience types overview********************/
                var trafficSource = [<?php $i=0;
                    foreach($this->source as $name => $precen){
                        if($i>0) echo ',';
                        echo "{name: '{$name}', y: {$precen[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_source',
                    title_text: '<?php _e( 'Visitor Sources. ', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: trafficSource,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Source', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************audience  types overview********************/
            <?php } ?>
            <?php if(isset($this->device_type)) { ?>
            /********************audience types overview********************/
                var trafficDevices = [<?php $i=0;
                    foreach($this->device_type as $name => $precen){
                        if($i>0) echo ',';
                        echo "{name: '{$name}', y: {$precen[1]} }";
                        $i++;
                    }
                ?>];

                var _obj = {
                    graph_load_in: 'graph_audience_device',
                    title_text: '<?php _e( 'Visitor Devices. ', SR_TEXTDOMAIN ); ?>',
                    sub_title_text: '<?php _e( 'Source: analytics.google.com', SR_TEXTDOMAIN )?>',
                    data: trafficDevices,
                    drill_down_data: '',
                    series_name: '<?php _e( 'Device', SR_TEXTDOMAIN )?>'
                };
                AiosNasCharts = new Aios_NasCharts( _obj );
                AiosNasCharts.init();
            /********************audience  types overview********************/
            <?php } ?>


            ( function( $ ) {
                    $( document ).ready( function() {

                        $('#reportrange span').html(moment("<?php echo date("Ymd",strtotime($this->from_date));?>", "YYYYMMDD").format('MMMM D, YYYY') + ' - ' + moment("<?php echo date("Ymd",strtotime($this->to_date));?>", "YYYYMMDD").format('MMMM D, YYYY'));
                        $('#reportrange').daterangepicker({
                            format: 'MM/DD/YYYY',
                            startDate: moment("<?php echo date("Ymd",strtotime($this->from_date));?>", "YYYYMMDD"),
                            endDate: moment("<?php echo date("Ymd",strtotime($this->to_date));?>", "YYYYMMDD"),
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

                            window.location ='<?php echo commonText::common_text()['base_url'] . '&tab=GoogleStatistics'; ?>&date_range='+$date_range;
                        });


                    }); //end of document
            })(jQuery);
        </script>
        <?php
    }
    
    public function get_report( $profile_id ){
        if( empty( $profile_id )) return false;
        
        if( $this->http->has('date_range')){
            $date_range = explode('-',$this->http->get('date_range', false));
            $this->from_date = date('Y-m-d', strtotime($date_range[0])); // 30 days
            $this->to_date = date('Y-m-d', strtotime($date_range[1])); // today
        }else{
            $this->from_date = date('Y-m-d', time()-30*24*60*60); // 30 days
            $this->to_date = date('Y-m-d'); // today
        }

        $obj = (object)array(
            'profile_id' => $profile_id,
            'metrics' => $this->get_metrics(0),
            'dimensions' =>  $this->get_dimensions(0)
        );
        $this->result = $this->google_query( $obj );
        //get country,lang,browser,usertype
        $obj = (object)array(
            'profile_id' => $profile_id,
            'metrics' => $this->get_metrics(1),
            'dimensions' =>  $this->get_dimensions(1)
        );
        $this->result2 = $this->google_query( $obj );
        $m_labels = $this->metrics_labels();
        if( !empty($this->result['totalsForAllResults']) ){
            $l = 3;
            foreach($this->result['totalsForAllResults'] as $val){
                $this->totalResults = array_merge( (array)$this->totalResults, array( $m_labels[$l] => round($val, 2) )); 
                $l++;
            }
        }
        
        if( !empty($this->result2['rows']) ){
            foreach($this->result2['rows'] as $row){
                
                $total = 0;
                if( isset( $this->country[$row[0]] ) ){
                    $total = $this->country[$row[0]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->country[$row[0]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->country = array_merge( (array)$this->country, array($row[0] => array($row[6], $percentage) ));
                }
                
                $total = 0;
                if( isset( $this->browsers[$row[1]] ) ){
                    $total = $this->browsers[$row[1]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->browsers[$row[1]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->browsers = array_merge( (array)$this->browsers, array($row[1] => array($row[6], $percentage) ));
                }
                
                $total = 0;
                if( isset( $this->languages[$row[2]] ) ){
                    $total = $this->languages[$row[2]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->languages[$row[2]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->languages = array_merge( (array)$this->languages, array($row[2] => array($row[6], $percentage) ));
                }
                
                $total = 0;
                if( isset( $this->visitor_type[$row[3]] ) ){
                    $total = $this->visitor_type[$row[3]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->visitor_type[$row[3]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->visitor_type = array_merge( (array)$this->visitor_type, array($row[3] => array($row[6], $percentage) ));
                }
                
                $total = 0;
                if( isset( $this->source[$row[4]] ) ){
                    $total = $this->source[$row[4]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->source[$row[4]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->source = array_merge( (array)$this->source, array($row[4] => array($row[6], $percentage) ));
                }
                
                $total = 0;
                if( isset( $this->device_type[$row[5]] ) ){
                    $total = $this->device_type[$row[5]][0] + $row[6];
                    $percentage = round( ($total / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    
                    $this->device_type[$row[5]] =  array( $total, $percentage );
                }else{
                    $percentage = round( ($row[6] / $this->result2['totalsForAllResults']['ga:users']) * 100, 2);
                    $this->device_type = array_merge( (array)$this->device_type, array($row[5] => array($row[6], $percentage) ));
                }
            }
        }
    }
    
    private function google_query( $argc ){
        global $AiosGooAppToken;
        if( isset($AiosGooAppToken) && empty($AiosGooAppToken['auth_status'])){
            return array( 'errors' => $AiosGooAppToken['auth_error_msg']);
        }else{
            $get_token = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_google_token', 'json' => true, 'json_array' => true ) ); 
            if(!isset($get_token['refresh_token']) && empty($get_token['refresh_token'])){
                return array( 'errors' => $AiosGooAppToken['auth_error_msg']);
            }
        }
        
        if( GeneralHelpers::check_internet_status() === false ){
            return $this->no_internet =  __( 'Your internet connection has down!', SR_TEXTDOMAIN );
        }
        
        //populate the access token and client obj
        $this->populate_access_token();
        $analytics = isset($this->client) ?  new \Google_Service_Analytics( $this->client ) : '';
        try{
            return @$analytics->data_ga->get( "ga:{$argc->profile_id}", $this->from_date, $this->to_date, $argc->metrics, array('dimensions' => $argc->dimensions));
        } catch (Google_ServiceException $ex) {
            return 'Google analytics internal server error: (Technical details) ' . $ex->getErrors()[0]['message'];
        }
    }


    /**
     * Get metrics
     * 
     * @return array
     */
    public function get_metrics( $type ){
        $m = array(
            'ga:percentNewSessions,ga:sessions,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:pageviews,ga:users',
            'ga:users'
        );
        return isset($m[$type]) ? $m[$type] : '';
    }
    
    /**
     * Get Dimensions
     * 
     * @return array
     */
    public function get_dimensions( $type ){
        $d = array(
            'ga:month,ga:day,ga:year',
            'ga:country,ga:browser,ga:language,ga:userType,ga:source,ga:deviceCategory'
        );
        return isset($d[$type]) ? $d[$type] : '';
    }
    
    

    /**
     * Metrics Lables
     * 
     * @return type
     */
    public function metrics_labels(){
        return array(
            3 =>  __( '% New Visits', SR_TEXTDOMAIN ), 4 => __( 'Visits', SR_TEXTDOMAIN ) , 5 => __( 'Avg. Visit Duration', SR_TEXTDOMAIN ) , 6 =>__( 'Bounce Rate', SR_TEXTDOMAIN ) ,7 => __( 'Pages / Visit', SR_TEXTDOMAIN ) , 8 => __( 'Pageviews', SR_TEXTDOMAIN ) , 9 => __( 'Unique Visitors', SR_TEXTDOMAIN ) 
        );
    }
    
    public function get_metrics_values( $index ){
        if( ! isset( $this->result['rows']) ) return false;
        $values = array_column($this->result['rows'], $index);
        array_walk_recursive($values,array( $this, 'aiosRound'));
        $data = '';
        if(!empty($values)){
            for($i=0;$i<count($values);$i++){
                $ts = mktime(0, 0, 0, $this->result['rows'][$i][0], $this->result['rows'][$i][1], $this->result['rows'][$i][2]) * 1000;
                if($i>0) $data .= ",";
                $data .= "[ {$ts}, {$values[$i]}]";
            }
        }
        return empty($data) ? '' : $data;
    }
    
    public function aiosRound(&$item) {
        $item = round($item, 2);
    }
}
