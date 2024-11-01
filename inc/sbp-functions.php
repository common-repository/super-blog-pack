<?php
/**
 * Declare required functions
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */

if( !function_exists( 'ts_sbp_related_posts' ) ) {
	/**
	 * Prints a list of posts related to current post
	 * 
	 * @return void
	 */
	function ts_sbp_related_posts() {

		$factors = array( 'tags' );

		global $post;

		$sbp = TS_SBP::getInstance();
		$options = $sbp->options;

		$image_only = $options->get_option( 'related_image_only' );

		$query_args = array(
			'post_type' => 'post',
			'post__not_in' => array( $post->ID ),
			'ignore_sticky_posts' => 1,
			'posts_per_page' => ts_sbp_num_related_posts(),
		);

		if( $image_only ) {
			$query_args['meta_query'] =array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS'
				),
			);
		}

		if( in_array( 'category', $factors ) ) {
			$query_args['category__in'] = wp_get_post_terms( $post->ID, 'category', array( 'fields' => 'ids' ) );
		}

		if( in_array( 'tags', $factors ) ) {
			$query_args['tag__in'] = wp_get_post_terms( $post->ID, 'post_tag', array( 'fields' => 'ids' ) );
		}

		if( in_array( 'author', $factors ) ) {
			$query_args['author__in'] = array( intval( $post->post_author ) );
		}

		query_posts( $query_args );

		if( have_posts() ) :

			do_action( 'ts_sbp_related_posts_before' );
			ts_sbp_related_before();

			while( have_posts() ) : the_post();

				do_action( 'ts_sbp_related_post_before' );
				ts_sbp_related_single();
				do_action( 'ts_sbp_related_post_after' );

			endwhile;

			ts_sbp_related_after();
			do_action( 'ts_sbp_related_posts_after' );

		endif;

		wp_reset_query();

	}
}

if( !function_exists( 'ts_sbp_format_number' ) ) {
	/**
	 * Format a number e.g. 1000 to 1k ( can round up like 1575 to 1.5k )
	 * 
	 * @param  int|float|string $number Number to format
	 * @return string         Formated number
	 */
	function ts_sbp_format_number( $number ) {
		$thousands = apply_filters( 'ts_sbp_thousand_formatter', 'k' );
		if( $number < 1000 ) {
			return $number;
		}
   		return round( $number/1000, 1 ) . $thousands;
	}
}

if( !function_exists( 'ts_sbp_has_liked_post' ) ) {
	/**
	 * Checks if current user has liked the post or not
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return bool User has liked the post or not
	 */
	function ts_sbp_has_liked_post( $post_id = null ) {
		$sbp = TS_Post_Statistics::getInstance();
		return $sbp->liked();
	}
}

if( !function_exists( 'ts_sbp_num_related_posts' ) ) {
	/**
	 * How many posts to show in related entries
	 * 
	 * @return int Number of post
	 */
	function ts_sbp_num_related_posts() {

		$sbp = TS_SBP::getInstance();
		$options = $sbp->options;

		$number = $options->get_option( 'related_number' , 3 );

		return apply_filters( 'ts_sbp_num_related_posts', $number );
	}
}

if( !function_exists( 'ts_sbp_count_likes' ) ) {
	/**
	 * Calculate how many likes the post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @param  bool $format Format the number e.g. 1000 to 1k ( can round up like 1575 to 1.5k )
	 * @return int|string Number of likes
	 */
	function ts_sbp_count_likes( $post_id = null, $format = true ) {
		$sbp = TS_Post_Statistics::getInstance();
		$likes = $sbp->count_likes( $post_id );
		if( $format ) {
			return ts_sbp_format_number( $likes );
		} else {
			return $likes;
		}
	}
}

