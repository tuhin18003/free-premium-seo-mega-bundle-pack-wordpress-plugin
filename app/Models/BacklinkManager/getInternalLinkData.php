<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager;

/**
 * Get getBacklinkData Data
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


class getInternalLinkData {
    
    /**
     * Get Category Data
     * 
     * @global object $wpdb
     * @return array
     */
    public function getGroups( $filter_conditions = false ){
        global $wpdb;
        
        $cat_filter = " ";
        if( !empty($filter_conditions) ){
            $cat_filter = trim( wp_kses( stripslashes( $filter_conditions ), array() ) );
        }
        $getCatArr = $wpdb->get_results("select g.*,count(ri.id) as rule_count, count(ri.id) as rule_count from {$wpdb->prefix}aios_internal_link_groups as g  left join {$wpdb->prefix}aios_internal_link_items as ri on ri.group_id = g.id $cat_filter GROUP BY g.id  order by name asc");
        if($getCatArr){
            return $getCatArr;
        }
        return false;
    }
    
    /**
     * Get Post Count
     * 
     * @global \CsSeoMegaBundlePack\Models\BacklinkManager\$wpdb $wpdb
     * @param string $tbl_name
     * @return int
     */
    public function getPostCount( $tbl_name, $where ){
        global $wpdb;
        $row_count = $wpdb->get_var("select count(*) from {$wpdb->$tbl_name} $where ");
        return empty($row_count) ? 0 : $row_count;
    }
    
    /**
     * Get Internal link Rules
     * 
     * @global object $wpdb
     * @param type $filter_cat_id
     * @return boolean
     */
    public function getInternalLinkRules( $filter_cat_id = false ){
        global $wpdb;
        
        $cat_filter = " ";
        if( !empty($filter_cat_id) && $filter_cat_id == 'inactive'){
            $cat_filter = " where group_id = '{$filter_cat_id}' ";
        }else if( !empty($filter_cat_id)){
            $cat_filter = " where group_id in ({$filter_cat_id})";
        }
        
        $getCatArr = $wpdb->get_results("select * from {$wpdb->prefix}aios_internal_link_items  $cat_filter");
        if($getCatArr){
            return $getCatArr;
        }
        return false;
    }
    
}
