<?php namespace CsSeoMegaBundlePack\Models\KeywordManager;

/**
 * My Website Menu - Actions Handler
 * 
 * @package Keyword Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\CsFlusher;
//use AiosSerp\Services\Google as Google;
use CsSeoMegaBundlePack\Library\SerpTracker\Services\Google;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Helper;

class KeywordHandler{
    
    /**
     * Hold Http
     *
     * @var type 
     */
    
    protected $http;
    
    /**
     * Hold table prefix
     *
     * @var type 
     */
    protected $tbl_prefix;


    public function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
        
        //get db table prefix
        $this->tbl_prefix = Helper::get('PLUGIN_TBL_PREFIX');
    }
    
    /**
     * Add New Keywords
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function add_keyword(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-add-new-keywords', $_aios_nonce, true );
        
        $new_keywords = CsQuery::Cs_Clean_Data( $this->http->get('new_keywords', false) ); 
        $keyword_domain = CsQuery::Cs_Clean_Data( $this->http->get('keyword_domain', false) );
        $auto_update = $this->http->get('auto_update_status', false);
        
        
        //create or get group
        $group_id = '';
        if ( $this->http->has('group') ) {
            $group_id = $this->http->get( 'group', false);
        }
        
        $new_group_name = '';
        if ( $this->http->has('group') ) {
            $new_group_name = CsQuery::Cs_Clean_Data( $this->http->get( 'new_group_name', false) );
        }

        if( $group_id === 'new'){
            $get_cat_id = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => 'groups',
                'where' => array( 'name' => $new_group_name, 'type' => 5 ),
                'query_type' => 'get_row',
                'query_var' => 101
            ));

            if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                $group_id = $get_cat_id->id;
            }else{
                $insert_group = array(
                    'groups',
                    array(
                        'name' => $new_group_name,
                        'type' => 5,
                        'created_on' => date('Y-m-d H:i:s')
                    ),
                    'query_var' => 101
                );
                $group_id = CsQuery::Cs_Insert( $insert_group );
            }
        }
        //insert domain
        if( $keyword_domain ){
            $get_domain = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => 'domains',
                'where' => array( 'url' => $keyword_domain, 'url_type' => 5 ),
                'query_type' => 'get_row',
                'query_var' => 101
            ));
           if( !isset( $get_domain->id ) ) {
               $domain_id = CsQuery::Cs_Insert( array(
                    'domains',
                    array(
                       'url' => $keyword_domain,
                       'url_type' => 5,
                       'url_group_id' => $group_id
                    ),
                   'query_var' => 101
               ) );
           }else{
               $domain_id = isset($get_domain->id) ? $get_domain->id : '';
           } 
        }
        
        //insert keyword
        if( $new_keywords && $domain_id > 0 ){
            $new_keywords = explode( ',', $new_keywords);
            foreach ( $new_keywords as $keyword ) {
                
                $check_exists = CsQuery::Cs_Get_Results( array(
                    'select' => 'id',
                    'from' => 'keywords',
                    'where' => array( 'domain_id' => $domain_id, 'keyword' => $keyword ),
                    'query_type' => 'get_row',
                    'query_var' => 101
                ) );
                    
                if( !isset( $check_exists->id ) ) {
                    $kid = CsQuery::Cs_Insert( array (
                        'keywords',
                        array(
                            'domain_id' => $domain_id,
                            'keyword' => $keyword,
                            'auto_update' => empty($auto_update) ? 2 : 1
                        ),
                        'query_var' => 101
                    ) );
                }else{
                    $kid = $check_exists->id;
                }
                
                CsFlusher::Cs_Single_Cron_Schedule_Flush( array( 'schedule' => 1 * 60, 'hook' => "csmbp_cron_getNewKeywordPosition", 'args' => array( 'id' => $kid ) ) );

                if( ! empty($auto_update) ){
                    //set auto update daily
                    CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => 'daily', 'hook' => 'csmbp_cron_monitorKeyword'));
                }
                
            }
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Rank will be update in few moment', SR_TEXTDOMAIN )
        );
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Get new keyword position
     * 
     * @param type $kid
     * @return boolean
     */
    public function csmbp_get_new_keyword_pos( $kid ){
        if( empty( $kid)) return false;
        
        $get_data = CsQuery::Cs_Get_Results(array(
            'select' => ' d.url, k.keyword',
            'from' => array( 'd' => 'domains' ),
            'join' => array( array( 'k' => 'keywords'), 'd.id = k.domain_id', 'LEFT' ),
            'where' => array( 'k.id' => $kid ),
            'query_type' => 'get_row',
            'query_var' => 101
        ));
       
            
       if( isset( $get_data->url ) ){
           
           if( GeneralHelpers::check_internet_status() === false ){ //if internet down schedule after 5mins
               CsFlusher::Cs_Single_Cron_Schedule_Flush( array( 'schedule' => 5 * 60, 'hook' => "csmbp_cron_getNewKeywordPosition", 'args' => array( 'id' => $kid )));
           }
           
           $serps = @Google::getSerps( $get_data->keyword, 100, $get_data->url );
           CsQuery::Cs_Insert(array(
               'keyword_rankings',
               array(
                   'keyword_id' => $kid,
                   'current_position' => isset( $serps[0]['position'] ) ? $serps[0]['position'] : 0,
                   'position_increased' => 0,
                   'position_decreased' => 0,
                   'created_on' => date('Y-m-d H:i:s')
               ),
               'query_var' => 101
           ));
               
           CsQuery::Cs_Update(array(
               'keywords',
               array( 'from_url' => isset($serps[0]['url']) ? $serps[0]['url'] : '' ),
               array( 'id' => $kid ),
               'query_var' => 101
           ));
       }     
    }

    /**
     * Delete Keyword
     * 
     * @version 1.0.0
     * @return JsonObject Description
     */
    public function delete_keyword(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-manage-all-keywords', $ajax_nonce, true );
        
        $raw_item_id = $item_id = array_filter($this->http->get('_item_id', false));
        if( is_array( $item_id )){
            $domain_id = '';
            for( $i =0; $i<count($item_id);$i++){
                if( $i == 0 ){
                    $get_domain_info = CsQuery::Cs_Get_Results(array(
                        'select' => 'd.id',
                        'from' => array( 'k' => 'keywords'),
                        'join' => array( array( 'd' => 'domains'), 'd.id = k.domain_id', 'LEFT' ),
                        'where' => array( 'k.id' => $item_id[ $i ] ),
                        'query_type' => 'get_row',
                        'query_var' => 101
                    ));
                    $domain_id = isset( $get_domain_info->id ) ? $get_domain_info->id : '';
                }
                
                //delete row from keywords tbl
                CsQuery::Cs_Delete(array(
                    'keywords',
                    array( 
                        'id' => $item_id[ $i ]
                    ),
                    'query_var' => 101
                ));
                //delete rows from rankings table
                CsQuery::Cs_Delete_In(array(
                    'keyword_rankings',
                    array( 
                        'keyword_id' => $item_id[ $i ]
                    ),
                    'query_var' => 101
                ));
            }
            
            //check keyword is empty for this domain
            $get_domain_rest_key_info = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => 'keywords' ,
                'where' => array( 'domain_id' => $domain_id ),
                'query_type' => 'get_row',
                'query_var' => 101
            ));
            
            if( ! isset ( $get_domain_rest_key_info->id ) ){
                //if no exists keyword found delete the main domain row
                CsQuery::Cs_Delete(array(
                    'domains',
                    array( 
                        'id' => $domain_id
                    ),
                    'query_var' => 101
                ));
                
                //check there is still need to run cron job
                $check_auto_update = CsQuery::Cs_Get_Results(array(
                    'select' => 'id',
                    'from' => 'keywords',
                    'where' => array( 'auto_update' => 1 ),
                    'query_type' => 'get_var',
                    'query_var' => 101
                ));
                if( empty( $check_auto_update ) ){
                    //remove cron - if no data set for auto update
                    CsFlusher::Cs_Cron_Remove( array( 'hook' => 'csmbp_cron_monitorKeyword'));
                }
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Keyword has been deleted successfully.', SR_TEXTDOMAIN ),
                'remove_id' => $raw_item_id
            );
            
        }else{
            
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
        
    }
    
    
    /**
     * auto update setup
     * 
     * @since 1.0.0
     */
    public function change_auto_update_status(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-manage-all-keywords', $ajax_nonce, true );
        
        $item_id = array_filter($this->http->get('_item_id', false));
        if( is_array( $item_id )){
            CsQuery::Cs_Update_In(array(
                'keywords',
                array( 'auto_update' => 1 ),
                array( 'id' => $item_id),
                'query_var' => 101
            ));
            
            //set auto update daily - if no cron has been set 
            CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => 'daily', 'hook' => 'csmbp_cron_monitorKeyword'));
                        
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Keyword will be monitor automatically.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
        
    }
    
    /**
     * Remove keyword monitoring
     * 
     * @since 1.0.0
     */
    public function remove_autoupdate(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'aios-manage-all-keywords', $ajax_nonce, true );
        
        $item_id = array_filter($this->http->get('_item_id', false));
        if( is_array( $item_id )){
            CsQuery::Cs_Update_In(array(
                'keywords',
                array( 'auto_update' => 2 ),
                array( 'id' => $item_id),
                'query_var' => 101
            ));
            
            //check there is still need to run cron job
            $check_auto_update = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => 'keywords',
                'where' => array( 'auto_update' => 1 ),
                'query_type' => 'get_var',
                'query_var' => 101
            ));
            if( empty( $check_auto_update ) ){
                //remove cron - if no data set for auto update
                CsFlusher::Cs_Cron_Remove( array( 'hook' => 'csmbp_cron_monitorKeyword'));
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Keyword has been removed from automatic monitoring.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    public function add_new_group(){
        global $wpdb;
        
        $ajax_nonce = $this->http->get('_wpnonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'Aios-manage-keywords-groups', $ajax_nonce, true );
        
        $new_group_name = '';
        if ( $this->http->has('new_cat_name') ) {
            $new_group_name = $this->http->get( 'new_cat_name', false);
            $new_group_des = $this->http->get( 'new_cat_des', false);
            
            $get_cat_id = CsQuery::Cs_Get_Results(array(
                'select' => 'id',
                'from' => 'groups',
                'where' => array( 'name' => $new_group_name  ,'type' => 5 ),
                'query_type' => 'get_row',
                'query_var' => 101
            ));

            if( isset( $get_cat_id->id ) && !empty( $get_cat_id->id ) ){
                $group_id = $get_cat_id->id;
                $json_data = array(
                    'type' => 'success',
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'text' => __( "Keyword groups '{$new_group_name}' already exists.", SR_TEXTDOMAIN ),
                );
            }else{
                $insert_group = array(
                    'groups',
                    array(
                        'name' => $new_group_name,
                        'description' => $new_group_des,
                        'type' => 5,
                        'created_on' => date('Y-m-d H:i:s')
                    ),
                    'query_var' => 101
                );
                $group_id = CsQuery::Cs_Insert( $insert_group );
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Group has been created successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
        
        
    }
    
    public function delete_group(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);

        //check security
        AjaxHelpers::check_ajax_referer( 'Aios-manage-keywords-groups', $ajax_nonce, true );
        
        if ( $this->http->has('_item_id') ) {
            $item_id = array_filter($this->http->get('_item_id', false));
            CsQuery::Cs_Delete_In(array(
                'groups',
                array(
                    'id' => $item_id
                ),
                'query_var' => 101
            ));
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Keyword group(s) has been deleted successfully.', SR_TEXTDOMAIN ),
                'remove_id' => $item_id
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Keyword Options
     */
    public function keyword_options(){
        $ajax_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-keyword-general-options', $ajax_nonce, true );
        
        if ( $this->http->has('_update_schedule') ) {
            $schedule = $this->http->get('_update_schedule', false);
            CsQuery::Cs_Update_Option(array(
                'option_name' => 'aios_keyword_options',
                'option_value' => array(
                    '_keyword_update_schedule' => $schedule
                ),
                'json' => true
            ));
            
            //update schedule for automatic backlink finder
            CsFlusher::Cs_Cron_Schedule_Flush( array( 'schedule' => $schedule, 'hook' => 'aios_keyword_manager_cron'));
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Your update has been saved successfully.', SR_TEXTDOMAIN )
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'text' => __( 'You didn\'t select any option.', SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Save keyword suggestion
     * 
     * @since 1.0.0
     */
    public function save_keyword_suggestion(){
        $_aios_nonce = $this->http->get( '_wpnonce', false );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-save-keyword-suggestion', $_aios_nonce, true );
        
        if( $this->http->has( 'save_data' ) ){
            if( $this->http->has( 'keywords' ) ) {
                $keywrods = $this->http->get( 'keywords', false);
                foreach($keywrods as $group => $values){
                    $option_name = Helper::get( 'PLUGIN_DATA_PREFIX' ) . "keyword_sugg_{$group}";
                    //get old result 
                    $old_keywords = CsQuery::Cs_Get_Option(array(
                        'option_name' => $option_name,
                        'json' => true,
                        'json_array' => true
                    ));
                    //merge with new and make unique
                    $new_keywords = array_unique(array_merge( $values, empty($old_keywords) ? array() : $old_keywords ) );    
                    CsQuery::Cs_Update_Option(array(
                        'option_name' => $option_name,
                        'option_value' => $new_keywords,
                        'json' => true
                    ));    
                }
                $json_data = array(
                    'type' => 'success',
                    'title' => __( 'Success!', SR_TEXTDOMAIN ),
                    'text' => __( 'Selected keywords has been saved successfully.', SR_TEXTDOMAIN ),
                );
            }else{
                $json_data = array(
                    'type' => 'error',
                    'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                    'text' => __( 'You didn\'t select any keyword', SR_TEXTDOMAIN )
                );
            }

            AjaxHelpers::output_ajax_response( $json_data );
        }else{
            //if export button fired
            $this->export_keywords();
        }
    }
    
    /**
     * Export Keyword Suggestion
     */
    public function export_keywords(){
        if( $this->http->has( 'keywords' ) ) {
            $items = array();
            $keywrods = $this->http->get( 'keywords', false);
            
            foreach($keywrods as $group => $values){
                if( is_array($values)){
                    foreach($values as $val){
                        if( ! empty( $val ) ){
                            $items[] = array( $val, $group);
                        }
                    }
                }
            }
            
            $data = array(
                'col_head' => array(
                    'Keyword', 'Group'
                ),
                'col_data' => $items
            );

            GeneralHelpers::Cs_Download_Send_Headers( Helper::get( 'PLUGIN_PREFIX' ).'_keyword_suggestion_'. date("Y_m_d") . ".csv" );
            echo GeneralHelpers::Cs_Array2csv( $data );
            die();
        }
    }
    
    /**
     * Delete selcted keywords
     * 
     * @since 1.0.0
     */
    public function delete_selected_keywords(){
        $ajax_nonce = $this->http->get('_ajax_nonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-selected-keywords', $ajax_nonce, true );
        
        $raw_item_id = $item_id = array_filter($this->http->get('_item_id', false));
        
        $groups = array();
        if( $raw_item_id ){
            foreach($raw_item_id as $item ){
                $keyword = explode('__', $item );
                if( isset( $groups[ $keyword[1] ] ) ){
                    $groups[ $keyword[1] ] = array_diff( $groups[ $keyword[1] ], array( $keyword[0] ) );
                }else{
                    $groups = array_merge( $groups, array( $keyword[1] => CsQuery::Cs_Get_Option(array(
                        'option_name' => Helper::get( 'PLUGIN_DATA_PREFIX' ) . "keyword_sugg_{$keyword[1]}",
                        'json' => true,
                        'json_array' => true
                    ))));
                        
                    $groups[ $keyword[1] ] = array_diff( $groups[ $keyword[1] ], array( $keyword[0] ) );
                }
            }
            
            if( $groups ){
                foreach( $groups as $group => $new_keywords ){
                    CsQuery::Cs_Update_Option(array(
                    'option_name' =>  Helper::get( 'PLUGIN_DATA_PREFIX' ) . "keyword_sugg_{$group}",
                    'option_value' => $new_keywords,
                    'json' => true
                ));   
                }
            }
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Success!', SR_TEXTDOMAIN ),
                'text' => __( 'Keyword has been deleted successfully.', SR_TEXTDOMAIN ),
            );
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'Something went wrong. Please try again later', SR_TEXTDOMAIN )
            );
        }
        
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * 
     * @global \CsSeoMegaBundlePack\Models\KeywordManager\type $wpdb
     * @return boolean
     * @global \CsSeoMegaBundlePack\Models\KeywordManager\type $wpdb
     * @return boolean
     */
    public function get_all_items_posts(){
        $type = $this->http->get('type', false);
        if( empty($type)){
            return false;
        }
        global $wpdb;
        $items = $wpdb->get_results( $wpdb->prepare( "select ID, post_title from {$wpdb->posts} where post_type = '%s' and post_status = '%s' ", $type, 'publish' ) );
        if( $items ){
            $result = '<select class ="form-control" name="item_id" id="item_id"><option value="0">=========================== Select '.$type.'===========================</option>';
            foreach( $items as $item ){
                $result .= '<option value = "'.$item->ID.'">'.$item->post_title.'</option>';
            }
            return $result . '</select>';
        }
        return false;
    }
    
    /**
     * Get old tags
     * 
     * @return boolean|string
     */
    public function get_old_tags(){
        $post_id = $this->http->get('id', false);
        if( empty($post_id)){
            return false;
        }
        $type = $this->http->get('type', false);
        $taxonomy = $type == 'post' ? 'post_tag' : 'product_tag';
        $post_tags =  get_the_terms( $post_id, $taxonomy);
        
        if( empty( $post_tags ) ){
            return 'No tag found!';
        }
        
        $ret = '<ul class="old_tags_list">';
        foreach( $post_tags as $item ){
            $ret .= '<li> <input type="checkbox" name="old_tags[]" value = "'.$item->term_id.'" />' . $item->name .'</li>';
        }
        return $ret .'</ul>';
    }
    
    /**
     * Save keyword suggestion
     * 
     * @since 1.0.0
     */
    public function add_tags(){
        $_aios_nonce = $this->http->get( '_wpnonce', false );
        
        //check security
        AjaxHelpers::check_ajax_referer( 'add-remove-custom-tags', $_aios_nonce, true );
//        print_r( $_POST );
        if( !$this->http->has( 'item_types' ) || !$this->http->has( 'item_types_id' ) ) {
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'You need to select item type', SR_TEXTDOMAIN )
            );
            AjaxHelpers::output_ajax_response( $json_data );
        }
        
        if( $this->http->has( 'keywords' ) ) {
            $tags = '';
            $keywrods = $this->http->get( 'keywords', false);
            $i = 0;
            foreach($keywrods as $group => $values){
                if( $i > 0 ){ $tags .= ','; }
                $tags .= is_array($values) ? implode( ',', $values ) : $values;
            }
            
            $old_tags_remove = $this->http->get( 'old_tags_remove', false);
            $type = $this->http->get( 'item_types', false);
            $tag_type = $type =='post' ? 'post_tag' : 'product_tag';
            if( !empty( $old_tags_remove ) ){
                $old_tags_remove = explode( '_', $old_tags_remove);
                foreach( $old_tags_remove as $old_tag ){
                    if(empty($old_tag)){
                        continue;
                    }
                    wp_delete_term( $old_tag, $tag_type );
                }
            }
            
            //add new tags
            $type_id = $this->http->get( 'item_types_id', false);
            wp_set_post_terms( $type_id, $tags, $tag_type);
            
            $json_data = array(
                'type' => 'success',
                'title' => __( 'Successfull!', SR_TEXTDOMAIN ),
                'text' => __( 'Tags has been added successfully.', SR_TEXTDOMAIN )
            );
            
        }else{
            $json_data = array(
                'type' => 'error',
                'title' => __( 'Ops!', SR_TEXTDOMAIN ),
                'text' => __( 'You didn\'t select any keyword', SR_TEXTDOMAIN )
            );
        }
        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
