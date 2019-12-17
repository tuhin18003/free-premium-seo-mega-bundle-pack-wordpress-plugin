<?php namespace CsSeoMegaBundlePack\Library\MetaBoxes\AllMetaBoxes;

/**
 * Library :  Social Meta Tags Options 
 * 
 * @since 1.0.0
 * @category Library
 * @author CodeSolz <customer-service@codesolz.net>
 */
use Herbert\Framework\Application;
use CsSeoMegaBundlePack\Helper;
use Herbert\Framework\Enqueue;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Models\CommonQuery\CsQuery;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsMetaTabGenerator;
use CsSeoMegaBundlePack\Library\CsSchema;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsForm;

class SocialMetaTagsOptions {
    
    /**
     * Hold Options
     *
     * @var type 
     */
    private $options;

    /**
     * Hold Nonce ID
     *
     * @var type 
     */
    private $_nonce_id = '_meta_tag_nonce';
    
    /**
     * Hold Nonce Action Value
     *
     * @var type 
     */
    private $_nonce = '_meta_tag_options';

    /**
     * Hold Tab Generator instance
     *
     * @var type object
     */
    private $_tabGenerator;
    
    /**
     * Hold data prefix
     *
     * @var type 
     */
    private $dataPrefix;

    public function __construct() {
        $this->dataPrefix = Helper::get('PLUGIN_DATA_PREFIX');
        
        /**load library script for this section**/
        $this->load_library_script();
        
        /**create instance of tab generator**/
        $this->_tabGenerator = new CsMetaTabGenerator();
        
        /**load custom script for this section**/
        add_action( 'admin_footer', array( $this, 'load_custom_script' ) );
    }
    
    /**
     * Register Meta box
     * 
     * @return array
     */
    public static function meta_box(){
        return array( array(
            'id' => 'meta_box',
            'title' => __( '%s - Social Meta Tag\'s options', SR_TEXTDOMAIN ),
            'callback' => array( 'CsSeoMegaBundlePack\\Library\\MetaBoxes\\AllMetaBoxes\\SocialMetaTagsOptions', 'meta_box_content' ),
            'screen' => '',
            'context' => 'normal',
            'priority' => 'core'
        ));
    }
    
    /**
     * Helper Script
     */
    private function load_library_script(){
        $app = new Application();
        $enqueue = new Enqueue($app);
        $enqueue->admin([
            'as'  => 'csmbp-icons',
            'src' => Helper::assetUrl('/default/css/icons.css'),
        ]);
        $enqueue->admin([
            'as'  => 'csmbp-wp-default-extension',
            'src' => Helper::assetUrl('/default/app_core/common/css/wp-default-extension.css'),
        ]);
        $enqueue->admin([
            'as'  => 'csmbp-responsive-tabs',
            'src' => Helper::assetUrl('/default/plugins/responsive-tabs/css/responsive-tabs.css'),
        ]);
        $enqueue->admin([
            'as'  => 'csmbp-style',
            'src' => Helper::assetUrl('/default/plugins/responsive-tabs/css/style.css'),
        ]);
        $enqueue->admin([
            'as'  => 'jquery.responsiveTabs.min',
            'src' => Helper::assetUrl('/default/plugins/responsive-tabs/js/jquery.responsiveTabs.js'),
        ]);
    }
    
