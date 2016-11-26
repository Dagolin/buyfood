<?php

 /*Define Contants */
define('TMFLAVOURS_FLAVOURS_VERSION', '1.0');  
define('TMFLAVOURS_THEME_PATH', get_template_directory());
define('TMFLAVOURS_THEME_URI', get_template_directory_uri());
define('TMFLAVOURS_THEME_STYLE_URI', get_stylesheet_directory_uri());
define('TMFLAVOURS_THEME_LIB_PATH', get_template_directory() . '/includes/');
define('TMFLAVOURS_THEME_NAME', 'flavours');


/* Include required tgm activation */
require_once (trailingslashit( get_template_directory()). '/includes/tgm_activation/install-required.php');
require_once (trailingslashit( get_template_directory()). '/includes/reduxActivate.php');
if (file_exists(trailingslashit( get_template_directory()). '/includes/reduxConfig.php')) {
    require_once (trailingslashit( get_template_directory()). '/includes/reduxConfig.php');
}

/* Include theme variation functions */ 
require_once(TMFLAVOURS_THEME_PATH . '/core/tm_framework.php');


if (!isset($content_width)) {
    $content_width = 800;
}



class TmFlavours {
   
  /**
  * Constructor
  */
  function __construct() {
    // Register action/filter callbacks
  
    add_action('after_setup_theme', array($this, 'tmFlavours_flavours_setup'));
    add_action( 'init', array($this, 'tmFlavours_theme'));
    add_action('wp_enqueue_scripts', array($this,'tmFlavours_custom_enqueue_google_font'));
    
    add_action('admin_enqueue_scripts', array($this,'tmFlavours_admin_scripts_styles'));
    add_action('wp_enqueue_scripts', array($this,'tmFlavours_scripts_styles'));
    add_action('wp_head', array($this,'tmFlavours_apple_touch_icon'));
  
    add_action('widgets_init', array($this,'tmFlavours_widgets_init'));
    add_action('wp_head', array($this,'tmFlavours_front_init_js_var'),1);
    add_action('wp_head', array($this,'tmFlavours_enqueue_custom_css'));
    
    add_action('add_meta_boxes', array($this,'tmFlavours_reg_page_meta_box'));
    add_action('save_post',array($this, 'tmFlavours_save_page_layout_meta_box_values')); 
    add_action('add_meta_boxes', array($this,'tmFlavours_reg_post_meta_box'));
    add_action('save_post',array($this, 'tmFlavours_save_post_layout_meta_box_values'));

      // Display 24 products per page. Goes in functions.php
      add_filter('loop_shop_per_page', create_function('$cols', 'return 3;'), 20);

      // Add phone number field in checkout shipping
      // Add cert field in checkout
      add_filter( 'woocommerce_checkout_fields' , array($this, 'custom_override_checkout_fields' ));

      /*
       * TODO :
       * 1. init 打開 session
       * 2. 畫面上新增簡訊 input box ，以及發送簡訊的按鈕
       * 3. 發送簡訊用AJAX，此按鈕會把四位數字加入Session 和 呼叫台灣簡訊送出此數字
       * 4. submit 後檢查該數字是否等於Session內
       *
       */

  }

    // Our hooked in function - $fields is passed via the filter!
    function tmFlavours_theme() {

global $flavours_Options;

}

    function custom_override_checkout_fields( $fields ) {
        $fields['billing']['billing_cert'] = array(
            'label'     => __('認證碼', 'woocommerce'),
            'placeholder'   => _x('請輸入簡訊內四位數認證碼', 'placeholder', 'woocommerce'),
            'class'     => array('form-row-first'),
            'clear'     => true,
            'default'   => '',
        );

        $fields['billing']['billing_phone']['placeholder'] = '0912345678';

        $fields['billing']['billing_phone_hidden'] = array(
            'type'      => 'text',
            'class'     => array('hidden'),
        );

        $fields['billing']['billing_skipcert'] = array(
            'type'      => 'text',
            'class'     => array('hidden'),
            'default'   => 'true'
        );

        return $fields;
    }

