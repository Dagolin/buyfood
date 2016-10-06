<?php
/**
 * Cart totals
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="cart_totals totals <?php if ( WC()->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <h3><?php esc_attr_e( 'Shopping Cart Total', 'flavours' ); ?></h3>



     <div class="inner">
        <table class="table shopping-cart-table-total" id="shopping-cart-totals-table">


            <tfoot>

         <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

        <tr class="order-total">
            <th><?php esc_attr_e( 'Total', 'woocommerce' ); ?></th>
            <td ><strong><span
                            class="price"><?php wc_cart_totals_order_total_html(); ?></span></strong></td>
        </tr>
        <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

         </tfoot>
            <tbody>
            <tr class="cart-subtotal">
                <td colspan="1" class="a-left"> <?php esc_attr_e('Subtotal', 'flavours'); ?> </td>
                <td ><span class="price"><?php wc_cart_totals_subtotal_html(); ?></span></td>
            </tr>


        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>


        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

            <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

        <?php elseif ( WC()->cart->needs_shipping() ) : ?>

            <tr class="shipping">
                <th><?php esc_attr_e( 'Shipping', 'woocommerce' ); ?></th>
                <td><?php woocommerce_shipping_calculator(); ?></td>
            </tr>

        <?php endif; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && WC()->cart->tax_display_cart == 'excl' ) : ?>
            <?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                    <tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                        <th><?php echo esc_html( $tax->label ); ?></th>
                        <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="tax-total">
                    <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                    <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        
    </tbody>

        </table>

    <?php if ( WC()->cart->get_cart_tax() ) : ?>
        <p class="wc-cart-shipping-notice"><small><?php

            $estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
                ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'woocommerce' ), WC()->countries->estimated_for_prefix() . __( WC()->countries->countries[ WC()->countries->get_base_country() ], 'woocommerce' ) )
                : '';

            printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'woocommerce' ), $estimated_text );

        ?></small></p>
    <?php endif; ?>

    <div class="wc-proceed-to-checkout">
 <ul class="checkout">
            <li>
        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
        </li>

        </ul>
    </div>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>

</div>
