<?php
get_header();
$tmFlavours_breadcrumb_status = get_post_meta($post->ID, 'tmFlavours_show_breadcrumb', true);
$design = get_post_meta($post->ID, 'tmFlavours_page_layout', true);

$leftbar = $rightbar = $main = '';

switch ((int)$design) {
    case 1:
        $rightbar = 'hidesidebar';
        $main = 'col2-left-layout';
        $col = 9;
        break;
    case 2:
        $leftbar = 'hidesidebar';
        $main = 'col2-right-layout';
        $col = 9;
        break;

    case 4:
        $main = 'col3-layout';
        $col = 6;
        break;

    default:
        $leftbar = $rightbar = 'hidesidebar';
        $main = 'col1-layout';
        $col = 12;
        break;

}

?>

  <div class="page-heading">
    <div class="breadcrumbs">
    <?php if ($tmFlavours_breadcrumb_status == 1 || !empty($TmFlavours->tmFlavours_breadcrumbs())) { ?>
        <div class="container">
        <div class="row">
        <div class="col-xs-12">
         <?php $TmFlavours->tmFlavours_breadcrumbs(); ?>
      </div>
          <!--col-xs-12--> 
      </div>
        <!--row--> 
      </div>
      <!--container--> 
        <?php } ?>
     </div>
    <div class="page-title">
      <h1 class="entry-title">
        <?php $TmFlavours->tmFlavours_page_title(); ?>
      </h1>
        </div>
</div>

<div class="main-container <?php echo esc_html($main) ?> wow bounceInUp">
  <div class="container">
    <div class="row">
    <?php if (empty($leftbar) && $leftbar != 'hidesidebar') { ?>
    <aside id="column-left" class="col-left sidebar col-sm-3 col-xs-12<?php echo esc_html($leftbar) ?>">
      <?php dynamic_sidebar('sidebar-content-left'); ?>
    </aside>
    <?php } ?>
    <div class="col-main col-sm-<?php echo esc_html($col); ?>" id="content">
      <?php while (have_posts()) : the_post(); ?>
    
      <?php if (is_search()) : // Only display Excerpts for Search ?>
      <div class="entry-summary">
        <?php the_excerpt(); ?>
      </div>
      <!-- .entry-summary -->
      <?php else : ?>
      <div class="page-content">
        <?php the_content(); ?>
        <?php wp_link_pages(array('before' => '<div class="page-links">' . esc_html__('Pages:', 'flavours'), 'after' => '</div>')); ?>
      </div>
      <!-- .entry-content -->
      <?php endif; ?>
      <?php endwhile; // end of the loop. ?>
      <?php
                if (comments_open())
                    comments_template(); ?>
    </div>
    <?php if (empty($rightbar) && $rightbar != 'hidesidebar') { ?>
    <aside id="column-right" class="col-right sidebar col-sm-3 col-xs-12<?php echo esc_html($rightbar) ?>">
      <?php dynamic_sidebar('sidebar-content-right'); ?>
    </aside>
    <?php } ?>
  </div>
  </div>
</div>
<?php get_footer(); ?>
