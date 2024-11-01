<?php
/**
 * Starting of related posts
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

global $wp_query;

$number_of_posts = $wp_query->post_count;

$cols = 'col-1';

if( $number_of_posts <= 4 ) {
	$cols = 'col-' . $number_of_posts;
} elseif( $number_of_posts == 6 ) {
	$cols = 'col-3';
} else {
	$cols = 'col-4';
}

?>
<div class="clearfix"></div>
<h4 class="ts-sbp-title"><?php echo esc_html( apply_filters( 'ts_sbp_related_post_title', __( 'Related Posts:', 'ts_sbp' ) ) ); ?></h4>
<div class="ts-spb-related-posts <?php echo esc_attr( $cols ); ?>">