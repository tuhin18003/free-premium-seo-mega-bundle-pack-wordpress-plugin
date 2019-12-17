<?php namespace CsSeoMegaBundlePack\Library\Backend\Messages;

/**
 * All Backend Notice Messages
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class BackendMessages {
    
    /**
     * instance
     *
     * @var type 
     */
    private static $instance;
    
    /**
     * Hold Notices
     *
     * @var type 
     */
    private $notices;


    public function __construct() {
        //get the notices
        $this->notices = $this->notices();
    }

        /**
     * Generate instance
     * 
     * @return type
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
                self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * Backend Notices
     * 
     * @param type $notice_id
     * @return type
     */
    private function notices(){
        return array(
            'notice_inet_down' => array(
               'type' => __( 'Error Notice!', SR_TEXTDOMAIN ),
               'msg' => __( 'Your Internet connection has down! You need to connect your internet to use this service.', SR_TEXTDOMAIN ),
           ),
           'notice_no_item' => array(
               'type' => __( 'Nothing Found!', SR_TEXTDOMAIN ),
               'msg' => __( 'No matching records found in this certain criteria! ', SR_TEXTDOMAIN ),
           ),
        );
    }
    
    /**
     * Get Notices
     */
    public function Cs_Get_Notices( $notice_id = false ){
        $return_notices = '';
        if( $notice_id === true ){
            if(is_array( $notice_id ) ){
                foreach( $notice_id as $id ){
                    if(in_array( $id, $this->notices )){
                        $return_notices = array_merge( $return_notices,  $this->notices[ $id ]);
                    }
                }
                return $return_notices;
            }else{
                return $this->notices[ $notice_id ];
            }
        }else{
            return $this->notices;
        }
    }
    
}
