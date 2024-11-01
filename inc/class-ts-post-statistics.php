<?php
/**
 * The core class that handles all the functionality of this plugin
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
if( !class_exists( 'TS_Post_Statistics' ) ) :
class TS_Post_Statistics {
	
	public static $_instance;

	/**
	 * TS_Post_Statistics::getInstance()
	 *
	 * The main constructor function
	 *
	 * @return object
	 */
	public static function getInstance() {
		if( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * TS_Post_Statistics::set_review_options()
	 *
	 * Set review options
	 *
	 * @return void
	 */
	function set_review_options( $args = array() ) {
		
		$args = wp_parse_args( $args, array(
			'enable' => true,
			'editable' => false,
			'anonymous' => true,
			'anonymous_see' => true,
			'avatar' => true,
			'moderate' => false,
			'leave_and_see' => false,
		) );
		
		$this->enable_review = $args['enable'];
		$this->allow_modify_review = $args['editable'];
		$this->allow_anonymous_review = $args['anonymous'];
		$this->anyone_can_see_review = $args['anonymous_see'];
		$this->enable_review_moderation = $args['moderate'];
		$this->must_leave_review_to_see = $args['leave_and_see'];
		$this->show_avatar_on_review = $args['avatar'];
		
	}

	/**
	 * Debug anything with print_r or var_dump
	 * 
	 * @param  mixed  $p    data to debug
	 * @param  boolean $var debug with var_dump
	 * @return void
	 */
	function p( $p, $var = false ) {
		echo '<pre>';
		if( $var ) {
			var_dump( $p );
		} else {
			print_r( $p );
		}
		echo '</pre>';
	}

	/**
	 * TS_Post_Statistics::__construct()
	 *
	 * The main constructor function
	 *
	 * @return void
	 */
	public function __construct() {
		
		$this->set_review_options();

		add_action( 'init', array( $this, 'setup_user'), 10 );

		add_action( 'wp_ajax_ts_like_button_click', array( $this, 'click' ) );
		add_action( 'wp_ajax_nopriv_ts_like_button_click', array( $this, 'click' ) );
		add_action( 'wp_ajax_nopriv_ts_submit_post_review', array( $this, 'add_review' ) );
		add_action( 'wp_ajax_ts_submit_post_review', array( $this, 'add_review' ) );
		add_action( 'wp_head', array( $this, 'header' ) );
		add_action( 'wp_footer', array( $this, 'nonce' ) );
		
		add_action( 'wp_ajax_ts_get_search_suggestions', array( $this, 'search_suggestions' ) );
		add_action( 'wp_ajax_nopriv_ts_get_search_suggestions', array( $this, 'search_suggestions' ) );

		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

	}

	/**
	 * TS_Post_Statistics::add_query_vars()
	 *
	 * Adds a query variable to the query object for review pagination
	 *
	 * @param array $vars array of the existing variable
	 * @return array The filtered array
	 */
	function add_query_vars( $vars ) {
		$vars[] = 'review_page';
		return $vars;
	}

	/**
	 * TS_Post_Statistics::setup_user()
	 *
	 * Sets up an unique user id/ip to self
	 * 
	 * @return void
	 */
	function setup_user() {
		if( is_user_logged_in() ) {
			$this->user = get_current_user_id();
		} else {
			$this->user = $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * TS_Post_Statistics::header()
	 *
	 * Header hook. Basically used to set post views
	 * 
	 * @return void
	 */
	public function header() {
		
		global $ts_post_viewed;
		
		if( ( is_single() || is_page() || is_singular() ) && $ts_post_viewed != true ) {
			
			$views = get_post_meta( get_the_ID(), '_ts_post_views_count', true );
			
			$views = empty( $views ) ? 0 : (integer) $views;
			
			$views++;
			
			update_post_meta( get_the_ID(), '_ts_post_views_count', $views );
			
			$unique_views = get_post_meta( get_the_ID(), '_ts_post_unique_views', true );
			
			if( !is_array( $unique_views ) ) {
				$unique_views = array();
			}
			
			if( !isset( $unique_views[ $this->user ] ) ) {
				$unique_views[ $this->user ] = array();
			}
			
			$unique_views[ $this->user ][] = time();
			
			update_post_meta( get_the_ID(), '_ts_post_unique_views', $unique_views );
			
			$ts_post_viewed = true;
			
		}
		
	}

	/**
	 * TS_Post_Statistics::rating_keys()
	 *
	 * Return the rating fields or factors in an array where keys are used as factor ID
	 *
	 * @return array Rating fields
	 */
	public function rating_keys() {

		$sbp = TS_SBP::getInstance();
		$options = $sbp->options;

		$fields1 = $options->get_option('rating_field_1');
		$fields2 = $options->get_option('rating_field_2');
		$fields3 = $options->get_option('rating_field_3');

		return array(
			$fields1,
			$fields2,
			$fields3,
		);

	}

	/**
	 * TS_Post_Statistics::rating_avg()
	 * 
	 * Calculate average rating point out of max for a specific post
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return float Avg. rating points
	 */
	function rating_avg( $post_id = null ) {

		$post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
		$meta = get_post_meta( $post_id, '_ts_post_review_ratings', true );

		if( is_array( $meta ) ) {
			$max = count( $meta );
			$mapped = array_map( array( $this, 'percent_mapper' ), $meta );
			return round( array_sum( $mapped ) / $max, 2 );
		} else {
			return 0;
		}

	}

	/**
	 * TS_Post_Statistics::rating_percent()
	 * 
	 * Calculate percentage of rating a post got
	 * 
	 * @param  int $post_id Post ID to check (optional)
	 * @return float Rating percentage
	 */
	function rating_percent( $post_id = null ) {

		$post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
		$meta = get_post_meta( $post_id, '_ts_post_review_ratings', true );

		if( is_array( $meta ) ) {
			$max = count( $meta ) * 5;
			$mapped = array_map( array( $this, 'percent_mapper' ), $meta );
			return ( array_sum( $mapped ) / $max ) * 100;
		} else {
			return 0;
		}

	}

	/**
	 * TS_Post_Statistics::percent_mapper()
	 * 
	 * Callback for array_map on percentage
	 * 
	 * @param  mixed $a array element to filter
	 * @return float|int    filtered item
	 */
	function percent_mapper( $a ) {
		$sum = array_sum( $a['_rating'] );
		$facts = count( $a['_rating'] );
		$max = $facts * 5;
		return $sum / $facts;
	}

	/**
	 * TS_Post_Statistics::get_review()
	 *
	 * get the rating given by current user
	 *
	 * @param  int|null $post_id post id
	 * @return boolean|array
	 */
	public function get_review( $post_id = null ) {

		$post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
		
		$meta = get_post_meta( $post_id, '_ts_post_review_ratings', true );
		$meta = (array)$meta;
		
		if( isset( $meta[ $this->user ] ) ) {
			return $meta[ $this->user ];
		} else {
			return 0;
		}

	}

	/**
	 * TS_Post_Statistics::search_suggestions()
	 *
	 * The ajax callback function for search suggestions
	 *
	 * @return void
	 */
	public function search_suggestions() {

		$this->security();
		
		if( !isset( $_POST['_search_query'] ) ) {
			die();
		}

		$data = array();
		
		$query = new WP_Query( 'posts_per_page=5&ignore_sticky_posts=1&s=' . $_POST['_search_query'] );
		
		if( $query->have_posts() ) {
			foreach( $query->posts as $post ) {
				$pid = $post->ID;
				$post_array = array(
					'title' => get_the_title( $pid ),
					'url' => get_permalink( $pid ),
					'img' => get_the_post_thumbnail_url( $pid, 'thumbnail' ),
					'date' => get_the_date( 'd M Y', $pid ),
				);
				if( $post->post_type == 'page' ) {
					$post_array['date'] = '';
				}
				$data[] = $post_array;
			}
		}

		wp_send_json_success( $data );

		die();

	}

	/**
	 * TS_Post_Statistics::click()
	 *
	 * The ajax callback function
	 *
	 * no parameters
	 *
	 * @return void
	 */
	public function click() {

		$this->security();

		$this->id = $_POST['_ts_post_id'];
		
		if( !post_password_required( $_POST['_ts_post_id'] ) ) {

			if( $this->liked( $this->id ) ) {
				$this->unlike();
			} else {
				$this->like();
			}
		
		}

		ob_start();
		ts_sbp_like_button( $this->id );
		$button = ob_get_clean();

		$data = array(
			'success' => true,
			'you_liked' => $this->liked( $this->id ),
			'total' => $this->count_likes( $_POST['_ts_post_id'] ),
			'button' => $button,
		);

		wp_send_json( $data );

		die();

	}

	/**
	 * TS_Post_Statistics::add_review()
	 *
	 * The function used to add or modify a review
	 *
	 * @return void
	 */
	public function add_review() {
		
		$this->security();
		
		$ratings = (array)$_POST['_ts_post_ratings'];
		$reviews = $_POST['_ts_post_review'];
		$post_id = $_POST['_ts_post_id'];
		$rating_keys = $this->rating_keys();
		
		foreach( $ratings as $rating => $value ) {
			if( !isset( $rating_keys[$rating] ) ) {
				unset( $ratings[$rating] );
			}
		}
		
		$nopriv_allowed = $this->allow_anonymous_review;
		
		$change_allowed = $this->allow_modify_review;
		
		$left_review = ( $this->get_review( $post_id ) == false ) ? false : true;
		
		if( !$nopriv_allowed && !is_user_logged_in() ) {
			die();
		}
		
		if( !$change_allowed && $left_review ) {
			die();
		}
		
		if( !isset( $_POST['_ts_post_ratings'] ) || !isset( $_POST['_ts_post_review'] ) ) {
			die();
		}
		
		$meta = get_post_meta( $post_id, '_ts_post_review_ratings', true );
		$meta = is_array( $meta ) ? $meta : array();
		
		$meta[ $this->user ] = array(
			'_review' => $reviews,
			'_rating' => $ratings,
		);
		
		update_post_meta( $post_id, '_ts_post_review_ratings', $meta );
		
		global $post;
		
		$post = get_post( $post_id );
		
		setup_postdata( $post );
		
		ob_start();
		
		ts_sbp_reviews();
		
		$output = ob_get_clean();
		
		wp_reset_postdata();

		$data = array(
			'success' => true,
			'data' => $output,
		);

		wp_send_json( $data );
		
		die();

	}

	/**
	 * TS_Post_Statistics::like()
	 *
	 * The function used to add like
	 *
	 * @return void
	 */
	public function like() {

		global $post;

		if( $this->liked( $this->id ) ) {
			if( $this->ajax() ) {
				die();
			}
			return;
		}

		$likes = get_post_meta( $this->id, '_ts_post_likes_count', true );

		if( !is_array( $likes ) ) {
			$likes = array();
		}

		$likes[] = $this->user;

		update_post_meta( $this->id, '_ts_post_likes_count', $likes );

	}

	/**
	 * TS_Post_Statistics::unlike()
	 *
	 * The function used to remove like
	 *
	 * @return void
	 */
	public function unlike() {

		if( !$this->liked( $this->id ) ) {
			if( $this->ajax() ) {
				die();
			}
			return;
		}

		$likes = get_post_meta( $this->id, '_ts_post_likes_count', true );

		if( is_array( $likes ) && !empty( $likes ) ) {

			foreach( $likes as $i => $user_id ) {
				if( $user_id === $this->user ) {
					unset( $likes[ $i ] );
				}
			}

		}

		update_post_meta( $this->id, '_ts_post_likes_count', $likes );

	}

	/**
	 * TS_Post_Statistics::liked()
	 *
	 * Check if user already liked the post or not
	 *
	 * @param  $id Post ID to check with
	 * @return boolean
	 */
	public function liked( $id = null ) {

		global $post;

		$id = is_null( $id ) ? $post->ID : $id;

		$likes = get_post_meta( $id, '_ts_post_likes_count', true );

		if( is_array( $likes ) && in_array( $this->user, $likes ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * TS_Post_Statistics::count_likes()
	 *
	 * Count the total likes
	 *
	 * @return string|int
	 */
	public function count_likes( $id = null ) {

		global $post;
		
		$id = is_null( $id ) ? get_the_ID() : $id;

		$likes = get_post_meta( $id, '_ts_post_likes_count', true );

		if( !is_array( $likes ) ) {
			return '0';
		}

		return intval( count( $likes ) );

	}

	/**
	 * Count number of hits or views the post got
	 * 
	 * @param  int|null  $id     Optional post ID
	 * @param  boolean $unique Count unique views or not
	 * @return int Number of views
	 */
	function get_views( $id = null, $unique = false ) {

		global $post;
		
		$id = is_null( $id ) ? get_the_ID() : $id;

		if( $unique ) {
			return intval( count( get_post_meta( $id, '_ts_post_unique_views', true ) ) );
		} else {
			return intval( get_post_meta( $id, '_ts_post_views_count', true ) );
		}

	}

	/**
	 * TS_Post_Statistics::ajax()
	 *
	 * Check if it's currently an AJAX request or not
	 *
	 * @return boolean
	 */
	public function ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * TS_Post_Statistics::security()
	 *
	 * Check the security if everything is normal. Kill the server if any required value is not provided
	 *
	 * @return void
	 */
	public function security() {

		if( !$this->ajax() ) {
			die();
		}

		if( !isset( $_POST['_ts_post_like_nonce'] ) ) {
			die();
		}

		if( !isset( $_POST['_ts_post_id'] ) ) {
			die();
		}

		if( !wp_verify_nonce( $_POST['_ts_post_like_nonce'], '_ts_post_like_nonce' ) ) {
			die();
		}

	}

	/**
	 * TS_Post_Statistics::nonce()
	 *
	 * prints a nonce field for security usage
	 *
	 * @return void
	 */
	public function nonce() {

		wp_nonce_field( '_ts_post_like_nonce', '_ts_post_like_nonce' );

	}

}
endif;

TS_Post_Statistics::getInstance();