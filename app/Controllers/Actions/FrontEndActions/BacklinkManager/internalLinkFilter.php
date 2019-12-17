<?php
/**
 * Front End Internal Links Filter
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Models\BacklinkManager\getInternalLinkData as getInternalLinkData;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;

add_action( 'the_content', 'filterContents' );
if( !function_exists('filter_internal_links')){
    
    /**
     * Filter internal link
     * 
     * @since 1.0.0
     * @param string $buffer
     * @return string
     */
    function filterContents( $buffer ) {
        $getInternalLinkData = new getInternalLinkData();
        $data = $getInternalLinkData->getInternalLinkRules();
        if( !empty($data)){
            foreach($data as $item){
                if(strpos($item->target_keywords, ',') === false){
                    $replace = '<a href="'.$item->target_url.'">'.$item->target_keywords.'</a>';
                    $buffer = str_replace( $item->target_keywords, $replace, $buffer );
                }else{
                    $keywordArr = explode(',', $item->target_keywords);
                    foreach($keywordArr as $keyword){
                        $replace = '<a href="'.$item->target_url.'">'.$keyword.'</a>';
                        $buffer = str_replace( $keyword, $replace, $buffer );
                    }
                }
            }
        }
        $get_Rtfar = get_option('rtafar_settings');
        if ( is_array( $get_Rtfar ) ) {
            foreach ( $get_Rtfar as $find => $replace ){
                $buffer = str_replace( $find, $replace, $buffer );
            }
        }
        return $buffer;
    }
}

add_action('wp_footer', '_aios_visitor_link_click_tracking');
if( !function_exists('_aios_visitor_link_click_tracking')){
    /**
     * Visitor Click Tracking
     */
    function _aios_visitor_link_click_tracking(){
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_general_option', 'json' => true ) );
        if( (isset($get_options->click_tracking) && $get_options->click_tracking === 'on' )){
        ?>
<!-- Click Tracking by CSMBP-->
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("a").on( "click", function(){
        var $data = {
            _aios_nonce: '<?php echo wp_create_nonce( 'aios-interlink-tracker' ); ?>',
            _href: jQuery(this).attr('href'),
            _keyword: jQuery(this).text()
        };
        jQuery.ajax({
              type: 'POST',
              url: '<?php echo site_url('/click-tracking'); ?>',
              data: $data,
              async: false,
              dataType: 'json',
              success: function( data, textStatus, XMLHttpRequest ) {
                    //console.log(data);
              }
        });
    });
});
</script>
<!-- Click Tracking by CSMBP-->
        <?php
        }
    }
}

if( !function_exists('aios_no_self_ping')){
    
    /**
     * Stop WP Auto Self Pingback
     * 
     * @param type $links
     */
    function aios_no_self_ping( &$links ) {
        $get_options = CsQuery::Cs_Get_Option( array( 'option_name'=> 'aios_general_option', 'json' => true ) );
        if( isset($get_options->self_ping) && $get_options->self_ping === 'off' ){
            $home = get_option( 'home' );
            foreach ( $links as $l => $link ){
                if ( 0 === strpos( $link, $home ) ){
                    unset($links[$l]);
                }
            }
        }
    }
}
add_action( 'pre_ping', 'aios_no_self_ping' );

