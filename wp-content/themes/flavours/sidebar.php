<?php
/**
 * @package flavours
 * @subpackage flavours
 */

if(class_exists( 'WooCommerce' ) && is_woocommerce()){
	dynamic_sidebar( 'sidebar-shop' );
} else {
	dynamic_sidebar( 'sidebar-blog' );
}
?> 