if( !function_exists( 'ts_sbp_count_hits' ) ) {
	/**
	 * Calculate how many hits the post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @param  boolean $unique Uniqe views or not
	 * @return int Number of views
	 */
	function ts_sbp_count_hits( $post_id = null, $unique = false ) {
		$sbp = TS_Post_Statistics::getInstance();
		return $sbp->get_views( $post_id, $unique );
	}
}

if( !function_exists( 'ts_sbp_get_rating_points' ) ) {
	/**
	 * Calculate average rating point out of max for a specific post
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return float Avg. rating points
	 */
	function ts_sbp_get_rating_points( $post_id = null ) {
		$sbp = TS_Post_Statistics::getInstance();
		return $sbp->rating_avg( $post_id );
	}
}

if( !function_exists( 'ts_sbp_get_rating_percent' ) ) {
	/**
	 * Calculate percentage of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return float Rating percentage
	 */
	function ts_sbp_get_rating_percent( $post_id = null ) {
		$post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
		$sbp = TS_Post_Statistics::getInstance();
		return $sbp->rating_percent( $post_id );
	}
}

if( !function_exists( 'ts_sbp_get_rating_count' ) ) {
	/**
	 * Calculate number of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return int Number of rating
	 */
	function ts_sbp_get_rating_count( $post_id = null ) {
		$post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
		$meta = get_post_meta( $post_id, '_ts_post_review_ratings', true );
		if( is_array( $meta ) ) {
			return count( $meta );
		} else {
			return false;
		}
	}
}

if( !function_exists( 'ts_sbp_review_form_ratings' ) ) {
	/**
	 * Calculate number of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return int Number of rating
	 */
	function ts_sbp_review_form_ratings() {

		$sbp = TS_Post_Statistics::getInstance();

		$user_reviewed = $sbp->get_review();
		$rating_keys = $sbp->rating_keys();
		
		$user_review = wp_parse_args( $user_reviewed, array(
			'_review' => '',
			'_rating' => $rating_keys,
		) );

		foreach ( $rating_keys as $rating_key => $rating_name ) {
			$rating_value = isset( $user_review['_rating'][$rating_key] ) ? intval( $user_review['_rating'][$rating_key] ) : 0;
			$rating_style = 'width: ' . ( $rating_value * 20 ) . '%;';
			$rating_value_input = ( empty( $rating_value ) || $rating_value == 0 ) ? '' : $rating_value;

			$rating_input = sprintf( '<input type="hidden" required="required" value="%s" data-rating-key="%s" class="star-value">', esc_attr( $rating_value_input ), esc_attr( $rating_key ) );

			if( $template = ts_sbp_locate_template( 'review/form/star-rating' ) ) {
				require $template;
			}
		}

	}
}

if( !function_exists( 'ts_sbp_review_form_review' ) ) {
	/**
	 * Calculate number of rating a post got
	 * 
	 * @return int Number of rating
	 */
	function ts_sbp_review_form_review() {

		$sbp = TS_Post_Statistics::getInstance();

		$user_reviewed = $sbp->get_review();
		$rating_keys = $sbp->rating_keys();
		
		$user_review = wp_parse_args( $user_reviewed, array(
			'_review' => '',
			'_rating' => $rating_keys,
		) );

		if( $template = ts_sbp_locate_template( 'review/form/review-text' ) ) {
			require $template;
		}
	}
}

if( !function_exists( 'ts_sbp_review_form_submit' ) ) {
	/**
	 * Calculate number of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return int Number of rating
	 */
	function ts_sbp_review_form_submit() {
		echo sprintf( '<input type="hidden" name="_ts_post_id" value="%s">', esc_attr( get_the_ID() ) );
		ts_sbp_load_template( 'review/form/submit' );
	}
}

if( !function_exists( 'ts_sbp_user_has_reviewed' ) ) {
	/**
	 * Calculate number of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return int Number of rating
	 */
	function ts_sbp_user_has_reviewed() {

		$sbp = TS_Post_Statistics::getInstance();
		return ( $sbp->get_review() == false ) ? false : true;
		
	}
}

