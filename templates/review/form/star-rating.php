<?php
/**
 * This template renders each star rating field in the review form
 * 
 * Please keep all the existing classes and other attributes as they are
 * For custom styling, you can add your own class beside of existing ones
 *
 * Also markup can be extended within the same structure
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="rating-field star-form">
	<div class="rating-title">
		<h5 class="rating-label"><?php echo esc_html( $rating_name ); ?></h5>
	</div>
	<div class="ts-star-rating">
		<div class="unlit-stars">
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
		</div>
		<div class="lit-stars" data-width="<?php echo esc_attr( $rating_value ); ?>" style="<?php echo esc_attr( $rating_style ); ?>">
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
			<i class="sbp-icon sbp-star star"></i>
		</div>
		<?php
		/**
		 * This code is required
		 * Removing it, the form will stop working
		 */
		echo $rating_input;
		?>
	</div>
</div>