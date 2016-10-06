<?php get_header();
$TmFlavours->tmFlavours_setPostViews(get_the_ID());
 $design = get_post_meta($post->ID, 'tmFlavours_post_layout', true);
 $leftbar = $rightbar = $main = '';

$leftbar = $rightbar = $main = '';

switch ((int)$design) {
    case 1:
        $rightbar ='hidesidebar';
        $main = 'col2-left-layout';
        $col = 'col-sm-9 col-md-9 col-lg-9 col-xs-12';
        break;
    case 3:
        $leftbar = $rightbar = 'hidesidebar';
        $main = 'col1-layout';
        $col = 'col-sm-12 col-md-12 col-lg-12 col-xs-12';
        break;

    default:
        $leftbar = 'hidesidebar';
        $main = 'col2-right-layout';
        $col = 'col-sm-9 col-md-9 col-lg-9 col-xs-12';
        break;

}

?>
  <div class="page-heading">
    <div class="breadcrumbs">
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
    </div>
</div>

    <div class="blog-wrapper <?php echo esc_html($main) ?>">
        <section class="container">
            <div class="row">

    <?php if (empty($leftbar) && $leftbar != 'hidesidebar') { ?>
    <aside id="column-left" class="col-left sidebar col-lg-3 col-sm-3 col-xs-12  <?php echo esc_html($leftbar) ?>">
     <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>

            <section class="<?php echo esc_html($col); ?>">
                    <?php  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                    get_query_var('page') ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="blog-post container-paper singlepost">
                           
                            <div class="post-container">
                                    <?php if (has_post_thumbnail()) : ?>
                              <div class="post-img <?php if (has_post_thumbnail()){?> has-img <?php } ?>">
                            
                                    <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail'); ?>
                                    <a href="<?php the_permalink(); ?>"> 
                                     <figure>     
                     <img src="<?php echo esc_url($image[0]); ?>"
                                alt="<?php the_title(); ?>">  </figure>  
                                 </a>
                             
                            
                              
                                    </div>
                                <?php endif; ?>
                                <div class="post-detail-container">
                                 <div class="title">
                                <h2>
                                    <?php the_title(); ?>
                                </h2>
                                  <?php if (!isset($flavours_Options) || $flavours_Options['blog_full_date'] == 1) { ?>
                                                    <div class="blogdate"><time class="entry-date updated">
                                                        <?php echo esc_html(get_the_date()); ?></time>
                                                    </div>
                                       <?php } ?>
                            </div>
                            <ul class="list-info">
                                        <?php if (!isset($flavours_Options
) || $flavours_Options
['blog_show_post_by'] == 1 ) { ?>
                                            <li><i class="fa fa-user"></i><?php esc_attr_e('By', 'flavours'); ?>  
                                                <?php the_author(); ?>
                                            </li><li class="divider">/</li>
                                        <?php } ?>
                                       <?php if(!isset($flavours_Options
) ||  $flavours_Options
['blog_display_category']==1) { if(has_category()){
                                            $cats_list = get_the_category_list(', ');
                                            ?>
                                            <li class="under-category">
                                            <i class="fa fa-tag"></i>
                                           <?php echo htmlspecialchars_decode($cats_list);?>
                                            </li>
                                            <li class="divider">|</li>
                                             <?php } } ?>
                                      
                                        <?php if (!isset($flavours_Options
) || $flavours_Options
['blog_display_view_counts'] == 1) { ?>
                                            <li><i class="fa fa-eye"></i><?php echo $TmFlavours->tmFlavours_getPostViews(get_the_ID()); ?> 
                                            </li><li class="divider">|</li>
                                        <?php } ?>
                                        <?php if (!isset($flavours_Options
) || $flavours_Options
['blog_display_comments_count'] == 1) { ?>
                                            <li><i class="fa fa-comment"></i><a href="<?php comments_link(); ?>"><?php comments_number('0 Comment', '1 Comment', '% Comments'); ?>
                                            </a></li>
                                             
                                        <?php } ?>
                                    </ul>
                                <div class="detaildesc">
                                <?php the_content(); ?>
                            </div>

                                <?php
                                $tag = get_the_tags();
                               ?>
                                <?php
                                            wp_link_pages(array('before' => '<div class="post_paginate">' . esc_html__('Pages:&nbsp;', 'flavours'), 'after' => '</div>', 'next_or_number' => 'number', 'nextpagelink' => '<span class="next">' . esc_html__('Next &raquo;', 'flavours').'</span>', 'previouspagelink' => '', 'link_before' => '<span>', 'link_after' => '</span>'));
                                            ?>
                                     <?php if ($tag) {
                                    ?>
                                 <footer class="entry-meta clearfix">
                                    <div class="line-divider"></div>

                                    <?php  the_tags('<ul class="post-tags"><li>', '</li><li>', '</li></ul>');
                                     ?>

                                </footer>
                                <?php } ?>
                              </div>  
                            </div>
                        </div>
                        <?php
                        // Author bio.
                    if (!isset($flavours_Options) || $flavours_Options['blog_show_authors_bio'] == 1) {
                            get_template_part('author-bio');
                        }
                        ?>
                    <?php endwhile; ?>
                    <?php
                
                    // Don't print empty markup if there's nowhere to navigate.
                    $previous = (is_attachment()) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
                    $next = get_adjacent_post(false, '', false);

                    ?>
                    <div class="post-navigation">
                        <?php if ($previous) {
                            ?>
                            <div class="pull-left btn-mega1">
                                <?php previous_post_link('%link', _x('<span class="fa fa-arrow-left"></span> &nbsp; Prev POST', 'Previous post link', 'flavours')); ?>
                            </div>
                        <?php } ?>
                        <?php if ($next) {
                            ?>
                            <div class="pull-right btn-mega1">
                                <?php next_post_link('%link', _x('Next Post &nbsp; <span class="fa fa-arrow-right"></span>', 'Next post link', 'flavours')); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <?php comments_template('', true); ?>
                </section>
    <?php if (empty($rightbar) && $rightbar != 'hidesidebar') { ?>
     <aside id="column-right" class="col-right sidebar col-lg-3 col-sm-3 col-xs-12 <?php echo esc_html($rightbar) ?>">
    <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>
        </section>
    </div>
<?php get_footer(); ?>