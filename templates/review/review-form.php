<?php
/**
 * Review form
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

do_action( 'ts_sbp_before_review_form' );

?>
<div class="sbp-user-review-form">
	<div class="review-form-opener">
		<?php
		if( ts_sbp_user_has_reviewed() ) {
			printf( '<h5>%s</h5>', esc_html__( 'Edit your review', 'ts_sbp' ) );
		} else {
			printf( '<h5>%s</h5>', esc_html__( 'Leave a review', 'ts_sbp' ) );
		}
		?>
	</div>
	<form class="sbp-review-submit form-hidden">
		<div class="sbp-notice sbp-error invalid-rating">
			<p><?php esc_html_e( 'Invalid: Please provide all the star ratings!', 'ts_sbp' ); ?></p>
		</div>
		<div class="user-ratings">
			<?php ts_sbp_review_form_ratings(); ?>
		</div>
		<div class="user-review">
			<?php ts_sbp_review_form_review(); ?>
		</div>
		<div class="user-review-submit">
			<?php ts_sbp_review_form_submit(); ?>
		</div>
	</form>
</div>