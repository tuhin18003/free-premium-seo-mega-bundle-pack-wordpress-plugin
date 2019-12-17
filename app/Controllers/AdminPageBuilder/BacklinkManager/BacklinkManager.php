<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Backlinks Manager Dashboard
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use Herbert\Framework\Http;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\Backend\Builder\AdminOptoinPageBuilder;

class BacklinkManager{
    
    protected $colors = array("#6CF", "#EA8422", "#365EBF", "#51A351", "#000", "#644183","#3b5998","#dd4b39","#394B65","#F5900E", "#55a32d", "#174558", "#E24547","#C82D28","#3d999e","#C1D900","#5E5E5E","#af0606","#7BC0FF");
    private $items = array();
    private $blcPrice;
    private $webPrice;

    /**
     * Http Instance
     *
     * @var type 
     */
    protected $http;
    
    /**
     *
     * @var type Admin option page builder isntanse
     */
    private $AdminOptionsPageBuilder;
    
    public function __construct(Http $http) {
        $this->http = $http;
        
        //create instance 
        $this->AdminOptionsPageBuilder = new AdminOptoinPageBuilder( $http );
    }
    
    /**
     * Default option page loader
     * 
     * @return string
     */
    public function backlink_manager_landing(){
        global $wpdb;
        
        //working on findbacklinkmatix file
        
        $current_tab = $this->http->get('tab', false);
        $current_page = $this->http->get('page', false);
        
        if( $this->http->has('tab') && 'cs-backlink-manager' == $current_page ){
            $package_name = $this->AdminOptionsPageBuilder->cs_option_page_loader( 'BacklinkManager' );

            if( false !== $package_name && class_exists( $package_name ) ){
                $newObj = new $package_name( $this->http );
                return $newObj->load_page_builder( CommonText::common_text( array( $current_page, $current_tab ) ) );
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
                   'back_btn_href' => CommonText::common_text()['base_url'],
                );
                return  view( '@CsSeoMegaBundlePack/error/error_404.twig', $data );
            }
            
        }else{
            
            
            /*************graph for new backlink found*************/
           $jan = date('Y-m-d', strtotime('3 months ago'));
            $dec = date('Y-m-d');
           
            $getBlcObj = CsQuery::Cs_Get_Results( array(
                'select' => 'b.item_created_on,p.item_domain as domain, b.item_domain as backlink',
                'from' => "{$wpdb->prefix}aios_blmanager_items as p, {$wpdb->prefix}aios_blmanager_items as b",
                'where' => "p.id = b.item_parent and b.item_created_on between '{$jan}' and '{$dec}'"       
            ));
                
           $draw_graph = false; 
           if( $getBlcObj ){
               foreach($getBlcObj as $ins ){
                   if( in_array( $ins->domain, $this->items) ){
                       $this->items[$ins->domain] = array($ins->item_created_on++);
                   }else{
                       $this->items[$ins->domain] = array($ins->item_created_on => 1);
                   }
               }
               $draw_graph = true; 
           }
           /*************graph for new backlink found*************/
           
           /*************Get Backlink Groups*************/
            $this->blcPrice = CsQuery::Cs_Get_results(array(
                'select' => '*,sum(price) as price,  c.id as cid',
                'from' => "{$wpdb->prefix}aios_blmanager_item_price as c",
                'join' => "left join {$wpdb->prefix}aios_blmanager_item_groups as g on g.id = c.item_id and g.group_type = 2",
                'where' => "c.type = 2",
                'group_by' => 'c.item_id'
            ));
            $this->webPrice = CsQuery::Cs_Get_results(array(
                'select' => '*,sum(price) as price,  c.id as cid',
                'from' => "{$wpdb->prefix}aios_blmanager_item_price as c",
                'join' => "left join {$wpdb->prefix}aios_blmanager_item_groups as g on g.id = c.item_id and g.group_type = 1",
                'where' => "c.type = 1",
                'group_by' => 'c.item_id'
            ));
             
            $total_blcPrice = CsQuery::Cs_Sum( array(
                'table' => "aios_blmanager_item_price",
                'sum_of' => 'price',
                'where' => 'type = 2'
            ));
            $total_web_price = CsQuery::Cs_Sum( array(
                'table' => "aios_blmanager_item_price",
                'sum_of' => 'price',
                'where' => 'type = 1'
            ));
           

           
           $data = array(
               'CommonText' => CommonText::common_text( array( $current_page, $current_tab ) ),
               
               'current_tab' => __( 'Dashboard', SR_TEXTDOMAIN ),
//               'CommonText' => CommonText::common_text(),
               'page_title' =>  __( 'Dashboard', SR_TEXTDOMAIN ),
               'page_subtitle' =>  __( 'Overview of backlink manager', SR_TEXTDOMAIN ),
               
               'label_p_count' =>  __( 'Websites', SR_TEXTDOMAIN ),
               'property_count' => CsQuery::Cs_Count( array( 'table' => 'aios_properties' ) ),
               'label_blc_count' =>  __( 'Backlinks', SR_TEXTDOMAIN ),
               'backlink_count' => CsQuery::Cs_Count(  array( 'table' => 'aios_backlinks' ) ),
               'label_404_count' =>  __( '404 Errors', SR_TEXTDOMAIN ),
               'count_404' => CsQuery::Cs_Count(  array( 'table' => 'aios_redirection_404' ) ),
               'label_redirection_count' =>  __( 'Redirection', SR_TEXTDOMAIN ),
               'count_redirection' => CsQuery::Cs_Count(  array( 'table' => 'aios_redirection_items' ) ),
               'label_internal_link' =>  __( 'Internal Links', SR_TEXTDOMAIN ),
               'label_backlink_tracking_graph_title' =>  __( 'BackLink Found', SR_TEXTDOMAIN ),
               'label_backlink_tracking_graph_subtitle' =>  __( 'BackLink found by date for the following properties.', SR_TEXTDOMAIN ),
               'no_backlink_found' =>  __( 'No Backlink Found Yet!', SR_TEXTDOMAIN ),
               'label_blc_price' =>  __( 'Total Cost of Backlinks', SR_TEXTDOMAIN ),
               'total_blc_price' => empty($total_blcPrice) ? '($0)' : '($'.$total_blcPrice.')',
               'label_website_price' =>  __( 'Total Cost of Websites', SR_TEXTDOMAIN ),
               'total_website_price' => empty($total_web_price) ? '($0)' : '($'.$total_web_price.')',
               'label_follow_nofollow_count' =>  __( 'Backlink Status', SR_TEXTDOMAIN ),
               'graph' => $draw_graph,
               'internal_link_rule_count' => CsQuery::Cs_Count(  array( 'table' => 'aios_internal_link_items' ) ),
            );
           
           add_action( 'admin_footer', array( $this, 'dashboard_footer_script') );
           
           return  view( '@CsSeoMegaBundlePack/backlinkManager/Dashboard.twig', $data );
            
        }
        
        
        
//        if(empty($current_tab_name) && $current_page_name == 'cs-backlink-manager'){
//            
//            $CommonText = new CommonText();
//
//            /*************graph for new backlink found*************/
//            $jan = date('Y-m-d', strtotime('3 months ago'));
//            $dec = date('Y-m-d');
//            
//            $getBlcObj = CsQuery::Cs_Get_Results( array(
//                'select' => 'b.item_created_on,p.item_domain as domain, b.item_domain as backlink',
//                'from' => "{$wpdb->prefix}aios_blmanager_items as p, {$wpdb->prefix}aios_blmanager_items as b",
//                'where' => "p.id = b.item_parent and b.item_created_on between '{$jan}' and '{$dec}'"       
//            ));
//           $draw_graph = false; 
//           if( $getBlcObj ){
//               foreach($getBlcObj as $ins ){
//                   if( in_array( $ins->domain, $this->items) ){
//                       $this->items[$ins->domain] = array($ins->item_created_on++);
//                   }else{
//                       $this->items[$ins->domain] = array($ins->item_created_on => 1);
//                   }
//               }
//               $draw_graph = true; 
//           }
//           /*************graph for new backlink found*************/
//           
//           /*************Get Backlink Groups*************/
//            $this->blcPrice = CsQuery::Cs_Get_results(array(
//                'select' => '*,sum(price) as price,  c.id as cid',
//                'from' => "{$wpdb->prefix}aios_blmanager_item_price as c",
//                'join' => "left join {$wpdb->prefix}aios_blmanager_item_groups as g on g.id = c.item_id and g.group_type = 2",
//                'where' => "c.type = 2",
//                'group_by' => 'c.item_id'
//            ));
//            $this->webPrice = CsQuery::Cs_Get_results(array(
//                'select' => '*,sum(price) as price,  c.id as cid',
//                'from' => "{$wpdb->prefix}aios_blmanager_item_price as c",
//                'join' => "left join {$wpdb->prefix}aios_blmanager_item_groups as g on g.id = c.item_id and g.group_type = 1",
//                'where' => "c.type = 1",
//                'group_by' => 'c.item_id'
//            ));
//             
//                
//            
//            $total_blcPrice = CsQuery::Cs_Sum( array(
//                'table' => "aios_blmanager_item_price",
//                'sum_of' => 'price',
//                'where' => 'type = 2'
//            ));
//            $total_web_price = CsQuery::Cs_Sum( array(
//                'table' => "aios_blmanager_item_price",
//                'sum_of' => 'price',
//                'where' => 'type = 1'
//            ));
//           
//           
//           
//           $data = array(
//               'current_tab' => __( 'Dashboard', SR_TEXTDOMAIN ),
//               'CommonText' => $CommonText->common_text(),
//               'page_title' =>  __( 'Dashboard', SR_TEXTDOMAIN ),
//               'page_subtitle' =>  __( 'Overview of backlink manager', SR_TEXTDOMAIN ),
//               'label_p_count' =>  __( 'Websites', SR_TEXTDOMAIN ),
//               'property_count' => CsQuery::Cs_Count( array( 'table' => 'aios_properties' ) ),
//               'label_blc_count' =>  __( 'Backlinks', SR_TEXTDOMAIN ),
//               'backlink_count' => CsQuery::Cs_Count(  array( 'table' => 'aios_backlinks' ) ),
//               'label_404_count' =>  __( '404 Errors', SR_TEXTDOMAIN ),
//               'count_404' => CsQuery::Cs_Count(  array( 'table' => 'aios_redirection_404' ) ),
//               'label_redirection_count' =>  __( 'Redirection', SR_TEXTDOMAIN ),
//               'count_redirection' => CsQuery::Cs_Count(  array( 'table' => 'aios_redirection_items' ) ),
//               'label_internal_link' =>  __( 'Internal Links', SR_TEXTDOMAIN ),
//               'label_backlink_tracking_graph_title' =>  __( 'BackLink Found', SR_TEXTDOMAIN ),
//               'label_backlink_tracking_graph_subtitle' =>  __( 'BackLink found by date for the following properties.', SR_TEXTDOMAIN ),
//               'no_backlink_found' =>  __( 'No Backlink Found Yet!', SR_TEXTDOMAIN ),
//               'label_blc_price' =>  __( 'Total Cost of Backlinks', SR_TEXTDOMAIN ),
//               'total_blc_price' => empty($total_blcPrice) ? '($0)' : '($'.$total_blcPrice.')',
//               'label_website_price' =>  __( 'Total Cost of Websites', SR_TEXTDOMAIN ),
//               'total_website_price' => empty($total_web_price) ? '($0)' : '($'.$total_web_price.')',
//               'label_follow_nofollow_count' =>  __( 'Backlink Status', SR_TEXTDOMAIN ),
//               'graph' => $draw_graph,
//               'internal_link_rule_count' => CsQuery::Cs_Count(  array( 'table' => 'aios_internal_link_items' ) ),
//            );
//           
//           add_action( 'admin_footer', array( $this, 'dashboard_footer_script') );
//           return  view( '@CsSeoMegaBundlePack/backlinkManager/Dashboard.twig', $data );
//           
//        }else{
//            $newClassPath = "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\BacklinkManager\\Tabs\\" . $current_tab_name;
//            if(class_exists($newClassPath)){
//                $newObj = new $newClassPath;
//                return $newObj->load_page_builder( $this->http );
//            }else{
//                $CommonText = new CommonText();
//                $data = array(
//                   'current_tab' => __( '', '' ),
//                   'CommonText' => $CommonText->common_text(),
//                   'page_title' =>  __( 'Error 404', SR_TEXTDOMAIN ),
//                   'page_subtitle' =>  __( 'Error page redirection', SR_TEXTDOMAIN ),
//                    
//                   'oops' =>  __( 'Whoops! Page not found!', SR_TEXTDOMAIN ),
//                   'not_found_msg' =>  __( 'This page cannot found or is missing.', SR_TEXTDOMAIN ),
//                   'dir_msg' =>  __( 'Use the navigation left or the button below to get back and track.', SR_TEXTDOMAIN ),
//                    
//                   'error_page_msg' =>  __( 'Sorry! we do not find the page you are looking for.', SR_TEXTDOMAIN ),
//                   'back_btn_label' =>  __( 'Back to Dashbaoard', SR_TEXTDOMAIN ),
//                   'back_btn_href' => admin_url('admin.php?page=cs-backlink-manager'),
//                );
//                return  view( '@CsSeoMegaBundlePack/error/error_404.twig', $data );
//            }
//        }
    }
    
    /**
     * 
     */
    public function dashboard_footer_script(){
        
//        pre_print($this->items);
        ?>
            <script type="text/javascript">
                <?php if( ! empty($this->items)) { ?>
                    var $c3_charts_colors = {
                        <?php $i =0; foreach($this->items as $property_url => $p_info ){ if($i>0) {echo ",\n"; } ?>'<?php echo $property_url; ?>' : "<?php echo $this->colors[$i]; ?>"
                        <?php $i++; } ?>
                    };
                /********************alexa ranking********************/
                    //labels(
                    var $c3_categories = [<?php $i =0; foreach($this->items as $property_url => $p_info ){ if($i>0) {echo ",\n"; } ?>'<?php echo isset(array_keys($p_info)[0]) ? array_keys($p_info)[0] : ''; ?>'<?php $i++; } ?>];
                    
                    var $c3_alexa_columns_data = [
                        <?php $r=0; foreach($this->items as $key => $val) { if($r>0) {echo ",\n";} ?> 
                            ['<?php echo $key;?>',<?php  echo isset(array_values($val)[0]) ? array_values($val)[0] : 0; ?>]    
                        <?php $r++; } ?>
                    ];
                /********************alexa ranking********************/
                <?php } 
                     if( ! empty( $this->blcPrice )) {
                ?>
                    //donut chart
                    var $donutData = [
                            <?php foreach($this->blcPrice as $item) {?>
                            {label: "<?php echo $item->group_name; ?>", value: <?php echo $item->price;?>},
                            <?php }?>
                        ];
                    var $donutColor = [ <?php echo implode(",", array_map( array($this, 'add_quotes'), $this->colors)); ?>];    
                 <?php } ?>
                     
                <?php if( ! empty( $this->webPrice )) { ?>
                    //donut chart
                    var $donutData1 = [
                            <?php foreach($this->webPrice as $item) {?>
                            {label: "<?php echo $item->group_name; ?>", value: <?php echo $item->price;?>},
                            <?php }?>
                        ];
                    var $donutColor1 = [ <?php shuffle($this->colors); echo implode(",", array_map( array($this, 'add_quotes'), $this->colors)); ?>];    
                 <?php } ?>
                     
            </script>
        <?php
    }
    
    function add_quotes( $str ){
        return sprintf( "'%s'", $str);
    }
    
    /**
     * Get Tab Types
     * 
     * @param type $tab
     * @return boolean|string
     */
    private function get_tab_type( $current_tab ){
        if( empty($current_tab)) return false;
        
        if(strpos( $current_tab, 'blc') !== false){
            return array(
                'BacklinkChecker',
                $function
            );
        }
    }
    
}

