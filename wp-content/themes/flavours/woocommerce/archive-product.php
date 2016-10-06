<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     2.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');

$plugin_url = plugins_url();
?>
<script type="text/javascript"><!--


    jQuery(function ($) {

        "use strict";


        jQuery.display = function (view) {

            view = jQuery.trim(view);

            if (view == 'list') {
                jQuery(".button-grid").removeClass("button-active");
                jQuery(".button-list").addClass("button-active");
                jQuery.getScript("<?php echo  esc_url(site_url()) ; ?>/wp-content/plugins/yith-woocommerce-quick-view/assets/js/frontend.js", function () {
                });
                jQuery('.products-grid').attr('class', 'products-list');


                jQuery('.products-list > ul > li').each(function (index, element) {

                    var htmls = '';
                    var element = jQuery(this);


                    element.attr('class', 'item');


                    htmls += '<div class="pimg">';

                    var element = jQuery(this);

                    var image = element.find('.pimg').html();

                    if (image != undefined) {
                        htmls += image;
                    }

                    htmls += '</div>';


            

                    htmls += '<div class="product-shop">';
                    if (element.find('.item-title').length > 0)
                        htmls += '<h2 class="product-name item-title"> ' + element.find('.item-title').html() + '</h2>';

                  

          

                     var ratings = element.find('.ratings').html();

                    htmls += ' <div class="rating"><div class="ratings">' + ratings + '</div></div>';

                    var descriptions = element.find('.desc').html();
                    htmls += '<div class="desc std">' + descriptions + '</div>';
                      var price = element.find('.price-box').html();

                    if (price != null) {
                        htmls += ' <div class="price-box">' + price + '</div>';
                    }
		    htmls += '<div class="action">' + element.find('.action').html() + '</div>'; 
                    htmls += '<div class="product-action actions">' + element.find('.product-action').html() + '</div>';
                    htmls += '</div>';
                 

                    element.html(htmls);
                });


                jQuery.cookie('display', 'list');

            } else {
            
                 jQuery(".button-list").removeClass("button-active");
                 jQuery(".button-grid").addClass("button-active");
                 jQuery.getScript("<?php echo esc_url(site_url()); ?>/wp-content/plugins/yith-woocommerce-quick-view/assets/js/frontend.js", function () {
                 });
                 jQuery('.products-list').attr('class', 'products-grid');

                 jQuery('.products-grid > ul > li').each(function (index, element) {
                    var html = '';

                     element = jQuery(this);

                    element.attr('class', 'item col-lg-4 col-md-4 col-sm-4 col-xs-6');

                    html += '<div class="item-inner"><div class="item-img"><div class="item-img-info"><div class="pimg">';

                    var element = jQuery(this);

                    var image = element.find('.pimg').html();

                    if (image != undefined) {

                        html += image;
                    }
                    html +='</div><div class="item-box-hover"><div class="box-inner product-action">';
                     var actions = element.find('.product-action').html();
                   
                     html +=actions;
                    html += '</div></div></div></div>';

                  

                    html += '<div class="item-info"><div class="info-inner">';
                    if (element.find('.item-title').length > 0)
                        html += '<div class="item-title"> ' + element.find('.item-title').html() + '</div>';                                


                    html += ' <div class="item-content">';
                     var ratings = element.find('.ratings').html();

                    html += ' <div class="rating"><div class="ratings">' + ratings + '</div></div>';

                       var price = element.find('.price-box').html();

                     if (price != null) {
                        html += '<div classs="item-price"><div class="price-box"> ' + price + '</div></div>';
                    }
                    var descriptions = element.find('.desc').html();
                    html += '<div class="desc std">' + descriptions + '</div>';
                   
                    html += '';  
                   
                   html += '<div class="action">';
                    var action = element.find('.action').html();
                   
                    html +=action;
                    html += '</div>';
                    html += '</div></div></div>';

                    element.html(html);
                 });

                 jQuery.cookie('display', 'grid');
            }
        }

        jQuery('a.list-trigger').click(function () {
            jQuery.display('list');

        });
        jQuery('a.grid-trigger').click(function () {
            jQuery.display('grid');
        });

        var view = 'grid';
        view = jQuery.cookie('display') !== undefined ? jQuery.cookie('display') : view;

        if (view) {
            jQuery.display(view);

        } else {
            jQuery.display('grid');
        }
        return false;


    });
    //--></script>
<?php
 do_action('woocommerce_before_main_content'); 

/**
 * woocommerce_before_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */

?>

<div class="main-container col2-left-layout bounceInUp animated">
  
    <div class="main container">
        <div class="row">
            <div class="col-main col-sm-9 col-sm-push-3 wow bounceInUp animated">
              <div class="pro-coloumn">

   <div class="category-description std">
            <div class="slider-items-products">
              <div id="category-desc-slider" class="product-flexslider hidden-buttons">
                <div class="slider-items slider-width-col1">                   
                                                    
                  <div class="item">
 
                  <?php   do_action('woocommerce_archive_description'); ?>
      
                   </div>
                  <!-- End Item --> 
                  
                </div>
              </div>
            </div>
          </div>
               
               
                
            
                <?php if (have_posts()) : ?>
                <div class="toolbar">
                    <?php
                    /**
                     * woocommerce_before_shop_loop hook
                     *
                     * @hooked woocommerce_result_count - 20
                     * @hooked woocommerce_catalog_ordering - 30
                     */
                    do_action('woocommerce_before_shop_loop');
                    ?>
                </div>
                <div class="category-products">
                    <?php woocommerce_product_loop_start(); ?>
                    <?php woocommerce_product_subcategories(); ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php wc_get_template_part('content', 'product'); ?>
                    <?php endwhile; // end of the loop. ?>
                    <?php woocommerce_product_loop_end(); ?>
                    <?php
                    /**
                     * woocommerce_after_shop_loop hook
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                   
                </div>
                 <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>
                        <?php wc_get_template('loop/no-products-found.php'); ?>
                    <?php
                    endif;
                    ?>
                </div> <!-- pro-coloumn -->
            </div>
            <aside class="col-left sidebar col-sm-3 col-xs-12 col-sm-pull-9 wow bounceInUp animated">
                <?php
                /**
                 * woocommerce_sidebar hook
                 *
                 * @hooked woocommerce_get_sidebar - 10
                 */
                do_action('woocommerce_sidebar');
                ?>
               
            </aside>
        </div>
    </div>
</div>
<?php
/**
 * woocommerce_after_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');
?>

<?php get_footer('shop'); ?>
