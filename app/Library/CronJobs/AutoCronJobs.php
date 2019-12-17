<?php namespace CsSeoMegaBundlePack\Library\CronJobs;

/**
 * Auto Cron Jobs Handler
 * 
 * @package AIOS
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
//use AiosSerp\Services\Google as Google;
use CsSeoMegaBundlePack\Library\SerpTracker\Services\Google;
use CsSeoMegaBundlePack\Library\CsFlusher;
use CsSeoMegaBundlePack\Library\Includes\HtAccess\CsHtaccesCreator;
use CsSeoMegaBundlePack\Library\Includes\SystemLogs;

class AutoCronJobs {
    
    /**
     * Check url has been removed or not
     * 
     * @global type $wpdb
     */
    public static function cs_check_urls_removed(){
        global $wpdb;
        $get_urls = CsQuery::Cs_Get_Results(array(
            'select' => 'url',
            'from' => $wpdb->prefix . 'aios_remove_urls',
            'where' => ' status = 1 '
        ));
        
        if( $get_urls ){
            $removed_count = 0;
            $removed_urls = '';
            foreach($get_urls as $url){
                try{
                $modified_url = str_replace( array('http:','https:','www'), '', $url->url);
                    $indexed_pages = Google::getSerps("site:{$modified_url}", 2 );
                    if( empty( $indexed_pages ) ) {
                        CsQuery::Cs_Update(array(
                            'table' => 'aios_remove_urls',
                            'update_data' => array(
                                'status' => 2,
                                'removed_on' => date('Y-m-d H:i:s')
                            ),
                            'update_condition' => array(
                                'url' => $url->url
                            ),
                        ));
                        $removed_urls .= $url->url .'<br>'; 
                        $removed_count++;
                    }else{
//                        error_log( "Url not removed : {$url->url}");
                    }
                    
                }catch (\AiosSerpException $e) {
                    error_log( 'Error: '. $e->getMessage());
                }
            }
            
            //generate new htaccess
            $get_urls = CsQuery::Cs_Get_Results(array(
                'select' => 'url',
                'from' => $wpdb->prefix . 'aios_remove_urls',
                'where' => 'status = 1'
            ));
            (new CsHtaccesCreator())->remove_urls( $get_urls, $this->site_url );
            
            if( $removed_count > 0 ){ // create email row
                CsQuery::Cs_Insert(array(
                    'table' => 'aios_email_msg',
                    'insert_data'=> array(
                        'section_title' => sprintf(__( '%s URLs has been removed!!', SR_TEXTDOMAIN ),$removed_count ),
                        'message' => sprintf( __( 'Congratulation!! Following URLs has been removed from Google index. <br> %s',SR_TEXTDOMAIN), $removed_urls),
                        'log_type' => 3,
                        'created_on' => date('Y-m-d H:i:s')
                    )
                ));
            }
            
        }else{
            //remove cron job if no new url found
            CsFlusher::Cs_Cron_Remove( array( 'hook' => 'aios_check_urls_removed'));
        }
    }
    
    /**
     * Monitor Keywords
     * 
     * @since 1.0.0
     * @return boolean
     */
    public static function monitor_keyword(){
        $get_keywords = CsQuery::Cs_Get_Results(array(
            'select' => 'k.id, k.keyword,d.url',
            'from' => array( 'k' => 'keywords'),
            'join' => array( array( 'd' => 'domains'), 'd.id = k.domain_id', 'LEFT' ),
            'where' => array( 'auto_update' => 1 ),
            'query_var' => 101
        ));
        
        if( $get_keywords->num_rows > 0 ){
            $delay = 0; $key = '';
            foreach( $get_keywords->rows as $keyword){
                $delay += 200000;
                usleep($delay);
                
                $serps = Google::getSerps( $keyword->keyword, 100, $keyword->url );
                $get_old_row = CsQuery::Cs_Get_Results(array(
                    'select' => '*',
                    'from' => 'keyword_rankings',
                    'where' => array( 'keyword_id' => $keyword->id ),
                    'order_by' => 'created_on desc',
                    'limit' => '1',
                    'query_type' => 'get_row',
                    'query_var' => 101
                ));
                
                $new_rank = isset( $serps[0]['position'] ) ? $serps[0]['position'] : 0;
                $position_increased = 0;
                $position_decreased = 0;
                
                if( isset( $get_old_row->id ) ){
                    if( $get_old_row->current_position > $new_rank ){
                        $position_increased = $get_old_row->current_position - $new_rank;
                    }else{
                        $position_decreased = $new_rank - $get_old_row->current_position;
                    }
                }
                
                CsQuery::Cs_Insert(array(
                   'keyword_rankings',
                   array(
                       'keyword_id' => $keyword->id,
                       'current_position' => $new_rank,
                       'position_increased' => $position_increased,
                       'position_decreased' => $position_decreased,
                       'created_on' => date('Y-m-d H:i:s')
                   ),
                   'query_var' => 101
               ));
                
                $key .=  '<li>'. 'Keyword - '. $keyword->keyword .' Old Rank - '.$new_rank .' New Rank - '.$get_old_row->current_position .'</li>';
            }
            
            //email log
            SystemLogs::Cs_Email_Log(array(
                'title' => __( 'Keyword Ranking Report', SR_TEXTDOMAIN ),
                'message' => __( 'Please check the following summary. ', SR_TEXTDOMAIN ) ." <Br/> <ul> {$key} </ul>",
                'type' => 4
            ));
        }else{
            //remove cron - if no data set for auto update
            CsFlusher::Cs_Cron_Remove( array( 'hook' => 'csmbp_cron_monitorKeyword'));
        }
        return true;
    }
    
    
}
