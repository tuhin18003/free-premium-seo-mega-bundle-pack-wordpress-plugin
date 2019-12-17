<?php namespace CsSeoMegaBundlePack\Models\CommonQuery;

/**
 * Common Custom Query
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class CsQuery {
    
    /**
     * Hold table prefix
     *
     * @var type 
     */
    public static $tbl_prefix;
    
    public static $where_count = 0;


    public function __construct() {
        
        //get db table prefix
        self::$tbl_prefix = Helper::get('PLUGIN_TBL_PREFIX');
    }
    
    /**
     * Get Results
     * 
     * @global Object $wpdb
     * @param array $args array( 'select' => '*,field_name', 'from' => 'table name', 'where' => 'where query', 'query_type' => ' get_var | get_row | empty' )
     * @return boolean|Object
     */
    public static function Cs_Get_Results( $args = array() ){
        global $wpdb;
        
        //reset old data
        self::$where_count = 0;
        
        //query version 1.0.1
        if( isset($args[ 'query_var' ]) && $args[ 'query_var' ] === 101 ){
            
            //custom table prefix
            if( isset( $args['default_tbl_prefix'] ) ){
                self::$tbl_prefix = $wpdb->prefix;
                unset( $args['default_tbl_prefix'] );
            }
            
            $select = isset( $args['select'] ) ? self::Cs_Clean_Data( $args['select'] ) : '';
            $query = '';
            foreach( $args as $function => $value ){
                if( preg_match( "/-?[0-9]+$/", $function) === 1 ){ //if multiple
                    $function = preg_replace( "/-?[_][0-9]+$/",'', $function);
                }
                if(method_exists( __CLASS__, $function ) ){
                    $query .= self::$function( $value );
                }
            }
            
            $query = "SELECT {$select} FROM {$query} ";
            
            if( isset($args[ 'debug' ] ) && true === $args[ 'debug' ] ){
                return $query;
            }
            
            if( isset( $args['query_type'] ) && $args['query_type'] == 'get_var' ){
                $result = $wpdb->get_var( $query );
            }
            else if( isset( $args['query_type'] ) && $args['query_type'] == 'get_row' ){
                $result = $wpdb->get_row( $query );
            }else{
                $result = $wpdb->get_results( $query );
                $result = (object) array(
                    'rows' => $result,
                    'num_rows' => $wpdb->num_rows
                );
            }
            return empty( $result ) ? false : $result;
            
        }else{
            
            $select = isset( $args['select'] ) ? $args['select'] : '';
            $from = isset( $args['from'] ) ? $args['from'] : '';
            $join = isset( $args['join'] ) ? $args['join'] : '';
            $where = isset( $args['where'] ) ? 'where '.$args['where'] : '';
            $group_by = isset( $args['group_by'] ) ? 'GROUP BY '.$args['group_by'] : '';
            $order_by = isset( $args['order_by'] ) ? 'ORDER BY '.$args['order_by'] : '';
            $query = 'SELECT '. $select .' FROM '. $from .' '. $join .' '. $where .' '. $group_by .' '. $order_by;
            if( !empty( $select ) && !empty( $from ) ){
                if( isset( $args['query_type'] ) && $args['query_type'] == 'get_var' ){
                    $result = $wpdb->get_var( $query );
                }
                else if( isset( $args['query_type'] ) && $args['query_type'] == 'get_row' ){
                    $result = $wpdb->get_row( $query );
                }else{
                    $result = $wpdb->get_results( $query );
                    if( isset($args[ 'num_rows' ]) && $args[ 'num_rows' ] === true ){
                        $result = (object) array(
                            'rows' => $result,
                            'num_rows' => $wpdb->num_rows
                        );
                    }
                }
                return empty( $result ) ? false : $result;
            }
            
        }
        
        return false;
    }
    
    /**
     * Get Post meta
     * 
     * @param type $post_id
     * @param type $meta_key
     * @return type
     */
    public static function Cs_Get_Postmeta( $post_id, $meta_key, $single = true ){
        return get_post_meta( $post_id, $meta_key, $single);
    }
    
    /**
     * Update post meta
     * 
     * @param type $post_id
     * @param type $meta_key
     * @param type $meta_value
     * @return type
     */
    public static function Cs_Update_Postmeta( $post_id, $meta_key, $meta_value ){
        return update_post_meta( $post_id, $meta_key, $meta_value );
    }
    
    /**
     * Update term meta
     * 
     * @param type $term_id
     * @param type $meta_key
     * @param type $meta_value
     * @return type
     */
    public static function Cs_UpdateTermmeta( $term_id, $meta_key, $meta_value ){
        return update_term_meta( $term_id, $meta_key, $meta_value );
    }

    /**
     * Insert
     * 
     * @global Object $wpdb
     * @param Array $args  $args = array( 'table' => 'table name', 'insert_data' => array( 'title' => 'title value' ) )
     * @return boolean | Int Inserted ID
     */
    public static function Cs_Insert( $args = array() ){
        global $wpdb;
        
        //query version 1.0.1
        if( isset($args[ 'query_var' ]) && $args[ 'query_var' ] === 101 ){
            $tbl = isset( $args[0]) ? self::from( $args[0] ) : '';
            $insert_data = isset( $args[1]) ? self::check_evil_script( $args[1] ) : '';
        }else{
            $tbl = $wpdb->prefix . $args['table'];
            $insert_data = isset( $args['insert_data']) ? $args['insert_data'] : '';
        }
        $wpdb->insert( $tbl, $insert_data );
        return isset( $wpdb->insert_id ) ? $wpdb->insert_id : false;
    }
    
    /**
     * Update
     * 
     * @global Object $wpdb
     * @param Array $args $args = array( 'table' => 'table name', 'update_data' => array( 'title' => 'new title' ), 'update_condition' => array( 'id' => 1 ) )
     * @return boolean
     */
    public static function Cs_Update( $args = array() ){
        global $wpdb;
        $format = isset( $args['format'] ) ? $args['format'] : null;
        $where_format = isset( $args['where_format'] ) ? $args['where_format'] : null;
        
        //query version 1.0.1
        if( isset($args[ 'query_var' ]) && $args[ 'query_var' ] === 101 ){
            $tbl = isset( $args[0]) ? self::from( $args[0] ) : '';
            $update_data = isset( $args[1]) ? self::check_evil_script( $args[1] ) : '';
            $update_condition = isset( $args[2]) ? self::check_evil_script( $args[2] ) : '';
        }else{
            $tbl = $wpdb->prefix . $args['table']; //will be removed from next version
            $update_data = $args['update_data'];
            $update_condition = $args['update_condition'];
        }
        
        return $wpdb->update( $tbl, $update_data, $update_condition, $format, $where_format );
    }
    
    /**
     * update In Query
     * 
     * @global Object $wpdb
     * @param type $args    $args = array( "table" => "tbl_name", 'where' => array( 'field_name' => 'id', 'field_value' => '1,2,3,4,5' ))
     * @return boolean
     */
    public static function Cs_Update_In( $args = array() ){
        global $wpdb;
        
        if( isset( $args[ 'query_var' ] ) && $args[ 'query_var' ] === 101 ){
            $table = isset( $args[ 0 ] ) ? self::from( $args[ 0 ] ) : '';
            $query = is_array( $args[ 1 ] ) ? self::set_value_update_data_helper( $args[ 1 ] ) : '';
            $query .= GeneralHelpers::Cs_Replace_First_Occur( 'AND', 'Where', self::where_in( $args[ 2 ] ) );
        }else{
            $table = $wpdb->prefix . $args['table'];
            $set_val = " SET " . $args['update'];
            $where_field = $args['where']['field_name']; 
            $query = "$set_val WHERE {$where_field} in (".$args['where']['field_value'] .")";
        }
        
        return $wpdb->query( "UPDATE `$table` $query " );
    }
    
    /**
     * Set Values for update rows
     * 
     * @param array $values
     * @return boolean
     */
    public static function set_value_update_data_helper( array $values ){
        if( ! is_array( $values ) ) return false;
        $ret_values = '';
        $i = 0;
        foreach( $values as $key => $val ){
            if( $i > 0 ) $ret_values .= ', ';
            $ret_values .= self::where_col_val_generate( $key, $val );
            $i++;
        }
        return " SET {$ret_values} ";
    }
    
    /**
     * Delete In Query
     * 
     * @global Object $wpdb
     * @param type $args    $args = array( "table" => "tbl_name", 'where' => array( 'field_name' => 'id', 'field_value' => '1,2,3,4,5' ))
     * @return boolean
     */
    public static function Cs_Delete_In( $args = array() ){
        global $wpdb;
        if( isset( $args[ 'query_var' ] ) && $args[ 'query_var' ] === 101 ){
            $table = isset( $args[ 0 ] ) ? self::from( $args[ 0 ] ) : '';
            $condition = GeneralHelpers::Cs_Replace_First_Occur( 'AND', 'Where', self::where_in( $args[ 1 ] ) );
            return isset( $args[ 0 ] ) ? $wpdb->query( "DELETE FROM `{$table}` {$condition} " ) : '';
            
        }else{
            $table = $wpdb->prefix . $args['table'];
            $where_field = $args['where']['field_name']; 
            return $wpdb->query( "DELETE FROM `$table` WHERE {$where_field} in (".$args['where']['field_value'] .")" );
        }
        return false;
    }
    
    /**
     * Delete
     * 
     * @global Object $wpdb
     * @param array $args $args = array( 'table' => 'table name', 'where' => array( 'id' => 1 ))
     * @return boolean
     */
    public static function Cs_Delete( $args = array() ){
        global $wpdb;
        if( isset( $args[ 'query_var'] ) && $args[ 'query_var'] === 101 ){
            $tbl = self::from( $args[ 0 ] );
            $where = is_array( $args[ 1 ] ) ? $args[ 1 ] : '';
        }else{
            $tbl = $wpdb->prefix . $args['table'];
            $where = $args['where'];
        }
        return empty( $where ) ? '' : $wpdb->delete( $tbl, $where );
    }
    
    /**
     * Count
     * 
     * @global Object $wpdb
     * @param Array $args $args = array( 'table' = 'table name', 'where' => ' where id = 1 ' )
     * @return Int
     */
    public static function Cs_Count( $args = array() ){
        global $wpdb;
        
        if( isset($args[ 'query_var' ]) && $args[ 'query_var' ] === 101 ){
            $table = self::from( $args['table'] );
        }else{
            $table = $wpdb->prefix . $args['table'];
        }
        
        $where = '';
        if( isset ( $args[ 'where' ] ) && !empty( $args[ 'where' ] ) ){
            $where = ' where '. trim($args[ 'where' ]);
        }
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` $where " );
        return empty( $count ) ? 0 : $count;
    }
    
    /**
     * Sum
     * 
     * @global Object $wpdb
     * @param Array $args $args = array( 'table' = 'table name', 'sum_of' => 'column name', 'where' => ' where id = 1 ' )
     * @return Int
     */
    public static function Cs_Sum( $args = array() ){
        global $wpdb;
        $table = $wpdb->prefix . $args['table'];
        $where = '';
        if( isset ( $args[ 'where' ] ) && !empty( $args[ 'where' ] ) ){
            $where = ' where '. trim($args[ 'where' ]);
        }
        $sum_col = trim($args['sum_of']);
        $count = $wpdb->get_var( "SELECT sum($sum_col) FROM `{$table}` $where " );
        return empty( $count ) ? 0 : $count;
    }
    
    /**
     * Get Options
     * 
     * @param type $args
     * @return string | Json object
     */
    public static function Cs_Get_Option( $args = array() ){
        $get_option = get_option( $args['option_name'] );
        if( isset($args['json']) && $args['json'] === true ){
            $json_array = isset($args['json_array']) ? true : false;
            $get_option = json_decode($get_option, $json_array);
        }
        return $get_option;
    }
    
    /**
     * Update Options
     * 
     * @param type $args
     * @return string | Json object
     */
    public static function Cs_Update_Option( $args = array() ){
        $get_option = $args['option_value'];
        if( isset($args['json']) && $args['json'] === true ){
            $get_option = json_encode($args['option_value']);
        }
        //autoload false
        return update_option( $args['option_name'], $get_option, false );
    }
    
    /**
     * 
     * @global \CsSeoMegaBundlePack\Models\CommonQuery\Object $wpdb
     * @param type $table_name
     * @return typeTruncate Table
     */
    public static function Cs_Truncate( $tables ){
        global $wpdb;
        $tbl = self::from($tables);
        return $wpdb->query( "TRUNCATE TABLE $tbl" );
    }

    /**
     * Direct query
     * 
     * @global \CsSeoMegaBundlePack\Models\CommonQuery\Object $wpdb
     * @param type $sql
     * @return type
     */
    public static function Cs_Query( $query ){
        global $wpdb;
        $result = $wpdb->get_results( $query );
        $result = (object) array(
            'rows' => $result,
            'num_rows' => $wpdb->num_rows
        );
        return empty($result->rows) ? false : $result;
    }

        /**
     * Check Evil Script Into User Input
     * 
     * @param array|string $user_input
     * @return type
     */
    public static function check_evil_script( $user_input, $textarea = false ){
        if( is_array( $user_input )){
            if( $textarea === true){
                $user_input = array_map( 'sanitize_textarea_field', $user_input);
            }else{
                $user_input = array_map( 'sanitize_text_field', $user_input);
            }
            $user_input = array_map( 'stripslashes_deep', $user_input);
        }else{
            if( $textarea === true){
                $user_input = sanitize_textarea_field( $user_input );
            }else{
                $user_input = sanitize_text_field( $user_input );
            }
            $user_input = stripslashes_deep( $user_input );
            $user_input = trim($user_input);
        }
        return $user_input;
    }
    
    
    /**
     * Clean data
     * 
     * @since 1.0.0
     */
    public static function Cs_Clean_Data( $data ){
        if( is_array( $data )){
            $data = array_map( 'trim', $data);
            $data = array_map( 'htmlspecialchars', $data);
            $data = array_map( 'stripcslashes', $data);
            $data = array_map( 'wp_kses', $data, array());
        }else{
            $data = trim( $data );
            $data = htmlspecialchars( $data );
            $data = stripcslashes( $data );
            $data = wp_kses( $data, array());
        }
        return $data;
    }
    
    /**
     * Query format for select tables
     * 
     * @param array $tables  array( 'posts' ) | array( 'p' => 'posts' ) | array key will be used 'posts as p' 
     * @return boolean
     */
    public static function from( $tables ){
        if( $tables ){
            if( ! isset( self::$tbl_prefix ) ){
                //get db table prefix
                self::$tbl_prefix = Helper::get('PLUGIN_TBL_PREFIX');
            }
            $from = '';
            if( is_array( $tables ) ){
                $i = 0;
                foreach( $tables as $as => $table ){
                    if( $i > 0 ) $from .= ', ';
                    $from .= self::$tbl_prefix . trim( $table );
                    if( ! is_integer( $as ) ){
                        $from .= " as {$as}";
                    }
                    $i++;
                }
            }else{
                $from = self::$tbl_prefix . trim( $tables );
            }
            return $from;
        }
        return false;
    }
    
    /**
     * Generate where
     * 
     * @param type $where
     * @return boolean
     */
    public static function where( $where ){
        if( $where ){
            if( self::$where_count >0 ){
                $where_query = ' AND ';
            }else{
                $where_query = ' WHERE ';
            }
            self::$where_count++;
            
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                    $where_query .= self::where_col_val_generate( $key, $val );
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * Generate or where
     * 
     * @param type $where
     * @return boolean
     */
    public static function or_where( $where ){
        if( $where ){
            $where_query = ' OR ';
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                    $where_query .= self::where_col_val_generate( $key, $val );
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * where column and value generate
     * 
     * @param type $key
     * @param type $val
     */
    public static function where_col_val_generate( $key, $val ){
        $where = '';
        $key = trim( $key );
        if( false !== GeneralHelpers::Cs_Multi_Strpos( $key, array( '>', '<', '>=', '<=', '!=' ) ) ){
            $where = $key;
        }else{
            $where = " {$key} = ";
        }
        
        
        if( false !== GeneralHelpers::Cs_Multi_Strpos( $val, array( '`' ) ) ){
            $val = $val;
        }else{
            $val = is_integer( $val ) ? $val : " '{$val}' ";
        }
        
        return $where .= $val ;
    }
    
    /**
     * Generate where in
     * 
     * @param array $where array( 'id' => array( 1, 2, 3) )
     * @return boolean
     */
    public static function where_in( $where ){
        if( $where ){
            $where_query = ' AND ';
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                    $where_query .= self::generate_where_in($key, $val);
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * Generate or where in
     * 
     * @param type $where
     * @return boolean
     */
    public static function or_where_in( $where ){
        if( $where ){
            $where_query = ' OR ';
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                        $where_query .= self::generate_where_in($key, $val);
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * where in helper
     * 
     * @param type $key
     * @param type $val
     * @return type
     */
    public static function generate_where_in( $key , $val ){
        $in = '';
        if( is_array( $val ) ){
            for( $j = 0; $j <count( $val ); $j++){
               if( $j > 0) $in .= ',';
               $in .= is_integer( $val[$j] ) ? $val[$j] : " '{$val[$j]}' " ;
            }
        }else{
            $in = is_integer( $val ) ? $val : " '{$val}' " ;
        }
        return " {$key} IN ({$in})";
    }
    
    /**
     * Generate where not in
     * 
     * @param type $where
     * @return boolean
     */
    public static function where_not_in( $where ){
        if( $where ){
            $where_query = ' AND ';
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                    $where_query .= self::generate_where_not_in($key, $val);
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * Generate or where not in
     * 
     * @param type $where
     * @return boolean
     */
    public static function or_where_not_in( $where ){
        if( $where ){
            $where_query = ' OR ';
            if( is_array( $where ) ){
                $i = 0;
                foreach( $where as $key => $val ){
                    if( $i > 0 ) $where_query .= ' AND ';
                    $where_query .= self::generate_where_not_in($key, $val);
                    $i++;
                }
            }else{
                $where_query = self::Cs_Clean_Data( $where );
            }
            return $where_query;
        }
        return false;
    }
    
    /**
     * where in helper
     * 
     * @param type $key
     * @param type $val
     * @return type
     */
    public static function generate_where_not_in( $key , $val ){
        $in = '';
        if( is_array( $val ) ){
            for( $j = 0; $j <count( $val ); $j++){
               if( $j > 0) $in .= ',';
               $in .= is_integer( $val[$j] ) ? $val[$j] : " '{$val[$j]}' " ;
            }
        }else{
            $in = is_integer( $val ) ? $val : " '{$val}' " ;
        }
        return " {$key} NOT IN ({$in})";
    }
    
    /**
     * Join Query
     * 
     * @param array $join array( 'tbl', 'condition', 'left')
     * @return boolean|string
     */
    public static function join( $join ){
        if( $join ){
            $join_query = ' ';
            $i = 0;
            foreach( $join as $data ){
                if( is_array( $data ) ){
                    if( $i > 0 ) $join_query .= ' ';
                    $join_query .= isset( $data[ 2 ] ) ? $data[ 2 ] : ( isset( $join[ 2 ] ) ? $join[ 2 ] : '' ); //join type
                    $join_query .= ' JOIN '; 
                    $join_query .= isset( $data[ 0 ] ) ? self::from( $data[ 0 ] )  : ( isset( $join[ 0 ] ) ? self::from( $join[ 0 ] )  : '' ); //table name
                    $join_query .= ' ON '; 
                    $join_query .= isset($data[ 1 ]) ? $data[ 1 ] : ( isset($join[ 1 ]) ? $join[ 1 ] : '' ); //condition
                }
                $i++;
            }
            return $join_query;
        }
        return false;
    }
    
    /**
     * Order by data
     * 
     * @param string $order_by  id desc
     * @return boolean
     */
    public static  function order_by( $order_by ){
        if( $order_by ){
            return ' ORDER BY '. self::Cs_Clean_Data( $order_by );
        }
        return false;
    }
    
    /**
     * Group by data
     * 
     * @param string $group_by group by id
     * @return boolean
     */
    public static  function group_by( $group_by ){
        if( $group_by ){
            return ' GROUP BY '. self::Cs_Clean_Data( $group_by );
        }
        return false;
    }
    
    /**
     * Limit data
     * 
     * @param string $group_by group by id
     * @return boolean
     */
    public static  function limit( $limit ){
        if( $limit ){
            return ' LIMIT '. self::Cs_Clean_Data( $limit );
        }
        return false;
    }
    
    /**
     * Combine meta keys & values
     * 
     * @param type $posts
     * @return type
     */
    public static function Cs_Combine_Meta_Keys( $posts ){
        $prefix = Helper::get( 'PLUGIN_DATA_PREFIX' );
        $meta_keys = explode('___', str_replace( $prefix, '', $posts->meta_keys ));
        $meta_values = array_map('maybe_unserialize',explode('___',$posts->meta_values));
        $min_combine = min(count($meta_keys), count($meta_values));
        return array_combine( array_slice( $meta_keys, 0, $min_combine ) , array_slice( $meta_values, 0, $min_combine ));
    }
 
    /**
     * Get post custom with metas
     * 
     * @param type $post_id
     * @return type
     */
    public static function Cs_Get_Post_Custom( $post_id ){
        $posts = array();
        $prefix = Helper::get( 'PLUGIN_DATA_PREFIX' );
        $posts = (array)$GLOBALS['wp_query']->get_queried_object();         
        $metas = get_post_custom();
        $posts['url'] = get_the_permalink( $post_id );
        if( isset( $posts['post_author'] ) && !empty( $author = $posts['post_author'] ) ){
            $posts['author'] = array(
                'first_name' => get_the_author_meta( 'first_name', $author ),
                'last_name' => get_the_author_meta( 'last_name', $author ),
                'nickname' => get_the_author_meta( 'nickname', $author ),
                'display_name' => get_the_author_meta( 'display_name', $author )
            );
        }
        $post = array_merge( array('wp_options' => $posts), array( "{$prefix}options" => GeneralHelpers::meta_key_filter( $metas, $prefix ) ));
        $json = json_encode( $post );
        return json_decode( $json );
//        return $obj->post_title = wp_title();
//        pre_print( $obj );
//        echo 'sdf'; exit;
        
//        $post_row = self::Cs_Get_Results(array(
//            'default_tbl_prefix' => true,
//            'select' => "p.*, GROUP_CONCAT(pm.meta_key ORDER BY pm.meta_key DESC SEPARATOR '___') as meta_keys,  GROUP_CONCAT(pm.meta_value ORDER BY pm.meta_key DESC SEPARATOR '___') as meta_values",
//            'from' => array( 'p' => 'posts' ),
//            'join' => array(
//                array( 'pm' => 'postmeta'), " p.ID = pm.post_id and pm.meta_key like '{$prefix}%' ", 'LEFT'
//            ),
//            'where' => array( 'p.ID' => $post_id ),
//            'group_by' => 'p.ID',
//            'limit'=> 1,
//            'query_var' => 101
//        ));
//        
//
//        
////        pre_print( $arr );
////        echo $time_elapsed_secs = microtime() - $start;
////        pre_print( $arr );
//                
//                
//        if( !isset( $post_row->rows[0]->meta_keys ) ) {
//            return isset( $post_row->rows[0] ) && !empty( $post_row->rows[0] ) ? $post_row->rows[0] : array();
//        }       
//        
//        //check post type
//        if( isset( $post_row->rows[0]->post_type) && $post_row->rows[0]->post_type == 'product'){
//            $post_row->rows[0]->post_content = $post_row->rows[0]->post_excerpt;
//        }
//        
////        pre_print($post_row->rows);
//        
//        $metas =  array_map( array( __CLASS__, 'Cs_Combine_Meta_Keys' ), $post_row->rows );
//        unset($post_row->rows[0]->meta_keys);
//        unset($post_row->rows[0]->meta_values);
//        if( isset($metas[0]['sharing_url']) && empty($metas[0]['sharing_url'])){
//            unset($metas[0]['sharing_url']);
//            $post_row->rows[0]->sharing_url = get_the_permalink( $post_id );
//        }
//        
//        return (object) array_merge( (array) $post_row->rows[0], (array)$metas[0] ); 
    }
    
    /**
     * Get Custom Term Object
     * 
     * @return type
     */
    public static function Cs_GetTermCustom(){
        $prefix = Helper::get( 'PLUGIN_DATA_PREFIX' );
        $term = $GLOBALS['wp_query']->get_queried_object();
        $metas =  get_term_meta( $GLOBALS['wp_query']->get_queried_object_id() );
        $sharing_url = get_term_link( $term );
        $term = array_merge( (array)$term, array( 'url' => $sharing_url )  );
        $term['is_term'] = true;
        $termData = array_merge( array('wp_options' => $term), array( "{$prefix}options" => GeneralHelpers::meta_key_filter( $metas, $prefix ) ));
        $json = json_encode( $termData );
        return json_decode( $json );
    }
}
