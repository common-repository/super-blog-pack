<?php
/**
 * Sublit button of the review form
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<button type="submit" data-post-id="<?php the_ID(); ?>" class="submit-review"><?php esc_html_e( 'Submit Review', 'ts_sbp' ); ?></button>