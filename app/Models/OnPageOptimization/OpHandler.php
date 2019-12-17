<?php namespace CsSeoMegaBundlePack\Models\OnPageOptimization;

/**
 * Actions Handler
 * 
 * @package Social Analytics
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
if ( ! defined( 'CSMBP_VERSION' ) ) {
    exit;
}

use CsSeoMegaBundlePack\HelperFunctions\AjaxHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagsTabs;

class OpHandler {
    
    private $http;
    
    function __construct(\Herbert\Framework\Http $http) {
        $this->http = $http;
    }


    /**
     */
    public function title_metas(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-title-meta-optimization', $_aios_nonce, true );
        
        //dynamically get form values
        $module = array(
            'seperator' => $this->http->get('seperator', false)
        );
        for($i=1; $i<=4; $i++){
            if( $this->http->has("inputs_tab{$i}")){
                $inputs = explode(',',$this->http->get("inputs_tab{$i}", false));
                foreach($inputs as $input){
                    $get_val = CsQuery::check_evil_script($this->http->get( $input, false));
                    if(!empty($get_val)){
                        $module = array_merge( $module, array( $input => $get_val ));
                    }
                }
            }
        }

        CsQuery::Cs_Update_Option(array(
            'option_name' => "aios_title_meta_default",
            'option_value' => $module,
            'json' => true
        ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Set meta robots status
     */
    public function meta_robots_settings(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-metarobots-settings', $_aios_nonce, true );
        
        //dynamically get form values
        $module = array();
        if( $this->http->has("inputs")){
            $inputs = explode(',',$this->http->get("inputs", false));
            foreach($inputs as $input){
                $get_val = CsQuery::check_evil_script($this->http->get( $input, false));
                if(!empty($get_val)){
                    $module = array_merge( $module, array( $input => $get_val ));
                }
            }
        }

        CsQuery::Cs_Update_Option(array(
            'option_name' => "aios_meta_robots_status",
            'option_value' => $module,
            'json' => true
        ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    public function meta_tags_list_settings(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-metatags-settings', $_aios_nonce, true );
        
        if( $this->http->has('metas')){
            CsQuery::Cs_Update_Option(array(
                'option_name' => "aios_metas_stop_render",
                'option_value' => $this->http->get('metas', false),
                'json' => true
            ));
        }
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Settings for Websites & Publishers
     * 
     */
    public function social_web_publishers_settings(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-social-websites-publishers', $_aios_nonce, true );
        
        //dynamically get form values
        $user_inputs = array();
        foreach((new MetaTagsTabs())->tabs_website_publishers() as $key => $val){
            if( $this->http->has("inputs_{$key}")){
                $inputs = explode(',',$this->http->get("inputs_{$key}", false));
                foreach($inputs as $input){
                    $get_val = CsQuery::check_evil_script($this->http->get( $input, false));
                    if(!empty($get_val)){
                        $user_inputs = array_merge( $user_inputs, array( $input => $get_val ));
                    }
                }
            }
        }
        
        CsQuery::Cs_Update_Option(array(
                'option_name' => "aios_web_pub_options",
                'option_value' => $user_inputs,
                'json' => true
            ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Social Websites / Graph Settings
     */
    public function social_web_graph_settings(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-social-websites-graph', $_aios_nonce, true );
        
        //dynamically get form values
        $user_inputs = array();
        foreach((new MetaTagsTabs())->tabs_website_graph() as $key => $val){
            if( $this->http->has("inputs_{$key}")){
                $inputs = explode(',',$this->http->get("inputs_{$key}", false));
                foreach($inputs as $input){
                    $get_val = CsQuery::check_evil_script($this->http->get( $input, false));
                    if(!empty($get_val)){
                        $user_inputs = array_merge( $user_inputs, array( $input => $get_val ));
                    }
                }
            }
        }
        
        CsQuery::Cs_Update_Option(array(
                'option_name' => "aios_web_graph_options",
                'option_value' => $user_inputs,
                'json' => true
            ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
    /**
     * Admin Author contact settings
     */
    public function admin_auth_contact_settings(){
        $_aios_nonce = $this->http->get('_wpnonce', false);
        //check security
        AjaxHelpers::check_ajax_referer( 'aios-admin-auth-contacts-settings', $_aios_nonce, true );
        
        //dynamically get form values
        $user_inputs = array();
        if( $this->http->has("inputs")){
            $inputs = explode(',',$this->http->get("inputs", false));
            foreach($inputs as $input){
                $input_val = CsQuery::check_evil_script($this->http->get( $input, false));
                if(!empty($input_val)){
                    $user_inputs = array_merge( $user_inputs, array( $input => $input_val ));
                    update_user_meta( 1, $input, $input_val); 
                }
            }
        }
        
        CsQuery::Cs_Update_Option(array(
                'option_name' => "aios_auth_contact_options",
                'option_value' => $user_inputs,
                'json' => true
            ));
        
        $json_data = array(
            'type' => 'success',
            'title' => __( 'Success!', SR_TEXTDOMAIN ),
            'text' => __( 'Your Settings has been saved successfully.', SR_TEXTDOMAIN ),
        );

        AjaxHelpers::output_ajax_response( $json_data );
    }
    
}