    /**
  * Theme setup
  */
  function tmFlavours_flavours_setup() {   
    global $flavours_Options;
     load_theme_textdomain('flavours', get_template_directory() . '/languages');
     load_theme_textdomain('woocommerce', get_template_directory() . '/languages');

      // Add default posts and comments RSS feed links to head.
      add_theme_support('automatic-feed-links');
      add_theme_support('title-tag');
      add_theme_support('post-thumbnails');
      add_image_size('tmFlavours-featured_preview', 55, 55, true);
      add_image_size('tmFlavours-article-home-large',1140, 450, true);
      add_image_size('tmFlavours-article-home-small', 335, 150, true);
      add_image_size('tmFlavours-article-home-medium', 335, 155, true); 
      add_image_size('tmFlavours-product-size-large',214, 214, true);      
          
         
    add_theme_support( 'html5', array(
      'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );
    
    add_theme_support( 'post-formats', array(
      'aside','video','audio'
    ) );
    
    // Setup the WordPress core custom background feature.
    $default_color = trim( 'ffffff', '#' );
    $default_text_color = trim( '333333', '#' );
    
    add_theme_support( 'custom-background', apply_filters( 'tmFlavours_custom_background_args', array(
      'default-color'      => $default_color,
      'default-attachment' => 'fixed',
    ) ) );
    
    add_theme_support( 'custom-header', apply_filters( 'tmFlavours_custom_header_args', array(
      'default-text-color'     => $default_text_color,
      'width'                  => 1170,
      'height'                 => 450,
      
    ) ) );

    /*
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, icons, and column width.
     */
    add_editor_style('css/editor-style.css' );
    
    /*
    * Edge WooCommerce Declaration: WooCommerce Support and settings
    */    
    
      if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
        require_once(TMFLAVOURS_THEME_PATH. '/woo_function.php');
        // Disable WooCommerce Default CSS if set
        if (!empty($flavours_Options['woocommerce_disable_woo_css'])) {
          add_filter('woocommerce_enqueue_styles', '__return_false');
          wp_enqueue_style('woocommerce_enqueue_styles', get_template_directory_uri() . '/woocommerce.css');
        }
      }
 
    // Register navigation menus
    
    register_nav_menus(
      array(
      'toplinks' => esc_html__( 'Top menu', 'flavours' ),
       'main_menu' => esc_html__( 'Main menu', 'flavours' )
      ));
    
  }

function tmFlavours_fonts_url() {
  $fonts_url = '';
  $fonts     = array();
  $subsets   = 'latin,latin-ext';
 
   if ( 'off' !== _x( 'on', 'Source Sans: on or off', 'flavours' ) ) {
       $fonts[]='Source Sans Pro:200,200italic,300,300italic,400,400italic,600,600italic,700,700italic,900,900italic';
    }
  

 
    if ( 'off' !== _x( 'on', 'Montserrat: on or off', 'flavours' ) ) {
       $fonts[]='Montserrat:400,700';
    }
    
 
    if ( 'off' !== _x( 'on', 'Roboto: on or off', 'flavours' ) ) {
        $fonts[]='Roboto:400,500,300,700,900';
    }
    
 
    if ( 'off' !== _x( 'on', 'Raleway: on or off', 'flavours' ) ) {
         $fonts[]='Raleway:400,100,200,300,600,500,700,800,900';
    }

    if ( $fonts ) {
    $fonts_url = add_query_arg( array(
      'family' => urlencode( implode( '|', $fonts ) ),
      'subset' => urlencode( $subsets ),
    ), 'https://fonts.googleapis.com/css' );
  }
    return $fonts_url;
}
/*
Enqueue scripts and styles.
*/
function tmFlavours_custom_enqueue_google_font() {

  wp_enqueue_style( 'tmFlavours-Fonts', $this->tmFlavours_fonts_url() , array(), '1.0.0' );
}


  function tmFlavours_admin_scripts_styles()
  {  
      wp_enqueue_script('tmFlavours-admin', TMFLAVOURS_THEME_URI . '/js/admin_menu.js', array(), '', true);
  }

function tmFlavours_scripts_styles()
{
    global $flavours_Options;
    /*JavaScript for threaded Comments when needed*/
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }


    wp_enqueue_style('bootstrap.min-css', TMFLAVOURS_THEME_URI . '/css/bootstrap.min.css', array(), '');   
    
   if(isset($flavours_Options['opt-animation']) && !empty($flavours_Options['opt-animation']))
   {
    wp_enqueue_style('animate', TMFLAVOURS_THEME_URI . '/css/animate.css', array(), '');
   }
  wp_enqueue_style('font-awesome', TMFLAVOURS_THEME_URI . '/css/font-awesome.css', array(), '');

  wp_enqueue_style('owl.carousel', TMFLAVOURS_THEME_URI . '/css/owl.carousel.css', array(), '');

  wp_enqueue_style('owl.theme', TMFLAVOURS_THEME_URI . '/css/owl.theme.css', array(), '');
  
  wp_enqueue_style('flexslider', TMFLAVOURS_THEME_URI . '/css/flexslider.css', array(), '');

   wp_enqueue_style('bxslider', TMFLAVOURS_THEME_URI . '/css/jquery.bxslider.css', array(), '');
  
     
  wp_enqueue_style('tmFlavours-style', TMFLAVOURS_THEME_URI . '/style.css', array(), '');    

  if (isset($flavours_Options['theme_layout']) && !empty($flavours_Options['theme_layout']))
  {
     wp_enqueue_style( 'tmFlavours-blog', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/blogs.css', array(), '');
     wp_enqueue_style( 'tmFlavours-revslider', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/revslider.css', array(), '');
     wp_enqueue_style('tmFlavours-layout', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/style.css', array(), '');
     wp_enqueue_style('tmFlavours-responsive', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/responsive.css', array(), '');
     wp_enqueue_style( 'tmFlavours-tm_menu', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/tm_menu.css', array(), '');  
     wp_enqueue_style('jquery.mobile-menu-js', TMFLAVOURS_THEME_URI . '/skins/' . $flavours_Options['theme_layout'] . '/jquery.mobile-menu.css', array(), '');
  } else {
     wp_enqueue_style( 'tmFlavours-blog', TMFLAVOURS_THEME_URI . '/skins/default/blogs.css', array(), '');
     wp_enqueue_style( 'tmFlavours-revslider', TMFLAVOURS_THEME_URI . '/skins/default/revslider.css', array(), '');
     wp_enqueue_style('tmFlavours-layout', TMFLAVOURS_THEME_URI . '/skins/default/style.css', array(), '');
     wp_enqueue_style('tmFlavours-responsive', TMFLAVOURS_THEME_URI . '/skins/default/responsive.css', array(), '');
     wp_enqueue_style( 'tmFlavours-tm_menu', TMFLAVOURS_THEME_URI . '/skins/default/tm_menu.css', array(), '');  
     wp_enqueue_style('jquery.mobile-menu-js', TMFLAVOURS_THEME_URI . '/skins/default/jquery.mobile-menu.css', array(), '');

  }   
    
 //theme js

    wp_enqueue_script('bootstrap.min', TMFLAVOURS_THEME_URI . '/js/bootstrap.min.js', array('jquery'), '', true);
     wp_enqueue_script('countdown',TMFLAVOURS_THEME_URI . '/js/countdown.js', array('jquery'), '', true);
    wp_enqueue_script('parallax',TMFLAVOURS_THEME_URI . '/js/parallax.js', array('jquery'), '', true);
   wp_enqueue_script('tmFlavours-cart',TMFLAVOURS_THEME_URI . '/js/common.js', array('jquery'), '', true);

    wp_enqueue_script('revolution', TMFLAVOURS_THEME_URI . '/js/revslider.js', array('jquery'), '', true);
    // wp_enqueue_script('revolution-exe', TMFLAVOURS_THEME_URI . '/js/revolution.extension.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.bxslider-js', TMFLAVOURS_THEME_URI . '/js/jquery.bxslider.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.flexslider-js', TMFLAVOURS_THEME_URI . '/js/jquery.flexslider.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.mobile-menu-js', TMFLAVOURS_THEME_URI . '/js/jquery.mobile-menu.min.js', array('jquery'), '', true);
    wp_enqueue_script('owl.carousel.min-js',TMFLAVOURS_THEME_URI . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('cloud-zoom-js', TMFLAVOURS_THEME_URI . '/js/cloud-zoom.js', array('jquery'), '', true);

    // Date picker
    wp_enqueue_script('jquery-ui-datepicker', TMFLAVOURS_THEME_URI . '/js/jquery-ui.min.js', array('jquery'), '', true);
    wp_enqueue_style('jquery-ui-datepicker', TMFLAVOURS_THEME_URI . '/css/jquery-ui.min.css', array(), '');

      wp_register_script('tmFlavours-theme', TMFLAVOURS_THEME_URI .'/js/tm_menu.js', array('jquery'), '', true );
        wp_enqueue_script('tmFlavours-theme');

            wp_localize_script( 'tmFlavours-theme', 'js_flavours_vars', array(
            'ajax_url' => esc_js(admin_url( 'admin-ajax.php' )),
            'container_width' => 1160,
            'grid_layout_width' => 20           
        ) );
           
}

 
  function tmFlavours_apple_touch_icon()
  {
    printf(
      '<link rel="apple-touch-icon" href="%s" />',
      esc_url(TMFLAVOURS_THEME_URI). '/images/apple-touch-icon.png'
    );
    printf(
      '<link rel="apple-touch-icon" href="%s" />',
      esc_url(TMFLAVOURS_THEME_URI). '/images/apple-touch-icon57x57.png'
    );
    printf(
      '<link rel="apple-touch-icon" href="%s" />',
       esc_url(TMFLAVOURS_THEME_URI). '/images/apple-touch-icon72x72.png'
    );
    printf(
      '<link rel="apple-touch-icon" href="%s" />',
      esc_url(TMFLAVOURS_THEME_URI). '/images/apple-touch-icon114x114.png'
    );
    printf(
      '<link rel="apple-touch-icon" href="%s" />',
      esc_url(TMFLAVOURS_THEME_URI). '/images/apple-touch-icon144x144.png'
    );
  }
  //register sidebar widget
  function tmFlavours_widgets_init()
  {
      register_sidebar(array(
      'name' => esc_html__('Blog Sidebar', 'flavours'),
      'id' => 'sidebar-blog',
      'description' => esc_html__('Sidebar that appears on the right of Blog and Search page.', 'flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="block-title">',
      'after_title' => '</h3>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Shop Sidebar','flavours'),
      'id' => 'sidebar-shop',
      'description' => esc_html__('Main sidebar that appears on the left.', 'flavours'),
      'before_widget' => '<div id="%1$s" class="block %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="block-title">',
      'after_title' => '</div>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Content Sidebar Left', 'flavours'),
      'id' => 'sidebar-content-left',
      'description' => esc_html__('Additional sidebar that appears on the left.','flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<div class="block-title">',
      'after_title' => '</div>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Content Sidebar Right', 'flavours'),
      'id' => 'sidebar-content-right',
      'description' => esc_html__('Additional sidebar that appears on the right.', 'flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<div class="block-title">',
      'after_title' => '</div>',
    ));
   
    register_sidebar(array(
      'name' => esc_html__('Footer Widget Area 1','flavours'),
      'id' => 'footer-sidebar-1',
      'description' => esc_html__('Appears in the footer section of the site.','flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Footer Widget Area 2', 'flavours'),
      'id' => 'footer-sidebar-2',
      'description' => esc_html__('Appears in the footer section of the site.', 'flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Footer Widget Area 3', 'flavours'),
      'id' => 'footer-sidebar-3',
      'description' => esc_html__('Appears in the footer section of the site.','flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Footer Widget Area 4', 'flavours'),
      'id' => 'footer-sidebar-4',
      'description' => esc_html__('Appears in the footer section of the site.', 'flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));
    register_sidebar(array(
      'name' => esc_html__('Footer Widget Area 5', 'flavours'),
      'id' => 'footer-sidebar-5',
      'description' => esc_html__('Appears in the footer section of the site.', 'flavours'),
      'before_widget' => '<aside id="%1$s" class="widget %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h4>',
      'after_title' => '</h4>',
    ));

  }


  function tmFlavours_front_init_js_var()
  {
    global $yith_wcwl, $post;
    ?>
  <script type="text/javascript">
      var TM_PRODUCT_PAGE = false;
      var THEMEURL = '<?php echo esc_url(TMFLAVOURS_THEME_URI) ?>';
      var IMAGEURL = '<?php echo esc_url(TMFLAVOURS_THEME_URI) ?>/images';
      var CSSURL = '<?php echo esc_url(TMFLAVOURS_THEME_URI) ?>/css';
      <?php if (isset($yith_wcwl) && is_object($yith_wcwl)) { ?>
      var TM_ADD_TO_WISHLIST_SUCCESS_TEXT = '<?php printf(preg_replace_callback('/(\r|\n|\t)+/',  create_function('$match', 'return "";'),htmlspecialchars_decode('Product successfully added to wishlist. <a href="%s">Browse Wishlist</a>')), esc_url($yith_wcwl->get_wishlist_url())) ?>';

      var TM_ADD_TO_WISHLIST_EXISTS_TEXT = '<?php printf(preg_replace_callback('/(\r|\n|\t)+/',  create_function('$match', 'return "";'),htmlspecialchars_decode('The product is already in the wishlist! <a href="%s">Browse Wishlist</a>')), esc_url($yith_wcwl->get_wishlist_url()) )?>';
      <?php } ?>
      <?php if(is_singular('product')){?>
      TM_PRODUCT_PAGE = true;
      <?php }?>
    </script>
  <?php
  }

  function tmFlavours_reg_page_meta_box() {
    $screens = array('page');

    foreach ($screens as $screen) {        
      add_meta_box(
          'tmFlavours_page_layout_meta_box', esc_html__('Page Layout', 'flavours'), 
          array($this, 'tmFlavours_page_layout_meta_box_cb'), $screen, 'normal', 'core'
      );
    }
  }

  function tmFlavours_page_layout_meta_box_cb($post) {

    $saved_page_layout = get_post_meta($post->ID, 'tmFlavours_page_layout', true);
    
    $show_breadcrumb = get_post_meta($post->ID, 'tmFlavours_show_breadcrumb', true);
    
   if(empty($saved_page_layout)) {
      $saved_page_layout = 3;
    }
    $page_layouts = array(
      1 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-1.png',
      2 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-2.png',
      3 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-3.png',
      4 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-4.png',
    );  
    ?>
  <style type="text/css">
        input.of-radio-img-radio{display: none;}
        .tile_img_wrap{
          display: block;                
        }
        .tile_img_wrap > span > img{
          float: left;
          margin:0 5px 10px 0;
        }
        .tile_img_wrap > span > img:hover{
          cursor: pointer;
        }            
        .tile_img_wrap img.of-radio-img-selected{
          border: 3px solid #CCCCCC;
        }
         #tmFlavours_page_layout_meta_box h2 {
    margin-top: 20px;
    font-size: 1.5em;
    
     }
        #tmFlavours_page_layout_meta_box .inside h2 {
    margin-top: 20px;
    font-size: 1.5em;
    margin-bottom: 15px;
    padding: 0 0 3px;
    clear: left;
}
        
      </style>
  <?php
    echo "<input type='hidden' name='tmFlavours_page_layout_verifier' value='".wp_create_nonce('tmFlavours_7a81jjde')."' />";    
    $output = '<div class="tile_img_wrap">';
      foreach ($page_layouts as $key => $img) {
        $checked = '';
        $selectedClass = '';
        if($saved_page_layout == $key){
          $checked = 'checked="checked"';
          $selectedClass = 'of-radio-img-selected';
        }
        $output .= '<span>';
        $output .= '<input type="radio" class="checkbox of-radio-img-radio" value="' . absint($key) . '" name="tmFlavours_page_layout" ' . esc_html($checked). ' />';            
        $output .= '<img src="' . esc_url($img) . '" alt="" class="of-radio-img-img ' . esc_html($selectedClass) . '" />';
        $output .= '</span>';
            
      }    
    $output .= '</div>';
    echo htmlspecialchars_decode($output);
    ?>
  <script type="text/javascript">
      jQuery(function($){            
        $(document.body).on('click','.of-radio-img-img',function(){
          $(this).parents('.tile_img_wrap').find('.of-radio-img-img').removeClass('of-radio-img-selected');
          $(this).parent().find('.of-radio-img-radio').attr('checked','checked');
          $(this).addClass('of-radio-img-selected');
        });            
    });
      
      </script>

  <h2><?php esc_attr_e('Show breadcrumb', 'flavours'); ?></h2>
  <p>
    <input type="radio" name="tmFlavours_show_breadcrumb" value="1" <?php echo "checked='checked'"; ?> />
    <label><?php esc_attr_e('Yes','flavours'); ?></label>
    &nbsp;
    <input type="radio" name="tmFlavours_show_breadcrumb" value="0"  <?php if($show_breadcrumb === '0'){ echo "checked='checked'"; } ?>/>
    <label><?php esc_attr_e('No', 'flavours'); ?></label>
  </p>
  <?php
  }

  function tmFlavours_save_page_layout_meta_box_values($post_id){
    if (!isset($_POST['tmFlavours_page_layout_verifier']) 
        || !wp_verify_nonce($_POST['tmFlavours_page_layout_verifier'], 'tmFlavours_7a81jjde') 
        || !isset($_POST['tmFlavours_page_layout']) 
       
        )
      return $post_id;
    
    
    add_post_meta($post_id,'tmFlavours_page_layout',sanitize_text_field( $_POST['tmFlavours_page_layout']),true) or 
    update_post_meta($post_id,'tmFlavours_page_layout',sanitize_text_field( $_POST['tmFlavours_page_layout']));
    
    add_post_meta($post_id,'tmFlavours_show_breadcrumb',sanitize_text_field( $_POST['tmFlavours_show_breadcrumb']),true) or 
    update_post_meta($post_id,'tmFlavours_show_breadcrumb',sanitize_text_field( $_POST['tmFlavours_show_breadcrumb']));  
  }


  /*Register Post Meta Boxes for Blog Post Layouts*/

    function tmFlavours_reg_post_meta_box() {
    $screens = array('post');

    foreach ($screens as $screen) {        
      add_meta_box(
          'tmFlavours_post_layout_meta_box', esc_html__('Post Layout', 'flavours'), 
          array($this, 'tmFlavours_post_layout_meta_box_cb'), $screen, 'normal', 'core'
      );
    }
  }

  function tmFlavours_post_layout_meta_box_cb($post) {

    $saved_post_layout = get_post_meta($post->ID, 'tmFlavours_post_layout', true);         
    if(empty($saved_post_layout))
    {
      $saved_post_layout = 2;
    }
    
    $post_layouts = array(
      1 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-1.png',
      2 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-2.png',
      3 => esc_url(TMFLAVOURS_THEME_URI).'/images/tmFlavours_col/category-layout-3.png',
      
    );  
    ?>
  <style type="text/css">
        input.of-radio-img-radio{display: none;}
        .tile_img_wrap{
          display: block;                
        }
        .tile_img_wrap > span > img{
          float: left;
          margin:0 5px 10px 0;
        }
        .tile_img_wrap > span > img:hover{
          cursor: pointer;
        }            
        .tile_img_wrap img.of-radio-img-selected{
          border: 3px solid #CCCCCC;
        }
        .postbox-container .inside .tile_img_wrap
        {
          height:70px;
        }
        
      </style>
  <?php
    echo "<input type='hidden' name='tmFlavours_post_layout_verifier' value='".wp_create_nonce('tmFlavours_7a81jjde1')."' />";    
    $output = '<div class="tile_img_wrap">';
      foreach ($post_layouts as $key => $img) {
        $checked = '';
        $selectedClass = '';
        if($saved_post_layout == $key){
          $checked = 'checked="checked"';
          $selectedClass = 'of-radio-img-selected';
        }
        $output .= '<span>';
        $output .= '<input type="radio" class="checkbox of-radio-img-radio" value="' . absint($key) . '" name="tmFlavours_post_layout" ' . esc_html($checked). ' />';            
        $output .= '<img src="' . esc_url($img) . '" alt="" class="of-radio-img-img ' . esc_html($selectedClass) . '" />';
        $output .= '</span>';
            
      }    
    $output .= '</div>';
    echo htmlspecialchars_decode($output);
    ?>
  <script type="text/javascript">
      jQuery(function($){            
        $(document.body).on('click','.of-radio-img-img',function(){
          $(this).parents('.tile_img_wrap').find('.of-radio-img-img').removeClass('of-radio-img-selected');
          $(this).parent().find('.of-radio-img-radio').attr('checked','checked');
          $(this).addClass('of-radio-img-selected');
        });            
    });
      
      </script>

  
  <?php
  }

  function tmFlavours_save_post_layout_meta_box_values($post_id){
    if (!isset($_POST['tmFlavours_post_layout_verifier']) 
        || !wp_verify_nonce($_POST['tmFlavours_post_layout_verifier'], 'tmFlavours_7a81jjde1') 
        || !isset($_POST['tmFlavours_post_layout']) 
       
        )
      return $post_id;
    
    
    add_post_meta($post_id,'tmFlavours_post_layout',sanitize_text_field($_POST['tmFlavours_post_layout']),true) or 
    update_post_meta($post_id,'tmFlavours_post_layout',sanitize_text_field($_POST['tmFlavours_post_layout']));
    
    
  }

  //custom functions 

  //search form code
  function tmFlavours_custom_search_form()
  { global $flavours_Options;
  ?>
 
<form name="myform"  method="GET" action="<?php echo esc_url(home_url('/')); ?>">
         <input type="text" name="s" class="tm-search" maxlength="70" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('Search', 'flavours'); ?>">
  
    
     <?php if (class_exists('WooCommerce')) : ?>    
      <input type="hidden" value="product" name="post_type">
    <?php endif; ?>
    <button type="submit" class="search-btn-bg search-icon"><span class="glyphicon glyphicon-search"></span>&nbsp;</button>
  </form>

  <?php
  }



// page title code
function tmFlavours_page_title() {

    global  $post, $wp_query, $author,$flavours_Options;

    $home = esc_html__('Home', 'flavours');

  
    if ( ( ! is_home() && ! is_front_page() && ! (is_post_type_archive()) ) || is_paged() ) {

        if ( is_home() ) {
           echo htmlspecialchars_decode(single_post_title('', false));

        } else if ( is_category() ) {

            echo esc_html(single_cat_title( '', false ));

        } elseif ( is_tax() ) {

            $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

            echo htmlspecialchars_decode(esc_html( $current_term->name ));

        }  elseif ( is_day() ) {

            printf( esc_html__( 'Daily Archives: %s', 'flavours' ), get_the_date() );

        } elseif ( is_month() ) {

            printf( esc_html__( 'Monthly Archives: %s', 'flavours' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'flavours' ) ) );

        } elseif ( is_year() ) {

            printf( esc_html__( 'Yearly Archives: %s', 'flavours' ), get_the_date( _x( 'Y', 'yearly archives date format', 'flavours' ) ) );

        }   else if ( is_post_type_archive() ) {
            sprintf( esc_html__( 'Archives: %s', 'flavours' ), post_type_archive_title( '', false ) );
        } elseif ( is_single() && ! is_attachment() ) {
        
                echo esc_html(get_the_title());

            

        } elseif ( is_404() ) {

            echo esc_html__( 'Error 404', 'flavours' );

        } elseif ( is_attachment() ) {

            echo esc_html(get_the_title());

        } elseif ( is_page() && !$post->post_parent ) {

            echo esc_html(get_the_title());

        } elseif ( is_page() && $post->post_parent ) {

            echo esc_html(get_the_title());

        } elseif ( is_search() ) {

            echo htmlspecialchars_decode(esc_html__( 'Search results for &ldquo;', 'flavours' ) . get_search_query() . '&rdquo;');

        } elseif ( is_tag() ) {

            echo htmlspecialchars_decode(esc_html__( 'Posts tagged &ldquo;', 'flavours' ) . single_tag_title('', false) . '&rdquo;');

        } elseif ( is_author() ) {

            $userdata = get_userdata($author);
            echo htmlspecialchars_decode(esc_html__( 'Author:', 'flavours' ) . ' ' . $userdata->display_name);

        } elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' ) {

            $post_type = get_post_type_object( get_post_type() );

            if ( $post_type ) {
                echo htmlspecialchars_decode($post_type->labels->singular_name);
            }

        }

        if ( get_query_var( 'paged' ) ) {
            echo htmlspecialchars_decode( ' (' . esc_html__( 'Page', 'flavours' ) . ' ' . get_query_var( 'paged' ) . ')');
        }
    } else {
        if ( is_home() && !is_front_page() ) {
            if ( ! empty( $home ) ) {               
                  echo htmlspecialchars_decode(single_post_title('', false));
            }
        }
    }
}

// page breadcrumbs code
function tmFlavours_breadcrumbs() {
    global $post, $flavours_Options,$wp_query, $author;

    $delimiter = ' &mdash;&rsaquo; ';
    $before = '<li>';
    $after = '</li>';
    $home = esc_html__('Home', 'flavours');

  
  // breadcrumb code
   
    if ( ( ! is_home() && ! is_front_page() && ! (is_post_type_archive()) ) || is_paged() ) {
        echo '<ul class="breadcrumb">';

        if ( ! empty( $home ) ) {
            echo htmlspecialchars_decode($before . '<a class="home" href="' . esc_url(home_url() ) . '">' . $home . '</a>' . $delimiter . $after);
        }

        if ( is_home() ) {

            echo htmlspecialchars_decode($before . single_post_title('', false) . $after);

         }      
         else if ( is_category() ) {

            if ( get_option( 'show_on_front' ) == 'page' ) {
                echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_permalink( get_option('page_for_posts' ) )) . '">' . esc_html(get_the_title( get_option('page_for_posts', true) )) . '</a>' . $delimiter . $after);
            }

            $cat_obj = $wp_query->get_queried_object();
            if ($cat_obj) {
                $this_category = get_category( $cat_obj->term_id );
                if ( 0 != $this_category->parent ) {
                    $parent_category = get_category( $this_category->parent );
                    if ( ( $parents = get_category_parents( $parent_category, TRUE, $delimiter . $after . $before ) ) && ! is_wp_error( $parents ) ) {
                        echo htmlspecialchars_decode($before . substr( $parents, 0, strlen($parents) - strlen($delimiter . $after . $before) ) . $delimiter . $after);
                    }
                }
                echo htmlspecialchars_decode($before . single_cat_title( '', false ) . $after);
            }

        } 
        elseif ( is_tax()) {      
                    
            $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

            $ancestors = array_reverse( get_ancestors( $current_term->term_id, get_query_var( 'taxonomy' ) ) );

            foreach ( $ancestors as $ancestor ) {
                $ancestor = get_term( $ancestor, get_query_var( 'taxonomy' ) );

                echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_term_link( $ancestor->slug, get_query_var( 'taxonomy' ) )) . '">' . esc_html( $ancestor->name ) . '</a>' . $delimiter . $after);
            }

            echo htmlspecialchars_decode($before . esc_html( $current_term->name ) . $after);

        } 
       
        elseif ( is_day() ) {

            echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>' . $delimiter . $after);
            echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_month_link(get_the_time('Y'),get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a>' . $delimiter . $after);
            echo htmlspecialchars_decode($before . get_the_time('d') . $after);

        } elseif ( is_month() ) {

            echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>' . $delimiter . $after);
            echo htmlspecialchars_decode($before . get_the_time('F') . $after);

        } elseif ( is_year() ) {

            echo htmlspecialchars_decode($before . get_the_time('Y') . $after);

        } elseif ( is_single() && ! is_attachment() ) {

         
            if ( 'post' != get_post_type() ) {
                $post_type = get_post_type_object( get_post_type() );
                $slug = $post_type->rewrite;
                echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_post_type_archive_link( get_post_type() )) . '">' . esc_html($post_type->labels->singular_name) . '</a>' . $delimiter . $after);
                echo htmlspecialchars_decode($before . get_the_title() . $after);

            } else {

                if ( 'post' == get_post_type() && get_option( 'show_on_front' ) == 'page' ) {
                    echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_permalink( get_option('page_for_posts' ) )) . '">' . esc_html(get_the_title( get_option('page_for_posts', true) )) . '</a>' . $delimiter . $after);
                }

                $cat = current( get_the_category() );
              if ( ( $parents = get_category_parents( $cat, TRUE, $delimiter . $after . $before ) ) && ! is_wp_error( $parents ) ) {
                $getitle=get_the_title();
                  if(empty($getitle))
                  {
                    $newdelimiter ='';
                  }
                  else
                  {
                     $newdelimiter=$delimiter;
                  }
                    echo htmlspecialchars_decode($before . substr( $parents, 0, strlen($parents) - strlen($delimiter . $after . $before) ) . $newdelimiter . $after);
                }
                echo htmlspecialchars_decode($before . get_the_title() . $after);

            }

        } elseif ( is_404() ) {

            echo htmlspecialchars_decode($before . esc_html__( 'Error 404', 'flavours' ) . $after);

        } elseif ( is_attachment() ) {

            $parent = get_post( $post->post_parent );
            $cat = get_the_category( $parent->ID );
            $cat = $cat[0];
            if ( ( $parents = get_category_parents( $cat, TRUE, $delimiter . $after . $before ) ) && ! is_wp_error( $parents ) ) {
                echo htmlspecialchars_decode($before . substr( $parents, 0, strlen($parents) - strlen($delimiter . $after . $before) ) . $delimiter . $after);
            }
            echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_permalink( $parent )) . '">' . esc_html($parent->post_title) . '</a>' . $delimiter . $after);
            echo htmlspecialchars_decode($before . get_the_title() . $after);

        } elseif ( is_page() && !$post->post_parent ) {

            echo htmlspecialchars_decode($before . get_the_title() . $after);

        } elseif ( is_page() && $post->post_parent ) {

            $parent_id  = $post->post_parent;
            $breadcrumbs = array();

            while ( $parent_id ) {
                $page = get_post( $parent_id );
                $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title( $page->ID )) . '</a>';
                $parent_id  = $page->post_parent;
            }

            $breadcrumbs = array_reverse( $breadcrumbs );

            foreach ( $breadcrumbs as $crumb ) {
                echo htmlspecialchars_decode($before . $crumb . $delimiter . $after);
            }

            echo htmlspecialchars_decode($before . get_the_title() . $after);

        } elseif ( is_search() ) {

            echo htmlspecialchars_decode($before . esc_html__( 'Search results for &ldquo;', 'flavours' ) . get_search_query() . '&rdquo;' . $after);

        } elseif ( is_tag() ) {

            if ( 'post' == get_post_type() && get_option( 'show_on_front' ) == 'page' ) {
                echo htmlspecialchars_decode($before . '<a href="' . esc_url(get_permalink( get_option('page_for_posts' ) )) . '">' . esc_html(get_the_title( get_option('page_for_posts', true) )) . '</a>' . $delimiter . $after);
            }

            echo htmlspecialchars_decode($before . esc_html__( 'Posts tagged &ldquo;', 'flavours' ) . single_tag_title('', false) . '&rdquo;' . $after);

        } elseif ( is_author() ) {

            $userdata = get_userdata($author);
            echo htmlspecialchars_decode($before . esc_html__( 'Author:', 'flavours' ) . ' ' . $userdata->display_name . $after);

        } elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' ) {

            $post_type = get_post_type_object( get_post_type() );

            if ( $post_type ) {
                echo htmlspecialchars_decode($before . $post_type->labels->singular_name . $after);
            }

        }

        if ( get_query_var( 'paged' ) ) {
            echo htmlspecialchars_decode($before . '&nbsp;(' . esc_html__( 'Page', 'flavours' ) . ' ' . get_query_var( 'paged' ) . ')' . $after);
        }

        echo '</ul>';
    } else { 
        if ( is_home() && !is_front_page() ) {
            echo '<ul class="breadcrumb">';

            if ( ! empty( $home ) ) {
                echo htmlspecialchars_decode($before . '<a class="home" href="' . esc_url(home_url()) . '">' . $home . '</a>' . $delimiter . $after);

               
                echo htmlspecialchars_decode($before . single_post_title('', false) . $after);
            }

            echo '</ul>';
        }
    }
}
  
  // breadcrumb
  function tmFlavours_page_breadcrumb()
  {
    /* === OPTIONS === */

    $text['home'] = 'Home'; // text for the 'Home' link
    $text['category'] = 'Archive by Category "%s"'; // text for a category page
    $text['tax'] = 'Archive for "%s"'; // text for a taxonomy page
    $text['search'] = 'Search Results for "%s" Query'; // text for a search results page
    $text['tag'] = 'Posts Tagged "%s"'; // text for a tag page
    $text['author'] = 'Articles Posted by %s'; // text for an author page
    $text['404'] = 'Error 404'; // text for the 404 page

    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = ' &mdash;&rsaquo; '; // delimiter between crumbs
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb
    /* === END OF OPTIONS === */

    global $post;

    $homeLink = home_url() . '/';
    $linkBefore = '<span typeof="v:Breadcrumb">';
    $linkAfter = '</span>';
    $linkAttr = ' rel="v:url" property="v:title"';
    $link = $linkBefore . '<a' . htmlspecialchars_decode($linkAttr) . ' href="%1$s">%2$s</a>' . htmlspecialchars_decode($linkAfter);

    if (is_home() || is_front_page()) {

      if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . esc_url($homeLink) . '">' . esc_html($text['home']) . '</a></div>';
     
    } else {
     
      echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, esc_url($homeLink), esc_html($text['home']) ). $delimiter;


      if (is_category()) {
        $thisCat = get_category(get_query_var('cat'), false);
        if ($thisCat->parent != 0) {
          $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
          $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
          $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
          echo htmlspecialchars_decode($cats);
        }
        echo htmlspecialchars_decode($before . sprintf($text['category'], single_cat_title('', false)) . $after);

      } elseif (is_tax()) {
        $thisCat = get_category(get_query_var('cat'), false);
        if ($thisCat->parent != 0) {
          $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
          $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
          $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
          echo htmlspecialchars_decode($cats);
        }
        echo htmlspecialchars_decode($before . sprintf(esc_html($text['tax']), single_cat_title('', false)) . $after);

      } elseif (is_search()) {
        echo htmlspecialchars_decode($before . sprintf(esc_html($text['search']), get_search_query()) . $after);

      } elseif (is_day()) {
        echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
        echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F')) . $delimiter;
        echo htmlspecialchars_decode($before . get_the_time('d') . $after);

      } elseif (is_month()) {
        echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
        echo htmlspecialchars_decode($before . get_the_time('F') . $after);

      } elseif (is_year()) {
        echo htmlspecialchars_decode($before . get_the_time('Y') . $after);

      } elseif (is_single() && !is_attachment()) {
        if (get_post_type() != 'post') {
          $post_type = get_post_type_object(get_post_type());
          $slug = $post_type->rewrite;
          printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
          if ($showCurrent == 1) echo htmlspecialchars_decode($delimiter . $before . get_the_title() . $after);
        } else {
          $cat = get_the_category();
          $cat = $cat[0];
          $cats = get_category_parents($cat, TRUE, $delimiter);
          if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
          $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
          $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
          echo htmlspecialchars_decode($cats);
          if ($showCurrent == 1) echo htmlspecialchars_decode($before . get_the_title() . $after);
        }

      } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
        $post_type = get_post_type_object(get_post_type());
        echo htmlspecialchars_decode($before . $post_type->labels->singular_name . $after);

      } elseif (is_attachment()) {
        $parent = get_post($post->post_parent);
        $cat = get_the_category($parent->ID);
        $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $delimiter);
        $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
        $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
        echo htmlspecialchars_decode($cats);
        printf($link, get_permalink($parent), $parent->post_title);
        if ($showCurrent == 1) echo htmlspecialchars_decode($delimiter . $before . get_the_title() . $after);

      } elseif (is_page() && !$post->post_parent) {
        if ($showCurrent == 1) echo htmlspecialchars_decode($before . get_the_title() . $after);

      } elseif (is_page() && $post->post_parent) {
        $parent_id = $post->post_parent;
        $breadcrumbs = array();
        while ($parent_id) {
          $page = get_page($parent_id);
          $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
          $parent_id = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        for ($i = 0; $i < count($breadcrumbs); $i++) {
          echo htmlspecialchars_decode($breadcrumbs[$i]);
          if ($i != count($breadcrumbs) - 1) echo htmlspecialchars_decode($delimiter);
        }
        if ($showCurrent == 1) echo htmlspecialchars_decode($delimiter . $before . get_the_title() . $after);

      } elseif (is_tag()) {
        echo htmlspecialchars_decode($before . sprintf($text['tag'], single_tag_title('', false)) . $after);

      } elseif (is_author()) {
        global $author;
        $userdata = get_userdata($author);
        echo htmlspecialchars_decode($before . sprintf($text['author'], $userdata->display_name) . $after);

      } elseif (is_404()) {
        echo htmlspecialchars_decode($before . $text['404'] . $after);
      }

      if (get_query_var('paged')) {
        if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ' (';
         esc_attr_e('Page', 'flavours') . ' ' . get_query_var('paged');
        if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ')';
      }

      echo '</div>';

    }
  }

  // mini cart
  function tmFlavours_mini_cart()
{
    global $woocommerce;
    ?>

<div class="mini-cart">
   <div  class="basket">
      <a href="<?php echo esc_url(WC()->cart->get_cart_url()); ?>">
         
        <span><?php echo esc_html($woocommerce->cart->cart_contents_count); ?> </span>
      </a>
   </div>

      <div class="top-cart-content arrow_box">
         <div class="block-subtitle">
            <div class="top-subtotal"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?> <?php  esc_attr_e('items','flavours'); ?>, <span class="price"><?php echo htmlspecialchars_decode(WC()->cart->get_cart_subtotal()); ?></span> </div>
         </div>
         <?php if (sizeof(WC()->cart->get_cart()) > 0) : $i = 0; ?>
         <ul id="cart-sidebar" class="mini-products-list">
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
            <?php
               $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
               $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
               
               if ($_product && $_product->exists() && $cart_item['quantity'] > 0
                   && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)
               ) :
               
                   $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                   $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(array(60, 60)), $cart_item, $cart_item_key);
                   $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                   $cnt = sizeof(WC()->cart->get_cart());
                   $rowstatus = $cnt % 2 ? 'odd' : 'even';
                   ?>
            <li class="item <?php echo esc_html($rowstatus); ?> <?php if ($cnt - 1 == $i) { ?>last<?php } ?>">
              <div class="item-inner">
               <a class="product-image"
                  href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>"  title="<?php echo esc_html($product_name); ?>"> <?php echo str_replace(array('http:', 'https:'), '', htmlspecialchars_decode($thumbnail)); ?> </a>
             

                  <div class="product-details">
                       <div class="access">
                     <a href="<?php echo esc_url(WC()->cart->get_remove_url($cart_item_key)); ?>"
                        title="<?php esc_attr_e('Remove This Item','flavours') ;?>" onClick="" class="btn-remove1"><?php esc_attr_e('Remove','flavours') ;?></a> <a class="btn-edit" title="<?php esc_attr_e('Edit item','flavours') ;?>"
                        href="<?php echo esc_url(WC()->cart->get_cart_url()); ?>"><i
                        class="icon-pencil"></i><span
                        class="hidden"><?php esc_attr_e('Edit item','flavours') ;?></span></a>
                         </div>
                      <strong><?php echo esc_html($cart_item['quantity']); ?>
                  </strong> x <span class="price"><?php echo htmlspecialchars_decode($product_price); ?></span>
                     <p class="product-name">
                         <a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>">
                             <?php echo $product_name; ?>
                         </a>
                     </p>
                  </div>
                  <?php echo htmlspecialchars_decode(WC()->cart->get_item_data($cart_item)); ?>
                     </div>
              
            </li>
            <?php endif; ?>
            <?php $i++; endforeach; ?>
         </ul>    
         <div class="actions">
            <button class="btn-checkout" type="button"
               onClick="window.location.assign('<?php echo esc_js(WC()->cart->get_checkout_url()); ?>')"><span><?php esc_attr_e('Checkout','flavours') ;?></span> </button>          
         </div>
         <?php else:?>
         <p class="a-center noitem">
            <?php esc_attr_e('Sorry, nothing in cart.', 'flavours');?>
         </p>
         <?php endif; ?>
      </div>
   </div>

