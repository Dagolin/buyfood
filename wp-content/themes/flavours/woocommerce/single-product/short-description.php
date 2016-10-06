<?php
/**
 * Single product short description
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;

if (!$post->post_excerpt) {
    return;
}

?>
<div itemprop="description" class="short-description">
    <h2>Quick Overview</h2>
    <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?>
</div>
