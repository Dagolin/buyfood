<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see       https://docs.woocommerce.com/document/template-structure/
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     2.6.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;


?>

            <div class="product-image">
                <div class="large-image">
                    <?php
                    if (has_post_thumbnail()) {

                        $attachment_count = count( $product->get_gallery_attachment_ids() );
                        $attachment_id=get_post_thumbnail_id();
                        $props      = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
                        $image_title = $props['title'];
                        $image_link = $props['url'];
     
                        $image     = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
                            'title'  => $props['title'],
                           'alt'    => $props['alt'],
                            ) );                       

                        if ($attachment_count > 0) {
                            $gallery = '[product-gallery]';
                        } else {
                            $gallery = '';
                        }

                        echo apply_filters('woocommerce_single_product_image_html', sprintf('<a href="%s" itemprop="image" class="woocommerce-main-image zoom cloud-zoom" title="%s" data-rel="prettyPhoto' . $gallery . '" id="zoom1" rel="useWrapper: false, adjustY:0, adjustX:20">%s</a>', esc_url($image_link), esc_html($image_title), $image), $post->ID);

                    } else {

                        echo apply_filters('woocommerce_single_product_image_html', sprintf('<img src="%s" alt="%s" />', esc_url(wc_placeholder_img_src()), __('Placeholder', 'woocommerce')), $post->ID);

                    }
                    ?>
                </div>
                <?php do_action('woocommerce_product_thumbnails'); ?>
            </div>
            <!-- end: more-images -->
           
      