<?php
}
 

 
  //social links
  function tmFlavours_social_media_links()
  {
    global $flavours_Options;

    if (isset($flavours_Options
  ['social_facebook']) && !empty($flavours_Options['social_facebook'])) {
      echo "<li class=\"fb pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_facebook']) ."'></a></li>";
    }

    if (isset($flavours_Options['social_twitter']) && !empty($flavours_Options['social_twitter'])) {
      echo "<li class=\"tw pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_twitter']) ."'></a></li>";
    }

    if (isset($flavours_Options['social_googlep']) && !empty($flavours_Options['social_googlep'])) {
      echo "<li class=\"googleplus pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_googlep'])."'></a></li>";
    }

    if (isset($flavours_Options['social_rss']) && !empty($flavours_Options['social_rss'])) {
      echo "<li class=\"rss pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_rss'])."'></a></li>";
    }

    if (isset($flavours_Options['social_pinterest']) && !empty($flavours_Options['social_pinterest'])) {
      echo "<li class=\"pintrest pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_pinterest'])."'></a></li>";
    }

    if (isset($flavours_Options['social_linkedin']) && !empty($flavours_Options['social_linkedin'])) {
      echo "<li class=\"linkedin pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_linkedin'])."'></a></li>";
    }

    if (isset($flavours_Options['social_youtube']) && !empty($flavours_Options['social_youtube'])) {
      echo "<li class=\"youtube pull-left\"><a target=\"_blank\" href='".  esc_url($flavours_Options['social_youtube'])."'></a></li>";
    }
  }


  // bottom cpyright text 
  function tmFlavours_footer_text()
  {
    global $flavours_Options;
    if (isset($flavours_Options['bottom-footer-text']) && !empty($flavours_Options['bottom-footer-text'])) {
      echo htmlspecialchars_decode ($flavours_Options['bottom-footer-text']);
    }
  }


  function tmFlavours_getPostViews($postID)
  {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
      delete_post_meta($postID, $count_key);
      add_post_meta($postID, $count_key, '0');
      return "0 View";
    }
    return $count . ' Views';
  }

  function tmFlavours_setPostViews($postID)
  {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
      $count = 0;
      delete_post_meta($postID, $count_key);
      add_post_meta($postID, $count_key, '0');
    } else {
      $count++;
      update_post_meta($postID, $count_key, $count);
    }
  }


  function tmFlavours_is_blog() {
    global  $post;
    $posttype = get_post_type($post );
    return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
  }
  //add to cart function
