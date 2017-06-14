<?php
/**
 * Cart Page
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version 2.3.8
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $product, $woocommerce;
wc_print_notices();

do_action('woocommerce_before_cart'); ?>

       
<div class="col-main">
    <div class="cart wow bounceInUp">
        <form action="<?php echo esc_url(WC()->cart->get_cart_url()); ?>" method="post">
        <div class="table-responsive shopping-cart-tbl row">
           
                <?php do_action('woocommerce_before_cart_table'); ?>
                <fieldset>
                    <table class="data-table cart-table" id="shopping-cart-table">
                        <thead>
                        <tr>
                            <th rowspan="1">&nbsp;</th>
                            <th rowspan="1"><span class="nobr">
                <?php esc_attr_e('Product Name', 'woocommerce'); ?>
                </span></th>
                            <th colspan="1" class="a-center"><span class="nobr">
                <?php esc_attr_e('單價', 'woocommerce'); ?>
                </span></th>
                            <th class="a-center " rowspan="1"><?php esc_attr_e('Qty', 'woocommerce'); ?></th>
                            <th colspan="1" class="a-center"><?php esc_attr_e('Subtotal', 'woocommerce'); ?></th>
                            <th class="a-center" rowspan="1">&nbsp;</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr class="first last">
                            <td class="a-right last" colspan="50">
                                <button
                                    onclick="location.href = '<?php echo esc_url(get_home_url()  . '/product-category/meat/'); ?>'"
                                    class="button btn-continue" title="<?php esc_attr_e('Continue Shopping', 'woocommerce'); ?>" type="button"><span><?php esc_attr_e('Continue Shopping', 'flavours'); ?></span>
                                </button>
                                <button type="submit" class="button btn-update" name="update_cart"
                                        value="<?php _e('Update Cart', 'flavours'); ?>"><span>
                <?php _e('Update Cart', 'woocommerce'); ?>
                </span></button>
                                <button id="empty_cart_button" class="button" title="<?php esc_attr_e('Clear Cart', 'flavours'); ?>" 
                                        name="clear-cart" type="submit"><span><?php esc_attr_e('Clear Cart', 'flavours'); ?></span></button>
                                <?php do_action('woocommerce_cart_actions'); ?>
                                <?php wp_nonce_field('woocommerce-cart'); ?>
                            </td>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php do_action('woocommerce_before_cart_contents'); ?>
                        <?php
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                ?>
                                <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                    <td class="image"><?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(array(75, 75)), $_product->get_image(), $cart_item, $cart_item_key);
                                        if (!$_product->is_visible())
                                            echo $thumbnail;
                                        else
                                            printf('<a href="%s" class="product-image">%s</a>', esc_url($_product->get_permalink($cart_item)), $thumbnail);
                                        ?>
                                    </td>
                                    <td><h2 class="product-name">
                                            <?php
                                            if (!$_product->is_visible())
                                                echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                                            else
                                                echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', $_product->get_permalink($cart_item), $_product->get_title()), $cart_item, $cart_item_key);

                                            // Meta data
                                            echo WC()->cart->get_item_data($cart_item);

                                            // Backorder notification
                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity']))
                                                echo '<p class="backorder_notification">' . __('Available on backorder', 'woocommerce') . '</p>';
                                            ?>
                                        </h2></td>
                                    <td class="a-center hidden-table"><span class="cart-price"> <span class="price">
                <?php
                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                ?>
                </span> </span></td>
                                    <td class="a-center movewishlist"><?php
                                        if ($_product->is_sold_individually()) {
                                            $product_quantity = sprintf('1 <input type="hidden" class="input-text qty name="cart[%s][qty]" value="1" />', $cart_item_key);
                                        } else {
                                            $product_quantity = woocommerce_quantity_input(array(
                                                'input_name' => "cart[{$cart_item_key}][qty]",
                                                'input_value' => $cart_item['quantity'],
                                                'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                                'min_value' => '0'
                                            ), $_product, false);
                                        }

                                        echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key);
                                        ?>
                                    </td>
                                    <td class="a-center movewishlist"><span class="cart-price"> <span class="price">
                <?php
                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                ?>
                </span> </span></td>
                                    <td class="a-center last"><?php
                                        echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="button remove-item" title="%s"><span><span>Remove item</span></span></a>', esc_url(WC()->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key);
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }

                        do_action('woocommerce_cart_contents');
                        ?>
                        <?php do_action('woocommerce_after_cart_contents'); ?>
                        </tbody>
                    </table>
                </fieldset>
                 </div>
                <?php do_action('woocommerce_after_cart_table'); ?>

                <!-- BEGIN CART COLLATERALS -->
                <div class="cart-collaterals row">

                    <div class="col-sm-6">
                        <?php if (WC()->cart->coupons_enabled()) { ?>
                            <div class="discount">
                                <h3><?php _e('Discount Codes', 'flavours'); ?></h3>

                                <div id="discount-coupon-form">
                                    <label for="coupon_code">
                                        <?php esc_attr_e('輸入優惠卷代號，立刻折價！', 'woocommerce'); ?>
                                        :</label>
                                    <input type="text" name="coupon_code" class="input-text fullwidth" id="coupon_code"
                                           value="" placeholder="<?php _e('Coupon code', 'woocommerce'); ?>"/>
                                    <button type="submit" class="button" name="apply_coupon"
                                            value="<?php _e('Apply Coupon', 'woocommerce'); ?>"/>
                <span>
                <?php _e('Apply Coupon', 'woocommerce'); ?>
                </span>
                                    </button>
                                    <?php do_action('woocommerce_cart_coupon'); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6">
                        <?php woocommerce_cart_totals(); ?>
                    </div>
                </div>

            </form>
        
    </div>
    <!-- begin cart-collaterals-->
    <?php do_action('woocommerce_cart_collaterals'); ?>
    <!--cart-collaterals-->
    <?php do_action('woocommerce_after_cart'); ?>
</div>
