<?php namespace CsSeoMegaBundlePack\Models\KeywordManager\KeywordQuery;
/**
 * Get Keyword Data
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class KeywordQuery {
    
    /**
     * Get keywords 
     * 
     * @param array $condition
     * @return type
     */
    public static function get_Key_Words( $condition = array() ){
        $tables = CsQuery::from( array( 'd' => 'domains' ));
        
        //join keyword
        $join_keyword = CsQuery::join( array( array( 'k' => 'keywords'), 'k.domain_id = d.id', 'INNER' ) );
        
        //order by
        $order_by = CsQuery::order_by( 'kr.created_on DESC' );
        
        $where = '';
        if( $condition ){
            if( isset( $condition[ 'group_id' ] ) && $condition[ 'group_id' ] > 0 ){ // when filtering by group
                $where = CsQuery::where(array(
                    'd.url_group_id' => $condition[ 'group_id' ]
                ));
                //filter multitple keyword / get unique keyword
                $group_by = CsQuery::group_by( 'k.keyword' );
                
                //get latest ranking
                $join_ranking = "INNER JOIN (select * from " . CsQuery::from( 'keyword_rankings' ) . " ORDER BY created_on DESC) kr ON kr.keyword_id = k.id";
                
            }else if( isset( $condition[ 'first_date' ] ) && !empty( $condition[ 'first_date' ] ) ){ //when comparing by date
                
                if( isset( $condition[ 'k_id' ]) ){
                    $where = GeneralHelpers::Cs_Replace_First_Occur( 'AND', ' where ', CsQuery::where_in( array(
                        'k.id' => isset( $condition[ 'k_id' ] ) ? explode( ',', $condition[ 'k_id' ] ) : ''
                    )));
                }else if( isset($condition [ 'dasshboard' ] ) && !empty( $condition [ 'dasshboard' ]) ){
                    //for dashboard data
                    $where = " where k.auto_update = 1 ";
                }
                
                
                //get latest ranking
                $join_ranking = "INNER JOIN (select * from " . CsQuery::from( 'keyword_rankings' ) . "  ORDER BY created_on DESC) kr ON kr.keyword_id = k.id and kr.created_on between '". $condition[ 'first_date' ] . "' and '". $condition[ 'last_date' ] ."' ";
                
                //get unique date - one row for each day
                $group_by = CsQuery::group_by( 'kr.created_on' );
            }
            
        }else{
            //get latest ranking
            $join_ranking = "INNER JOIN (select * from " . CsQuery::from( 'keyword_rankings' ) . " ORDER BY created_on DESC) kr ON kr.keyword_id = k.id";
            //filter multitple keyword / get unique keyword
            $group_by = CsQuery::group_by( 'k.keyword, k.domain_id' );
        }
        
        $query = " SELECT *,k.id as kid FROM {$tables}{$join_keyword} {$join_ranking} {$where} {$group_by} {$order_by}"; 
        $rows = CsQuery::Cs_Query( $query );
        return empty( $rows->num_rows ) ? false : $rows;
    }

    

    /**
     * Get Property
     * 
     * @since 1.0.0
     * @global $wpdb
     * @return boolean | obj
     */
    public static function getKeywords(  $condition = array()  ){
        global $wpdb;
        $date_range = ''; $group_by = ''; $where = ''; $subQuery = ''; $order = '';
        if( isset( $condition['compare_by_date'] ) && empty( $condition['compare_by_date'] ) ) {
            $group_by = " GROUP BY k.keyword ";
//            $group_by = "  ";
            $subQuery = "( SELECT * FROM `{$wpdb->prefix}aios_blmanager_item_assets` ORDER BY `created_on` DESC )";
            $order = " ORDER BY k.created_on DESC ";
            $where = " where p.item_type = 5";
        }
        
        if( isset( $condition['compare_by_date'] ) && $condition['compare_by_date'] === true ) {
            $join_date_range = " and n.created_on between '". $condition[ 'first_date' ] . "' and '". $condition[ 'last_date' ] ."' ";
            $subQuery = " LEFT JOIN `{$wpdb->prefix}aios_blmanager_item_assets` as n on m.keyword = n.keyword {$join_date_range}";
            $order = " ORDER BY m.created_on ASC ";
            $where = " where m.id in (".$condition['p_id'].") "  . ' ';
            $date_range = " and m.created_on between '". $condition[ 'first_date' ] . "' and '". $condition[ 'last_date' ] ."' ";
            
//            echo "SELECT * from `{$wpdb->prefix}aios_blmanager_item_assets` as m $subQuery $where $date_range $order";
//            exit;
            
            $query = $wpdb->get_results( "SELECT * from `{$wpdb->prefix}aios_blmanager_item_assets` as m $subQuery $where $date_range $order" );
            if( $query ){
                return $query;
            }
            return false;
        }
       
        if( isset( $condition['group_id'] ) && ! empty( $condition['group_id'] ) ) {
            if(empty( $where) ){
                $where = ' where p.item_group_id = ' . $condition['group_id'] . ' and p.item_type = 5 ';
            }else{
                $where = $where . ' and p.item_group_id = ' . $condition['group_id'] . ' and p.item_type = 5 ';
            }
        }
        $query = $wpdb->get_results( "SELECT *,p.id as p_id FROM `{$wpdb->prefix}aios_blmanager_items` as p LEFT JOIN {$subQuery} AS k ON p.id = k.item_id and k.item_id != '' {$date_range} {$where} {$group_by} {$order}" );
        if( $query ){
            return $query;
        }
        return false;
    }
    
}
