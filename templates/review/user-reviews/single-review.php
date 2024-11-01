<?php
/**
 * This template renders review from a specific user
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="sbp-review-dialog post-rating-review">
	<div class="dialog-header">
		<h5 class="dialog-title">
			<a href="#"><?php echo esc_html( $user_name ); ?></a>
		</h5>
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
	</div>
	<div class="dialog-body">
		<ul class="star-ratings-group">
			<?php ts_sbp_review_star_ratings(); ?>
		</ul>
		<?php if( isset( $user['_review'] ) ) : ?>
		<div class="review">
			<?php
			if( $show_avatar ) {
				$default = apply_filters( 'ts_sbp_avatar_default', 'mystery' );
				if( $author != false ) {
					echo get_avatar( $user_id, 200 );
				} elseif( isset( $user['_email'] ) && !empty( $user['_email'] ) ) {
					echo get_avatar( $user['_email'], 200 );
				} else {
					echo get_avatar( $user_id, 200, $default, '', array( 'force_default' => true ) );
				}
			}
			?>
			<p><?php echo esc_html( $user['_review'] ); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>