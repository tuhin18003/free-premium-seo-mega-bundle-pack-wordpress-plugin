<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager;

/**
 * Get Redirection Data
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


class getRedirectionData {
    
    /**
     * Get Post Count
     * 
     * @global \CsSeoMegaBundlePack\Models\BacklinkManager\$wpdb $wpdb
     * @param string $tbl_name
     * @return int
     */
    public function getPostCount( $tbl_name, $where ){
        global $wpdb;
        $row_count = $wpdb->get_var("select count(*) from {$wpdb->prefix}$tbl_name $where ");
        return empty($row_count) ? 0 : $row_count;
    }
    
    /**
     * Get All Redirections
     * 
     * @global \CsSeoMegaBundlePack\Models\BacklinkManager\$wpdb $wpdb
     * @param type $filter_cat_id
     * @return boolean | array
     */
    public function getRedirectionRules( $filter_cat_id = false ){
        global $wpdb;
        
        $cat_filter = " ";
        if( !empty($filter_cat_id) ){
            $cat_filter = " where group_id = '{$filter_cat_id}' ";
        }
        
        $getCatArr = $wpdb->get_results("select * from {$wpdb->prefix}aios_redirection_items $cat_filter order by id desc");
        if($getCatArr){
            return $getCatArr;
        }
        
        return false;
    }
    
    /**
     * Get All Redirection Groups
     * 
     * @global $wpdb
     * @param type $filter_cat_id
     * @return boolean | array
     */
    public function getRedirectionGroups( $filter_cat_id = false ){
        global $wpdb;
        
        $cat_filter = " ";
        if( !empty($filter_cat_id) ){
            $cat_filter = " where module_id = '{$filter_cat_id}' ";
        }
        $getCatArr = $wpdb->get_results("select g.*,count(ri.id) as rule_count, count(ri.id) as rule_count from {$wpdb->prefix}aios_redirection_groups as g left join {$wpdb->prefix}aios_redirection_items as ri on ri.group_id = g.id GROUP BY g.id $cat_filter order by name asc");
//        pre_print($getCatArr);
        if($getCatArr){
            return $getCatArr;
        }
        return false;
    }
    
    /**
     * Get all 404 erros
     * 
     * @global \CsSeoMegaBundlePack\Models\BacklinkManager\$wpdb $wpdb
     * @return boolean | obj
     */
    public function get404Errors(){
        global $wpdb;
        $query = $wpdb->get_results(" select * from {$wpdb->prefix}aios_redirection_404 order by id desc");
        if($query){
            return $query;
        }
        return false;
    }
    
    /**
     * Delete 404 Errors Log
     * 
     * @global $wpdb
     * @param String $log_ids   seperated by comma
     * @return boolean
     */
    public function delete404Errors( $log_ids ){
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}aios_redirection_404 WHERE id in (%s)", $log_ids) );
        return true;
    }
    
}
