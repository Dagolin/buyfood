<?php
/* Template name: Home */
get_header();
$TmFlavours = new TmFlavours();
global $flavours_Options;
?>
<?php tmFlavours_home_page_banner(); ?>
<?php tmFlavours_home_offer_banners(); ?>
<?php tmFlavours_category_product($flavours_Options['home-product-categories-limit']!='' ? esc_html($flavours_Options['home-product-categories-limit']) : 10);?>
<?php
for ($i = 1; $i <= 4; $i++) {
    if(isset($flavours_Options['enable_home_bestseller_products_' . $i]) && $flavours_Options['enable_home_bestseller_products_' . $i]) {
        tmFlavours_bestseller_products($i);
    }
}

for ($i = 1; $i <= 4; $i++) {
    if(isset($flavours_Options['enable_home_feature_products_' . $i]) && $flavours_Options['enable_home_feature_products_' . $i]) {
        tmFlavours_bestseller_products_customize('home_feature_products_' . $i, 'title_home_feature_products_' . $i);
    }
}

?>
<?php tmFlavours_home_blog_posts();?>
<div class="brand-logo wow bounceInUp animated animated" style="visibility: visible;">
  <div class="container">
    <div class="row">
	<?php tmFlavours_footer_brand_logo();?>
	<?php tmFlavours_home_testimonial();?>
	</div>
  </div>
</div>

<?php tmFlavours_home_sub_banners ();?>
<?php get_footer(); ?>
