<?php
/**
 * Simple product add to cart
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

if (!$product->is_purchasable()) {
    return;
}

?>





<?php if ($product->is_in_stock()) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>
    <div class="add-to-box">
        <div class="add-to-cart woocommerce">
            <form class="cart" method="post" enctype='multipart/form-data'>
                <?php do_action('woocommerce_before_add_to_cart_button'); ?>
                <div class="pull-left">
                    <div class="custom pull-left">
                        <?php
                        if (!$product->is_sold_individually())
                            woocommerce_quantity_input(array(
                                'min_value' => apply_filters('woocommerce_quantity_input_min', 1, $product),
                                'max_value' => apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product)
                            ));
                        ?>

                        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>"/>
                    </div>
                </div>
                <div>
                    <a class="single_add_to_cart_button add_to_cart_button  product_type_simple ajax_add_to_cart button btn-cart" title='<?php echo esc_html($product->add_to_cart_text()); ?>' data-quantity="1" data-product_id="<?php echo esc_attr($product->id); ?>"
                       href='<?php echo esc_url($product->add_to_cart_url()); ?>'>
                        <span><?php echo esc_html($product->add_to_cart_text()); ?> </span>
                    </a>

<!--                    <button type="submit" class="single_add_to_cart_button button alt button btn-cart"><span><i-->
<!--                                class="icon-basket"></i> --><?php //echo $product->single_add_to_cart_text(); ?><!--</span>-->
<!--                    </button>-->
                </div>
                <?php do_action('woocommerce_after_add_to_cart_button'); ?>
            </form>
        </div>
    </div>
    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>
