<?php
/**
 * Each element of related posts
 *
 * As it's inside the loop, you can use any template tag or loop related functions
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<div class="ts-spb-related-entry">
	<a href="<?php the_permalink(); ?>">
		<?php
		if( has_post_thumbnail() ) {
			the_post_thumbnail( 'thumbnail' );
		} else {
			ts_sbp_fallback_thumbnail();
		}
		?>
		<?php the_title( '<span class="title">', '</span>' ); ?>
	</a>
</div>