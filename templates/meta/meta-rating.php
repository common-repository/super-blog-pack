<?php
/**
 * Show the star rating for current post
 *
 * Get number of rating with ts_sbp_get_rating_count() function
 * Get percentage of rating with ts_sbp_get_rating_percent() function (To use as CSS width)
 * Get average rating point with ts_sbp_get_rating_points() function
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

/**
 * It's better to not show an empty rating
 */
$rating_count = ts_sbp_get_rating_count();
if( empty( $rating_count ) ) {
	return;
}

?>
<div class="ts-star-rating" title="<?php echo esc_attr( ts_sbp_get_rating_points() ); ?>">
	<div class="unlit-stars">
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
	</div>
	<div class="lit-stars" style="width: <?php echo esc_attr( ts_sbp_get_rating_percent() ); ?>%;">
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
		<i class="sbp-icon sbp-star star"></i>
	</div>
</div>