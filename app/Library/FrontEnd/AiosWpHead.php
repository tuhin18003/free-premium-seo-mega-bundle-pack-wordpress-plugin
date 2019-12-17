<?php namespace CsSeoMegaBundlePack\Library\FrontEnd;

/**
 * WP Front End Header 
 * 
 * @package FrontEnd
 * @since 1.0.0
 * @version 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\Helper;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\FrontEnd\CsGetOptions;
use CsSeoMegaBundlePack\Library\FrontEnd\WpHead;
use CsSeoMegaBundlePack\Library\FrontEnd\CsPageFunc;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\TitleFormater;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\MetaDescription;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\MetaKeyword;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\Canonical;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\MetaRobots;
use CsSeoMegaBundlePack\Library\FrontEnd\HeadFormater\SocialTags;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;

class AiosWpHead extends WpHead {
    
    /**
     * Options
     *
     * @var array
     */
    private $options;
    private static $soptions;

    /**
     * hold Meta Desc
     *
     * @var type 
     */
    private $metadesc;
    
    /**
     * hold Canonical
     *
     * @var type 
     */
    private $canonical;

    /**
     * Hold page mode
     *
     * @var array 
     */
    public $page_mod;

    /**
     * Hold Plugins data
     *
     * @var array 
     */
    private $CsPlugin = array(
        'name' => '', 'version' => '', 'storeUrl' => '', 'dataPrefix' => ''
    );

    /**
     * Hold Plugin Data Prefix
     *
     * @var string 
     */
    private $pluginDataPrefix = 'CSMBP_';
    
    /**
     * All Meta Tags Holder
     *
     * @var array 
     */
    private $metaTags = array();

    /**
     * Hold Config
     *
     * @var string 
     */
    private $CsConfig;
    
    /**
     * @var type
     * hold home title
     * 
     * @var string
     */
    protected $page_title;
    /**
     * Function construct
     * 
     */
    function __construct() {
        parent::__construct();
        
        //get options
        $this->get_options();
        
//        add_action( "CSMBP_header", array( $this, 'Cs_DebugMarker' ), 1 );
        add_action( "CSMBP_header", array( $this, 'Cs_RobotsBots' ), 1 );
        add_action( "CSMBP_header", array( $this, 'Cs_RenderHomeMetaTags' ), 5 );
        add_action( "CSMBP_header", array( $this, 'Cs_SocialTags' ), 10 );
//        add_action( "CSMBP_header", array( $this, 'Cs_TwitterCard' ), 15 );
//        add_action( "CSMBP_header", array( $this, 'aios_metadesc' ), 4 );
//        add_action( "CSMBP_header", array( $this, 'aios_meta_robots' ), 5 );
//        add_action( "CSMBP_header", array( $this, 'aios_metakeywords' ), 6 );
//        add_action( "CSMBP_header", array( $this, 'aios_canonical' ), 7 );
//        add_action( "CSMBP_header", array( $this, 'google_analytics_code' ), 50 );
        
    }
    
    /**
     * Get options
     * 
     * @version 1.0.0
     */
    private function get_options(){
        isset($this->options) || $this->options = CsGetOptions::get_instance()->options;
        self::$soptions = $this->options;
        
        $cache_id = $this->pluginDataPrefix . GeneralHelpers::Cs_Md5_Hash( 'plugin_optons' );
        if( ( $this->CsPlugin = get_transient( $cache_id ) ) === false ){
            $this->CsPlugin['name'] = Helper::get('PLUGIN_NAME');
            $this->CsPlugin['version'] = Helper::get('PLUGIN_VERSION');
            $this->CsPlugin['storeUrl'] = Helper::get('PLUGIN_STORE_URL');
            $this->CsPlugin['dataPrefix'] = Helper::get('PLUGIN_DATA_PREFIX');
            set_transient( $cache_id, $this->CsPlugin, 7 * DAY_IN_SECONDS );
        }
    }
    
    /**
     * Get Page Mod
     * 
     * @version 1.0.0
     * @return String Description
     */
    public function page_mod(){
        $this->page_mod = $this->Cs_Get_Page_Mod();
    }
    
    /**
     * Filter rendered page data
     * 
     * @version 1.0.0
     * @return array
     */
    public function Cs_RenderPageFunc(){
        $PF = new CsPageFunc( $this->page_title );
        $PF->options = isset( $this->options ) ? $this->options : false;
        $PF->page_mod = $this->page_mod;
        $PF->CsPlugin = $this->CsPlugin;
        $this->page_mod = $PF->Cs_RenderPageData();
    }

    /**
     * Render WP Header Meta Tags
     * 
     * @see https://developer.wordpress.org/reference/functions/do_action/
     * @version 1.0.0
     * @return Boolean print element before return
     */
    public function Cs_RendersMetaTags(){
        global $wp_query;

        $old_wp_query = null;

        if ( ! $wp_query->is_main_query() ) {
            $old_wp_query = $wp_query;
            wp_reset_query();
        }
        
        /**Time Counter Start**/
        $starttime = microtime(true);
        
        /**Debug Market Start**/
        echo $this->_cs_DebugMarker('begin');
        
        
        
        /**Meta Tags initilization**/
//        $this->_cs_meta_tags_initialization();
        
        /**Generate Meta Tags**/
        $this->_cs_GenerateMetaTags();
        
        /**Time Counter End**/
        printf("\n<!-- Added on %s in %f seconds -->", date('c'), microtime(true) - $starttime );
        
        /**Debug Market End**/
        echo $this->_cs_DebugMarker('end');
        
        if ( ! empty( $old_wp_query ) ) {
            $GLOBALS['wp_query'] = $old_wp_query;
            unset( $old_wp_query );
        }
        return;
    }
    
    /**
     * CSMBP marker
     * 
     * @version 1.0.0
     * @return String Description
     */
    private function _cs_DebugMarker( $type ){
        $ret = '';
        switch( $type ){
            case 'begin':
                $ret = sprintf(
                    '<!-- Website Optimization Has Been Done by - "%3$s" v%1$s - %2$s -->',
                    $this->CsPlugin['version'], $this->CsPlugin['storeUrl'], $this->CsPlugin['name']
                );
                break;
            case 'end':
                $ret = sprintf( "\n<!-- '%s' meta tags end. -->\n\n", $this->CsPlugin['name'] );
                break;    
        }
        
        return $ret;
    }

    /**
     * Google Analytics Tracking Code
     * 
     * @version 1.0.0
     * @return String Description
     */
    public function google_analytics_code(){
        if( isset($this->options->google_settings->auto_add_tracking_code) && $this->options->google_settings->auto_add_tracking_code == 'on'){
            echo "\n";
            ?>
<!-- Google Analytics by CSMBP-->
<script type="text/javascript">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '<?php echo $this->options->google_settings->profile_id; ?>', 'auto');
    ga('send', 'pageview');
</script>
<!-- End Google Analytics -->
            <?php
        }
    }

    /**
     * Renders Webmaster Tools authentication Meta Tags
     * 
     * @version 1.0.0
     * @return String
     */
    public function Cs_RenderHomeMetaTags(){ 
        if( !$this->page_mod['is_home'] && !$this->page_mod['is_home_page'] && !$this->page_mod['is_home_index'] ){
            return;
        }
        
        //filter home page url
        $this->page_mod['obj']->wp_options->url = GeneralHelpers::base_url();
        
        //Renders Webmaster Verification Meta Tags
        $webmaster_meta = array( 'name' => array() );
        if( isset( $this->options->aios_webmaster_verify_meta) &&  !empty( $webmaster_tags = $this->options->aios_webmaster_verify_meta ) ){
            foreach( $webmaster_tags as $name => $content ){
                if( ! empty( $name ) ){
                    $webmaster_meta['name'] += array( MetaTagAssets::$MTA['webmaster_tags'][ $name ] =>  $content );
                }
            }
        }
        
        $this->metaTags[] = $webmaster_meta;
    }
    
    /**
     * Title Generator
     * 
     * @param String $title
     * @param String $separator
     * @param String $separator_location
     * @return String
     */
    public function Cs_WpTitle($title, $separator = '', $separator_location = '') {
        $TF = new TitleFormater( $this->options );
        $TF->page_mod = isset( $this->page_mod ) ? $this->page_mod : '';
        return $this->page_title = $TF->title($title, $separator, $separator_location );
    }

    /**
     * Generate meta description
     * 
     * @return type
     */
    public function aios_metadesc( $echo = true ){
        if ( is_null( $this->metadesc ) ) {
            $MD = new MetaDescription();
            $MD->options = $this->options->aios_title_meta_default;
            $MD->page_mod = $this->page_mod;
            $this->metadesc = $MD->Cs_RendersMetaTagsdesc();
        }

        if ( $echo !== false ) {
            if ( is_string( $this->metadesc ) && $this->metadesc !== '' ) {
                echo "\n",'<meta name="description" content="', esc_attr( strip_tags( stripslashes( $this->metadesc ) ) ), '"/>';
            }
            elseif ( current_user_can( 'manage_options' ) && self::Cs_Get_Page_Mod()[ 'is_singular' ] ) {
                echo "\n", '<!-- ', __( 'Admin only notice: this page doesn\'t show a meta description because it doesn\'t have one, either write it for this page specifically or go into the -> On Page Optimization -> Meta Description and set up a rule.', SR_TEXTDOMAIN ), ' -->';
            }
        }
        else {
            return $this->metadesc;
        }
    }
    
    /**
     * Generate meta keywords
     * 
     * @return type
     */
    public function aios_metakeywords(){
        $MK = new MetaKeyword( self::$soptions->aios_title_meta_default );
        return $MK->Cs_RendersMetaTagskeyword();
    }
    
    /**
     * Generate Canonical Link
     * 
     * @param type $echo
     * @param type $un_paged
     * @param type $no_override
     * @return type
     */
    public function aios_canonical( $echo = true, $un_paged = false, $no_override = false ) {
        if ( is_null( $this->canonical ) ) {
            $MK = new Canonical( self::$soptions->aios_title_meta_default );
            $this->canonical = $MK->generate_canonical();
        }

        $canonical = $this->canonical;

        if ( $un_paged ) {
            $canonical = $this->canonical_unpaged;
        }
        elseif ( $no_override ) {
            $canonical = $this->canonical_no_override;
        }

        if ( $echo === false ) {
            return $canonical;
        }

        if ( is_string( $canonical ) && '' !== $canonical ) {
            echo "\n", '<link rel="canonical" href="' . esc_url( $canonical, null, 'other' ) . '" />';
        }
    }
    
    /**
     * Add Opengraph Attributes - prefix
     * 
     * @param String $html_attr
     * @return String
     */
    public function Cs_OgpnsAttributes( $html_attr ) {
        $OG = new SocialTags();
        $OG->options = $this->options;
        $OG->page_mod = isset( $this->page_mod ) ? $this->page_mod : '';
        $langAttr =  $OG->Cs_OgpnsAttributes( $html_attr );
        //get og type
        $this->page_mod = $OG->page_mod;
        return $langAttr;
    }
    
    /**
     * Generate meta roboots
     * 
     * @return type
     */
    public function Cs_RobotsBots(){
        $RB = new MetaRobots();
        $RB->options = $this->options->aios_meta_robots_status;
        $RB->page_mod = isset( $this->page_mod ) ? $this->page_mod : '';
        $this->metaTags[] = $RB->generate_meta_robots();
        return;
    }


    /**
     * Generate OG tags
     * 
     * @since 1.0.0
     * @return string
     */
    public function Cs_SocialTags(){
        $OG = new SocialTags();
        $OG->options = $this->options;
        $OG->page_mod = isset( $this->page_mod ) ? $this->page_mod : '';
        $OG->CsPlugin = isset( $this->CsPlugin ) ? $this->CsPlugin : '';
        $this->metaTags = array_merge_recursive( $this->metaTags, $OG->Cs_RenderMetaTags());
        return;
    }
    
    /**
     * Generate Twitter Card tags
     * 
     * @since 1.0.0
     * @return string
     */
    public function Cs_TwitterCard(){
        $OG = new OpenGraph();
        $OG->options = $this->options;
        $OG->page_mod = isset( $this->page_mod ) ? $this->page_mod : '';
        $OG->CsPlugin = isset( $this->CsPlugin ) ? $this->CsPlugin : '';
        $this->metaTags = array_merge_recursive( $this->metaTags, $OG->Cs_RenderMetaTags());
        return;
    }
    
    /**
     * Generate Meta Tags
     * 
     * @version 1.0.0
     * @return String Renders Final Meta Tags
     */
    private function _cs_GenerateMetaTags(){
        //caching function will go here
        
        /**
         * Action: "CSMBP_header" - Allow other plugins to output inside the AIOS section of the head section.
         */
        do_action( "CSMBP_header" );
        
//        pre_print( $this->options );
        
        //generate tags
        if( empty( $this->metaTags ) ){
            return;
        }
        
        foreach($this->metaTags as   $key => $tag_group ){
            foreach( $tag_group as $tag_type => $tags ){
                if( empty( $tags ) ){
                    continue;
                }
                
                foreach( $tags as $tag_name => $tag_val ){
                    
                    //check for stop render tags
                    if( in_array( $tag_name, $this->options->aios_metas_stop_render['meta'][ $tag_type ] ) ){
                        continue;
                    }
                    
                    if( is_array( $tag_val ) ){
                        foreach( $tag_val as $sub_tag_val ){
                            echo  "\n" . '<meta ' . $tag_type . '="'.$tag_name.'" content="'.$sub_tag_val.'"/>';
                        }
                    }else{
                        echo  "\n" . '<meta ' . $tag_type . '="'.$tag_name.'" content="'.$tag_val.'"/>';
                    }
                    
                }
            }
        }
    }

    
}