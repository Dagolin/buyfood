<?php  get_header();

if(isset($flavours_Options['blog-page-layout']))
{
$design = $flavours_Options['blog-page-layout'];
}
else
{
$design=''; 
}

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
    <div class="page-title">
      <h1 class="entry-title">
        <?php $TmFlavours->tmFlavours_page_title(); ?>
      </h1>
        </div>
</div>

<div class="blog-wrapper <?php echo esc_html($main) ?>">
  <div class="container">
    <div class="row">
    <?php if (empty($leftbar) && $leftbar != 'hidesidebar') { ?>
    <aside id="column-left" class="col-left sidebar col-lg-3 col-sm-3 col-xs-12  <?php echo esc_html($leftbar) ?>">
     <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>

            <div class="<?php echo esc_html($col); ?>">
        <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
             <article  id="post-<?php echo esc_html($post->ID); ?>" <?php post_class('blog-post container-paper'); ?>>
         
                        
                        <div class="post-container">
                        <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-img <?php if (has_post_thumbnail()){?> has-img <?php } ?>">
                                        
                                        <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                             <figure>
                                            <?php
                                                the_post_thumbnail('tmFlavours-article-home-large', array('class' => 'img-responsive', 'alt' => esc_html(get_the_title())));
                                            ?>
                                             </figure>
                                        </a>
                                       
                                       
                                    </div>
                                    <?php endif;
                                            ?>
                                 <div class="post-detail-container">
                                <div class="title">
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>" >
                                    <?php the_title(); ?>
                                </a></h2>
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
                                            </li><li class="divider">|</li>
                                        <?php } ?>
                                       <?php if(!isset($flavours_Options
) ||  $flavours_Options
['blog_display_category']==1) {  if(has_category()){
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
                            <div class="row">
                                <div class="col-l col-md-12 col-lg-12">
                                  <div class="blogdesc">
                                     <?php echo get_the_excerpt(); ?> 
                                  
                                  </div>
                                   <?php
                                            wp_link_pages(array('before' => '<div class="post_paginate">' . esc_html__('Pages:&nbsp;', 'flavours'), 'after' => '</div>', 'next_or_number' => 'number', 'nextpagelink' => '<span class="next">' . esc_html__('Next &raquo;', 'flavours').'</span>', 'previouspagelink' => '', 'link_before' => '<span>', 'link_after' => '</span>'));
                                            ?>
                                    
                                        <a href="<?php the_permalink(); ?>" class="btn-mega">
                                            <?php esc_attr_e('Read More', 'flavours') ?>
                                        </a>
                              
                                </div>
                              
                            </div>
                            <footer class="entry-meta clearfix">                 
                      </footer>
            
                        </div>
                    </div>
                    </article>
        <?php endwhile; ?>
        <div class="pagination">
          <?php

                    $big = 999999999; // need an unlikely integer
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $wp_query->max_num_pages,
                        'type' => 'list'
                    ));
                    ?>
        </div>
        <?php else: ?>
        <div><?php esc_attr_e("Sorry, What you are looking isn't here.",'flavours') ;?>. </div>
        <?php endif; ?>
      </div>
    <?php if (empty($rightbar) && $rightbar != 'hidesidebar') { ?>
    <aside id="column-right" class="col-right sidebar col-lg-3 col-sm-3 col-xs-12 <?php echo esc_html($rightbar) ?>">
    <?php get_sidebar('content'); ?>
    </aside>
    <?php } ?>
    </div>
  </div>
</div>

<?php get_footer(); ?>
