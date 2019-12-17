<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Tabs\Facebook;
/**
 * Manage Scheduled Post
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\SocialAnalytics\Common\CommonText;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class FbScheduledPost {
    
    public function load_page_builder( $common_text ){
        global $wpdb;
        $items = CsQuery::Cs_Get_results(array(
            'select' => 's.*, p.post_title',
            'from' => "{$wpdb->prefix}aios_social_publish_schedule s",
            'join' => "left join {$wpdb->prefix}posts as p on p.ID = s.post_id",
             'where' => 's.type = 1 '       
        ));
           
       $data = array_merge( CommonText::form_element( 'manage_fb_scheduled_post', 'Aios-manage-fb-scheduled-posts'), array(
           'CommonText' => $common_text,
           'page_title' =>  __( 'Scheduled Post', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Facebook auto publish queue.', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Post Title', SR_TEXTDOMAIN ),
                   __( 'Posible Publishing Time', SR_TEXTDOMAIN ),
                   __( 'Created On', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $items,
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_span_edit' => __( 'Edit', SR_TEXTDOMAIN ),

       ));
       
       add_action( 'admin_footer', array( $this, '_scriptManageKeywordGroups') );
       return  view( '@CsSeoMegaBundlePack/SocialAnalytics/tabs/facebook/FbScheduledPost.twig', $data );
    }
    
    public function _scriptManageKeywordGroups(){
        ?>
            <script type="text/javascript">
                ( function( $ ) {
                        $( document ).ready( function() {
                            
                            //create new group
                            var _obj = {
                                $: $,
                                form_reset: true,
                                redirect: window.location.href
                            };
                            var form_general_options = new Aios_Form_Handler( _obj );
                            form_general_options.setup( ".card-box" );
                            
                            //delete groupd
                            var _obj = {
                                $: $,
                                data_section_id: '#demo-custom-toolbar',
                                row_id : "#item_id_",
                                action_type: 'delete'
                            };
                            var action_handler = new Aios_Action_Handler( _obj );
                            action_handler.setup( "#btn_delete" ); 
                           
                        } );
                } )( jQuery );
            </script>
        <?php
    }
    
}
