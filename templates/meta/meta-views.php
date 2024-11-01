<?php
/**
 * Template that shows post views
 *
 * By default, it's linked to the post, it can be changed as required
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?><a href="<?php the_permalink(); ?>" class="ts-post-view-button"<?php if( is_single() ) { ?> rel="nofollow"<?php } ?>><i class="sbp-icon sbp-eye"></i>&nbsp;<span class="view-numbers"><?php echo esc_html( ts_sbp_count_hits() ); ?></span></a>