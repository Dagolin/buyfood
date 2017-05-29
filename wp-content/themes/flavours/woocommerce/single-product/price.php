<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price-block">
    <div class="price-box price"> <?php echo $product->get_price_html(); ?></div>
        
        <?php if($product->is_in_stock()){
            $stock = number_format($product->stock, 0);

            ?>
            <p class="availability in-stock">
                <span>
                    <?php esc_attr_e('熱銷中','flavours');?>
                    <?php if ($stock > 0) {
                        echo '，目前庫存量 ' . $stock . ' 份';
                    }
                    ?>
                </span>
            </p>
        <?php } else { ?>
            <p class="availability out-of-stock pull-right">
                <span><?php esc_attr_e('補貨中','flavours');?></span>
            </p>
        <?php } ?>
        <meta itemprop="price" content="<?php echo $product->get_price(); ?>"/>
        <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>"/>
        <link itemprop="availability"
              href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>"/>
    </div>

