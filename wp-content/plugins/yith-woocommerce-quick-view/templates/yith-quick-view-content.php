<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

while ( have_posts() ) : the_post(); ?>

	<div class="product">

		<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class('product'); ?>>

			<?php do_action( 'yith_wcqv_product_image' ); ?>

			<div class="summary entry-summary">
				<div class="summary-content">
					<?php do_action( 'yith_wcqv_product_summary' ); ?>
				</div>
			</div>

		</div>

	</div>

<?php endwhile; // end of the loop.?>

<script>
	// script of time limit
	jQuery(document).ready(function($) {
		var now = Date.now();
		var startDate;
		var endDate;
		// in product page

		if ($('#meta_start_date').val() !== ''
			&& $('#meta_end_date').val() !== ''
			&& $('#meta_start_date').length > 0
			&& $('#meta_end_date').length > 0) {

			startDate = new Date($('#meta_start_date').val().replace(' ', 'T'));
			endDate = new Date($('#meta_end_date').val().replace(' ', 'T'));

			if (now > startDate && now < endDate) {
				$('div#clock').countdown($('#meta_end_date').val())
					.on('update.countdown', function (event) {
						$(this).html(event.strftime('截止時間： %I:%M:%S'));
						$('.clock-large').html(event.strftime('%I:%M:%S'));
					})
					.on('finish.countdown', function (event) {
						$(this).hide();
						$('.add-to-box').hide();
						$('#outofdate').show();
					});
			} else {
				$('.add-to-box').hide();
				$('#outofdate').show();
			}
		}
	});
</script>
