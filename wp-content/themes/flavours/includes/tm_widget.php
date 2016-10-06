<?php
class tmFlavours_Widget_Posts extends  tmFlavours_Widget {
    public function __construct() {
        $this->widget_cssclass    = 'widget-posts';
        $this->widget_description = esc_html__( "Posts widget", 'flavours' );
        $this->widget_id          = 'tmFlavours-posts';
        $this->widget_name        = esc_html__( 'Flavours: Posts', 'flavours' );
        $this->settings           = array(
            'title' => array(
                'type' => 'text',
                'std' => '',
                'label' => esc_html__('Title','flavours')
            ),
            'source'  => array(
                'type'    => 'select',
                'std'     => '',
                'label'   => esc_html__( 'Source', 'flavours' ),
                'options' => array(
                    'random' => esc_html__('Random','flavours'),
                    'popular' => esc_html__('Popular','flavours'),
                    'recent'  => esc_html__( 'Recent', 'flavours' ),
                    'oldest' => esc_html__('Oldest','flavours')
                )
            ),
            'number' => array(
                'type'  => 'number',
                'std'   => '5',
                'label' => esc_html__( 'Number of posts to show', 'flavours' ),
            )
        );
        parent::__construct();
    }

    function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) )
            return;
    
        extract( $args, EXTR_SKIP );
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
        $source        = empty( $instance['source'] ) ? '' : $instance['source'];
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
            $number = 5;
        $query_args = array();
     
        switch ($source) {
              case 'recent':
                $query_args = array(
                    'posts_per_page' => $number,
                    'no_found_rows' => true,
                    'post_status' => 'publish',
                    'post__not_in' => get_option('sticky_posts'),
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                    'post_type' => 'post',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                            'terms' => array('post-format-quote', 'post-format-link', 'post-format-audio','post-format-video','post-format-gallery','post-format-image'),
                            'operator' => 'NOT IN'
                        )
                    )
                );
                break;
                
              case 'oldest':
                $query_args = array(
                    'posts_per_page' => $number,
                    'no_found_rows' => true,
                    'post_status' => 'publish',
                    'post__not_in' => get_option('sticky_posts'),
                    'orderby' => 'post_date',
                    'order' => 'ASC',
                    'post_type' => 'post',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                          'terms' => array('post-format-quote', 'post-format-link', 'post-format-audio','post-format-video','post-format-gallery','post-format-image'),
                            'operator' => 'NOT IN'
                        )
                    )
                );
                break;   
            case 'random' :
                $query_args = array(
                    'posts_per_page' => $number,
                    'no_found_rows' => true,
                    'post_status' => 'publish',
                    'post__not_in' => get_option('sticky_posts'),
                    'orderby' => 'rand',
                    'order' => 'DESC',
                    'post_type' => 'post',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                            'terms' => array('post-format-quote', 'post-format-link', 'post-format-audio','post-format-video','post-format-gallery','post-format-image'),
                            'operator' => 'NOT IN'
                        )
                    )
                );
                break;
            case 'popular':
                $query_args = array(
                    'posts_per_page' => $number,
                    'no_found_rows' => true,
                    'post_status' => 'publish',
                    'post__not_in' => get_option('sticky_posts'),
                    'orderby' => 'comment_count',
                    'order' => 'DESC',
                    'post_type' => 'post',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                           'terms' => array('post-format-quote', 'post-format-link', 'post-format-audio','post-format-video','post-format-gallery','post-format-image'),
                            'operator' => 'NOT IN'
                        )
                    )
                );
                break;

          
           
        }

        $class = array('widget-posts-wrap');

        ob_start();
        $r = new WP_Query( $query_args);
  
        if ($r->have_posts()) : ?>
            <?php echo wp_kses_post($args['before_widget']); ?>
            <?php if ( $title ) {
                echo wp_kses_post($args['before_title']) . esc_html($title) . wp_kses_post($args['after_title']);
            } ?>
            <div class="<?php echo join(' ',$class); ?>">
                <?php while ( $r->have_posts() ) : $r->the_post(); ?>
                    <div class="widget_posts_item clearfix">
                        <?php
                          
                            $thumbnail = tmFlavours_post_thumbnail('thumbnail');
                            if (!empty($thumbnail)) : ?>
                                <div class="widget-posts-thumbnail">
                                    <?php echo wp_kses_post($thumbnail); ?>
                                </div>
                            <?php endif; ?>
                        <div class="widget-posts-content-wrap">
                            <a class="widget-posts-title" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                            <div class="widget-posts-date">
                                <?php echo esc_html(get_the_date('M j Y')); ?> | <?php esc_attr_e('By','flavours') ?> <?php printf('<a href="%1$s">%2$s</a>',esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),esc_html( get_the_author() )); ?>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>
            <?php echo wp_kses_post($args['after_widget']); ?>
        <?php endif;
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();
        $content =  ob_get_clean();
        echo wp_kses_post($content);
        $this->cache_widget( $args, $content );
    }
}

if (!function_exists('tmFlavours_register_widget_posts')) {
    function tmFlavours_register_widget_posts() {
        register_widget('tmFlavours_Widget_Posts');
    }
    add_action('widgets_init', 'tmFlavours_register_widget_posts', 1);
}

?>