if( !function_exists( 'ts_sbp_review_template' ) ) {
	/**
	 * Build the review template and render it
	 * 
	 * @return void
	 */
	function ts_sbp_review_template() {

		$sbp = TS_Post_Statistics::getInstance();
		
		if( ! $sbp->enable_review ) {
			return;
		}
		
		$moderate_enable = $sbp->enable_review_moderation;
		$show_avatar = $sbp->show_avatar_on_review;
		
		$meta = get_post_meta( get_the_ID(), '_ts_post_review_ratings', true );

		$total = count( $meta );
		
		$rating_keys = $sbp->rating_keys();
		
		if( empty( $meta ) || !is_array( $meta ) ) {
			ts_sbp_load_template( 'review/user-reviews/no-reviews' );
			return;
		}

		ts_sbp_load_template( 'review/user-reviews/reviews-before' );

		$per_page = apply_filters( 'ts_sbp_reviews_per_page', 5 );

		$pagenow = get_query_var( 'review_page', 1 );

		$meta = array_slice( $meta, ( ($per_page * $pagenow) - $per_page ), $per_page, true );

		foreach( $meta as $user_id => $user ) {
			if( is_array( $user ) && isset( $user['_rating'] ) ) {

				global $ts_sbp_global_temp;
				
				if( $moderate_enable && !( isset( $user['_moderated'] ) && $user['_moderated'] == true ) ) {
					continue;
				}
				
				$user_name = __( 'Anonymous', 'ts_sbp' );
				
				$author = get_user_by( 'id', $user_id );
				
				if( $author != false ) {
					$user_name = trim( $author->first_name . ' ' . $author->last_name );
					$user_name = empty( $user_name ) ? $author->user_login : $user_name;
				}
				
				$ratings = (array)$user['_rating'];

				(array)$ts_sbp_global_temp['user_ratings'] = $ratings;
				
				$value = ( array_sum( $ratings )/count( $ratings ) ) * 20;
				$rating_style = 'width: ' . $value . '%;';
				
				$user_hash = 'reviewdetails_' . $user_id;

				if( $template = ts_sbp_locate_template( 'review/user-reviews/single-review' ) ) {
					require $template;
				}
			}
		}

		ts_sbp_load_template( 'review/user-reviews/reviews-after' );

		if( $total > $per_page ) {
			ts_sbp_load_template( 'review/user-reviews/nav' );
		}
	}
}

if( !function_exists( 'ts_sbp_review_nav' ) ) {
	/**
	 * Print a navigation for post reviews (if available)
	 * 
	 * @param  string $id   Post ID to do with
	 * @param  string $prev previous text
	 * @param  string $next next text
	 * @return void
	 */
	function ts_sbp_review_nav( $id = 'ts-sbp-review', $prev = '', $next = '' ) {
		
		$meta = get_post_meta( get_the_ID(), '_ts_post_review_ratings', true );
		
		if( empty( $meta ) || !is_array( $meta ) ) {
			return;
		}

		$per_page = apply_filters( 'ts_sbp_reviews_per_page', 5 );

		$pagenow = get_query_var( 'review_page', 1 );

		$total = count( $meta );

		if( $total <= $per_page ) {
			return;
		}

		$max_page = floor( $total / $per_page );

		$args = array(
			'base'               => get_permalink() . '%_%',
			'format'             => '?review_page=%#%',
			'total'              => $max_page,
			'current'            => $pagenow,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_text'          => $prev,
			'next_text'          => $next,
			'type'               => 'list',
			'add_args'           => false,
			'add_fragment'       => '#' . $id,
			'before_page_number' => '',
			'after_page_number'  => ''
		);

		ob_start();

		echo paginate_links( $args );

		$html = ob_get_clean();

		$html = str_replace( 'class="prev page-numbers"', 'class="prev"', $html );
		$html = str_replace( 'class="next page-numbers"', 'class="next"', $html );

		echo $html;

	}
}

