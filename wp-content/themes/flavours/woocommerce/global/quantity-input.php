<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$max = is_numeric($max_value) ? $max_value : 10;
$max = $max < 10 ? $max : 10;
$max = $max < $input_value ? $input_value : $max;

$min = is_numeric($min_value) ? $min_value : 1;
$min = $min > 0 ? $min : 1;
?>
<div class="quantity">
	<select title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-select qty" name="<?php echo esc_attr( $input_name ); ?>">
		<?php
		for ($i = $min; $i <= $max; $i++) { ?>
			<option value="<?php echo $i; ?>" <?php echo ($input_value == $i) ? 'selected' : '' ?>><?php echo $i; ?></option>
		<?php } ?>
	</select>
</div>
