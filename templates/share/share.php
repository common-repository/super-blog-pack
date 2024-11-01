<?php
/**
 * Before share links
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="ts-sbp-share-links">
	<h4 class="ts-sbp-title"><?php echo esc_html( apply_filters( 'ts_sbp_post_share_title', __( 'Share:', 'ts_sbp' ) ) ); ?></h4>
	<ul>
		<?php
		/**
		 * Use this function to generate share links
		 * 
		 * First parameter for before each link and second one is for after
		 **/
		ts_sbp_post_share_links( '<li>', '</li>' );
		?>
	</ul>
</div>