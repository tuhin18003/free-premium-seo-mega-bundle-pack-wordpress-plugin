<?php namespace CsSeoMegaBundlePack\Library\Includes;
/**
 * Log Class
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class SystemLogs {
    
    
    /**
     * Log Email Messages
     * 
     * @param type $args array( 'title' => 'section title', 'message' => 'section message', 'type' => 1 )
     * @return boolean
     */
    public static function Cs_Email_Log( $args = array() ){
        if( !is_array( $args ) && empty( $args )) return false;
        $data = array(
            'section_title' => isset( $args[ 'title' ]) ? $args[ 'title' ] : '',
            'message' => isset( $args[ 'message' ]) ? $args[ 'message' ] : '',
            'log_type' => isset( $args[ 'type' ]) ? $type = $args[ 'type' ] : '',
            'created_on' => date('Y-m-d H:i:s')
        );
        $check_exists = CsQuery::Cs_Count(array(
                'table' => 'aios_email_msg',
                'where' => " log_type = {$type} "
            ));

        if($check_exists === 0 ){
            CsQuery::Cs_Insert(array(
                'table' => 'aios_email_msg',
                'insert_data'=> $data
            ));
        }
        
        return true;
    }
    
}
