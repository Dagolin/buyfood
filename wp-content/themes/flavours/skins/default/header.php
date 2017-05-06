<!DOCTYPE html>
<html <?php language_attributes(); ?> id="parallax_scrolling">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
 <?php wp_head(); ?>
</head>
<?php
global $flavours_Options;
 $TmFlavours = new TmFlavours();
$limitDates = get_limit_product_list(); ?>
<body <?php body_class('cms-index-index  cms-home-page'); ?> >
  <div id="page" class="page catalog-category-view">

      <!-- Header -->
      <header id="header" >
	 <?php tmFlavours_daily_offer();?>
         <div class="header-container container">
         <div class="row">
            <!-- Header Logo -->
            <div class="logo">
             <?php tmFlavours_logo_image();?>
            </div>
            <!-- End Header Logo -->
          

            <div class="top-menu">

                <a class="mobile-toggle"><i class="fa fa-reorder"></i>
                    <span style="display: inline-block;font-size: 14px; font-weight: bold; margin-left: 5px; line-height: 20px;vertical-align: text-top;"> 展開選單</span>
                </a>
                <a href="<?php echo $flavours_Options['marketing_group_purchase_link']; ?>">
                    <div class="clockdate-full-mobile mobile-only clock-large-bg">
                        <div class="wrapper-clckdate-mobile">
                            <div class="clock-large-mobile"></div>
                        </div>
                    </div>
                </a>
                <div class="tm-main-menu">
                    <div id="main-menu">
                        <?php echo tmFlavours_main_menu(); ?>

                    </div>
                </div>
               
            </div>
             <?php

             if (isset($flavours_Options['enable_header_mobile_links']) && $flavours_Options['enable_header_mobile_links'] == 1) :
                 ?>
             <div class="dock-menu">
                 <?php
                 for ($i = 1; $i <= 4; $i++) {
                     if (isset($flavours_Options['header_mobile_link_text_' . $i]) && $flavours_Options['header_mobile_link_url_' . $i]) {
                 ?>
                 <div id="dock-<?php echo $i;?>" class="dock-<?php echo $i;?>"><a href="<?php echo $flavours_Options['header_mobile_link_url_' . $i];?>"><?php echo $flavours_Options['header_mobile_link_text_' . $i];?></a></div>
                 <?php }} ?>
             </div>
             <?php endif; ?>
             
               <div class="header-right-col">
                <?php  if ( has_nav_menu( 'toplinks' ) ) :?>
                  <div class="click-nav">
                     <div class="no-js">
                        <a title="<?php esc_attr_e('clicker:', 'flavours');?>" class="clicker"></a>
                        <div class="top-links">
                            <?php tmFlavours_currency_language();?>
                            <?php echo tmFlavours_top_navigation(); ?>                           
                        </div>
                     </div>
                  </div>
               <?php endif ;?>
                  <div class="top-cart-contain">
                     <?php
                        if (class_exists('WooCommerce')) :
                             $TmFlavours->tmFlavours_mini_cart();
                             endif;
                             ?>
                     <!--top-cart-content-->
                  </div>

                  <!--mini-cart-->

                    <!-- top search code -->
              <div class="top-search">
              
                     <?php echo tmFlavours_search_form(); ?>  
                   
                     
          </div>
                 
                  <!--links-->
               </div>
            </div>
         </div>
      </header>
      <!-- end header -->
      <?php if (class_exists('WooCommerce') && is_woocommerce()) : ?>
     <div class="page-heading">
    <div class="breadcrumbs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
                  <?php woocommerce_breadcrumb(); ?>
              </div>
          <!--col-xs-12--> 
        </div>
        <!--row--> 
      </div>
      <!--container--> 
    </div>
         <?php if(is_product_category()){?>
         <div class="page-title">
             <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                            <h2>
                                <?php esc_html(woocommerce_page_title()); ?>
                            </h2>
                        <?php endif; ?>
     
    </div>
    <?php } ?>
      </div>
      <?php endif; ?>
