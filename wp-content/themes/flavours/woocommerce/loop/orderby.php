<?php
/**
 * Show options for ordering
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.2.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>

<div class="woocommerce-ordering sorter mobile-only">
    <select id="quick-cate-link"  class="orderby" style="width: 300px">
    <?php

    $taxonomy     = 'product_cat';
    $orderby      = 'name';
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no
    $title        = '';
    $empty        = 0;

    $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'show_count'   => $show_count,
        'pad_counts'   => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li'     => $title,
        'hide_empty'   => $empty
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
            echo '<option value="' . get_term_link($cat->slug, 'product_cat') . '">' . $cat->name . '</option>';

            $args2 = array(
                'taxonomy'     => $taxonomy,
                'child_of'     => 0,
                'parent'       => $category_id,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
            );
            $sub_cats = get_categories( $args2 );
            if($sub_cats) {
                foreach($sub_cats as $sub_category) {
                    //echo  $sub_category->name ;
                    echo '<option value="' . get_term_link($sub_category->slug, 'product_cat') . '">' . $sub_category->name . '</option>';
                }
            }
        }
    }
    ?>
    </select>
</div>
<script>
    jQuery(function($){
        // bind change event to select
        $('#quick-cate-link').on('change', function () {
            var url = $(this).val(); // get selected value
            if (url) { // require a URL
                window.location = url; // redirect
            }
            return false;
        });
    });
</script>

<form class="woocommerce-ordering" method="get">
    <div id="sort-by">
        <select name="orderby" class="orderby">
            <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                <option
                    value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    // Keep query string vars intact
    foreach ($_GET as $key => $val) {
        if ('orderby' === $key || 'submit' === $key) {
            continue;
        }
        if (is_array($val)) {
            foreach ($val as $innerVal) {
                echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($innerVal) . '" />';
            }
        } else {
            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
        }
    }
    ?>
</form>
