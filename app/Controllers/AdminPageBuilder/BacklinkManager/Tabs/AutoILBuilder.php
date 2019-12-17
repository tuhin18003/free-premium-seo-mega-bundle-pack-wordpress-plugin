<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs;
/**
 * AutoILBuilder
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Common\CommonText as CommonText;
use CsSeoMegaBundlePack\Helper;

class AutoILBuilder {
    
    public function load_page_builder(){
       $CommonText = new CommonText();
       $data = array(
           'CommonText' => $CommonText->common_text(),
           'current_tab' => __( 'Internal Link Builder', SR_TEXTDOMAIN ),
           'page_title' =>  __( 'Internal Link Builder', SR_TEXTDOMAIN ),
           'page_subtitle' =>  __( 'Automatically Build Your Website Internal Link', SR_TEXTDOMAIN ),
           'label_add_new_property' => __( 'Add a website url', SR_TEXTDOMAIN ),
           'placeholder_add_new_property' => __( 'Please enter a new website url. Example: http://example.com', SR_TEXTDOMAIN ),
           'label_automatic_backlink' => __( 'Automatically Find Backlinks', SR_TEXTDOMAIN ),
           'label_submit_btn' => __( 'Add Now', SR_TEXTDOMAIN ),
           'panel_subtitle' => __( '*Before adding a new website please read FAQ.', SR_TEXTDOMAIN ),
           'faq_title' => __( 'FAQ', SR_TEXTDOMAIN ),
           'faq_ques_ans' => array(
               'How many properties i can add?' => 'There is no limitation',
               'Does the system will automatically find backlinks for all properties?' => 'Yes! The system will find first 1000 backlinks automatically for all properties you added. But manually, you can add unlimited backlink for any properties  to track link juice'
           ),
           'loading_gif' => LOADING_GIF_URL
       );
       add_action( 'admin_footer', array( $this, 'add_new_property_script') );
       return  view( '@CsSeoMegaBundlePack/backlinkManager/tabs/AutoILBuilder.twig', $data );
    }
    
    /**
     * Add new property script
     * 
     * @since 1.0.0
     */
    public function add_new_property_script(){
        ?>
            <script type='text/javascript' src='<?php echo Helper::assetUrl('/default/app_core/backlink_manager/js/add_manage_property.js'); ?>'></script>
        <?php
    }
}