    /**
     * Custom Script
     * 
     * @return string 
     */
    public function load_custom_script(){
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function( $ ) {
                $(".input-copy").on( 'focus', function(){
                    var $this = $(this);
                    $this.select();
                    document.execCommand( 'Copy', false, null );
                    $this.next("p").text('Coppied to Clipboard!').css('color','forestgreen').slideDown('slow');
                }).on('blur', function(){
                    $(this).next("p").slideUp('slow');
                });
                
                $("#CSMBP_og_post_type").on( 'change', function(){
                    var $this = $(this);
                    var $thisID = $("#CSMBP_og_post_type");
                    
                    console.log( $this );
                    console.log( $thisID );
                    
                    var type = $this.val();
                    if( type === 'article' ){
                        $("#CSMBP_og_art_section").removeAttr('disabled');
                        $(".csmbp_custom_og_tags").remove().slideUp('slow');
                    }else{
                        //disable article section
                        $this.attr( 'disabled', 'disabled' );
                        $("#CSMBP_og_art_section").attr( 'disabled', 'disabled');
                        $(".csmbp_custom_og_tags").remove().slideUp('slow');
                        $('<img src="<?php echo Helper::assetUrl('default/images/loader/progress-loader.gif'); ?>" class="csmbp-preloader"/>').insertAfter( $thisID );
                        var input_data = {
                            action : 'cs_custom_call',
                            data   : {
                                method : 'Library\\Includes\\Builders\\CsForm@getOgInputs',
                                type   : type
                            }
                        };
                        $.post( ajaxurl, input_data, function( ret ){
                            $this.removeAttr('disabled');
                            $("#CSMBP_social_meta_options_0").append( ret );
                            $('body, html').animate({ scrollTop: parseInt( $('.csmbp_custom_og_tags').offset().top ) - 50 }, 1000);
                            $(".csmbp-preloader").hide('slow');
                        });
                    }
                });
                
            });
            
