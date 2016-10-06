<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */

if (!class_exists('Redux_Framework_sample_config')) {

    class Redux_Framework_sample_config
    {

        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;

        public function __construct()
        {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (true == Redux_Helpers::isTheme(__FILE__)) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }


        public function initSettings()
        {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            add_action('redux/loaded', array($this, 'remove_demo'));

            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);

            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );

            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**
         * This is a test function that will let you see when the compiler hook occurs.
         * It only runs if a field    set with compiler=>true is changed.
         * */
        function compiler_action($options, $css, $changed_values)
        {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r($changed_values); // Values that have changed since the last save
            echo "</pre>";
       
        }

        /**
         * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
         * Simply include this function in the child themes functions.php file.
         * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
         * so you must use get_template_directory_uri() if you want to use any of the built in icons
         * */
        function dynamic_section($sections)
        {
            $sections[] = array(
                'title' => esc_html__('Section via hook', 'flavours'),
                'desc' => esc_html__('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'flavours'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**
         * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
         * */
        function change_arguments($args)
        {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**
         * Filter hook for filtering the default value of any given field. Very useful in development mode.
         * */
        function change_defaults($defaults)
        {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo()
        {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(
                    ReduxFrameworkPlugin::instance(),
                    'plugin_metalinks'
                ), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections()
        {

            /**
             * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (($sample_patterns_file = readdir($sample_patterns_dir)) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[] = array(
                                'alt' => $name,
                                'img' => $sample_patterns_url . $sample_patterns_file
                            );
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct = wp_get_theme();
            $this->theme = $ct;
            $item_name = $this->theme->get('Name');
            $tags = $this->theme->Tags;
            $screenshot = $this->theme->get_screenshot();
            $class = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(esc_html__('Customize &#8220;%s&#8221;', 'flavours'), $this->theme->display('Name'));

            ?>
            <div id="current-theme" class="<?php echo esc_html($class); ?>">
                <?php if ($screenshot) : ?>
                    <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo esc_url(wp_customize_url()); ?>" class="load-customize hide-if-no-customize"
                           title="<?php echo esc_html($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>"
                                 alt="<?php esc_attr_e('Current theme preview', 'flavours'); ?>"/>
                        </a>
                    <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>"
                         alt="<?php esc_attr_e('Current theme preview', 'flavours'); ?>"/>
                <?php endif; ?>

                <h4><?php echo esc_html($this->theme->display('Name')); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(esc_html__('By %s', 'flavours'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(esc_html__('Version %s', 'flavours'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . esc_html__('Tags', 'flavours') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo esc_html($this->theme->display('Description')); ?></p>
                    <?php
                    if ($this->theme->parent()) {
                        printf(' <p class="howto">' . esc_html__('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'flavours') . '</p>', esc_html__('http://codex.wordpress.org/Child_Themes', 'flavours'), $this->theme->parent()->display('Name'));
                    }
                    ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                Redux_Functions::initWpFilesystem();

                global $wp_filesystem;

                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }


             global $woocommerce;
               $cat_arg=array();
               $cat_data='';
                if(class_exists('WooCommerce')) {
                   
                     $cat_data='terms';
                    $cat_arg=array('taxonomies'=>'product_cat', 'args'=>array());
                }

            // ACTUAL DECLARATION OF SECTIONS
            // Edgesettings: Home Page Settings Tab
            $this->sections[] = array(
                'title' => esc_html__('Home Settings', 'flavours'),
                'desc' => esc_html__('Home page settings ', 'flavours'),
                'icon' => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields' => array(   

                    array(
                        'id' => 'theme_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Theme Variation', 'flavours'),
                        'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'aspire'),
                        'options' => array(
                            'default' => array(
                                'title' => esc_html__('Default', 'flavours'),
                                'alt' => esc_html__('Default', 'flavours'),
                                'img' => get_template_directory_uri() . '/images/variations/screen1.jpg'
                            ),
                            'version2' => array(
                                'title' => esc_html__('Version2', 'flavours'),
                                'alt' => esc_html__('Version 2', 'flavours'),
                                'img' => get_template_directory_uri() . '/images/variations/screen2.jpg'
                            ),
                                                    
                           
                        ),
                        'default' => 'default'
                    ), 
                                  
                    array(
                        'id' => 'enable_home_gallery',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Home Page Gallery', 'flavours'),
                        'subtitle' => esc_html__('You can enable/disable Home page Gallery', 'flavours')
                    ),

                    array(
                        'id' => 'home-page-slider',
                        'type' => 'slides',
                        'title' => esc_html__('Home Slider Uploads', 'flavours'),
                        'required' => array('enable_home_gallery', '=', '1'),
                        'subtitle' => esc_html__('Unlimited slide uploads with drag and drop sortings.', 'flavours'),
                        'placeholder' => array(
                            'title' => esc_html__('This is a title', 'flavours'),
                            'description' => esc_html__('Description Here', 'flavours'),
                            'url' => esc_html__('Give us a link!', 'flavours'),
                        ),
                    ),
                      
                        array(
                     'id'=>'topslide',
                     'type' => 'multi_text',
                     'required' => array(array('theme_layout', '=', 'default')),
                     'title' => esc_html__('Top slides', 'flavours'),                     
                     'subtitle' => esc_html__('Add content for top offer slides', 'flavours'),
                     'desc' => esc_html__('Add content for top offer slides', 'flavours')
                      ),                
            

                        
                  
                    array(
                        'id' => 'enable_home_offer_banners',
                        'type' => 'switch',              
                        'title' => __('Enable Home Page Offer Banners', 'flavours'),
                        'subtitle' => __('You can enable/disable Home page offer Banners', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner1',
                        'type' => 'media',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner 1', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours'),                                    
                    ),   
                    array(
                        'id' => 'home-offer-banner1-url',
                        'type' => 'text',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner-1 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ), 
                    array(
                        'id' => 'home-offer-banner1-text1',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-1 Small Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ), 
                    array(
                        'id' => 'home-offer-banner1-text2',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-1 Big Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ), 
                    array(
                        'id' => 'home-offer-banner2',
                        'type' => 'media',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner 2', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner2-url',
                        'type' => 'text',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner-2 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ),    
                    array(
                        'id' => 'home-offer-banner2-text1',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-2 Small Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ),                  
                    array(
                        'id' => 'home-offer-banner2-text2',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-2 Big Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ),   
                    array(
                        'id' => 'home-offer-banner3',
                        'type' => 'media',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner 3', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner3-url',
                        'type' => 'text',
                        'required' => array('enable_home_offer_banners', '=', '1'),
                        'title' => __('Home offer Banner-3 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ),
                    array(
                        'id' => 'home-offer-banner3-text1',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-3 Small Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ), 
                    array(
                        'id' => 'home-offer-banner3-text2',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'version2')),
                        'title' => __('Home offer Banner-3 Big Text', 'flavours'),
                        'subtitle' => __('Text on the offer banner.', 'flavours'),
                    ), 
                    array(
                        'id' => 'home-offer-banner4',
                        'type' => 'media',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner 4', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner4-url',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner-4 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ),
                    array(
                        'id' => 'home-offer-banner5',
                        'type' => 'media',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner 5', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner5-url',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner-5 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ),     
                    array(
                        'id' => 'home-offer-banner6',
                        'type' => 'media',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner 6', 'flavours'),
                        'desc' => __('', 'flavours'),
                        'subtitle' => __('Upload offer banner to appear on  home page ', 'flavours')
                    ),
                    array(
                        'id' => 'home-offer-banner6-url',
                        'type' => 'text',
                        'required' => array(array('enable_home_offer_banners', '=', '1'),array('theme_layout', '=', 'default')),
                        'title' => __('Home offer Banner-6 URL', 'flavours'),
                        'subtitle' => __('URL for the offer banner.', 'flavours'),
                    ),                      
                    

                  
                    array(
                       'id'=>'home-product-categories',
                        'type' => 'select',
                        'data' => $cat_data,                            
                        'args' => $cat_arg,         
                        'title' => __('Product Categories ', 'flavours'), 
                     'subtitle' => __('Please choose a product categories to show on home.', 'flavours'),                       
                    ),
                    array(
                    'id'=>'home-product-categories-limit',
                     'type' => 'text',                                                        
                     'title' => __('Product Categories - Limit', 'flavours'), 
                     'subtitle' => __('Number of products show from  category.', 'flavours'),                           
                    ),

                        
                    array(
                        'id' => 'enable_home_bestseller_products',
                        'type' => 'switch',
                        'title' => esc_html__('Show Best Seller Products', 'flavours'),
                        'subtitle' => esc_html__('You can show best seller products on home page.', 'flavours')
                    ),

                   array(
                            'id'=>'home_bestseller_categories',
                            'type' => 'select',
                            'multi'=> true,                        
                            'data' => $cat_data,                            
                            'args' => $cat_arg,
                            'title' => esc_html__('Best Seller Category', 'flavours'), 
                            'required' => array('enable_home_bestseller_products', '=', '1'),
                            'subtitle' => esc_html__('Please choose Best Seller Category to show  its product in home page.', 'flavours'),
                            'desc' => '',
                        ),
                       array(
                            'id' => 'bestseller_image',
                            'type' => 'media',
                            'required' => array('enable_home_bestseller_products', '=', '1'),
                            'title' => esc_html__('Home bestseller image', 'flavours'),
                            'desc' => esc_html__('', 'flavours'),
                            'subtitle' => esc_html__('Upload bestseller image appear to the left of best seller on  home page ', 'flavours')
                    ),
                      array(
                        'id' => 'bestseller_product_url',
                        'type' => 'text',
                        'required' => array('enable_home_bestseller_products', '=', '1'),
                        'title' => esc_html__('Home Best seller   Url', 'flavours'),
                        'subtitle' => esc_html__('Home Best seller  Url.', 'flavours'),
                    ),
                 
                           
                    // array(
                    //     'id' => 'enable_home_featured_products',
                    //     'type' => 'switch',
                    //     'title' => esc_html__('Show Featured Products', 'flavours'),
                    //     'subtitle' => esc_html__('You can show featured products on home page.', 'flavours')
                    // ),
                    
                    //   array(
                    //         'id' => 'featured_image',
                    //         'type' => 'media',
                    //         'required' => array('enable_home_featured_products', '=', '1'),
                    //         'title' => esc_html__('Home featured image', 'flavours'),
                    //         'desc' => esc_html__('', 'flavours'),
                    //         'subtitle' => esc_html__('Upload featured image appear to right of featured product on  home page ', 'flavours')
                    // ),

                    //     array(
                    //     'id' => 'featured_product_url',
                    //     'type' => 'text',
                    //     'required' => array('enable_home_featured_products', '=', '1'),
                    //     'title' => esc_html__('Home featured  Url', 'flavours'),
                    //     'subtitle' => esc_html__('Home featured  Url.', 'flavours'),
                    // ),

                    //   array(
                    //     'id' => 'featured_description',
                    //     'type' => 'text',
                    //     'required' => array('enable_home_featured_products', '=', '1'),
                    //     'title' => esc_html__('Featured Products description', 'flavours'),
                    //     'subtitle' => esc_html__('Featured Products description.', 'flavours')
                    // ),   
                    // array(
                    //     'id' => 'featured_per_page',
                    //     'type' => 'text',
                    //     'required' => array('enable_home_featured_products', '=', '1'),
                    //     'title' => esc_html__('Number of Featured Products', 'flavours'),
                    //     'subtitle' => esc_html__('Number of Featured products on home page.', 'flavours')
                    // ),                             

                 array(
                        'id' => 'enable_home_blog_posts',
                        'type' => 'switch',
                        'title' => esc_html__('Show Latest Post', 'flavours'),
                        'subtitle' => esc_html__('You can show latest blog post on home page.', 'flavours')
                    ),

                ), // fields array ends
            );


            
            // Edgesettings: General Settings Tab
            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('General Settings', 'flavours'),
                'fields' => array(
                    array(
                        'id'       => 'enable_brand_logo',
                        'type'     => 'switch',                    
                        'title'    => __( 'Enable Company Logo Uploads', 'flavours' ),
                        'subtitle' => __( 'You can enable/disable Company Logo Uploads', 'flavours' ),
                          'default' => '0'
                    ),                   
                    array(
                        'id' => 'all-company-logos',
                        'type' => 'slides',
                        'title' => __('Company Logo Uploads', 'flavours'),
                        'subtitle' => __('Unlimited Logo uploads with drag and drop sortings.', 'flavours'),
                        'placeholder' => array(
                            'title' => __('This is a title', 'flavours'),
                            'description' => __('Description Here', 'flavours'),
                            'url' => __('Give us a link!', 'flavours'),
                        ),
                    ),
                                                                                                                   
                     array(
                     'id'       => 'category_item',
                     'type'     => 'spinner', 
                     'title'    => esc_html__('Product display in product category page', 'flavours'),
                     'subtitle' => esc_html__('Number of item display in product category page','flavours'),
                     'desc'     => esc_html__('Number of item display in product category page', 'flavours'),
                     'default'  => '9',
                     'min'      => '0',
                     'step'     => '1',
                     'max'      => '100',
                     ),

                      array(
                        'id'       => 'enable_testimonial',
                        'type'     => 'switch',                    
                        'required' => array(array('theme_layout', '=', 'default')),
                        'title'    => esc_html__( 'Enable Testimonial ', 'Flavours' ),
                        'subtitle' => esc_html__( 'You can enable/disable Testimonial Uploads', 'Flavours' ),
                          'default' => '0'
                    ),                   
                    array(
                        'id' => 'all_testimonial',
                        'type' => 'slides',
                        'required' => array('enable_testimonial', '=', '1'),
                        'title' => esc_html__('Add Testimonial here', 'Flavours'),
                        'subtitle' => esc_html__('Unlimited testimonial.', 'Flavours'),
                        'placeholder' => array(
                            'title' => esc_html__('This is a title', 'Flavours'),
                            'description' => esc_html__('Description Here', 'Flavours'),
                            'url' => esc_html__('Give us a link!', 'flavours'),
                        ),
                        ),
                    array(
                        'id' => 'back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('Back To Top Button', 'flavours'),
                        'subtitle' => esc_html__('Toggle whether or not to enable a back to top button on your pages.', 'flavours'),
                        'default' => true,
                    ),

                    /*array(
                        'id'       => 'enable_footer_middle',
                        'type'     => 'switch',                    
                        'title'    => esc_html__( 'Enable Footer middle section', 'flavours' ),
                        'subtitle' => esc_html__( 'You can Footer middle section', 'flavours' ),
                          'default' => '0'
                    ),
                    array(
                        'id' => 'footer-text',
                        'type' => 'editor',
                        'title' => esc_html__('Footer Text', 'flavours'), 
                        'required' => array('enable_footer_middle', '=', '1'),                     
                       'subtitle' => esc_html__('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'flavours'),
                        'default' => '',
                    ),*/
                )
            );
            // Edgesettings: General Options -> Styling Options Settings Tab
            $this->sections[] = array(
                'icon' => 'el-icon-website',
                'title' => esc_html__('Styling Options', 'flavours'),
               
                'fields' => array(
                        array(
                        'id' => 'opt-animation',
                        'type' => 'switch',
                        'title' => esc_html__('Use animation effect', 'flavours'),
                        'subtitle' => esc_html__('', 'flavours'),
                        'default' => 0,
                        'on' => 'On',
                        'off' => 'Off',
					),     
                    array(
                        'id' => 'set_body_background_img_color',
                        'type' => 'switch',
                        'title' => esc_html__('Set Body Background', 'flavours'),
                        'subtitle' => esc_html__('', 'flavours'),
                        'default' => 0,
                        'on' => 'On',
                        'off' => 'Off',
                    ),
                    array(
                        'id' => 'opt-background',
                        'type' => 'background',
                        'required' => array('set_body_background_img_color', '=', '1'),
                        'output' => array('body'),
                        'title' => esc_html__('Body Background', 'flavours'),
                        'subtitle' => esc_html__('Body background with image, color, etc.', 'flavours'),               
                        'transparent' => false,
                    ),                   
                    array(
                        'id' => 'opt-color-footer',
                        'type' => 'color',
                        'title' => esc_html__('Footer Background Color', 'flavours'),
                        'subtitle' => esc_html__('Pick a background color for the footer.', 'flavours'),
                        'validate' => 'color',
                        'transparent' => false,
                        'mode' => 'background',
                        'output' => array('.footer')
                    ),
                    array(
                        'id' => 'opt-color-rgba',
                        'type' => 'color',
                        'title' => esc_html__('Header Nav Menu Background', 'flavours'),
                        'output' => array('.tm-main-menu'),
                        'mode' => 'background',
                        'validate' => 'color',
                        'transparent' => false,
                    ),
                    array(
                        'id' => 'opt-color-header',
                        'type' => 'color',
                        'title' => esc_html__('Header Background', 'flavours'),
                        'transparent' => false,
                        'output' => array('.header-container'),
                        'mode' => 'background',
                    ),                   
                )
            );


            // Edgesettings: Header Tab
            $this->sections[] = array(
                'icon' => 'el-icon-file-alt',
                'title' => esc_html__('Header', 'flavours'),
                'heading' => esc_html__('All header related options are listed here.', 'flavours'),
                'desc' => esc_html__('', 'flavours'),
                'fields' => array(
                    array(
                        'id' => 'enable_header_currency',
                        'type' => 'switch',
                        'title' => esc_html__('Show Currency HTML', 'flavours'),
                        'subtitle' => esc_html__('You can show Currency in the header.', 'flavours')
                    ),
                    array(
                        'id' => 'enable_header_language',
                        'type' => 'switch',
                        'title' => esc_html__('Show Language HTML', 'flavours'),
                        'subtitle' => esc_html__('You can show Language in the header.', 'flavours')
                    ),
                    array(
                        'id' => 'header_use_imagelogo',
                        'type' => 'checkbox',
                        'title' => esc_html__('Use Image for Logo?', 'flavours'),
                        'subtitle' => esc_html__('If left unchecked, plain text will be used instead (generated from site name).', 'flavours'),
                        'desc' => esc_html__('', 'flavours'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'header_logo',
                        'type' => 'media',
                        'required' => array('header_use_imagelogo', '=', '1'),
                        'title' => esc_html__('Logo Upload', 'flavours'),
                        'desc' => esc_html__('', 'flavours'),
                        'subtitle' => esc_html__('Upload your logo here and enter the height of it below', 'flavours'),
                    ),
                    array(
                        'id' => 'header_logo_height',
                        'type' => 'text',
                        'required' => array('header_use_imagelogo', '=', '1'),
                        'title' => esc_html__('Logo Height', 'flavours'),
                        'subtitle' => esc_html__('Don\'t include "px" in the string. e.g. 30', 'flavours'),
                        'desc' => '',
                        'validate' => 'numeric'
                    ),
                    array(
                        'id' => 'header_logo_width',
                        'type' => 'text',
                        'required' => array('header_use_imagelogo', '=', '1'),
                        'title' => esc_html__('Logo Width', 'flavours'),
                        'subtitle' => esc_html__('Don\'t include "px" in the string. e.g. 30', 'flavours'),
                        'desc' => '',
                        'validate' => 'numeric'
                    ),    
                                 
                    array(
                        'id' => 'header_remove_header_search',
                        'type' => 'checkbox',
                        'title' => esc_html__('Remove Header Search', 'flavours'),
                        'subtitle' => esc_html__('Active to remove the search functionality from your header', 'flavours'),
                        'desc' => esc_html__('', 'flavours'),
                        'default' => '0'
                    ),
                     array(
                        'id' => 'header_show_info_banner',
                        'type' => 'switch',
                        'title' => esc_html__('Show Info Banners', 'flavours'),
                          'default' => '0'
                    ),

                 
                    array(
                        'id' => 'header_shipping_banner',
                        'type' => 'text',
                        'required' => array('header_show_info_banner', '=', '1'),
                        'title' => esc_html__('Shipping Banner Text', 'flavours'),
                    ),

                    array(
                        'id' => 'header_customer_support_banner',
                        'type' => 'text',
                        'required' => array('header_show_info_banner', '=', '1'),
                        'title' => esc_html__('Customer Support Banner Text', 'flavours'),
                    ),

                    array(
                        'id' => 'header_moneyback_banner',
                        'type' => 'text',
                        'required' => array('header_show_info_banner', '=', '1'),
                        'title' => esc_html__('Warrant/Gaurantee Banner Text', 'flavours'),
                    ),
                      array(
                        'id' => 'header_returnservice_banner',
                        'type' => 'text',
                        'required' => array('header_show_info_banner', '=', '1'),
                        'title' => esc_html__('Return service Banner Text', 'flavours'),
                    ),
                   
                 
                   
                ) //fields end
            );

             // Edgesettings: Menu Tab
            $this->sections[] = array(
                'icon' => 'el el-website icon',
                'title' => esc_html__('Menu', 'flavours'),
                'heading' => esc_html__('All Menu related options are listed here.', 'flavours'),
                'desc' => esc_html__('', 'flavours'),
                'fields' => array(
                   array(
                        'id' => 'show_menu_arrow',
                        'type' => 'switch',
                        'title' => esc_html__('Show Menu Arrow', 'flavours'),
                        'desc'  => esc_html__('Show arrow in menu.', 'flavours'),
                        
                    ),               
                   array(
                    'id'       => 'login_button_pos',
                    'type'     => 'radio',
                    'title'    => esc_html__('Show Login/sign and logout link', 'flavours'),                   
                    'desc'     => esc_html__('Please Select any option from above.', 'flavours'),
                     //Must provide key => value pairs for radio options
                    'options'  => array(
                    'none' => 'None', 
                   'toplinks' => 'In Top Menu', 
                   'main_menu' => 'In Main Menu'
                    ),
                   'default' => 'none'
                    )
                  
                ) // fields ends here
            );
            // Edgesettings: Footer Tab
            $this->sections[] = array(
                'icon' => 'el-icon-file-alt',
                'title' => esc_html__('Footer', 'flavours'),
                'heading' => esc_html__('All footer related options are listed here.', 'flavours'),
                'desc' => esc_html__('', 'flavours'),
                'fields' => array(
                    array(
                        'id' => 'footer_color_scheme',
                        'type' => 'switch',
                        'title' => esc_html__('Custom Footer Color Scheme', 'flavours'),
                        'subtitle' => esc_html__('', 'flavours')
                    ),               
                    array(
                        'id' => 'footer_copyright_background_color',
                        'type' => 'color',
                        'required' => array('footer_color_scheme', '=', '1'),
                        'transparent' => false,
                        'title' => esc_html__('Footer Copyright Background Color', 'flavours'),
                        'subtitle' => esc_html__('', 'flavours'),
                        'validate' => 'color',
                    ),
                    array(
                        'id' => 'footer_copyright_font_color',
                        'type' => 'color',
                        'required' => array('footer_color_scheme', '=', '1'),
                        'transparent' => false,
                        'title' => esc_html__('Footer Copyright Font Color', 'flavours'),
                        'subtitle' => esc_html__('', 'flavours'),
                        'validate' => 'color',
                    ),                    
                    array(
                        'id' => 'bottom-footer-text',
                        'type' => 'editor',
                        'title' => esc_html__('Bottom Footer Text', 'flavours'),
                        'subtitle' => esc_html__('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'flavours'),
                        'default' => esc_html__('Powered by ThemesMartGroup', 'flavours'),
                    ),
                ) // fields ends here
            );

            //Edgesettings: Blog Tab
            $this->sections[] = array(
                'icon' => 'el-icon-pencil',
                'title' => esc_html__('Blog Page', 'flavours'),
                'fields' => array( 
                       array(
                        'id' => 'blog-page-layout',
                        'type' => 'image_select',
                        'title' => esc_html__('Blog Page Layout', 'flavours'),
                        'subtitle' => esc_html__('Select main blog listing and category page layout from the available blog layouts.', 'flavours'),
                        'options' => array(
                            '1' => array(
                                'alt' => 'Left sidebar',
                                'img' => get_template_directory_uri() . '/images/tmFlavours_col/category-layout-1.png'

                            ),
                            '2' => array(
                                'alt' => 'Right Right',
                                'img' => get_template_directory_uri() . '/images/tmFlavours_col/category-layout-2.png'
                            ),
                            '3' => array(
                                'alt' => '2 Column Right',
                                'img' => get_template_directory_uri() . '/images/tmFlavours_col/category-layout-3.png'
                            )                                                                                 
                          
                        ),
                        'default' => '2'
                    ), 
                     array(
                        'id' => 'blog_show_authors_bio',
                        'type' => 'switch',
                        'title' => esc_html__('Author\'s Bio', 'flavours'),
                        'subtitle' => esc_html__('Show Author Bio on Blog page.', 'flavours'),
                         'default' => true,
                        'desc' => esc_html__('', 'flavours')
                    ),                  
                    array(
                        'id' => 'blog_show_post_by',
                        'type' => 'switch',
                        'title' => esc_html__('Display Post By', 'flavours'),
                         'default' => true,
                        'subtitle' => esc_html__('Display Psot by Author on Listing Page', 'flavours')
                    ),
                    array(
                        'id' => 'blog_display_tags',
                        'type' => 'switch',
                        'title' => esc_html__('Display Tags', 'flavours'),
                         'default' => true,
                        'subtitle' => esc_html__('Display tags at the bottom of posts.', 'flavours')
                    ),
                    array(
                        'id' => 'blog_full_date',
                        'type' => 'switch',
                        'title' => esc_html__('Display Full Date', 'flavours'),
                        'default' => true,
                        'subtitle' => esc_html__('This will add date of post meta on all blog pages.', 'flavours')
                    ),
                    array(
                        'id' => 'blog_display_comments_count',
                        'type' => 'switch',
                        'default' => true,
                        'title' => esc_html__('Display Comments Count', 'flavours'),
                        'subtitle' => esc_html__('Display Comments Count on Blog Listing.', 'flavours')
                    ),
                    array(
                        'id' => 'blog_display_category',
                        'type' => 'switch',
                        'title' => esc_html__('Display Category', 'flavours'),
                         'default' => true,
                        'subtitle' => esc_html__('Display Comments Category on Blog Listing.', 'flavours')
                    ),
                    array(
                        'id' => 'blog_display_view_counts',
                        'type' => 'switch',
                        'title' => esc_html__('Display View Counts', 'flavours'),
                         'default' => true,
                        'subtitle' => esc_html__('Display View Counts on Blog Listing.', 'flavours')
                    ),                  
                )
            );

            // Edgesettings: Social Media Tab
            $this->sections[] = array(
                'icon' => 'el-icon-file',
                'title' => esc_html__('Social Media', 'flavours'),
                'fields' => array(
                    array(
                        'id' => 'social_facebook',
                        'type' => 'text',
                        'title' => esc_html__('Facebook URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your Facebook URL.', 'flavours'),
                    ),
                    array(
                        'id' => 'social_twitter',
                        'type' => 'text',
                        'title' => esc_html__('Twitter URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your Twitter URL.', 'flavours'),
                    ),
                    array(
                        'id' => 'social_googlep',
                        'type' => 'text',
                        'title' => esc_html__('Google+ URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your Google Plus URL.', 'flavours'),
                    ),
                  
                    array(
                        'id' => 'social_pinterest',
                        'type' => 'text',
                        'title' => esc_html__('Pinterest URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your Pinterest URL.', 'flavours'),
                    ),
                    array(
                        'id' => 'social_youtube',
                        'type' => 'text',
                        'title' => esc_html__('Youtube URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your Youtube URL.', 'flavours'),
                    ),
                    array(
                        'id' => 'social_linkedin',
                        'type' => 'text',
                        'title' => esc_html__('LinkedIn URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your LinkedIn URL.', 'flavours'),
                    ),
                    array(
                        'id' => 'social_rss',
                        'type' => 'text',
                        'title' => esc_html__('RSS URL', 'flavours'),
                        'subtitle' => esc_html__('Please enter in your RSS URL.', 'flavours'),
                    )                   
                )
            );


            $theme_info = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . esc_html__('<strong>Theme URL:</strong> ', 'flavours') . '<a href="' . esc_url($this->theme->get('ThemeURI')) . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . esc_html__('<strong>Author:</strong> ', 'flavours') . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . esc_html__('<strong>Version:</strong> ', 'flavours') . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . esc_html__('<strong>Tags:</strong> ', 'flavours') . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';


          
            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'flavours'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'flavours'),
                'icon' => 'el-icon-refresh',
                'fields' => array(
                    array(
                        'id' => 'opt-import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'type' => 'divide',
            );


        }

        public function setHelpTabs()
        {


        }

        /**
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */
        public function setArguments()
        {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name' => 'tm_option',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name' => $theme->get('Name'),
                // Name that appears at the top of your panel
                'display_version' => $theme->get('Version'),
                // Version that appears at the top of your panel
                'menu_type' => 'menu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu' => true,
                // Show the sections below the admin menu item or not
                'menu_title' => esc_html__('Flavours Options', 'flavours'),
                'page_title' => esc_html__('Flavours Options', 'flavours'),

                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography' => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar' => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon' => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable' => 'flavours_Options',
                // Set a different name for your global variable other than the opt_name
                'dev_mode' => false,
                // Show the time the page took to load, etc
                'update_notice' => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer' => true,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority' => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent' => 'themes.php',
                
                'page_permissions' => 'manage_options',
                // Permissions needed to access the options panel.
                'menu_icon' => '',
                // Specify a custom URL to an icon
                'last_tab' => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon' => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug' => '_options',
                // Page slug used to denote the panel
                'save_defaults' => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show' => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark' => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time' => 60 * MINUTE_IN_SECONDS,
                'output' => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag' => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database' => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info' => false,
                // REMOVE

                // HINTS
                'hints' => array(
                    'icon' => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color' => 'lightgray',
                    'icon_size' => 'normal',
                    'tip_style' => array(
                        'color' => 'light',
                        'shadow' => true,
                        'rounded' => false,
                        'style' => '',
                    ),
                    'tip_position' => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect' => array(
                        'show' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'mouseover',
                        ),
                        'hide' => array(
                            'effect' => 'slide',
                            'duration' => '500',
                            'event' => 'click mouseleave',
                        ),
                    ),
                )
            );

            // ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
            $this->args['admin_bar_links'][] = array(
                'id' => 'redux-docs',
                'href' => 'http://docs.reduxframework.com/',
                'title' => esc_html__('Documentation', 'flavours'),
            );

            $this->args['admin_bar_links'][] = array(
            
                'href' => 'https://github.com/ReduxFramework/redux-framework/issues',
                'title' => esc_html__('Support', 'flavours'),
            );

            $this->args['admin_bar_links'][] = array(
                'id' => 'redux-extensions',
                'href' => 'reduxframework.com/extensions',
                'title' => esc_html__('Extensions', 'flavours'),
            );

            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url' => 'https://github.com/ReduxFramework/ReduxFramework',
                'title' => 'Visit us on GitHub',
                'icon' => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url' => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
                'title' => 'Like us on Facebook',
                'icon' => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url' => 'http://twitter.com/reduxframework',
                'title' => 'Follow us on Twitter',
                'icon' => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url' => 'http://www.linkedin.com/company/redux-framework',
                'title' => 'Find us on LinkedIn',
                'icon' => 'el-icon-linkedin'
            );

            $this->args['intro_text'] = '';

            // Add content after the form.
            $this->args['footer_text'] = '';
        }

        public function validate_callback_function($field, $value, $existing_value)
        {
            $error = true;
            $value = 'just testing';

        

            $return['value'] = $value;
            $field['msg'] = 'your custom error message';
            if ($error == true) {
                $return['error'] = $field;
            }

            return $return;
        }

        public function class_field_callback($field, $value)
        {
            print_r($field);
            echo '<br/>CLASS CALLBACK';
            print_r($value);
        }

    }

    global $reduxConfig;
    $reduxConfig = new Redux_Framework_sample_config();
} else {
    echo "The class named Redux_Framework_sample_config has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
}

/**
 * Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value)
    {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
 * Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value)
    {
        $error = true;
        $value = 'just testing';

   

        $return['value'] = $value;
        $field['msg'] = 'your custom error message';
        if ($error == true) {
            $return['error'] = $field;
        }

        return $return;
    }
endif;
