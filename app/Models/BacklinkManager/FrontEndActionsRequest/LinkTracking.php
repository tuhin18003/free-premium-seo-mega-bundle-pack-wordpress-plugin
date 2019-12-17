<?php namespace CsSeoMegaBundlePack\Models\BacklinkManager\FrontEndActionsRequest;

/**
 * Link Tracking
 * 
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;

class LinkTracking{
    
    /**
     * Track Visitor Link Click
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function save_click_tracking(){
        global $wpdb;
        $_aios_nonce = CsQuery::check_evil_script($_POST['_aios_nonce']);
//        check security
        $ret = AjaxHelpers::check_ajax_referer( 'aios-interlink-tracker', $_aios_nonce );
        
        $link = $_POST['_href'];
        
        CsQuery::Cs_Insert(array(
            'table' => 'aios_visitor_click_tracker',
            'insert_data' => array(
                'url' => $link,
                'keyword' => $_POST['_keyword'],
                'referrer' => GeneralHelpers::Cs_User_Referrer(),
                'visitor_ip' => GeneralHelpers::Cs_Real_Ip_Addr(),
                'visitor_agent' => GeneralHelpers::Cs_User_Agent(),
                'visited_on' => date('Y-m-d H:i:s')
            )
        ));
        
        $get_row = CsQuery::Cs_Get_Results(array(
            'select' => '*',
            'from' => $wpdb->prefix . 'aios_visitor_link_click_count',
            'where' => " link = '{$link}'",
            'query_type' => 'get_row'
        ));
        if( isset($get_row->id)){
            CsQuery::Cs_Update(array(
                'table' => 'aios_visitor_link_click_count',
                'update_data' => array(
                    'click_count' => ($get_row->click_count + 1),
                    'last_clicked' => date('Y-m-d')
                ),
                'update_condition' => array(
                    'id' => $get_row->id
                )    
            ));
        }else{
            CsQuery::Cs_Insert(array(
                'table' => 'aios_visitor_link_click_count',
                'insert_data' => array(
                    'link' => $link,
                    'click_count' => 1,
                    'last_clicked' => date('Y-m-d')
                )
            ));
        }    
        
        return 'success';
    }
    
}
