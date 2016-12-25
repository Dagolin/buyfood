<?php
/**
 * Single Product title
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<div class="product-name"><h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1></div>
<div class="countdown-style">
    <input type="hidden" name="meta_start_date" id="meta_start_date" value="<?php echo get_product_meta_start_date(); ?>"/>
    <input type="hidden" name="meta_end_date" id="meta_end_date" value="<?php echo get_product_meta_end_date(); ?>"/>
    <h2><div id="clock"></div></h2>
    <h2><div id="outofdate" style="display: none;color:darkred"><?php echo get_option('woocommerce_countdown_word', ''); ?></div></h2>
</div>
