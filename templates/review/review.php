<?php
/**
 * The main review area
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="clearfix"></div>
<div id="ts-sbp-review" class="sbp-review-container">
	<h4 class="ts-sbp-title"><?php echo esc_html( apply_filters( 'ts_sbp_post_reviews_title', __( 'Post Reviews:', 'ts_sbp' ) ) ); ?></h4>
	<?php
	/**
	 * Show the review area
	 *
	 * @hooked ts_sbp_review_template - 5
	 * @hooked ts_sbp_review_form - 10
	 */
	do_action( 'ts_sbp_review_area' );
	?>
</div>