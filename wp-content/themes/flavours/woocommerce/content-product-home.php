<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$TmFlavours = new TmFlavours();
global $product, $woocommerce_loop, $yith_wcwl;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
$class = 'item col-lg-4 col-md-4 col-sm-4 col-xs-6';
if ( 0 === ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 === $woocommerce_loop['columns'] ) {
    $class = 'item col-lg-4 col-md-4 col-sm-4 col-xs-6';
    $classes[] = $class;
}
if ( 0 === $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
    $class = 'item col-lg-4 col-md-4 col-sm-4 col-xs-6';
    $classes[] = $class;
}
?>

<li class="<?php echo $class; ?>" >
   <div class="item-inner">
      <div class="item-img">
         <div class="item-img-info">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>
            <div class="pimg">
            <a  href="<?php the_permalink(); ?>" class="product-image">
              
                  <?php
                     /**
                      * woocommerce_before_shop_loop_item_title hook
                      *
                      * @hooked woocommerce_show_product_loop_sale_flash - 10
                      * @hooked woocommerce_template_loop_product_thumbnail - 10
                      */
                     do_action('woocommerce_before_shop_loop_item_title');
                     ?>
             
            </a>
                <?php if ($product->is_on_sale()) : ?>
                    <div class="sale-label sale-top-left">
                        <?php esc_attr_e('Sale', 'flavours'); ?>
                    </div>
                <?php endif; ?>
          </div>
        
         <div class="item-box-hover">
             <div class="box-inner product-action">
              <div class="product-detail-bnt">
                   <?php if (class_exists('YITH_WCQV_Frontend')) { ?>
                  <a title="<?php esc_attr_e('Quick View', 'flavours'); ?>" class="button detail-bnt yith-wcqv-button quickview" type="button" data-product_id="<?php echo esc_html($product->id); ?>"><span><?php esc_attr_e('Quick View', 'flavours'); ?></span></a>
                  <?php } ?>
                </div> 
                <?php if (isset($yith_wcwl) && is_object($yith_wcwl)) {
		        $classes = get_option('yith_wcwl_use_button') == 'yes' ? 'class="link-wishlist"' : 'class="link-wishlist"';
	        ?>
		<a href="<?php echo esc_url($yith_wcwl->get_addtowishlist_url()) ?>"
	           data-product-id="<?php echo esc_html($product->id); ?>"
        	   data-product-type="<?php echo esc_html($product->product_type); ?>" <?php echo htmlspecialchars_decode($classes); ?>
	           title="<?php esc_attr_e('Add to WishList','flavours'); ?>"></a>
		<?php
	        }
	        
	        if (class_exists('YITH_Woocompare_Frontend')) {

        		$tm_yith_cmp = new YITH_Woocompare_Frontend;
          		$tm_yith_cmp->add_product_url($product->id);
	         ?>
		<a class="compare add_to_compare_small link-compare" data-product_id="<?php echo esc_html($product->id); ?>"
	href="<?php echo esc_url($tm_yith_cmp->add_product_url($product->id)); ?>" title=" <?php esc_attr_e('Add to Compare','flavours'); ?>"></a>
		<?php
		}
	        ?>
                                  
	        
            </div>
         </div>
          </div>
      </div>
    <div class="item-info">
      <div class="info-inner">
            <div class="item-title"><a href="<?php the_permalink(); ?>">
               <?php the_title(); ?>
               </a>
            </div>
            <div class="item-content">
               <div class="rating">
                  <div class="ratings">
                     <div class="rating-box">
                        <?php $average = $product->get_average_rating(); ?>
                        <div style="width:<?php echo esc_html(($average / 5) * 100); ?>%" class="rating"></div>
                     </div>
                  </div>
               </div>
               <div class="item-price">
                  <div class="price-box"> <?php echo htmlspecialchars_decode($product->get_price_html()); ?>
                     <?php
                        /**
                         * woocommerce_after_shop_loop_item_title hook
                         *
                         * @hooked woocommerce_template_loop_rating - 5
                         * @hooked woocommerce_template_loop_price - 10
                         */
                        
                        ?>                   
                  </div>
               </div>
                 
               <div class="desc std">
                  <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?>
               </div>
               <div class="action">

                   <?php
                   $TmFlavours->tmFlavours_woocommerce_product_add_to_cart_text();
                   ?>
              </div>     
            </div>
         </div>
      </div>
   </div>
</li>
