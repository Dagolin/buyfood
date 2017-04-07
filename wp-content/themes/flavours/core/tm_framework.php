<?php 
require_once(TMFLAVOURS_THEME_PATH . '/includes/layout.php');
require_once(TMFLAVOURS_THEME_PATH . '/core/resize.php');
require_once(TMFLAVOURS_THEME_PATH . '/includes/tm_menu.php');
require_once(TMFLAVOURS_THEME_PATH . '/includes/widget.php');
require_once(TMFLAVOURS_THEME_PATH . '/includes/tm_widget.php');
require_once(TMFLAVOURS_THEME_PATH .'/core/social_share.php');


 /* Include theme variation functions */  
 if ( ! function_exists ( 'tmFlavours_theme_layouts' ) ) {
 function tmFlavours_theme_layouts()
 {
 global $flavours_Options;   
 if (isset($flavours_Options['theme_layout']) && !empty($flavours_Options['theme_layout'])) { 
require_once (get_template_directory(). '/skins/' . $flavours_Options['theme_layout'] . '/functions.php');   
} else {
require_once (get_template_directory(). '/skins/default/functions.php');   
}
 }
}

// call theme skins function
tmFlavours_theme_layouts();


 /* Include theme variation header */   
 if ( ! function_exists ( 'tmFlavours_theme_header' ) ) {
   function tmFlavours_theme_header()
 {
 global $flavours_Options;   
  if (isset($flavours_Options['theme_layout']) && !empty($flavours_Options['theme_layout'])) {
load_template(get_template_directory() . '/skins/' . $flavours_Options['theme_layout'] . '/header.php', true);
} else {
load_template(get_template_directory() . '/skins/default/header.php', true);
}
 }
}

/* Include theme variation homepage */ 
if ( ! function_exists ( 'tmFlavours_theme_homepage' ) ) {
  function tmFlavours_theme_homepage()
 {  
 global $flavours_Options;  

 if (isset($flavours_Options['theme_layout']) && !empty($flavours_Options['theme_layout'])) { 
load_template(get_template_directory() . '/skins/' . $flavours_Options['theme_layout'] . '/homepage.php', true);
} else {
load_template(get_template_directory() . '/skins/default/homepage.php', true);
}
 }
}

 /* Include theme variation footer */
if ( ! function_exists ( 'tmFlavours_theme_footer' ) ) {  
function tmFlavours_theme_footer()
{
     
 global $flavours_Options;   
  if (isset($flavours_Options['theme_layout']) && !empty($flavours_Options['theme_layout'])) {
load_template(get_template_directory() . '/skins/' . $flavours_Options['theme_layout'] . '/footer.php', true);
} else {
load_template(get_template_directory() . '/skins/default/footer.php', true);
} 
}
}

if ( ! function_exists ( 'tmFlavours_simple_product_link' ) ) {  
function tmFlavours_simple_product_link()
{
  global $product,$class;
  $product_type = $product->product_type;
  $product_id=$product->id;
    $startDateTemp = get_post_meta($product_id, 'limit_start_date', true);
    $endDateTemp = get_post_meta($product_id, 'limit_end_date', true);
  if($product->price=='')
  { ?>
<a class="button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>'
       onClick='window.location.assign("<?php echo esc_js(get_permalink($product_id)); ?>")' >
    <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
    </a>
<?php  }
  else{
      if (!empty($startDateTemp) && !empty($endDateTemp) )
      {
          $now = time();
          $startDateTime = strtotime($startDateTemp);
          $endDateTime = strtotime($endDateTemp);
          if ($now >= $startDateTime && $now <= $endDateTime)
          {
              ?>
              <a class="single_add_to_cart_button add_to_cart_button  product_type_simple ajax_add_to_cart button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>' data-quantity="1" data-product_id="<?php echo esc_attr($product->id); ?>"
                 href='<?php echo esc_url($product->add_to_cart_url()); ?>'>
                  <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
              </a>
          <?php } else { ?>
              <div class="single_add_to_cart_button add_to_cart_button  product_type_simple ajax_add_to_cart button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>'>
                  <span>搶購截止</span>
              </div>
              <?php }
      } else {
          ?>
          <a class="single_add_to_cart_button add_to_cart_button  product_type_simple ajax_add_to_cart button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>' data-quantity="1" data-product_id="<?php echo esc_attr($product->id); ?>"
             href='<?php echo esc_url($product->add_to_cart_url()); ?>'>
              <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
          </a>
      <?php
      }
  ?>

<?php
}
}
}

if ( ! function_exists ( 'tmFlavours_allowedtags' ) ) {  
function tmFlavours_allowedtags() {
    // Add custom tags to this string
        return '<script>,<style>,<br>,<em>,<i>,<ul>,<ol>,<li>,<a>,<p>,<img>,<video>,<audio>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<b>,<blockquote>,<strong>,<figcaption>'; 
    }
}
if ( ! function_exists( 'tmFlavours_wp_trim_excerpt' ) ) : 

    function tmFlavours_wp_trim_excerpt($wpse_excerpt) {
    $raw_excerpt = $wpse_excerpt;
        if ( '' == $wpse_excerpt ) {

            $wpse_excerpt = get_the_content('');
            $wpse_excerpt = strip_shortcodes( $wpse_excerpt );
            $wpse_excerpt = apply_filters('the_content', $wpse_excerpt);
            $wpse_excerpt = str_replace(']]>', ']]&gt;', $wpse_excerpt);
            $wpse_excerpt = strip_tags($wpse_excerpt, tmFlavours_allowedtags()); /*IF you need to allow just certain tags. Delete if all tags are allowed */

            //Set the excerpt word count and only break after sentence is complete.
                $excerpt_word_count = 75;
                $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); 
                $tokens = array();
                $excerptOutput = '';
                $count = 0;

                // Divide the string into tokens; HTML tags, or words, followed by any whitespace
                preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);

                foreach ($tokens[0] as $token) { 

                    if ($count >= $excerpt_length && preg_match('/[\,\;\?\.\!]\s*$/uS', $token)) { 
                    // Limit reached, continue until , ; ? . or ! occur at the end
                        $excerptOutput .= trim($token);
                        break;
                    }

                    // Add words to complete sentence
                    $count++;

                    // Append what's left of the token
                    $excerptOutput .= $token;
                }

            $wpse_excerpt = trim(force_balance_tags($excerptOutput));

                $excerpt_end = ' '; 
                $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end); 

                $wpse_excerpt .= $excerpt_more; /*Add read more in new paragraph */

            return $wpse_excerpt;   

        }
        return apply_filters('tmFlavours_wp_trim_excerpt', $wpse_excerpt, $raw_excerpt);
    }

endif; 

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'tmFlavours_wp_trim_excerpt');

 
?>