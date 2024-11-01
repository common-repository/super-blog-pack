<?php
/**
 * Declares function to search and load template files
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

/**
 * ts_sbp_load_template()
 *
 * Loads a template, theme gets priority
 */
if( !function_exists( 'ts_sbp_load_template' ) ) {
	function ts_sbp_load_template( $template = '', $slug = '' ) {

		if( empty( $template ) ) {
			return;
		}

		$template_dir = apply_filters( 'sbp_template_dir', 'super-blog-pack', $template, $slug );

		if( locate_template( $template_dir . '/' . $template . '-' . $slug . '.php', true, false ) == '' ) {
			if( file_exists( SBP_TEMPLATE_DIR . $template . '-' . $slug . '.php' ) ) {
				require SBP_TEMPLATE_DIR . $template . '-' . $slug . '.php';
			} elseif( locate_template( $template_dir . '/' . $template . '.php', true, false ) == '' ) {
				if( file_exists( SBP_TEMPLATE_DIR . $template . '.php' ) ) {
					require SBP_TEMPLATE_DIR . $template . '.php';
				} else {
					if( apply_filters( 'sbp_enable_debug', current_user_can( 'manage_options' ) ) ) {
						$filename = empty( $slug ) ? $template . '.php' : $template . '-' . $slug . '.php';
						echo sprintf( esc_html__( 'Missing template file: %s', 'ts_sbp' ), '<strong>' . $filename . '</strong>' );
						echo '<br><br>';
						echo '<strong>' . esc_html__( 'Locations searched ordered from top:', 'ts_sbp' ) . '</strong>';
						echo '<br><br>';
						$search_locations = array();
						if( !empty( $slug ) ) {
							$search_locations[] = get_stylesheet_directory() . '/' . $template_dir . '/' . $template . '-' . $slug . '.php';
							$search_locations[] = SBP_TEMPLATE_DIR . $template . '-' . $slug . '.php';
						}
						$search_locations[] = get_stylesheet_directory() . '/' . $template_dir . '/' . $template . '.php';
						$search_locations[] = SBP_TEMPLATE_DIR . $template . '.php';

						foreach( $search_locations as $value ) {
							echo sprintf( '<em>%s</em><br><br>', str_replace( '\\', DIRECTORY_SEPARATOR, str_replace( '/', DIRECTORY_SEPARATOR, $value ) ) );
						}
					}
				}
			}
		}

	}
}

if( !function_exists( 'ts_sbp_locate_template' ) ) {
	function ts_sbp_locate_template( $template = '', $slug = '' ) {

		if( empty( $template ) ) {
			return false;
		}

		$template_dir = apply_filters( 'sbp_template_dir', 'super-blog-pack', $template, $slug );

		$first_search = $template_dir . '/' . $template . '-' . $slug . '.php';
		$second_search = SBP_TEMPLATE_DIR . $template . '-' . $slug . '.php';
		$third_search = $template_dir . '/' . $template . '.php';
		$fourth_search = SBP_TEMPLATE_DIR . $template . '.php';

		if( file_exists( $first_search ) ) {
			return $first_search;
		} elseif( file_exists( $second_search ) ) {
			return $second_search;
		} elseif( file_exists( $third_search ) ) {
			return $third_search;
		} elseif( file_exists( $fourth_search ) ) {
			return $fourth_search;
		}

		return false;

	}
}

/**
 * ts_sbp_meta()
 *
 * Shows meta info about likes views and ratings
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_meta' ) ) :
	function ts_sbp_meta( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'meta/meta' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_like_button()
 *
 * Renders a button that shows total likes and has ability to like post
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_like_button' ) ) :
	function ts_sbp_like_button( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'meta/meta', 'likes' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_views_button()
 *
 * Renders a button that shows total views
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_views_button' ) ) :
	function ts_sbp_views_button( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'meta/meta', 'views' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_mini_rating()
 *
 * Renders rating stars
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_mini_rating' ) ) :
	function ts_sbp_mini_rating( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'meta/meta', 'rating' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_share()
 *
 * Renders post share links
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_share' ) ) :
	function ts_sbp_share( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'share/share' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_reviews()
 *
 * Renders detailed reviews and ratings
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_reviews' ) ) :
	function ts_sbp_reviews( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		ts_sbp_load_template( 'review/review' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_review_form()
 *
 * Renders review form
 *
 * @param    post_id         post ID to work with specific post
 */
if( !function_exists( 'ts_sbp_review_form' ) ) :
	function ts_sbp_review_form( $post_id = null ) {
		global $post;
		$original_post = $post;

		if( $post_id != null ) {
			$post = get_post( $post_id );
			setup_postdata( $post );
		}

		$sbp = TS_Post_Statistics::getInstance();

		$user_reviewed = $sbp->get_review();
		$rating_keys = $sbp->rating_keys();
		
		$user_review = wp_parse_args( $user_reviewed, array(
			'_review' => '',
			'_rating' => $rating_keys,
		) );
		
		$nopriv_allowed = $sbp->allow_anonymous_review;
		
		$change_allowed = $sbp->allow_modify_review;
		
		$left_review = ( $sbp->get_review() == false ) ? false : true;
		
		if( !$nopriv_allowed && !is_user_logged_in() ) {
			return;
		}
		
		if( !$change_allowed && $left_review ) {
			return;
		}

		ts_sbp_load_template( 'review/review', 'form' );

		if( $post_id != null ) {
			$post = $original_post;
			setup_postdata( $original_post );
		}
	}
endif;

/**
 * ts_sbp_related()
 *
 * Related posts
 */
if( !function_exists( 'ts_sbp_related' ) ) :
	function ts_sbp_related() {
		ts_sbp_load_template( 'related/related' );
	}
endif;

/**
 * ts_sbp_related_before()
 *
 * Start of the related post container
 */
if( !function_exists( 'ts_sbp_related_before' ) ) :
	function ts_sbp_related_before() {
		ts_sbp_load_template( 'related/related', 'before' );
	}
endif;

/**
 * ts_sbp_related_after()
 *
 * End of the related post container
 */
if( !function_exists( 'ts_sbp_related_after' ) ) :
	function ts_sbp_related_after() {
		ts_sbp_load_template( 'related/related', 'after' );
	}
endif;

/**
 * ts_sbp_related_single()
 *
 * Used for the related posts loop
 */
if( !function_exists( 'ts_sbp_related_single' ) ) :
	function ts_sbp_related_single() {
		ts_sbp_load_template( 'related/related', 'loop' );
	}
endif;