if( !function_exists( 'ts_sbp_review_star_ratings' ) ) {
	/**
	 * Prints the star rating for a specific review
	 * 
	 * @return void
	 */
	function ts_sbp_review_star_ratings() {

		$sbp = TS_Post_Statistics::getInstance();
		$rating_keys = $sbp->rating_keys();
		
		global $ts_sbp_global_temp;

		if( !isset( $ts_sbp_global_temp['user_ratings'] ) ) {
			return;
		}

		foreach( $ts_sbp_global_temp['user_ratings'] as $rating_key => $rating ) {
			if( isset( $rating_keys[ $rating_key ] ) && $rating > 0 ) {
				$value = ( $rating/5 ) * 100;
				$rating_style = 'width: ' . $value . '%;';
				$rating_subject = $rating_keys[ $rating_key ];

				if( $template = ts_sbp_locate_template( 'review/user-reviews/star-rating' ) ) {
					require $template;
				}
			}
		}
	}
}

if( !function_exists( 'ts_sbp_fallback_thumbnail' ) ) {
	/**
	 * Prints a fallback thumbnail when there is no post thumbnail
	 * 
	 * @return void
	 */
	function ts_sbp_fallback_thumbnail() {
		echo sprintf( '<img src="%s" alt="%s" />', esc_url( SBP_URL . '/img/thumb.png' ), esc_attr__( 'Thumbnail', 'ts_sbp' ) );
	}
}

if( !function_exists( 'ts_sbp_post_share_links' ) ) {
	/**
	 * Prints post share links
	 * 
	 * @param  string What to show before each link
	 * @param  string What to show after each link
	 * @return void
	 */
	function ts_sbp_post_share_links( $before = '', $after = '' ) {

		$urls = array(
			'facebook' => array(
				'icon' => 'sbp-icon sbp-facebook',
				'url' => 'http://www.facebook.com/sharer.php?u=%1$s&amp;t=%2$s',
				'title' => __( 'Facebook', 'ts_sbp' ),
				'popup' => true,
			),
			'twitter' => array(
				'icon' => 'sbp-icon sbp-twitter',
				'url' => 'http://twitter.com/home/?status=%2$s - %1$s',
				'title' => __( 'Twitter', 'ts_sbp' ),
				'popup' => true,
			),
			'linkedin' => array(
				'icon' => 'sbp-icon sbp-linkedin',
				'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;title=%2$s&amp;url=%1$s',
				'title' => __( 'LinkedIn', 'ts_sbp' ),
				'popup' => true,
			),
			'google-plus' => array(
				'icon' => 'sbp-icon sbp-google-plus',
				'url' => 'https://plus.google.com/share?url=%1$s',
				'title' => __( 'Google Plus', 'ts_sbp' ),
				'popup' => true,
			),
			'pinterest' => array(
				'icon' => 'sbp-icon sbp-pinterest',
				'url' => 'http://pinterest.com/pin/create/button/?url=%1$s&media=%3$s',
				'title' => __( 'Pinterest', 'ts_sbp' ),
				'popup' => true,
			),
		);

		$urls = apply_filters( 'ts_sbp_share_urls', $urls );

		foreach( (array)$urls as $key => $url ) {
			$url = wp_parse_args( $url, array(
				'url' => '',
				'icon' => '',
				'title' => '',
				'popup' => true,
			) );
			$img_url = '';
			if( has_post_thumbnail() ) {
				$img_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			}
			$url_raw = sprintf( $url['url'], get_permalink(), get_the_title(), $img_url );
			printf( '%s<a href="%s" class="ts-sbp-share-link" data-popup="%s"><i class="%s"></i>%s</a>%s', wp_kses_post( $before ), esc_url( $url_raw ), esc_attr( $url['popup'] ? 'true' : 'false' ), esc_attr( $url['icon'] ), esc_html( rtrim( ' ' . $url['title'] ) ), wp_kses_post( $after ) );
		}

	}
}