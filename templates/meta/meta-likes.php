<?php
/**
 * This is the template to print the like button
 *
 * Style or change markup as required
 * Make sure the main clickable button has .ts-post-like-button class and post ID in 'data-id' attribute
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

if( ts_sbp_has_liked_post() ) {
	?><a href="#" class="ts-post-like-button already-liked" data-id="<?php the_ID(); ?>" title="<?php esc_attr_e( 'You already like this post', 'ts_sbp' ); ?>"><i class="sbp-icon sbp-thumbs-up"></i>&nbsp;<span class="like-numbers"><?php echo esc_html( ts_sbp_count_likes() ); ?></span></a><?php
} else {
	?><a href="#" class="ts-post-like-button" data-id="<?php the_ID(); ?>"><i class="sbp-icon sbp-thumbs-up"></i>&nbsp;<span class="like-numbers"><?php echo esc_html( ts_sbp_count_likes() ); ?></span></a><?php
}