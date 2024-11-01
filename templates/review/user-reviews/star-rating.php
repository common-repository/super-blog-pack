<?php
/**
 * This template renders each star rating of a review
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<li class="review-group-item">
	<h5 class="rating-subject"><?php echo esc_html( $rating_subject ); ?></h5>
	<div class="ts-star-rating">
		<div class="unlit-stars">
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
		</div>
		<div class="lit-stars" style="<?php echo esc_attr( $rating_style ); ?>">
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
		</div>
	</div>
</li>