        </script>
        <?php
    }
    
    /**
     * Meta Box Content
     * 
     * @param object $post
     * @param object $options
     */
    public function meta_box_content( $post, $options ){
        $this->_tabGenerator->tabID = 'social_meta_options';
        $this->_tabGenerator->nonce = array(
          'action' => $this->_nonce,
          'name' => $this->_nonce_id
        );
        $this->_tabGenerator->tabsName = array(
          __( 'Meta Content', SR_TEXTDOMAIN),
          __( 'Select Media', SR_TEXTDOMAIN),
          __( 'Validation', SR_TEXTDOMAIN),
        );
        
        $default_og_type = ''; 
        $og_art_section_disable  = true;
        if( isset( $options[ 'args' ]->aios_web_graph_options['og_post_type'] ) ){
            $default_og_type = trim( $options[ 'args' ]->aios_web_graph_options['og_post_type'] );
        }
        if( $default_og_type == 'article' ){
            $og_art_section_disable  = false;
        }
        
        $permanlink = get_permalink();
        $sharing_url_encoded = urlencode( $permanlink );
        //          $amp_url = $mod['is_post'] && function_exists( 'amp_get_permalink' ) ?
        //				'https://validator.ampproject.org/#url='.urlencode( amp_get_permalink( $mod['id'] ) ) : '';
        
//        pre_print( $post );
//        pre_print( $options[ 'args' ]->post );
       
            
        $schema_type = isset( $options[ 'args' ]->post->wp_options->is_term ) ? (new CsSchema())->get_schema_types( null, true) :  GeneralHelpers::Cs_Article_Topics();
        
        $og_post_type = '';
        if( empty( $og_post_type = $this->__get_value( $options, 'og_post_type' ) ) ){
            $og_post_type = $default_og_type;
        }
        
        //get og fields
        $og_fields = array();
        if( ! empty( $og_post_type ) && $og_post_type != '[None]'){
            $og_fields = (new CsForm())->getOgInputs( array( 'type' => $og_post_type ), false, $options );
        }
        
        array( array_merge( $a, $b ) );
        
        $this->_tabGenerator->tabsContent = array(
                array_merge( array(
                    'og_post_type' => array(
                        'label' => __( 'Select Open Graph Type', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Open Graph Type', SR_TEXTDOMAIN ),
                        'helptext' => __( 'Open Graph type for the WordPress post object (posts, pages, and custom post types). Custom post types with a matching Open Graph type name (article, book, place, product, etc.) will use that type name instead of the default selected. If not selected choose from here.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' => '[None]'
                        ) + array_keys( MetaTagAssets::$MTA['head']['og_type_ns'] ),
                        'option_key' => 'same_as_value',
                        'label_filter' => true,
                        'select_value' => $og_post_type,
                        'type' => 'select',
                        'wrapper' => true
                    ),
                    'og_art_section' => array(
                        'label' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? __( 'Schema Type', SR_TEXTDOMAIN ) : __( 'Article Topic Type', SR_TEXTDOMAIN ),
                        'helptext' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? __( 'Selet Schema Type', SR_TEXTDOMAIN ) : sprintf( __( 'A custom topic, different from the default Article Topic selected in the %1$s Website / Graph %2$s Settings.', SR_TEXTDOMAIN ),'<a href="'.admin_url('admin.php?page=cs-on-page-optimization&tab=MetaWebsiteGraph').'" target="_blank">', '</a>' ).' '.sprintf( __( 'The Facebook / Open Graph %1$s meta tag must be an "article" to enable this option.', SR_TEXTDOMAIN ), '<code>og:type</code>' ).' '.sprintf( __( 'This value will be used in the %1$s Facebook / Open Graph and Pinterest Rich Pin meta tags. Select "[None]" if you prefer to exclude the %1$s meta tag.', SR_TEXTDOMAIN ), '<code>article:section</code>' ),
                        'type' => 'select',
                        'options' => array_merge( array( 'none' => '[None]' ),  $schema_type ),
                        'option_key' => 'same_as_value',
                        'select_value' => $this->__get_value( $options, 'og_art_section' ),
                        'disabled' => $og_art_section_disable,
                        'wrapper' => true
                    ),
                    'og_title' => array(
                        'label' => __( 'Default Title', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Default Title', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom title for the Facebook / Open Graph, Pinterest Rich Pin, and Twitter Card meta tags (all Twitter Card formats).', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'maxlength' =>  isset( $options[ 'args' ]->aios_web_graph_options ) ? $options[ 'args' ]->aios_web_graph_options['og_title_len'] : 70,
                        'value' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? $this->__get_value( $options, 'og_title', 'name' ) : $this->__get_value( $options, 'og_title', 'post_title' ), 
                        'wrapper' => true
                    ),
                    'og_desc' => array(
                        'label' => __( 'Default Description (Facebook / Open Graph, LinkedIn, Pinterest Rich Pin)', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Default Description (Facebook / Open Graph, LinkedIn, Pinterest Rich Pin)', SR_TEXTDOMAIN ),
                        'helptext' => sprintf( __( 'A custom description for the Facebook / Open Graph %1$s meta tag and the default value for all other description meta tags.', SR_TEXTDOMAIN ), '<code>og:description</code>' ).' '.__( 'The default description value is based on the category / tag description or biographical info for users.', SR_TEXTDOMAIN ).' '.__( 'Update and save the custom Facebook / Open Graph description to change the default value of all other description fields.', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'maxlength' =>  isset( $options[ 'args' ]->aios_web_graph_options ) ? $options[ 'args' ]->aios_web_graph_options['og_desc_len'] : 300,
                        'value' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? $this->__get_value( $options, 'og_desc', 'description' ) : $this->__get_value( $options, 'og_desc', 'post_content' ), 
                        'wrapper' => true
                    ),
                    'seo_desc' => array(
                        'label' => __( 'Google Search / SEO Description', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Google Search / SEO Description', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom description for the Google Search / SEO description meta tag.', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'maxlength' =>  isset( $options[ 'args' ]->aios_web_graph_options ) ? $options[ 'args' ]->aios_web_graph_options['og_desc_len'] : 300,
                        'value' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? $this->__get_value( $options, 'seo_desc', 'description' ) : $this->__get_value( $options, 'seo_desc', 'post_content' ),  
                        'wrapper' => true
                    ),
                    'tc_desc' => array(
                        'label' => __( 'Twitter Card Description', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Twitter Card Description', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom description for the Twitter Card description meta tag (all Twitter Card formats).', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'maxlength' =>  isset( $options[ 'args' ]->aios_web_graph_options ) ? $options[ 'args' ]->aios_web_graph_options['og_desc_len'] : 300,
                        'value' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? $this->__get_value( $options, 'tc_desc', 'description' ) : $this->__get_value( $options, 'tc_desc', 'post_content' ),  
                        'wrapper' => true
                    ),
                    'sharing_url' => array(
                        'label' => __( 'Sharing URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Sharing URL', SR_TEXTDOMAIN ),
                        'helptext' =>  __( 'A custom sharing URL used for the Facebook / Open Graph / Pinterest Rich Pin meta tags, Schema markup, and (optional) social sharing buttons.', SR_TEXTDOMAIN ).' '.__( 'Please make sure any custom URL you enter here is functional and redirects correctly.', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'value' => $this->__get_value( $options, 'sharing_url' ),  
                        'wrapper' => true
                    ),
                    'st1' => array(
                        'helptext' => __( '%s Structured Data / Schema Markup', SR_TEXTDOMAIN ),
                        'type' => 'section_title'
                    ),
                    'schema_desc' => array(
                        'label' => __( 'Schema Description', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Schema Description', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom description for the Schema item type\'s description property.', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'maxlength' =>  isset( $options[ 'args' ]->aios_web_graph_options ) ? $options[ 'args' ]->aios_web_graph_options['og_desc_len'] : 300,
                        'value' => isset( $options[ 'args' ]->post->wp_options->is_term ) ? $this->__get_value( $options, 'schema_desc', 'description' ) : $this->__get_value( $options, 'schema_desc', 'post_content' ), 
                        'wrapper' => true
                    ),   
                ), (array)$og_fields ) ,
                array(
                    'st2' => array(
                        'helptext' => __( '%s All Social Websites / Open Graph', SR_TEXTDOMAIN ),
                        'type' => 'section_title'
                    ),
                    
                    'open_graph_img_dimensions' => array(
                        'wrapper' => true,
                        'label' => __( 'Open Graph Image Dimensions', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The image dimensions used in the Facebook / Open Graph meta tags (the default dimensions are 600x315 cropped). Facebook has published a preference for Open Graph image dimensions of 1200x630px cropped (for retina and high-PPI displays), 600x315px cropped as a minimum (the default settings value), and ignores images smaller than 200x200px. Note that images in the WordPress Media Library and/or NextGEN Gallery must be larger than your chosen image dimensions.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'og_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'type' => 'input',
                                'value' => $this->__get_value( $options, 'og_img_width' ),
                            ),
                            'og_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'concat_text' => 'X',
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                                'value' => $this->__get_value( $options, 'og_img_height' ),
                            ),
                            'og_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                                'value' => $this->__get_value( $options, 'og_img_crop' ),
                            ),
                            'og_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'select_value' => $this->__get_value( $options, 'og_img_crop_x' ),
                                'type' => 'select',
                            ),
                            'og_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'select_value' => $this->__get_value( $options, 'og_img_crop_y' ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'og_img_id' => array(
                        'wrapper' => true,
                        'label' => __( 'Image ID', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom image ID to include first, before any featured, attached, or content images.', SR_TEXTDOMAIN ),
                        'type' => 'miscellaneous',
                        'options' => array(
                            'og_def_img_id' => array(
                                'placeholder' => __( 'image id', SR_TEXTDOMAIN ),
                                'input_width' => '100px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'after_text' => __( 'in media library', SR_TEXTDOMAIN ),
                                'value' => $this->__get_value( $options, 'og_def_img_id' ),
                            )
                        )
                    ),
                    'og_def_img_url' => array(
                        'wrapper' => true,
                        'label' => __( 'or an Image URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an Image URL', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'helptext' => __( 'A custom image URL (instead of an image ID) to include first, before any featured, attached, or content images. Please make sure your custom image is large enough, or it may be ignored by social website(s). Facebook has published a preference for Open Graph image dimensions of 1200x630px cropped (for retina and high-PPI displays), 600x315px cropped as a minimum (the default settings value), and ignores images smaller than 200x200px. <em>This field is disabled if a custom image ID has been selected.</em>', SR_TEXTDOMAIN ),
                        'value' => $this->__get_value( $options, 'og_def_img_url' ),
                    ),
                    'og_def_img_on_index' => array(
                        'label' => __( 'Maximum Images', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The maximum number of images to include in the Facebook / Open Graph meta tags. There is no advantage in selecting a maximum value greater than 1.', SR_TEXTDOMAIN ),
                        'type' => 'select',
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'select_value' => $this->__get_value( $options, 'og_def_img_on_index' ),
                        'wrapper' => true
                    ),
                    'og_vid_embed' => array(
                        'label' => __( 'Video Embed HTML', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Video Embed HTML', SR_TEXTDOMAIN ),
                        'helptext' => __( 'Custom Video Embed HTML to use for the first in the Facebook / Open Graph, Pinterest Rich Pin, and \'Player\' Twitter Card meta tags. If the URL is from Youtube, Vimeo or Wistia, an API connection will be made to retrieve the preferred sharing URL, video dimensions, and video preview image.', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'type' => 'textarea',
                        'rows' => 3,
                        'width' => '100%',
                        'value' => $this->__get_value( $options, 'og_vid_embed' ), 
                        'wrapper' => true
                    ),   
                    'og_vid_url' => array(
                        'label' => __( 'or a Video URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Video URL', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom Video URL to include first in the Facebook / Open Graph, Pinterest Rich Pin, and \'Player\' Twitter Card meta tags. If the URL is from Youtube, Vimeo or Wistia, an API connection will be made to retrieve the preferred sharing URL, video dimensions, and video preview image.', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'value' => $this->__get_value( $options, 'og_vid_url' ),
                        'wrapper' => true
                    ),
                    'og_vid_title' => array(
                        'label' => __( 'Video Name / Title', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Video Name / Title', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The video name / title is used for Schema JSON-LD markup (extension plugin required), which can be read by both Google and Pinterest.', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'value' => $this->__get_value( $options, 'og_vid_title' ),
                        'wrapper' => true
                    ),
                    'og_vid_desc' => array(
                        'label' => __( 'Video Description', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Video Description', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The video description text is used for Schema JSON-LD markup (extension plugin required), which can be read by both Google and Pinterest.', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'value' => $this->__get_value( $options, 'og_vid_desc' ),
                        'wrapper' => true
                    ),
                    'og_vid_max' => array(
                        'label' => __( 'Maximum Images', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The maximum number of embedded videos to include in the Facebook / Open Graph meta tags. There is no advantage in selecting a maximum value greater than 1.', SR_TEXTDOMAIN ),
                        'type' => 'select',
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'value' => $this->__get_value( $options, 'og_vid_max' ),
                        'wrapper' => true
                    ),
                    'og_vid_prev_img' => array(
                        'label' => __( 'Include Preview Images', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Video Description', SR_TEXTDOMAIN ),
                        'helptext' => __( 'When video preview images are enabled and available, they are included in webpage meta tags before any custom, featured, attached, etc. images.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                        'value' => $this->__get_value( $options, 'og_vid_prev_img' ),
                        'wrapper' => true
                    ),
                    'st3' => array(
                        'helptext' => __( '%s Structured Data / Schema Markup / Pinterest', SR_TEXTDOMAIN ),
                        'type' => 'section_title'
                    ),
                    'schema_img_dimensions' => array(
                        'wrapper' => true,
                        'label' => __( 'Open Graph Image Dimensions', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The image dimensions used in the Google / Schema meta tags and JSON-LD markup (the default dimensions are 800x1600 uncropped). The minimum image width required by Google is 696px for the resulting resized image. If you do not choose to crop this image size, make sure the height value is large enough for portrait / vertical images.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'schema_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'type' => 'input',
                                'value' => $this->__get_value( $options, 'schema_img_width' ),
                            ),
                            'schema_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'concat_text' => 'X',
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                                'value' => $this->__get_value( $options, 'schema_img_height' ),
                            ),
                            'schema_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                                'value' => $this->__get_value( $options, 'schema_img_crop' ),
                            ),
                            'schema_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'select_value' => $this->__get_value( $options, 'schema_img_crop_x' ),
                                'type' => 'select',
                            ),
                            'schema_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'select_value' => $this->__get_value( $options, 'schema_img_crop_y' ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'schema_img_id' => array(
                        'wrapper' => true,
                        'label' => __( 'Image ID', SR_TEXTDOMAIN ),
                        'helptext' => __( 'A custom image ID to include first in the Google / Schema meta tags and JSON-LD markup, before any featured, attached, or content images.', SR_TEXTDOMAIN ),
                        'type' => 'miscellaneous',
                        'options' => array(
                            'og_def_img_id' => array(
                                'placeholder' => __( 'image id', SR_TEXTDOMAIN ),
                                'input_width' => '100px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'after_text' => __( 'in media library', SR_TEXTDOMAIN ),
                                'value' => $this->__get_value( $options, 'schema_def_img_id' ),
                            )
                        )
                    ),
                    'schema_def_img_url' => array(
                        'wrapper' => true,
                        'label' => __( 'or an Image URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an Image URL', SR_TEXTDOMAIN ),
                        'type' => 'input',
                        'input_type' => 'text',
                        'class' => 'form-control',
                        'helptext' => __( 'A custom image URL (instead of an image ID) to include first in the Google / Schema meta tags and JSON-LD markup. <em>This field is disabled if a custom image ID has been selected.</em>', SR_TEXTDOMAIN ),
                        'value' => $this->__get_value( $options, 'schema_def_img_url' ),
                    ),
                    'schema_def_img_on_index' => array(
                        'label' => __( 'Maximum Images', SR_TEXTDOMAIN ),
                        'helptext' => __( 'The maximum number of images to include in the Google / Schema meta tags and JSON-LD markup.', SR_TEXTDOMAIN ),
                        'type' => 'select',
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'select_value' => $this->__get_value( $options, 'schema_def_img_on_index' ),
                        'wrapper' => true
                    ),
                ),
                array(
                    'fb_debugger' => array(
                        'label' => __( 'Facebook Debugger', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Open Graph', SR_TEXTDOMAIN ),
                            'href' => sprintf( "https://developers.facebook.com/tools/debug/og/object?q=%s", $sharing_url_encoded ),
                            'class' => 'button-primary'
                        ),
                        'helptext' => __( "Facebook and most social websites read Open Graph meta tags. The Facebook debugger allows you to refresh Facebook's cache, while also validating the Open Graph meta tag values. The Facebook debugger remains the most stable and reliable method to verify Open Graph meta tags.", SR_TEXTDOMAIN ),
                        'wrapper' => true
                    ),
                    'google_sd_validator' => array(
                        'label' => __( 'Google Structured Data Testing Tool', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Data Markup', SR_TEXTDOMAIN ),
                            'href' => sprintf( 'https://search.google.com/structured-data/testing-tool/u/0/#url=%s', $sharing_url_encoded),
                            'class' => 'button-primary'
                        ),
                        'helptext' => __( "Verify that Google can correctly parse your structured data markup (meta tags, Schema, Microdata, and JSON-LD markup) for Google Search and Google+.", SR_TEXTDOMAIN ),
                        'wrapper' => true
                    ),
                    'rich_pins' => array(
                        'label' => __( 'Pinterest Rich Pin Validator', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Rich Pins', SR_TEXTDOMAIN ),
                            'href' => sprintf( 'https://developers.pinterest.com/tools/url-debugger/?link=%s', $sharing_url_encoded),
                            'class' => 'button-primary'
                        ),
                        'helptext' => __( "Validate the Open Graph / Rich Pin meta tags and apply to have them shown on Pinterest zoomed pins.", SR_TEXTDOMAIN ),
                        'wrapper' => true
                    ),
                    'twitter_card' => array(
                        'label' => __( 'Twitter Card Validator', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Twitter Card', SR_TEXTDOMAIN ),
                            'href' => 'https://cards-dev.twitter.com/validator',
                            'class' => 'button-primary'
                        ),
                        'copyurl' => $permanlink,
                        'helptext' => __( 'The Twitter Card Validator does not accept query arguments — paste the following URL in the Twitter Card Validator "Card URL" input field (copy the URL using the clipboard icon):', SR_TEXTDOMAIN ),
                        'wrapper' => true
                    ),
                    'html_markdup' => array(
                        'label' => __( 'W3C Markup Validation', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Html Markup', SR_TEXTDOMAIN ),
                            'href' => sprintf( 'https://validator.w3.org/nu/?doc=%s', $sharing_url_encoded ),
                            'class' => 'button-primary'
                        ),
                        'helptext' => sprintf( __( 'Validate the HTML syntax and HTML 5 conformance of your meta tags and theme templates markup.
                                    When the %1$sMeta Property Containers%2$s option is enabled, the W3C validator will show errors for itemprop attributes in meta elements — you may ignore these errors or disable the %1$sMeta Property Containers%2$s option.' , SR_TEXTDOMAIN ), '<a href="'.admin_url('admin.php?page=cs-on-page-optimization&tab=MetaWebstiePublisher').'" target="_blank">', '</a>'),
                        'wrapper' => true
                    ),
                    'bing_markdup' => array(
                        'label' => __( 'Bing Markup Validation', SR_TEXTDOMAIN ),
                        'type' => 'validator',
                        'anchor' => array(
                            'text' => __( 'Click Here to Validate Bing Markup', SR_TEXTDOMAIN ),
                            'href' => sprintf( 'https://www.bing.com/webmaster/diagnostics/markup/validator?url=%s', $sharing_url_encoded),
                            'class' => 'button-primary'
                        ),
                        'helptext' => __( 'Verify the markup that you have added to your pages with Markup Validator. Get an on-demand report that shows the markup we’ve discovered, including HTML Microdata, Microformats, RDFa, Schema.org, and OpenGraph. To get started simply sign in or sign up for Bing Webmaster Tools.', SR_TEXTDOMAIN),
                        'wrapper' => true
                    ),
                )                
          );
          $this->_tabGenerator->get_meta_contents();
    }
    
    /**
     * Get Values
     * 
     * @param type $options
     * @param type $id
     * @param type $wp_id
     * @return type
     */
    private function __get_value( $options, $id, $wp_id = '' ){
        if( isset( $options[ 'args' ]->post->wp_options->$id ) && !empty( $value = $options[ 'args' ]->post->wp_options->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) && !empty( $value = $options[ 'args' ]->post->{"{$this->dataPrefix}options"}->$id ) ){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_graph_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_graph_options[ $id ] )){
            return $value;
        }
        elseif( isset( $options[ 'args' ]->aios_web_pub_options[ $id ] ) && !empty( $value = $options[ 'args' ]->aios_web_pub_options[ $id ] )){
            return $value;
        }
    }

    /**
     * Save custom data
     * 
     * @param type $post
     * @return boolean
     */
    public function on_save( $post ) {
//        $this->dataPrefix = Helper::get('PLUGIN_DATA_PREFIX');
        if ( 
            ! isset($_POST[ $this->dataPrefix . $this->_nonce_id  ]) 
            || ! wp_verify_nonce( $_POST[ $this->dataPrefix . $this->_nonce_id ], $this->dataPrefix . $this->_nonce ) 
        ) return false;
            
        unset( $_POST[ $this->dataPrefix . $this->_nonce_id ]);
       
        if( !empty( $_POST ) ){
            foreach( $_POST as $key => $val ){
                if( strpos( $key, $this->dataPrefix) !== false ){
                    if( isset( $_POST['action'] ) && $_POST['action'] == 'editedtag' ){
                        CsQuery::Cs_UpdateTermmeta( $_POST['tag_ID'], $key, CsQuery::check_evil_script( $val ) );
                    }else{
                        CsQuery::Cs_Update_Postmeta( $_POST['post_ID'], $key, CsQuery::check_evil_script( $val ) );
                    }
                }
            }    
        }    
        
        return true;
    }
    
   
}




