<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager;
/**
 * Get getBacklinkData Data
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


class getBacklinkData {
    
    /**
     * Get Backlink
     * 
     * @global $wpdb
     * @param type $condition
     * @return boolean | Object
     */
    public function getBacklink( $condition = array() ){
        global $wpdb;
        $date_range = ''; $group_by = ''; $where = ''; $subQuery = ''; $order = '';
        if( isset( $condition['compare_by_date'] ) && empty( $condition['compare_by_date'] ) ) {
            $group_by = " GROUP BY p.item_domain ";
            $subQuery = "( SELECT * FROM `{$wpdb->prefix}aios_blmanager_item_assets` ORDER BY `created_on` DESC )";
            $order = " ORDER BY ps.created_on DESC ";
            $where = " where p.item_type = 2";
        }
        
        if( isset( $condition['compare_by_date'] ) && $condition['compare_by_date'] === true ) {
            $date_range = " and ps.created_on between '". $condition[ 'first_date' ] . "' and '". $condition[ 'last_date' ] ."' ";
            $subQuery = " `{$wpdb->prefix}aios_blmanager_item_assets` ";
            $order = " ORDER BY ps.created_on ASC ";
            $where = " where p.id in (".$condition['p_id'].") "  . ' and p.item_type = 2 ';
        }
       
        if( isset( $condition['group_id'] ) && ! empty( $condition['group_id'] ) ) {
            if(empty( $where) ){
                $where = ' where p.item_group_id = ' . $condition['group_id'] . ' and p.item_type = 2 ';
            }else{
                $where = $where . ' and p.item_group_id = ' . $condition['group_id'] . ' and p.item_type = 2';
            }
        }
        
        $query = $wpdb->get_results( "SELECT *,p.id as b_id FROM `{$wpdb->prefix}aios_blmanager_items` as p LEFT JOIN {$subQuery} AS ps ON p.id = ps.item_id {$date_range} {$where} {$group_by} {$order}" );
        if( $query ){
            return $query;
        }
        return false;
    }
}
