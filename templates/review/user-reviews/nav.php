<?php
/**
 * Post reviews navigation
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<nav class="post-reviews-navigation">
	<?php
	/**
	 * This function will print links to paginate through reviews on a post
	 *
	 * Parameters you should pass
	 *
	 * 1. The id of review element without hash
	 * 2. Text or markup for previous button
	 * 3. Text or markup for next button
	 */
	ts_sbp_review_nav( 'ts-sbp-review', esc_html__( 'Previous', 'ts_sbp' ), esc_html__( 'Next', 'ts_sbp' ) );
	?>
</nav>