function tmFlavours_woocommerce_product_add_to_cart_text() {
    global $product;
    $product_type = $product->product_type;
    $product_id=$product->id;
    if($product->is_in_stock())
    {
    switch ( $product_type ) {
    case 'external':
    ?>
    <button class="button btn-cart" title='<?php esc_attr_e("Buy product",'flavours'); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")'>
    <span> <?php esc_attr_e('Buy product', 'flavours'); ?></span>
    </button>
    <?php
       break;
       case 'grouped':
        ?>
    <button class="button btn-cart" title='<?php esc_attr_e("View products",'flavours'); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")' >
    <span><?php esc_attr_e('View products', 'flavours'); ?> </span>
    </button>
    <?php
       break;
       case 'simple':
        ?>
    <?php tmFlavours_simple_product_link();?>
    <?php
       break;
       case 'variable':
        ?>
    <button class="button btn-cart"  title='<?php esc_attr_e("Select options",'flavours'); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")'>
    <span>
    <?php esc_attr_e('Select options', 'flavours'); ?>
    </span> 
    </button>
    <?php
       break;
       default:
        ?>
    <button class="button btn-cart" title='<?php esc_attr_e("Read more",'flavours'); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")'>
    <span><?php esc_attr_e('Read more', 'flavours'); ?></span> 
    </button>
    <?php
       break;
       
       }
       }
       else
       {
       ?>
    <button type='button' class="button btn-cart" title='<?php esc_attr_e('Out of stock', 'flavours'); ?> '
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")'
       class='button btn-cart'>
    <span> <?php esc_attr_e('Out of stock', 'flavours'); ?> </span>
    </button>
    <?php
    }
}
 
 // comment display 
  function tmFlavours_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>

  <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
    <div class="comment-body">
      <div class="img-thumbnail">
        <?php echo get_avatar($comment, 80); ?>
      </div>
      <div class="comment-block">
        <div class="comment-arrow"></div>
        <span class="comment-by">
          <strong><?php echo get_comment_author_link() ?></strong>
          <span class="pt-right">
            <span> <?php edit_comment_link('<i class="fa fa-pencil"></i> ' . esc_html__('Edit', 'flavours'),'  ','') ?></span>
            <span> <?php comment_reply_link(array_merge( $args, array('reply_text' => '<i class="fa fa-reply"></i> ' . esc_html__('Reply', 'flavours'), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
          </span>
        </span>
        <div>
          <?php if ($comment->comment_approved == '0') : ?>
            <em><?php echo esc_html__('Your comment is awaiting moderation.', 'flavours') ?></em>
            <br />
          <?php endif; ?>
          <?php comment_text() ?>
        </div>
        <span class="date pt-right"><?php printf(esc_html__('%1$s at %2$s', 'flavours'), get_comment_date(),  get_comment_time()) ?></span>
      </div>
    </div>
  </li>
  <?php }

  //css manage by admin
  function tmFlavours_enqueue_custom_css() {
    global $flavours_Options;

    ?>
    <style rel="stylesheet" property="stylesheet" type="text/css">
      <?php if(isset($flavours_Options['opt-color-rgba']) &&  !empty($flavours_Options['opt-color-rgba'])) {
      ?>
      .tm-main-menu {
        background-color: <?php echo esc_html($flavours_Options['opt-color-rgba'])." !important";
      ?>
      }
      <?php
      }
      ?>
       
      
      <?php if(isset($flavours_Options['footer_color_scheme']) && $flavours_Options['footer_color_scheme']) {
      if(isset($flavours_Options['footer_copyright_background_color']) && !empty($flavours_Options['footer_copyright_background_color'])) {
       ?>
      .footer-bottom {
        background-color: <?php echo esc_html($flavours_Options['footer_copyright_background_color'])." !important";
       ?>
      }

      <?php
       }
       ?>
      <?php if(isset($flavours_Options['footer_copyright_font_color']) && !empty($flavours_Options['footer_copyright_font_color'])) {
       ?>
      .coppyright {
        color: <?php echo esc_html($flavours_Options['footer_copyright_font_color'])." !important";
      ?>
      }

      <?php
       }
       ?>
      <?php
       }
       ?>
    </style>
    <?php
  }
}


// Instantiate theme
$TmFlavours = new TmFlavours();

/**
 * Send SMS after order completed
 */

add_action( 'woocommerce_order_status_changed', 'my_order_status_changed');

function my_order_status_changed($order_id, $old_status = '', $new_status = '') {

    $sendDeliveryMessage = (get_option('woocommerce_enable_delivery_notice', 'no') == 'yes');
    $sendPaymentMessage = (get_option('woocommerce_enable_payment_notice', 'no') == 'yes');

    $acceptStatus = ['completed', 'wc-completed', 'processing', 'wc-processing'];

    global $wpdb;

    $order = new WC_Order($order_id);

    if (!(in_array($new_status, $acceptStatus) || in_array($order->post->post_status, $acceptStatus)))
    {
        return;
    }
/*
    $deilveryDate = $wpdb->get_var("
            SELECT date
            FROM wp_jckwds
            WHERE order_id = '{$order_id}'
        ");

    $deilveryDate = date('m月d日', strtotime($deilveryDate));
*/
    $items = $order->get_items();
    $total = $order->get_total();
    $itemName = '';

    foreach ($items as $itemKey => $item) {
        $itemName = $item['name'];
        break;
    }

    if ($new_status == 'completed' || $order->post->post_status == 'wc-completed') {
        $replacements = [
            '%item' => $itemName,
            '%no' => $order_id,
            '%phone' => get_option('woocommerce_company_number', '')
        ];

        $option = get_option('woocommerce_delivery_notice');
        $sendMessage = $sendDeliveryMessage;

    } else if ($new_status == 'processing' || $order->post->post_status == 'wc-processing') {
        $replacements = [
            '%item' => $itemName,
            '%payment' => $total,
            '%no' => $order_id,
            //'%date' => $deilveryDate,
        ];

        $option = get_option('woocommerce_payment_notice');
        $sendMessage = $sendPaymentMessage;
    }

    $defaultPhone = get_option('woocommerce_message_phone');

    $orderPhone = empty($defaultPhone) ? $order->billing_phone : $defaultPhone;

    $notice = str_replace(array_keys($replacements), $replacements, $option);

    $url = 'http://api.twsms.com/smsSend.php';
    $attr = [
        'body' => [
            'username' => 'dagolin',
            'password' => 'buyfood911',
            'mobile' => $orderPhone,
            'message' => $notice,
        ]
    ];

    if ($sendMessage) {
        $msgResponse = wp_safe_remote_get($url, $attr);
    }
}


// - -

/**
 * Displays product attributes in the top right of the single product page.
 *
 * @param $product
 */
function tutsplus_list_attributes( $product ) {

    global $product;
    global $flavours_Options;

    $product_shipping_classes = get_the_terms( $product->id, 'product_shipping_class' );
    $product_shipping_class_name = ( $product_shipping_classes && ! is_wp_error( $product_shipping_classes ) ) ? current( $product_shipping_classes )->name : '';

    $defaultImgUrl = esc_url(TMFLAVOURS_THEME_URI).'/images/cool.gif';

    $option = 'normal_image';

    switch ($product_shipping_class_name){
        case '常溫':
            break;
        case '冷藏':
            $option = 'cool_image';
            break;
        case '冷凍':
            $option = 'frozen_image';
            break;
        default:
            break;
    }

    $imageUrl = isset($flavours_Options[$option]) ? $flavours_Options[$option] : $defaultImgUrl;

    if (!empty($imageUrl)) {
        echo '<span class="posted_in"><img src="' . $imageUrl['url'] . '" /></span>';
    }
}

add_action( 'woocommerce_product_meta_end', 'tutsplus_list_attributes' );

//
//Adding Meta container admin shop_order pages
//
add_action( 'add_meta_boxes', 'mv_add_meta_boxes' );
if ( ! function_exists( 'mv_add_meta_boxes' ) )
{
    function mv_add_meta_boxes()
    {
        global $woocommerce, $order, $post;

        add_meta_box( 'mv_other_fields', __('出貨時間','woocommerce'), 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core' );
    }
}

//
//adding Meta field in the meta container admin shop_order pages
//
if ( ! function_exists( 'mv_save_wc_order_other_fields' ) )
{
    function mv_add_other_fields_for_packaging()
    {
        global $woocommerce, $order, $post;


        //$meta_field_data = get_post_meta( $post->ID, '_my_choice', true ); //? get_post_meta( $post->ID, '_my_choice', true ) : '';
        $meta_field_data = get_post_meta( $post->ID, 'delivery_date', true );

        echo '<input type="hidden" name="mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">
        <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
            <input type="text" class="date-picker" style="width:250px;";" name="delivery_date" placeholder="' . $meta_field_data
            . '" value="' . $meta_field_data . '" ></p>';


    }
}

//Save the data of the Meta field
add_action( 'save_post', 'mv_save_wc_order_other_fields', 10, 1 );
if ( ! function_exists( 'mv_save_wc_order_other_fields' ) )
{

    function mv_save_wc_order_other_fields( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'mv_other_meta_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'mv_other_meta_field_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        update_post_meta( $post_id, 'delivery_date', $_POST[ 'delivery_date' ] );
    }
}





// - -
/**
 * Display field value on the order edit page
 */

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_phone_display_admin_order_meta', 10, 1 );

function my_custom_checkout_phone_display_admin_order_meta($order){
    echo '<p><strong>'.__('連絡電話').':</strong> ' . get_post_meta( $order->id, '_shipping_phone', true ) . '</p>';
}


add_action('woocommerce_checkout_process', 'is_cert');

function is_cert() {
    // Check if set, if its not set add an error.
    if ($_REQUEST['billing_skipcert'] == 'false'){
        if (!isset($_SESSION['cert'])
            || empty($_SESSION['cert'])
            || empty($_REQUEST['billing_cert'])
            || (string) $_SESSION['cert'] !== (string) $_REQUEST['billing_cert']
            || (time() - $_SESSION['time']) > 3600) {
            wc_add_notice( __( '認證碼錯誤或過期，請重新輸入.' ), 'error' );
        }
    }
}


function register_session(){
    if( !session_id() ){
        //session_start();
    }
}

add_action('init','register_session');


function cert_check() {

    $response = [
        'code' => '404',
        'message' => '錯誤發生，請聯絡管理員'
    ];

    $cert = rand(1000, 9999);

    $_SESSION['cert'] = $cert;
    $_SESSION['time'] = time();

    $replacements = [
        '%cert' => $cert,
    ];

    $certString = str_replace(array_keys($replacements), $replacements, get_option('woocommerce_registration_notice'));
    $defaultPhone = get_option('woocommerce_message_phone');

    $phone = empty($defaultPhone) ? $_REQUEST['phone'] : $defaultPhone;

    $url = 'http://api.twsms.com/smsSend.php';
    $attr = [
        'body' => [
            'username' => 'dagolin',
            'password' => 'buyfood911',
            'mobile' => $phone,
            'message' => $certString,
        ]
    ];

    $msgResponse = wp_safe_remote_get($url, $attr);

    if ($msgResponse['response']['code'] == 200){
        $response = $msgResponse['response'];
        $response['message'] = '感謝您的支持，認證碼 已發送到您的手機，請稍待片刻，並輸入簡訊內的四位數字。';
    }

    echo json_encode($response);

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_cert_check', 'cert_check' );
}

add_action( 'wp_enqueue_scripts', 'ajax_cert_enqueue_scripts' );

function ajax_cert_enqueue_scripts() {

    wp_enqueue_script( 'cert', get_template_directory_uri() . '/js/cert.js', array('jquery'), '1.0', true );

    wp_localize_script( 'cert', 'cert', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}

/*
 * Add this to your (child) theme's functions.php file
 */
add_filter( 'woocommerce_cart_item_name', 'add_product_shipping_class', PHP_INT_MAX, 3 );
/*
 * add_product_shipping_class.
 */
function add_product_shipping_class( $cart_item_name, $cart_item, $cart_item_key ) {
    $product_id = ( 0 != $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
    $product_shipping_classes = get_the_terms( $product_id, 'product_shipping_class' );
    $product_shipping_class_name = ( $product_shipping_classes && ! is_wp_error( $product_shipping_classes ) ) ? current( $product_shipping_classes )->name : '';
    $product_shipping_class_style = '';

    switch ($product_shipping_class_name){
        case '常溫':
            $product_shipping_class_style = 'degree-normal';
            break;
        case '冷藏':
            $product_shipping_class_style = 'degree-cool';
            break;
        case '冷凍':
            $product_shipping_class_style = 'degree-frozen';
            break;
        default:
            break;
    }

    return $cart_item_name . ' ' . '<span class="' . $product_shipping_class_style . '">'  . $product_shipping_class_name . '</span>';
}

/*
 * Sort shopping cart by shipping class
 */
add_action( 'woocommerce_cart_loaded_from_session', function() {

    global $woocommerce;
    $products_in_cart = array();
    foreach ( $woocommerce->cart->cart_contents as $key => $item ) {
        $product_shipping_classes = get_the_terms( $item['data']->id, 'product_shipping_class' );
        $products_in_cart[ $key ] = ( $product_shipping_classes && ! is_wp_error( $product_shipping_classes ) ) ? current( $product_shipping_classes )->term_id : '';
    }

    asort( $products_in_cart );

    $cart_contents = array();
    foreach ( $products_in_cart as $cart_key => $product_title ) {
        $cart_contents[ $cart_key ] = $woocommerce->cart->cart_contents[ $cart_key ];
    }
    $woocommerce->cart->cart_contents = $cart_contents;

}, 100 );

?>
