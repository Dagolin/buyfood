<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();
wc_print_notice( __( '已寄出密碼重設信件', 'woocommerce' ) );
?>

<p><?php echo apply_filters( 'woocommerce_lost_password_message', __( '密碼重設信已寄到您指定的信箱中，請稍待片刻，為避免重複信件而造成您的困擾，如要重新寄送，請等10分鐘後重試。', 'woocommerce' ) ); ?></p>
