<?php 
$TmFlavours = new TmFlavours();?>
 <?php tmFlavours_header_service(); ?>
  
<footer class="footer bounceInUp animated">
        
  <div class="footer-inner">
    <div class="newsletter-row">
    <div class="container">
      <div class="row"> 
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col"> 
          <!-- Footer Payment Link -->
          <?php if (is_active_sidebar('footer-sidebar-5')) : ?>
            <?php dynamic_sidebar('footer-sidebar-5'); ?>
          <?php endif; ?> 
        </div>
        <!-- Footer Newsletter -->
        <?php if( function_exists( 'mc4wp_form' ) ) { ?> 
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 col1">
          <div class="newsletter-wrap">              
            <?php mc4wp_form(); ?>                
          </div>
          <!--newsletter-wrap--> 
        </div>
        <?php } ?>
      </div>
    </div>
    <!--footer-column-last--> 
    </div>
    
      <div class="container">
        <div class="row">
          <div class="col-sm-4 col-xs-12 col-lg-4">
            <!-- <div class="footer-column co-info footer-first"> -->
                <?php if (is_active_sidebar('footer-sidebar-1')) : ?>
                    <div class="co-info">
                    <?php dynamic_sidebar('footer-sidebar-1'); ?>
                
                    <div class="social">
                        <ul>
                            <?php $TmFlavours->tmFlavours_social_media_links(); ?>
                        </ul>
                    </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-8 col-xs-12 col-lg-8">
              <div class="footer-column">
                  <?php if (is_active_sidebar('footer-sidebar-2')) : ?>
                      <?php dynamic_sidebar('footer-sidebar-2'); ?>
                  <?php endif; ?>
              </div>
              <div class="footer-column">
                  <?php if (is_active_sidebar('footer-sidebar-3')) : ?>
                      <?php dynamic_sidebar('footer-sidebar-3'); ?>
                  <?php endif; ?>
              </div>
              <div class="footer-column">
                  <?php if (is_active_sidebar('footer-sidebar-4')) : ?>
                      <?php dynamic_sidebar('footer-sidebar-4'); ?>
                  <?php endif; ?>                
              </div>
            </div>        
          </div>
          <!--col-sm-12 col-xs-12 col-lg-8-->
          <!--col-xs-12 col-lg-4-->
        </div>
        <!--row-->
      </div>
      <!--container-->
    

     
      <div class="footer-bottom">
      <div class="container">
      <div class="row">
          <?php $TmFlavours->tmFlavours_footer_text(); ?>
      </div>
      <!--row-->
      </div>
      <!--container-->
      </div>
    <!--footer-bottom-->
  <!--/div-->
  </footer>

    </div>
   
    <script type="text/javascript">
    jQuery(document).ready(function($){ 
        
        new UISearch(document.getElementById('form-search'));
    });

    </script>

<?php // navigation panel
require_once(TMFLAVOURS_THEME_PATH .'/menu_panel.php');
 ?>
    <!-- JavaScript -->
    
    <?php wp_footer(); ?>
<?php

$limitDates = get_limit_product_list();
$isProductPage = is_product();

global $flavours_Options;

$isLimitOverlayEnable = isset($flavours_Options['enable_home_countdown_overlay']) ? $flavours_Options['enable_home_countdown_overlay'] : false;
$limitOverlayTemplate = isset($flavours_Options['countdown_overlay_template']) ? $flavours_Options['countdown_overlay_template'] : '%h : %i : %s';
?>
<div>
    <input type="hidden" name="product_start_date" id="product_start_date" value="<?php echo $limitDates['startDate']; ?>"/>
    <input type="hidden" name="product_end_date" id="product_end_date" value="<?php echo $limitDates['endDate']; ?>"/>
</div>
<div id="twzipcode" class="hide"></div>
<div id="twzipcode2" class="hide"></div>
<div id="twzipcode3" class="hide"></div>
<?php if ($isLimitOverlayEnable) : ?>
    <link href='http://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet' type='text/css'/>

    <a href="<?php echo $flavours_Options['marketing_group_purchase_link']; ?>">
        <div class="clockdate-full desktop-only">
            <div class="wrapper-clockdate">
                <div class="clock-large clock-large-bg"></div>
            </div>
        </div>
    </a>
    <a href="<?php echo $flavours_Options['marketing_group_purchase_link']; ?>">
        <div class="clockdate-full-mobile mobile-only clock-large-bg">
            <div class="wrapper-clockdate-mobile">
                <div class="clock-large-mobile"></div>
            </div>
        </div>
    </a>
<?php endif ?>


    </body></html>
