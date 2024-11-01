<?php
/**
 * Template to show post rating(mini), views & likes
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="sbp-entry-meta">
	<?php
	/**
	 * ts_sbp_entry_meta hook
	 * 
	 * @hooked ts_sbp_mini_rating - 5
	 * @hooked ts_sbp_like_button - 10
	 * @hooked ts_sbp_views_button - 15
	 */
	do_action( 'ts_sbp_entry_meta' );
	?>
</div>