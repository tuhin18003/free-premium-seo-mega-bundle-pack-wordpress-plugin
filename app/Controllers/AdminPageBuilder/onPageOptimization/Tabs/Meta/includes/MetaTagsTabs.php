<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes;
/**
 * Tabs Fields
 * 
 * @package On Page Optimization
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\onPageOptimization\Tabs\Meta\includes\MetaTagAssets;
use CsSeoMegaBundlePack\HelperFunctions\GeneralHelpers;
use CsSeoMegaBundlePack\Library\CsSchema;
use CsSeoMegaBundlePack\Library\Includes\Builders\CsForm;

class MetaTagsTabs{
    
    /**
     * Metas 
     *
     * @var type 
     */
    private $opt;
    private $defaults;
    private $CsSchema;
    protected $types_exp = MONTH_IN_SECONDS;	// schema types array expi
    private $taglist_opts = array();
            
    function __construct() {
        //init settings
        $this->init_settings();
    }
    
    /**
     * Get all meta tags list
     * 
     * @return type
     */
    public function get_all_metas(){
        $metas = array();
        foreach( $this->meta_tagslist_tabs() as $key => $value ){
            $func = "filter_taglist_{$key}";
            $metas = array_merge( (array)$metas, array( $key => $this->$func()));
        }
        return $metas;
    }
    
    public function filter_taglist_fb() {
        return $this->get_taglist(array('/^add_(meta)_(property)_((fb|al):.+)$/'));
    }
    
    public function filter_taglist_og() {
        return $this->get_taglist(array( '/^add_(meta)_(property)_(.+)$/' ));
    }

    public function filter_taglist_twitter() {
        return $this->get_taglist( array( '/^add_(meta)_(name)_(twitter:.+)$/' ) );
    }

    public function filter_taglist_schema() {
        return $this->get_taglist( array('/^add_(meta|link)_(itemprop)_(.+)$/'));
    }

    public function filter_taglist_other() {
        return $this->get_taglist( array('/^add_(link)_([^_]+)_(.+)$/', '/^add_(meta)_(name)_(.+)$/'));
    }

    private function get_taglist(array $opt_preg_include) {
        $table_cells = array();
        foreach ($opt_preg_include as $preg) {
            foreach ($this->defaults as $opt => $val) {
                if (strpos($opt, 'add_') !== 0) { // optimize
                    continue;
                } elseif (isset($this->taglist_opts[$opt])) { // check cache for tags already shown
                    continue;
                } elseif (!preg_match($preg, $opt, $match)) { // check option name for a match
                    continue;
                }

                $highlight = '';
                $this->taglist_opts[$opt] = $val;
                switch ($opt) {
                    // disabled with a constant instead
                    case 'add_meta_name_generator':
                        continue 2;
                }
                if( !empty( $match ) && is_array($match)){
                    $table_cells[] = array($match[1], $match[2], $match[3]);
                }
            }
        }
//        GeneralHelpers::array_sort_by_column($table_cells, 2);
        return $table_cells;
    }
    
    /**
     * Get Meta Tags List Tabs
     * 
     * @return type
     */
    public function meta_tagslist_tabs(){
        return array(
            'fb' => __( 'Facebook', SR_TEXTDOMAIN ),
            'og' => __( 'Open Graph', SR_TEXTDOMAIN ),
            'twitter' => __( 'Twitter', SR_TEXTDOMAIN ),
            'schema' => __( 'Schema', SR_TEXTDOMAIN ),
            'other' => __( 'SEO / Other', SR_TEXTDOMAIN ),
        );
    }
    
    /**
     * Website & Publishers
     * 
     * @return type
     */
    public function tabs_website_publishers(){
        return array(
            'facebook' => __( 'Facebook', SR_TEXTDOMAIN ),
            'google' => __( 'Google / Schema', SR_TEXTDOMAIN ),
            'pinterest' => __( 'Pinterest', SR_TEXTDOMAIN ),
            'twitter' => __( 'Twitter', SR_TEXTDOMAIN ),
            'other' => __( 'Other', SR_TEXTDOMAIN ),
        );
    }
    
    /**
     * 
     */
    public function tab_assets_website_publishers(){
        
//        pre_print( $this->CsSchema->get_schema_types( null, true) );
        
        return array(
            'facebook' => array(
                'inputs' => array(
                    'facebook_publisher_url' => array(
                        'label' => __( 'Facebook Business Page URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Facebook Business Page URL', SR_TEXTDOMAIN ),
                        'help_text' => __( "If you have a <a href=&quot;https://www.facebook.com/business&quot; target=&quot;_blank&quot;>Facebook Business Page for your website / business</a>, you may enter its URL here (for example, the Facebook Business Page URL for CodeSolz is <a href=&quot;https://www.facebook.com/CodeSolzCom&quot; target=&quot;_blank&quot;>https://www.facebook.com/CodeSolzCom</a>). The Facebook Business Page URL will be used in Open Graph <em>article</em> webpages and in the site's Schema Organization markup. Google Search may use this information to display additional publisher / business details in its search results.", SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'fb:app_id' => array(
                        'label' => __( 'Facebook Application ID', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Facebook Application ID', SR_TEXTDOMAIN ),
                        'help_text' => __( 'If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application ID for your website</a>, enter it here. The Facebook Application ID will appear in webpage meta tags and is used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for accounts associated with that Application ID.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'fb:admins' => array(
                        'label' => __( 'or Facebook Admin Username(s)', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Facebook Admin Username(s)', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The Facebook Admin Username(s) are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for your website. Note that these are <strong>user account names, not Facebook Page names</strong>. Enter one or more Facebook user names, separated with commas. When viewing your own Facebook wall, your user name is located in the URL (for example, https://www.facebook.com/<strong>user_name</strong>). Enter only the user names, not the URLs. You may update your Facebook user name in the <a href="https://www.facebook.com/settings?tab=account&section=username&view" target="_blank">Facebook General Account Settings</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'fb_author_name' => array(
                        'label' => __( 'Author / Person Name Format', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Author / Person Name Format', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select an <strong>Author Name Format</strong> for the author / Person markup, or <strong>[None]</strong> to disable this feature (the recommended value is <strong>Display Name</strong>).", SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' => '[None]',
                            'fullname' => __( 'First and Last Names', SR_TEXTDOMAIN ),
                            'display_name' => __( 'Display Name (default)', SR_TEXTDOMAIN ),
                            'nickname' => __( 'Nickname', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                    ),
                    'al:ios:url' => array(
                        'label' => __( 'Your IOS APP Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter a custom URL that will be used to launch your app', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A custom URL that will be used to launch your app. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'url',
                        'type' => 'input'
                    ),
                    'al:ios:app_store_id' => array(
                        'label' => __( 'Your IOS APP store ID / package', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter the app store ID / package that will handle the content.', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The app store ID / package that will handle the content. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'al:ios:app_name' => array(
                        'label' => __( 'Your IOS APP name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter the app name that will handle the content.', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The app name that will handle the content. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'al:android:url' => array(
                        'label' => __( 'Your Android APP Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter a custom URL that will be used to launch your app', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A custom URL that will be used to launch your app. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'url',
                        'type' => 'input'
                    ),
                    'al:android:package' => array(
                        'label' => __( 'Your Android APP package', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter the app package that will handle the content.', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The app package that will handle the content. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'al:android:app_name' => array(
                        'label' => __( 'Your Android APP name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter the app name that will handle the content.', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The app name that will handle the content. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'al:web:url' => array(
                        'label' => __( 'APP shared link', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter a shared link for an app whose web content may be found at another URL.', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A shared link for an app whose web content may be found at another URL. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'al:web:should_fallback' => array(
                        'label' => __( 'No web content', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'An app that has no web content, but has apps on iOS and Android', SR_TEXTDOMAIN ),
                        'help_text' => __( 'An app that has no web content, but has apps on iOS and Android. <a href="https://developers.facebook.com/docs/applinks/metadata-reference/" target="_blank">Facebook Metadata Reference</a>.', SR_TEXTDOMAIN ),
                        'options' => array(
                            '0' =>  __( 'None', SR_TEXTDOMAIN ),
                            'true' =>  __( 'True', SR_TEXTDOMAIN ),
                            'false' =>  __( 'False', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                    ),
                )
            ),
            'google' => array(
                'inputs' => array(
                    'google_publisher_url' => array(
                        'label' => __( 'Google+ Business Page URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Google+ Business Page URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'If you have a <a href="http://www.google.com/+/business/" target="_blank">Google+ Business Page for your website / business</a>, you may enter its URL here (for example, the Google+ Business Page URL for Surnia Ulula is <a href="https://plus.google.com/+CodeSolz/" target="_blank">https://plus.google.com/+CodeSolz/</a>). The Google+ Business Page URL will be used in a link relation head tag, and the schema publisher (Organization) social JSON. Google Search may use this information to display additional publisher / business details in its search results.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'seo_desc_len' => array(
                        'label' => __( 'Search / SEO Description Length', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Search / SEO Description Length', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum length of text used for the Google Search / SEO description meta tag. The length should be at least 156 characters or more (the default is 156 characters).', SR_TEXTDOMAIN ),
                        'input_type' => 'number',
                        'type' => 'input'
                    ),
                    'seo_author_field' => array(
                        'label' => __( 'Author Link URL Field', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Author Link URL Field', SR_TEXTDOMAIN ),
                        'help_text' => __( "AIOS can include an <em>author</em> and <em>publisher</em> links in the webpage head section. These are not Facebook / Open Graph and Pinterest Rich Pin meta property tags — they are used primarily by Google's search engine to associate Google+ profiles with search results. Select which field to use from the author's profile for the <em>author</em> link tag.", SR_TEXTDOMAIN ),
                        'options' => array(
                            'author' =>  __( 'Author Archive', SR_TEXTDOMAIN ),
                            'gplus' =>  __( 'Google+ URL (default)', SR_TEXTDOMAIN ),
                            'instagram' =>  __( 'Instagram URL', SR_TEXTDOMAIN ),
                            'linkedin' =>  __( 'LinkedIn URL', SR_TEXTDOMAIN ),
                            'myspace' =>  __( 'Myspace URL', SR_TEXTDOMAIN ),
                            'pinterest' =>  __( 'Pinterest URL', SR_TEXTDOMAIN ),
                            'tumblr' =>  __( 'Tumblr URL', SR_TEXTDOMAIN ),
                            'twitter' =>  __( 'Twitter @username', SR_TEXTDOMAIN ),
                            'url' =>  __( ' Website', SR_TEXTDOMAIN ),
                            'youtube' =>  __( 'YouTube Channel URL', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                    ),
                    'schema_add_noscript' => array(
                        'label' => __( 'Meta Property Containers', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Check for Meta Property Containers', SR_TEXTDOMAIN ),
                        'help_text' => __( 'When additional schema properties are available (product ratings, recipe ingredients, etc.), one or more <code>noscript</code> containers may be included in the webpage head section. <code>noscript</code> containers are read correctly by Google and Pinterest, but the W3C Validator will show errors for the included meta tags (these errors can be safely ignored). The <code>noscript</code> containers are always disabled for AMP webpages, and always enabled for the Pinterest crawler.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'checkbox'
                    ),
                    'multiple_checkbox' => array(
                        'label' => __( 'Google Knowledge Graph', SR_TEXTDOMAIN ),
                        'type' => 'multiple_checkbox',
                        'checkboxes' => array(
                            'schema_website_json' => array(
                                'help_text' => __( 'Include <a href="https://developers.google.com/structured-data/site-name" target="_blank">Website Information</a> for Google Search', SR_TEXTDOMAIN ),
                            ),
                            'is_checkbox_schema_organization_json' => array(
                                'help_text' => __( 'Include <a href="https://developers.google.com/structured-data/customize/social-profiles" target="_blank">Organization Social Profile</a>', SR_TEXTDOMAIN ),
                            ),
                            'is_checkbox_schema_person_json' => array(
                                'help_text' => __( ' Include <a href="https://developers.google.com/structured-data/customize/social-profiles" target="_blank">Person Social Profile</a> for Site Owner', SR_TEXTDOMAIN ),
                            ),
                        )
                    ),
                    'schema_logo_url' => array(
                        'label' => __( 'Organization Logo URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Organization Logo URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A URL for the website / organization&#039;s logo image that Google can use in search results and its <a href="https://developers.google.com/structured-data/customize/logos" target="_blank">Knowledge Graph</a>', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'schema_banner_url' => array(
                        'label' => __( 'Organization Banner URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Organization Banner URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A URL for the website / organization&#039;s banner image &mdash; &lt;strong&gt;measuring exactly 600x60px&lt;/strong&gt; &mdash; that Google / Google News can use to display content from Schema Article webpages.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'schema_img_max' => array(
                        'label' => __( 'Maximum Images to Include', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Maximum Images to Include', SR_TEXTDOMAIN ),
                        'help_text' => __( "The maximum number of images to include in the Google / Schema markup -- this includes the <strong>featured</strong> or <strong>attached</strong> images, and any images found in the Post or Page content. If you select <em>0</em>, then no images will be listed in the Google / Schema meta tags (<strong>not recommended</strong>).", SR_TEXTDOMAIN ),
                        'options' => GeneralHelpers::numbers_array(1, 20),
                        'type' => 'select',
                    ),
                    'google_card_img_dimensions' => array(
                        'label' => __( 'Schema Image Dimensions', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The image dimensions used in the Google / Schema meta tags and JSON-LD markup (the default dimensions are 800x1600 uncropped). The minimum image width required by Google is 696px for the resulting resized image. If you do not choose to crop this image size, make sure the height value is large enough for portrait / vertical images.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'schema_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'type' => 'input'
                            ),
                            'schema_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                                'concat_text' => 'X'
                            ),
                            'schema_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                            ),
                            'schema_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                            'schema_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'schema_desc_len' => array(
                        'label' => __( 'Maximum Description Length', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Maximum Description Length<', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum length of text used for the Google+ / Schema description meta tag. The length should be at least 156 characters or more (the default is 250 characters).', SR_TEXTDOMAIN ),
                        'input_type' => 'number',
                        'input_width' => '100px',
                        'type' => 'input'
                    ),
                    'schema_author_name' => array(
                        'label' => __( 'Author / Person Name Format', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Author / Person Name Format', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select an <strong>Author Name Format</strong> for the author / Person markup, or <strong>[None]</strong> to disable this feature (the recommended value is <strong>Display Name</strong>).", SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' => '[None]',
                            'fullname' => __( 'First and Last Names', SR_TEXTDOMAIN ),
                            'display_name' => __( 'Display Name (default)', SR_TEXTDOMAIN ),
                            'nickname' => __( 'Nickname', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                    ),
                    'schema_type_for_home_index' => array(
                        'label' => __( 'Item Type for Blog Front Page', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Item Type for Blog Front Page', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select the Schema type for a blog (non-static) front page. The default Schema type is <a href='https://schema.org/CollectionPage' target='_blank'> https://schema.org/CollectionPage</a>.", SR_TEXTDOMAIN ),
                        'options' => $this->CsSchema->get_schema_types( null, true),
                        'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                        'schema' => true,
                        'type' => 'select',
                    ),
                    'schema_type_for_home_page' => array(
                        'label' => __( 'Item Type for Static Front Page', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Item Type for Static Front Page', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select the Schema type for a static front page. The default Schema type is <a href='https://schema.org/WebSite' target='_blank'> https://schema.org/WebSite</a>.", SR_TEXTDOMAIN ),
                        'options' => $this->CsSchema->get_schema_types( null, true),
                        'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                        'schema' => true,
                        'type' => 'select',
                    ),
                    'schema_type_for_archive_page' => array(
                        'label' => __( 'Item Type for Archive Page', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Item Type for Archive Page', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select the Schema type for archive pages (Category, Tags, etc.). The default Schema type is <a href='https://schema.org/CollectionPage' target='_blank'> https://schema.org/CollectionPage</a>.", SR_TEXTDOMAIN ),
                        'options' => $this->CsSchema->get_schema_types( null, true),
                        'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                        'schema' => true,
                        'type' => 'select',
                    ),
                    'schema_type_for_user_page' => array(
                        'label' => __( 'Item Type for User / Author Page', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Item Type for User / Author Page', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select the Schema type for user / author pages. The default Schema type is  <a href='https://schema.org/ProfilePage' target='_blank'> https://schema.org/ProfilePage</a>.", SR_TEXTDOMAIN ),
                        'options' => $this->CsSchema->get_schema_types( null, true),
                        'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                        'schema' => true,
                        'type' => 'select',
                    ),
                    'schema_type_for_search_page' => array(
                        'label' => __( 'Item Type for Search Results Page', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Item Type for Search Results Page', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select the Schema type for search results pages. The default Schema type is <a href='https://schema.org/SearchResultsPage' target='_blank'> https://schema.org/SearchResultsPage</a>.", SR_TEXTDOMAIN ),
                        'options' => $this->CsSchema->get_schema_types( null, true),
                        'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                        'schema' => true,
                        'type' => 'select',
                    ),
                ) +
                $this->get_schema_types_for_post_types()
            ),
            'pinterest' => array(
                'inputs' => array(
                    'pinterest_publisher_url' => array(
                        'label' => __( 'Pinterest Company Page URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Pinterest Company Page URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'If you have a <a href="https://business.pinterest.com/" target="_blank">Pinterest Business Page for your website / business</a>, you may enter its URL here. The Publisher Business Page URL will be used in the schema publisher (Organization) social JSON. Google Search may use this information to display additional publisher / business details in its search results.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'p_author_name' => array(
                        'label' => __( 'Author Name Format', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Author Name Format', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Pinterest ignores Facebook-style Author Profile URLs in the <code>article:author</code> Open Graph meta tags. A different meta tag value can be used when the Pinterest crawler is detected. Select an <em>Author Name Format</em> for the <code>article:author</code> meta tag or "[None]" to disable this feature (the recommended value is "Display Name").', SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' => '[None]',
                            'fullname' => __( 'First and Last Names', SR_TEXTDOMAIN ),
                            'display_name' => __( 'Display Name (default)', SR_TEXTDOMAIN ),
                            'nickname' => __( 'Nickname', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                    ),
                    'p_add_img_html' => array(
                        'label' => __( 'Add Hidden Image for Pin It Button', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Add the Google / Schema image to the content (in a hidden container) for the Pinterest Pin It browser button.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                    'p_add_nopin_header_img_tag' => array(
                        'label' => __( 'Add "nopin" to Header Image Tag', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Add a "nopin" attribute to the header image (since WP v4.4) to prevent the Pin It button from suggesting that image.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                    'p_add_nopin_media_img_tag' => array(
                        'label' => __( 'Add "nopin" to Media Lib Images', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Add a "nopin" attribute to images from the WordPress Media Library to prevent the Pin It button from suggesting those images.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                )
            ),
            'twitter' => array(
                'inputs' => array(
                    'twitter:site' => array(
                        'label' => __( 'Twitter Business @username ', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Twitter Business @username ', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The <a href="https://business.twitter.com/" target="_blank">Twitter @username for your website and/or business</a> (not your personal Twitter @username). As an example, the Twitter @username for Code Solz is <a href="https://twitter.com/codesolz" target="_blank">codesolz</a>. The website / business @username is also used for the schema publisher (Organization) social JSON. Google Search may use this information to display additional publisher / business details in its search results.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'tc_desc_len' => array(
                        'label' => __( 'Maximum Description Length ', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Maximum Description Length ', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum length of text used for the Twitter Card description. The length should be at least 156 characters or more (the default is 200 characters).', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'tc_type_default' => array(
                        'label' => __( 'Twitter Card Type by Default & Home Page', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The Twitter Card type for all other images (default, image from content text, etc).', SR_TEXTDOMAIN ),
                        'options' => array(
                            'summary' => __( 'Summary (default)', SR_TEXTDOMAIN ),
                            'summary_large_image' => __( 'Summary Large Image ', SR_TEXTDOMAIN ),
                            'player' => __( 'Player Card', SR_TEXTDOMAIN ),
                            'app' => __( 'App card', SR_TEXTDOMAIN ),
                        ),
                        'select_default' => 'summary',
                        'type' => 'select',
                    ),
                    'tc_type_post' => array(
                        'label' => __( 'Twitter Card for Post / Page', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The Twitter Card type for posts / pages with a custom, featured, and/or attached image.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'summary' => __( 'Summary', SR_TEXTDOMAIN ),
                            'summary_large_image' => __( 'Summary Large Image (default)', SR_TEXTDOMAIN ),
                            'player' => __( 'Player Card', SR_TEXTDOMAIN ),
                            'app' => __( 'App card', SR_TEXTDOMAIN ),
                        ),
                        'select_default' => 'summary_large_image',
                        'type' => 'select',
                    ),
                    'section_title_twitter_summary_card' => array(
                        'type' => 'section_title',
                        'help_text' => __( 'Following information will be use to generate twitter summary card', SR_TEXTDOMAIN )
                    ),
                    'tc_card_img_dimensions' => array(
                        'label' => __( 'Summary Card Image Dimensions', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The dimension of content images provided for the <a href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary" target="_blank">Summary Card</a> ( Images for this Card support an aspect ratio of 1:1 with minimum dimensions of 144x144 or maximum of 4096x4096 pixels. Images must be less than 5MB in size. The image will be cropped to a square on all platforms.)', SR_TEXTDOMAIN ),
                        'options' => array(
                            'tc_sum_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'min' => 144,
                                'max' => 4096,
                                'type' => 'input'
                            ),
                            'tc_sum_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'min' => 144,
                                'max' => 4096,
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                                'concat_text' => 'X'
                            ),
                            'tc_sum_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                            ),
                            'tc_sum_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                            'tc_sum_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'tc_card_lrg_img_dimensions' => array(
                        'label' => __( 'Large Image Card Img Dimensions', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The dimension of Post Meta, Featured or Attached images provided for the <a href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/summary-card-with-large-image" target="_blank">Large Image Summary Card</a> ( Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'tc_lrg_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'min' => 300,
                                'max' => 4096,
                                'type' => 'input'
                            ),
                            'tc_lrg_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'min' => 157,
                                'max' => 4096,
                                'concat_text' => 'X',
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                            ),
                            'tc_lrg_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                            ),
                            'tc_lrg_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                            'tc_lrg_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'section_title_twitter_app_card' => array(
                        'type' => 'section_title',
                        'help_text' => __( 'Following information will be use to generate twitter app card.', SR_TEXTDOMAIN )
                    ),
                    'twitter:app:name:iphone' => array(
                        'label' => __( 'Your Iphone APP name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your Iphone app name', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app name in iphone app store (.i.e. “Cannonball”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:id:iphone' => array(
                        'label' => __( 'Your Iphone APP ID', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your Iphone app id', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app ID in the iphone App Store (.i.e. “307234931”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:url:iphone' => array(
                        'label' => __( 'Your Iphone APP Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your iphone app url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'your iphone app url (.i.e. “http://cannonball.fabric.io/poem/5149e249222f9e600a7540ef”)', SR_TEXTDOMAIN ),
                        'input_type' => 'url',
                        'type' => 'input'
                    ),
                    'twitter:app:name:ipad' => array(
                        'label' => __( 'Your Ipad APP name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your Ipad app name', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app name in ipad app name (.i.e. “Cannonball”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:id:ipad' => array(
                        'label' => __( 'Your Ipad APP ID', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your Ipad app id', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app ID in the App Store (.i.e. “307234931”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:url:ipad' => array(
                        'label' => __( 'Your Ipad APP Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your Ipad app url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'your iphone app url (.i.e. “http://cannonball.fabric.io/poem/5149e249222f9e600a7540ef”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:name:googleplay' => array(
                        'label' => __( 'Your Google APP Name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your google app name', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app name in Google Play (.i.e. “Cannonball”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:id:googleplay' => array(
                        'label' => __( 'Your Google APP ID', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your google app id', SR_TEXTDOMAIN ),
                        'help_text' => __( 'should be the numeric representation of your app ID in Google Play (.i.e. “com.android.app”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:app:url:googleplay' => array(
                        'label' => __( 'Your Google APP Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your google app url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'your Google app url (.i.e. “http://cannonball.fabric.io/poem/5149e249222f9e600a7540ef”)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'section_title_twitter_player_card' => array(
                        'type' => 'section_title',
                        'help_text' => __( 'Following information will be use to generate twitter player card.', SR_TEXTDOMAIN )
                    ),
                    'twitter:player' => array(
                        'label' => __( 'Twitter Player Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your twitter player url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'HTTPS URL to iFrame player. This must be a HTTPS URL which does not generate active mixed content warnings in a web browser. The audio or video player must not require plugins such as Adobe Flash.', SR_TEXTDOMAIN ),
                        'input_type' => 'url',
                        'type' => 'input'
                    ),
                    'twitter:player:width' => array(
                        'label' => __( 'Twitter Player Width', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your twitter player width', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Width of iFrame specified in twitter:player in pixels.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:player:height' => array(
                        'label' => __( 'Twitter Player Height', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter your twitter player height', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Height of iFrame specified in twitter:player in pixels.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:image' => array(
                        'label' => __( 'Twitter Player Cover Image', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an image url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Image to be displayed in place of the player on platforms that don’t support iFrames or inline players. You should make this image the same dimensions as your player. Images with fewer than 68,600 pixels (a 262x262 square image, or a 350x196 16:9 image) will cause the player card not to render. Images must be less than 5MB in size. JPG, PNG, WEBP and GIF formats are supported. Only the first frame of an animated GIF will be used. SVG is not supported.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'twitter:image:alt' => array(
                        'label' => __( 'Twitter Player Image Alt', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an image alt text', SR_TEXTDOMAIN ),
                        'help_text' => __( 'A text description of the image conveying the essential nature of an image to users who are visually impaired. Maximum 420 characters.', SR_TEXTDOMAIN ),
                        'type' => 'textarea',
                        'maxlength' => 420
                    ),
                    'twitter:player:stream' => array(
                        'label' => __( 'Twitter Player Stream Url', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an stream url', SR_TEXTDOMAIN ),
                        'help_text' => __( 'URL to raw stream that will be rendered in Twitter’s mobile applications directly. If provided, the stream must be delivered in the MPEG-4 container format (the .mp4 extension). The container can store a mix of audio and video with the following codecs:Video: H.264, Baseline Profile (BP), Level 3.0, up to 640 x 480 at 30 fps.Audio: AAC, Low Complexity Profile (LC)', SR_TEXTDOMAIN ),
                        'input_type' => 'url',
                        'type' => 'input'
                    ),
                    'twitter:player:stream:content_type' => array(
                        'label' => __( 'Twitter Player Stream Content Type', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter an stream content type', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The MIME type/subtype combination that describes the content contained in twitter:player:stream. Takes the form specified in RFC 6381. Currently supported content_type values are those defined in RFC 4337 (MIME Type Registration for MP4)', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                )
            ),
            'other' => array(
                'inputs' => $this->other_tabs_assets()
            ),
        );
        
    }

    /**
     * Init settings
     */
    private function init_settings(){
        $this->opt = MetaTagAssets::$MTA['opt'];
        $this->defaults = $this->opt['defaults'];
        $this->CsSchema = new CsSchema();
    }
    
    /**
     * Get Schema type For Custom post types 
     * 
     * @return type
     */
    private function get_schema_types_for_post_types(){
        $array = array();
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        foreach($post_types as $post_type){
            $array = array_merge( $array, array(
                "schema_type_for_{$post_type->name}" => array(
                    'label' => sprintf( __( 'Item Type for %s', SR_TEXTDOMAIN ), $post_type->labels->name ),
                    'placeholder' => sprintf( __( 'Item Type for %s', SR_TEXTDOMAIN ), $post_type->labels->name ),
                    'help_text' => sprintf( __( 'Item Type for %s', SR_TEXTDOMAIN ), $post_type->labels->name ),
                    'options' => $this->CsSchema->get_schema_types( null, true),
                    'select_default' => __( '[None]', SR_TEXTDOMAIN ),
                    'schema' => true,
                    'type' => 'select',
                )
            ));
        }
        return $array;
    }
    
    /**
     * Get Other Tabs Assets
     * 
     * @return type
     */
    private function other_tabs_assets(){
        $cache_salt = 'AIOS_Social_Accounts';
        $cache_id = GeneralHelpers::Cs_Md5_Hash( $cache_salt );

        if ( $this->types_exp > 0 ) {
            $this->types_cache = get_transient( $cache_id );	// returns false when not found
        }
        
        
        if ( ! isset( $this->types_cache['social_accounts'] ) ) {	// from transient cache or not, check if filtered
            $this->types_cache['social_accounts'] = MetaTagAssets::$MTA['form']['social_accounts'];
            $this->types_cache['social_accounts'] = GeneralHelpers::Cs_Array_Unset( $this->types_cache['social_accounts'], array( 'fb_publisher_url', 'seo_publisher_url') );
            
            //unset twitter & pinterset
            unset( $this->types_cache['social_accounts'][ 'tc_site' ] );
            unset( $this->types_cache['social_accounts'][ 'p_publisher_url' ] );
            if ( $this->types_exp > 0 ) {
                set_transient( $cache_id, $this->types_cache, $this->types_exp );
            }
        }
        
        
        $accounts = array();
        if( $this->types_cache ){
            foreach( $this->types_cache['social_accounts'] as $id => $label){
                $accounts = array_merge( $accounts, array(
                    "{$id}" => array(
                        'label' => $label,
                        'placeholder' => sprintf( __( 'Enter your %s', SR_TEXTDOMAIN ), $label ),
                        'help_text' => sprintf( __( 'Enter your %s', SR_TEXTDOMAIN ), $label ),
                        'input_type' => 'text',
                        'type' => 'input'
                    )
                ));
            }
        }
        
        return $accounts;
    }
    
    /**
     * Website & Graph
     * 
     * @return type
     */
    public function tabs_website_graph(){
        return array(
            'general' => __( 'Site Information', SR_TEXTDOMAIN ),
            'content' => __( 'Descriptions', SR_TEXTDOMAIN ),	// same text as Social Settings tab
            'author' => __( 'Authorship', SR_TEXTDOMAIN ),
            'images' => __( 'Images', SR_TEXTDOMAIN ),
            'videos' => __( 'Videos', SR_TEXTDOMAIN ),
        );
    }
    
    /**
     * 
     */
    public function tabs_website_graph_assets(){
        
        return array(
            'general' => array(
                'inputs' => array(
                    'site_name' => array(
                        'label' => __( 'Website Name', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Website Name', SR_TEXTDOMAIN ),
                        'help_text' => sprintf(__( 'The WordPress Site Name is used for the Facebook / Open Graph and Pinterest Rich Pin <code>og:site_name</code> meta tag. You may override <a href="%s" target="_blank">the default WordPress Site Title value</a>.', SR_TEXTDOMAIN ), admin_url('options-general.php') ),
                        'input_type' => 'text',
                        'type' => 'input',
                        'default' => get_bloginfo('name')
                    ),
                    'site_desc' => array(
                        'label' => __( 'Website Description', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Website Description', SR_TEXTDOMAIN ),
                        'help_text' => sprintf(__( 'The WordPress tagline is used as a description for the blog (non-static) front page, and as a fallback for the Facebook / Open Graph and Pinterest Rich Pin <code>og:description</code> meta tag. You may override <a href="%s" target="_blank">the default WordPress Tagline value</a> here, to provide a longer and more complete description of your website.', SR_TEXTDOMAIN ), admin_url('options-general.php') ),
                        'type' => 'textarea',
                        'default' => get_bloginfo('description')
                    ),
                    'og_post_type' => array(
                        'label' => __( 'Default Post / Page Type', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Default Post / Page Type', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The default Open Graph type for the WordPress post object (posts, pages, and custom post types). Custom post types with a matching Open Graph type name (article, book, place, product, etc.) will use that type name instead of the default selected here. Please note that each type has a unique set of meta tags, so by selecting "website" here, you are excluding all "article" related meta tags (<code>article:author</code>, <code>article:section</code>, etc.).', SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' => '[None]'
                        ) + CsForm::get_labels( array_keys( MetaTagAssets::$MTA['head']['og_type_ns'] ) ),
                        'option_key' => 'same_as_value',
                        'label_filter' => true,
                        'type' => 'select',
                    ),
                    'og_art_section' => array(
                        'label' => __( 'Default Article Topic', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Default Article Topic', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The topic that best describes the Posts and Pages on your website. This value will be used in the <code>article:section</code> Facebook / Open Graph and Pinterest Rich Pin meta tags. Select "[None]" if you prefer to exclude the <code>article:section</code> meta tag. This plugin also allows you to select a custom Topic for each individual Post and Page.', SR_TEXTDOMAIN ),
                        'options' => array_merge( array( 'none' => '[None]' ), GeneralHelpers::Cs_Article_Topics()),
                        'type' => 'select'
                    ),
                )
            ), // end general tab
            'content' => array(
                'inputs' => array(
                    'min_title_len' => array(
                        'label' => __( 'Maximum Title Length', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum length of text used in the Facebook / Open Graph and Rich Pin title tag (default is 70 characters).', SR_TEXTDOMAIN ),
                        'options' => array(
                            'og_title_len' => array(
                                'placeholder' => __( '70', SR_TEXTDOMAIN ),
                                'default' => 70,
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'type' => 'input',
                                'after_text' => __( 'characters or less (hard limit), and warn at', SR_TEXTDOMAIN ),
                            ),
                            'og_title_warn' => array(
                                'placeholder' => __( '40', SR_TEXTDOMAIN ),
                                'default' => 40,
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'after_text' => __( 'characters (soft limit)', SR_TEXTDOMAIN ),
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'max_des_len' => array(
                        'label' => __( 'Maximum Description Length', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum length of text used in the Facebook / Open Graph and Rich Pin description tag. The length should be at least 156 characters or more, and the default is 300 characters.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'og_desc_len' => array(
                                'placeholder' => __( '70', SR_TEXTDOMAIN ),
                                'default' => 300,
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'type' => 'input',
                                'after_text' => __( 'characters or less (hard limit), and warn at', SR_TEXTDOMAIN ),
                            ),
                            'og_desc_warn' => array(
                                'placeholder' => __( '40', SR_TEXTDOMAIN ),
                                'default' => 200,
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'after_text' => __( 'characters (soft limit)', SR_TEXTDOMAIN ),
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'og_desc_hashtags' => array(
                        'label' => __( 'Add Hashtags to Descriptions', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Add Hashtags to Descriptions', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum number of tag names (converted to hashtags) to include in the Facebook / Open Graph and Pinterest Rich Pin description, tweet text, and social captions. Each tag name is converted to lowercase with whitespaces removed.  Select "0" to disable the addition of hashtags.', SR_TEXTDOMAIN ),
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'type' => 'select',
                    ),
                    'og_page_parent_tags' => array(
                        'label' => __( 'Add Parent Page Tags / Hashtags', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Add the WordPress tags from the <em>Page</em> ancestors (parent, parent of parent, etc.) to the Facebook / Open Graph and Pinterest Rich Pin article tags and Hashtag list (default is unchecked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                )
            ),
            'author' => array(
                'inputs' => array(
                    'og_author_field' => array(
                        'label' => __( 'Author Profile URL Field', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Author Profile URL Field', SR_TEXTDOMAIN ),
                        'help_text' => __( "Select which contact field to use from the author's WordPress profile page for the Facebook / Open Graph <code>article:author</code> meta tag. The preferred setting is the Facebook URL field (default value). Select &quot;[None]&quot; if you prefer to exclude the <code>article:author</code> meta tag, and prevent Facebook from including author attribution in shared links.", SR_TEXTDOMAIN ),
                        'options' => array(
                            'none' =>  __( 'None', SR_TEXTDOMAIN ),
                            'facebook' =>  __( 'Facebook User URL (default)', SR_TEXTDOMAIN ),
                            'author' =>  __( 'Author Archive', SR_TEXTDOMAIN ),
                            'gplus' =>  __( 'Google+ URL ', SR_TEXTDOMAIN ),
                            'instagram' =>  __( 'Instagram URL', SR_TEXTDOMAIN ),
                            'linkedin' =>  __( 'LinkedIn URL', SR_TEXTDOMAIN ),
                            'myspace' =>  __( 'Myspace URL', SR_TEXTDOMAIN ),
                            'pinterest' =>  __( 'Pinterest URL', SR_TEXTDOMAIN ),
                            'skype' =>  __( 'Skype Username', SR_TEXTDOMAIN ),
                            'soundcloud' =>  __( 'Soundcloud URL', SR_TEXTDOMAIN ),
                            'tumblr' =>  __( 'Tumblr URL', SR_TEXTDOMAIN ),
                            'twitter' =>  __( 'Twitter @username', SR_TEXTDOMAIN ),
                            'url' =>  __( ' Website', SR_TEXTDOMAIN ),
                            'youtube' =>  __( 'YouTube Channel URL', SR_TEXTDOMAIN ),
                        ),
                        'type' => 'select',
                        'default_value' => 'facebook'
                    ),
                    'og_author_fallback' => array(
                        'label' => __( "Fallback to Author's Archive Page", SR_TEXTDOMAIN ),
                        'help_text' => sprintf( __( 'If the Author Profile URL Field is not a valid URL, then fallback to using the author archive URL from this website (example: "%s"). Uncheck this option to disable the author URL fallback feature (default is unchecked).', SR_TEXTDOMAIN ), site_url('/author/username')),
                        'type' => 'checkbox'
                    ),
                    'og_author_gravatar_img' => array(
                        'label' => __( "Include Author Gravatar Image", SR_TEXTDOMAIN ),
                        'help_text' => __( "Check this option to include the author's Gravatar image in meta tags for author index / archive webpages (default is checked).", SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                )
            ),
            'images' => array(
                'inputs' => array(
                    'og_img_max' => array(
                        'label' => __( 'Maximum Images to Include', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Maximum Images to Include', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum number of images to include in the Facebook / Open Graph meta tags -- this includes the <em>featured</em> image, <em>attached</em> images, and any images found in the content. If you select "0", then no images will be listed in the Facebook / Open Graph meta tags (<strong>not recommended</strong>). If no images are listed in your meta tags, social websites may choose an unsuitable image from your webpage (including headers, sidebars, etc.). There is no advantage in selecting a maximum value greater than 1.', SR_TEXTDOMAIN ),
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'type' => 'select',
                    ),
                    'open_graph_img_dimensions' => array(
                        'label' => __( 'Open Graph Image Dimensions', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The image dimensions used in the Facebook / Open Graph meta tags (the default dimensions are 600x315 cropped). Facebook has published a preference for Open Graph image dimensions of 1200x630px cropped (for retina and high-PPI displays), 600x315px cropped as a minimum (the default settings value), and ignores images smaller than 200x200px. Note that images in the WordPress Media Library and/or NextGEN Gallery must be larger than your chosen image dimensions.', SR_TEXTDOMAIN ),
                        'options' => array(
                            'og_img_width' => array(
                                'placeholder' => __( 'Width', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'input_type' => 'number',
                                'min' => 200,
                                'type' => 'input'
                            ),
                            'og_img_height' => array(
                                'placeholder' => __( 'Height', SR_TEXTDOMAIN ),
                                'input_width' => '75px',
                                'type' => 'input',
                                'input_type' => 'number',
                                'min' => 200,
                                'concat_text' => 'X',
                                'after_text' => __( 'px', SR_TEXTDOMAIN ),
                            ),
                            'og_img_crop' => array(
                                'line_break' => true,
                                'after_text' => __( 'Crop From', SR_TEXTDOMAIN ),
                                'type' => 'checkbox',
                            ),
                            'og_img_crop_x' => array(
                                'sub_options' => array(
                                    'left' => __( 'Left', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'right' => __( 'Right', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                            'og_img_crop_y' => array(
                                'sub_options' => array(
                                    'top' => __( 'Top', SR_TEXTDOMAIN ),
                                    'center' => __( 'Center', SR_TEXTDOMAIN ),
                                    'bottom' => __( 'Bottom', SR_TEXTDOMAIN ),
                                ),
                                'type' => 'select',
                            ),
                        ),
                        'type' => 'miscellaneous',
                    ),
                    'og_def_img_url' => array(
                        'label' => __( 'Default / Fallback Image URL', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Enter Default / Fallback Image URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'You can enter a default image URL (including the http:// prefix) instead of choosing an image ID — if a default image ID is specified, the image URL option is disabled. <strong>The image URL option allows you to use an image outside of a managed collection (WordPress Media Library or NextGEN Gallery), and/or a smaller logo style image.</strong> The image should be at least 200x200 or more in width and height. The default image is used for index / archive pages, and as a fallback for Posts and Pages that do not have a suitable image featured, attached, or in their content.', SR_TEXTDOMAIN ),
                        'input_type' => 'text',
                        'type' => 'input'
                    ),
                    'og_def_img_on_index' => array(
                        'label' => __( "Use Default Image on Archive", SR_TEXTDOMAIN ),
                        'help_text' => __( "Check this option to force the default image on index webpages (blog front page, archives, categories). If this option is <em>checked</em>, but a Default Image ID or URL has not been defined, then <strong>no image will be included in the meta tags</strong>. If the option is <em>unchecked</em>, then CSMBP will use image(s) from the first entry on the webpage (default is checked).", SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                    'og_def_img_on_search' => array(
                        'label' => __( "Use Default Image on Search Results", SR_TEXTDOMAIN ),
                        'help_text' => __( "Check this option to force the default image on search results. If this option is <em>checked</em>, but a Default Image ID or URL has not been defined, then <strong>no image will be included in the meta tags</strong>. If the option is <em>unchecked</em>, then CSMBP will use image(s) returned in the search results (default is unchecked).", SR_TEXTDOMAIN ),
                        'type' => 'checkbox'
                    ),
                )
            ),
            'videos' => array(
                'inputs' => array(
                    'og_max_vid_include' => array(
                        'label' => __( 'Maximum Videos to Include', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Maximum Videos to Include', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The maximum number of videos, found in the Post or Page content, to include in the Facebook / Open Graph and Pinterest Rich Pin meta tags. If you select "0", then no videos will be listed in the Facebook / Open Graph and Pinterest Rich Pin meta tags. There is no advantage in selecting a maximum value greater than 1.', SR_TEXTDOMAIN ),
                        'options' => GeneralHelpers::numbers_array(0, 10),
                        'type' => 'select',
                    ),
                    'og_https_vid_reqs' => array(
                        'label' => __( 'Use HTTPS for Video API Requests', SR_TEXTDOMAIN ),
                        'placeholder' => __( 'Select Use HTTPS for Video API Requests', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Use an HTTPS connection whenever possible to retrieve information about videos from YouTube, Vimeo, Wistia, etc. (default is checked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_vid_prev_img' => array(
                        'label' => __( 'Include Video Preview Images', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Include video preview images in the webpage meta tags (default is unchecked). When video preview images are enabled and available, they are included before any custom, featured, attached, etc. images.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_vid_incl_text_html_type' => array(
                        'label' => __( 'Include Embed text/html Type', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Include additional Open Graph meta tags for the embed video URL as a text/html video type (default is checked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_vid_force_autoplay' => array(
                        'label' => __( 'Force Autoplay when Possible', SR_TEXTDOMAIN ),
                        'help_text' => __( 'When possible, add or modify the "autoplay" argument of video URLs in webpage meta tags (default is checked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_def_fallback_vid_url' => array(
                        'label' => __( 'Default / Fallback Video URL', SR_TEXTDOMAIN ),
                        'help_text' => __( 'The Default Video URL is used as a <strong>fallback value for Posts and Pages that do not have any videos</strong> in their content. Do not specify a Default Video URL <strong>unless you want to include video information in all your Posts and Pages</strong>.', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_def_vid_on_arch' => array(
                        'label' => __( 'Use Default Video on Archive', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Check this option to force the default video on index webpages (blog front page, archives, categories). If this option is <em>checked</em>, but a Default Video URL has not been defined, then <strong>no video will be included in the meta tags</strong> (this is usually preferred). If the option is <em>unchecked</em>, then CSMBP will use video(s) from the first entry on the webpage (default is checked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                    'og_def_vid_on_search' => array(
                        'label' => __( 'Use Default Video on Search Results', SR_TEXTDOMAIN ),
                        'help_text' => __( 'Check this option to force the default video on search results. If this option is <em>checked</em>, but a Default Video URL has not been defined, then <strong>no video will be included in the meta tags</strong>. If the option is <em>unchecked</em>, then CSMBP will use video(s) returned in the search results (default is unchecked).', SR_TEXTDOMAIN ),
                        'type' => 'checkbox',
                    ),
                )
            ),
            
        );
    }
    
    /**
     * Get Author Contacts
     * 
     * @return type
     */
    public function author_contacts(){
        $cache_id = GeneralHelpers::Cs_Md5_Hash( 'AIOS_Author_Contacts' );

        if ( $this->types_exp > 0 ) {
            $this->types_cache = get_transient( $cache_id );	// returns false when not found
        }
        
       
        
        if ( ! isset( $this->types_cache['author_contacts'] ) || empty($this->types_cache['author_contacts']) ) {	// from transient cache or not, check if filtered
            if(is_array( $sorted_opt_pre = $this->opt['cm_prefix'])){
                ksort( $sorted_opt_pre );
                $methods = array();
                foreach ( $sorted_opt_pre as $id => $opt_pre ) {
                    $cm_cb = 'plugin_cm_'.$opt_pre.'_enabled'; 
                    $cm_name = 'plugin_cm_'.$opt_pre.'_name';
                    $cm_label = 'plugin_cm_'.$opt_pre.'_label';
                    
                    if ( isset( $this->defaults[$cm_cb] ) ) {
                        $methods = array_merge( $methods, array(
                            $this->defaults[$cm_name] => $this->defaults[$cm_label]
                        ));
                    }            
                }
                $this->types_cache['author_contacts'] = $methods;
                if ( $this->types_exp > 0 ) {
                    set_transient( $cache_id, $this->types_cache, $this->types_exp );
                }
            }
        }
        return isset($this->types_cache['author_contacts']) ? $this->types_cache['author_contacts'] : false;
    }
    
    /**
     * Author contact fields form
     * 
     * @return type
     */
    public function get_author_contact_fields(){
        $inputs = array();
        $contacts = $this->author_contacts();
        if($contacts){
            foreach($contacts as $id => $label){
                $inputs = array_merge( $inputs, array(
                    $id => array(
                        'label' => sprintf( __( 'Enter %s', SR_TEXTDOMAIN ), $label),
                        'placeholder' => sprintf( __( 'Enter %s', SR_TEXTDOMAIN ), $label),
                        'help_text' => sprintf( __( 'You Can Enter %s from here or from <a href="%s" target="_blank">the user profile page</a>', SR_TEXTDOMAIN ), $label, admin_url('profile.php')),
                        'input_type' => 'text',
                        'type' => 'input'
                    )
                ));
            }
        }
        return $inputs;
    }
    
    /**
     * Filter Contacts in User Profile page 
     * 
     * @param type $default_contacts
     * @return type
     */
    public function filter_author_profile_page_contacts( $default_contacts = false ){
        $contacts = $this->author_contacts();
        unset( $default_contacts['googleplus'] );
        if($contacts){
            // Display each fields
            foreach($contacts as $id => $label){
                if ( !isset( $default_contacts[ $id ] ) ){
                    $default_contacts[ $id ] = $label;
                }
            }
        }

        // Returns the contact methods
        return $default_contacts;
    }
    
}


