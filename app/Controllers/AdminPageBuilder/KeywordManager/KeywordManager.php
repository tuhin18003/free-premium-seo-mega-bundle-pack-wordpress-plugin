<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager;
/**
 * Keyword Manager Dashboard
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\KeywordManager\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Models\KeywordManager\KeywordQuery\KeywordQuery;

class KeywordManager{

    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
    /**
     * Hold date range
     * @var type 
     */
    private $first_day;
    private $last_day;
    private $data;
    private $rows;


    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }
    
    public function keyword_manager_landing(){
        global $wpdb;
        
        $current_tab = $this->http->get('tab', false);
        $current_page = $this->http->get('page', false);
        if( $this->http->has('tab') && 'cs-keyword-manager' == $current_page){
            $tabTypes = $this->get_tab_type($current_tab); 
            $newClassPath = "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\KeywordManager\\Tabs\\{$tabTypes}\\" . $current_tab;
            if(class_exists($newClassPath)){
                $newObj = new $newClassPath( $this->http );
                return $newObj->load_page_builder( CommonText::common_text( array( $current_page, $current_tab)) );
            }else{
                $data = array(
                   'current_tab' => __( '', '' ),
                   'CommonText' => CommonText::common_text(),
                   'page_title' =>  __( 'Error 404', SR_TEXTDOMAIN ),
                   'page_subtitle' =>  __( 'Error page redirection', SR_TEXTDOMAIN ),
                    
                   'oops' =>  __( 'Whoops! Page not found!', SR_TEXTDOMAIN ),
                   'not_found_msg' =>  __( 'This page cannot found or is missing.', SR_TEXTDOMAIN ),
                   'dir_msg' =>  __( 'Use the navigation left or the button below to get back and track.', SR_TEXTDOMAIN ),
                    
                   'error_page_msg' =>  __( 'Sorry! we do not find the page you are looking for.', SR_TEXTDOMAIN ),
                   'back_btn_label' =>  __( 'Back to Dashbaoard', SR_TEXTDOMAIN ),
                   'back_btn_href' => admin_url('admin.php?page=cs-keyword-manager'),
                );
                return  view( '@CsSeoMegaBundlePack/error/error_404.twig', $data );
            }
        }else{
            $this->dash_init_data();
            $this->data = array(
               'CommonText' => CommonText::common_text(),
               'page_title' =>  __( 'Dashboards', SR_TEXTDOMAIN ),
               'page_subtitle' =>  __( 'Overview of keyword manager tools', SR_TEXTDOMAIN ),
               'overview_of_keyword_ranking' =>  __( 'Overview of keyword ranking', SR_TEXTDOMAIN ),
               'tbl_rows' => isset($this->rows->num_rows) ? $this->rows->num_rows : '',
                'date_range' => date( 'M d, Y', strtotime($this->first_day)) .' to '. date( 'M d, Y', strtotime($this->last_day))
            );
            
            add_action( 'admin_footer', array( $this, '_compare_date_script') );
            return  view( '@CsSeoMegaBundlePack/KeywordManager/Dashboard.twig', $this->data );
        }
        
    }
    
    /**
     * Get Tab Types
     * 
     * @param type $tab
     * @return boolean|string
     */
    private function get_tab_type( $current_tab ){
        if( empty($current_tab)) return false;
        
        if(strpos( $current_tab, 'Kres') !== false){
            return 'KeywordResearch';
        }
        elseif(strpos( $current_tab, 'RnkChk') !== false){
            return 'RankingChecker';
        }
        elseif(strpos( $current_tab, 'Tags') !== false){
            return 'EasyTags';
        }
    }
    
    /**
     * Init data
     */
    private function dash_init_data(){
        $this->first_day = date('Y-m-d', strtotime('2 weeks ago'));
        $this->last_day = date('Y-m-d', strtotime( '+1 days'));

        $args = array(
            'first_date' => $this->first_day,
            'last_date' => $this->last_day
        );
        $this->rows = KeywordQuery::get_Key_Words( $args );
    }

    /**
     * Get keywords
     * 
     * @return Array
     */
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
    
    /**
     * Script
     */
    public function _compare_date_script(){
        ?>
            <script type="text/javascript">
                <?php if( $this->get_compared_data() ) { ?>
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
                <?php } ?>
            </script>
        <?php
    }
}
