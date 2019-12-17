<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * All Ditected Links
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Models\BacklinkManager\BackEndActionRequest\BrokenLinkChecker\Module\PatternMatching;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

class AllDetectedLink {
    
    
    public function load_page_builder( $Http ){
        global $wpdb;
        
       $CommonText = new CommonText();
       
       
       $filter_msg_title = $filter_msg_subtitle = '';
       $where = array();
       if($Http->has('filter')){
           $filter_by = $Http->get('filter', false);
           $filter_by_sql = $filter_by === 'posts' ? " in ('posts', 'page')" : " = '{$filter_by}'";
           $filter_msg_title =  __( 'Filtered result', SR_TEXTDOMAIN );
           $filter_msg_subtitle = __( 'Total: <b>%s</b> link(s) found by : <b>%s</b>', SR_TEXTDOMAIN );
           $where = array( 'where' => "ref_container {$filter_by_sql}");
       }
       
       $data_item = CsQuery::Cs_Get_Results(array_merge(array(
           'select' => 'l.*, p.post_title, c.comment_content',
           'from' => $wpdb->prefix . 'aios_all_internal_links as l',
           'join' => "left join {$wpdb->prefix}posts as p on p.id = l.ref_id left join {$wpdb->prefix}comments as c on c.comment_ID = l.ref_id and l.ref_container = 'comments'",
           'order_by' => ' id desc',
           'num_rows' => true
       ), $where) );
     
       if($Http->has('filter')){
           $filter_msg_subtitle = sprintf( $filter_msg_subtitle, $data_item->num_rows, PatternMatching::get_abv( $filter_by ) );
       }
           
           
//           pre_print($data_item);
       
       $data = array_merge( $CommonText->form_element( 'aios-delete-link', 'bl_delete_link'), array(
           'CommonText' => $CommonText->common_text(),
           'page_title' =>  __( 'All Links', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'All Links found in your website.', SR_TEXTDOMAIN ),
           'tbl_headers' => array(
                   __( 'Source', SR_TEXTDOMAIN ),
                   __( 'Url', SR_TEXTDOMAIN ),
                   __( 'Anchor Text / Link Type', SR_TEXTDOMAIN ),
                   __( 'Actions', SR_TEXTDOMAIN ),
               ),
           'tbl_data_array' => $data_item->rows,
           'redirect_group' => '',
           'filter_title' => $filter_msg_title,
           'filter_subtitle' => $filter_msg_subtitle,
           'actn_btns' => __( 'Bulk Actions', SR_TEXTDOMAIN ),
           'actn_btn_active' => __( 'Make follow', SR_TEXTDOMAIN ),
           'actn_btn_inactive' => __( 'Make Nofollow', SR_TEXTDOMAIN ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_filter' => __( 'Filters', SR_TEXTDOMAIN ),
           'filters' => array(
               'links' => __( 'Blogrolls', SR_TEXTDOMAIN ),
               'posts' => __( 'Posts / Pages', SR_TEXTDOMAIN ),
           ),
           'actn_delete_btn' => __( 'Delete', SR_TEXTDOMAIN ),
           'label_span_edit' => __( 'Edit', SR_TEXTDOMAIN ),
           'label_actions_span_delete' => __( 'Delete', SR_TEXTDOMAIN ),
           
           'label_back_to_btn' => __( 'Back To All', SR_TEXTDOMAIN ),
           'current_url' => admin_url('admin.php?page=cs-backlink-manager&tab=AllDetectedLink'),
           'admin_url' => admin_url()
       ));
       
       add_action("admin_footer", [$this, '_action_script']);
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/broken_links/AllDetectedLink.twig', $data );
    }
    
    public function _action_script(){
        ?>
        <script type="text/javascript">    
                jQuery( document ).ready( function( $ ) {
                    
                     var _obj = {
                        $: $,
                        data_section_id: '#demo-custom-toolbar',
                        row_id : "#item_id_",
                        action_type: 'delete'
                    };
                    var action_handler = new Aios_Action_Handler( _obj );
                    action_handler.setup( "#btn_delete" );
                    
//                     var _obj = {
//                        $: $,
//                        row_id : "#item_id_",
//                        action_type: 'delete',
//                        single_item: true
//                    };
//                    var action_handler = new Aios_Action_Handler( _obj );
//                    action_handler.setup( ".btn-delete-link" ); 
//                    console.log('started..');
//                    $( ".btn-delete-link" ).each(function(){
//                        $(this).on("click", function(){
//                            console.log( $( this ).data( 'item_id'));
//                        });
//                    });
                    
                    $( ".btn-delete-link" ).on('click', function(){
                        console.log('fired');
                    });
            });
        </script>    
            
        <?php
        
    }
    
}
