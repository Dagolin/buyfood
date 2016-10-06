<?php
// Do not delete these lines
$TmFlavours = new TmFlavours();
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die (esc_html__('Please do not load this page directly. Thanks!'. 'flavours'));

if ( post_password_required() ) : ?>
    <p class="no-comments"><?php echo esc_html__('This post is password protected. Enter the password to view comments.', 'flavours'); ?></p>
    <?php
    return;
endif;

if ( have_comments() ) : ?>
    <div class="post-block post-comments clearfix">
        <h2 class="title"><?php
                printf( _nx( 'Comment (1)', 'Comments (%1$s)', get_comments_number(), 'comments title', 'flavours'),
                    number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
            ?>
        </h2>

        <ul class="comments">
            <?php
                // Comments list
                wp_list_comments( array(
                    'short_ping'  => true,
                    'avatar_size' => 80,
                    'callback' => array($TmFlavours, "tmFlavours_comment")
                  
                ) );
            ?>
        </ul>

        <?php
        // Are there comments to navigate through?
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <div class="clearfix">
                <div class="pagination" role="navigation">
                    <?php paginate_comments_links() ?>
                </div>
            </div>
        <?php endif; // Check for comment navigation ?>

        <?php if ( ! comments_open() && get_comments_number() ) : ?>
            <p class="no-comments"><?php esc_attr_e( 'Comments are closed.' , 'flavours' ); ?></p>
        <?php endif; ?>
    </div>
<?php endif; // have_comments() ?>

<?php comment_form(); ?>
