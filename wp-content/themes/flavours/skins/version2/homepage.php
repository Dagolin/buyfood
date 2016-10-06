<?php
/* Template name: Home */
get_header();
$TmFlavours = new TmFlavours();
?>
<?php tmFlavours_home_page_banner(); ?>
<?php tmFlavours_home_offer_banners(); ?>
<?php tmFlavours_category_product($flavours_Options['home-product-categories-limit']!='' ? esc_html($flavours_Options['home-product-categories-limit']) : 10);?>
<?php tmFlavours_bestseller_products(); ?>
<?php tmFlavours_home_blog_posts();?>
<div class="logo-brand wow bounceInUp animated animated" style="visibility: visible;">
  <div class="container">
    <div class="row">
	<?php tmFlavours_footer_brand_logo();?>	
	</div>
  </div>
</div>

<?php tmFlavours_home_sub_banners ();?>
<?php get_footer(); ?>
