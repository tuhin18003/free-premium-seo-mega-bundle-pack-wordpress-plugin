<?php namespace CsSeoMegaBundlePack\Library;

/**
 * Common Flush
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class CsFlusher {
    
    /**
     * Cron Job Schedule Flusher
     * 
     * @param array $args
     * @return boolean
     */
    public static function Cs_Cron_Schedule_Flush( $args = array() ){
        if( empty( $args['schedule']) || empty( $args['hook'])){
            return false;
        }
        
        //clear old schedule
        wp_clear_scheduled_hook( $args['hook'] );
        
        $hook_args = isset( $args['args'] ) && is_array( $args['args'] ) ? $args['args'] : array();
        
        //add new schedule
        wp_schedule_event( time(), $args['schedule'], $args['hook'], $hook_args );
        
        return true;
    }
    
    /**
     * Single Cron Job Schedule Flusher
     * 
     * @param array $args
     * @return boolean
     */
    public static function Cs_Single_Cron_Schedule_Flush( $args = array() ){
        if( empty( $args['schedule']) || empty( $args['hook'])){
            return false;
        }
        
        //check for args
        $hook_args = isset( $args['args'] ) ? $args['args'] : '';
        
        //add new schedule
        wp_schedule_single_event( $args['schedule'], $args['hook'], $hook_args );
        
        return true;
    }

     /**
     * Remove Cron
     * 
     * @param type $args
     * @return boolean
     */
    public static function Cs_Cron_Remove( $args = array() ){
        if( empty( $args['hook'])){
            return false;
        }
        
        //clear old schedule
        wp_clear_scheduled_hook( $args['hook'] );
        
        return true;
    } 
    
}
