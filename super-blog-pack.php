<?php
/**
 * @package  SuperBlogPack
 * @version  1.0
 * 
 * Plugin Name: Super Blog Pack
 * Plugin URI: https://wordpress.org/plugins/super-blog-pack/
 * Description: A plugin that makes a WordPress site powerful by adding bunch of blog features
 * Version: 1.0
 * Author: ThemeStones
 * Author URI: http://themestones.net/
 * Text Domain: ts_sbp
 */

defined( 'SBP_DIR' ) or define( 'SBP_DIR', plugin_dir_path( __FILE__ ) );
defined( 'SBP_URL' ) or define( 'SBP_URL', plugin_dir_url( __FILE__ ) );
defined( 'SBP_TEMPLATE_DIR' ) or define( 'SBP_TEMPLATE_DIR', SBP_DIR . 'templates/' );

/**
 * The main class that constructs the plugin and connect everything
 *
 * @package  SuperBlogPack
 * @version  1.0
 */
class TS_SBP {

	/**
	 * Holds the instance for serving any time.
	 * 
	 * @var object
	 */
	private static $_instance;

	/**
	 * Returns the instance of self, if not already instanced, creates one
	 * 
	 * @return object Returns a instance of self
	 */
	public static function getInstance() {

		if( !(self::$_instance instanceof self) ) {
			self::$_instance = new self;
		}

		return self::$_instance;

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
	 * The main constructor
	 * 
	 * @return void
	 */
	function __construct() {

		$this->init_options();

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 10 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );

		add_action( 'init', array( $this, 'add_templates' ), 10 );

		add_filter( 'sbp_enable_debug', '__return_false', 1, 10 );

		add_filter( 'ts_sbp_reviews_per_page', array( $this, 'mod_review_pagination' ), 1, 10 );

		require_once SBP_DIR . '/inc/class-ts-post-statistics.php';
		require_once SBP_DIR . '/inc/sbp-templates.php';
		require_once SBP_DIR . '/inc/sbp-functions.php';

		$this->add_template_hooks();

	}

	/**
	 * Loading css & js on front end
	 * 
	 * @return void
	 */
	function scripts() {

		wp_enqueue_style( 'ts-sbp-icons', plugins_url( 'icons/style.css', __FILE__ ) );
		wp_enqueue_style( 'ts-sbp-icons-ie7', plugins_url( 'icons/ie7/ie7.css', __FILE__ ) );
		wp_enqueue_style( 'ts-sbp-basic', plugins_url( 'css/sbp-basic.css', __FILE__ ) );

		if( !current_theme_supports( 'ts-super-blog-pack-advanced' ) ) {
			wp_enqueue_style( 'ts-sbp-theme', plugins_url( 'css/sbp-theme.css', __FILE__ ) );
		}

		wp_style_add_data( 'ts-sbp-icons-ie7', 'conditional', 'lt IE 8' );

		wp_enqueue_script( 'ts-post-stats-js', plugins_url( 'js/ts-post-stats.js', __FILE__ ), array( 'jquery' ), '1.0', true );

		wp_localize_script( 'ts-post-stats-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

	}

	/**
	 * Loading css & js on back end
	 * 
	 * @return void
	 */
	function admin_scripts( $hook ) {

		if( $hook == 'post.php' ) {
			wp_enqueue_script( 'ts-sbp-options', plugins_url( 'js/options.js', __FILE__ ), array( 'media-views' ), time() );
		}

	}

	/**
	 * Add templates to desired locations
	 * 
	 * @return void
	 */
	function add_templates() {

		if( !current_theme_supports( 'ts-super-blog-pack-advanced' ) ) {

			add_filter( 'the_content', array( $this, 'content_filter' ) );

		}

	}

	function mod_review_pagination( $default ) {

		$options = $this->options;
		return $options->get_option( 'paginate_reviews' );

	}

	/**
	 * The filter for the_content to add elements
	 * 
	 * @param  string $content Content to be filtered
	 * @return string          Filtered content
	 */
	function content_filter( $content ) {

		$options = $this->options;

		if( get_post_type() != 'post' ) {
			return $content;
		}

		$share_pages = $options->get_option( 'share_visibility' );

		if( is_single() ) {

			ob_start();

			if( ( in_array( 'single', $share_pages ) && is_single() ) ) {

				ts_sbp_share();

			}
			
			ts_sbp_reviews();

			ts_sbp_related_posts();

			$content .= ob_get_clean();

		}

		$meta_pages = $options->get_option( 'meta_visibility' );

		$archive = ( in_array( 'archive', $meta_pages ) && is_archive() );
		$home = ( in_array( 'archive', $meta_pages ) && is_home() );
		$single = ( in_array( 'single', $meta_pages ) && is_single() );
		$search = ( in_array( 'search', $meta_pages ) && is_search() );

		if( $archive || $home || $single || $search ) {
			ob_start();

			ts_sbp_meta();

			$content = ob_get_clean() . $content;
		}

		$archive = ( in_array( 'archive', $share_pages ) && is_archive() );
		$home = ( in_array( 'archive', $share_pages ) && is_home() );
		$search = ( in_array( 'search', $share_pages ) && is_search() );

		if( $archive || $home || $search ) {
			ob_start();

			ts_sbp_share();

			$content .= ob_get_clean();
		}

		return $content;

	}

	/**
	 * Hooks functions to various locations
	 * 
	 * @return void
	 */
	function add_template_hooks() {

		$options = $this->options;

		/**
		 * Meta hooks
		 */
		$enable_rating = $options->get_option( 'meta_rating' );
		$enable_like = $options->get_option( 'meta_likes' );
		$enable_view = $options->get_option( 'meta_views' );

		if( $enable_rating ) {
			add_action( 'ts_sbp_entry_meta', 'ts_sbp_mini_rating', 5 );
		}

		if( $enable_like ) {
			add_action( 'ts_sbp_entry_meta', 'ts_sbp_like_button', 10 );
		}

		if( $enable_view ) {
			add_action( 'ts_sbp_entry_meta', 'ts_sbp_views_button', 15 );
		}

		/**
		 * Review hooks
		 */
		add_action( 'ts_sbp_review_area', 'ts_sbp_review_template', 5 );
		add_action( 'ts_sbp_review_area', 'ts_sbp_review_form', 10 );

	}

	function init_options() {

		require_once SBP_DIR . '/inc/ts-options-api.php';

		$templates = array(
			array(
				'menu_title' => esc_html__( 'Post share', 'ts_sbp' ),
				'description' => esc_html__( 'These options are related to post sharing links.', 'ts_sbp' ),
				'menu_slug' => 'sbp-post-share',
				'fields' => array(
					array(
						'title' => esc_html__( 'Where to show', 'ts_sbp' ),
						'info' => esc_html__( 'Some themes may not apply filters or strip the html content on search or archive pages. This doesn\'t allow to hook and show the meta or anything else.', 'ts_sbp' ),
						'id' => 'share_visibility',
						'type' => 'checkbox',
						'default' => array( 'single' ),
						'options' => array(
							'single' => esc_html__( 'Single Post', 'ts_sbp' ),
							'archive' => esc_html__( 'Post Archive', 'ts_sbp' ),
							'search' => esc_html__( 'Search Result Page', 'ts_sbp' ),
						),
					),
				),
			),
			array(
				'menu_title' => esc_html__( 'Post meta', 'ts_sbp' ),
				'description' => esc_html__( 'These options are related to post meta, where you can see post ratings(mini), post like button & post views counter.', 'ts_sbp' ),
				'menu_slug' => 'sbp-post-meta',
				'fields' => array(
					array(
						'title' => esc_html__( 'Where to show', 'ts_sbp' ),
						'info' => esc_html__( 'Some themes may not apply filters or strip the html content on search or archive pages. This doesn\'t allow to hook and show the meta or anything else.', 'ts_sbp' ),
						'id' => 'meta_visibility',
						'type' => 'checkbox',
						'default' => array( 'single', 'archive' ),
						'options' => array(
							'single' => esc_html__( 'Single Post', 'ts_sbp' ),
							'archive' => esc_html__( 'Post Archive', 'ts_sbp' ),
							'search' => esc_html__( 'Search Result Page', 'ts_sbp' ),
						),
					),
					array(
						'title' => esc_html__( 'Display Post Rating', 'ts_sbp' ),
						'id' => 'meta_rating',
						'type' => 'checkbox',
						'default' => true,
					),
					array(
						'title' => esc_html__( 'Display Post Likes', 'ts_sbp' ),
						'id' => 'meta_likes',
						'type' => 'checkbox',
						'default' => true,
					),
					array(
						'title' => esc_html__( 'Display Post Views', 'ts_sbp' ),
						'id' => 'meta_views',
						'type' => 'checkbox',
						'default' => true,
					),
				),
			),
			array(
				'menu_title' => esc_html__( 'Post reviews', 'ts_sbp' ),
				'description' => esc_html__( 'These options are related to post review & ratings.', 'ts_sbp' ),
				'menu_slug' => 'sbp-post-reviews',
				'fields' => array(
					array(
						'title' => esc_html__( 'Break # of reviews in to pages', 'ts_sbp' ),
						'id' => 'paginate_reviews',
						'type' => 'number',
						'default' => 5,
					),
					array(
						'title' => esc_html__( 'Rating fields', 'ts_sbp' ),
						'type' => 'heading',
					),
					array(
						'type' => 'seperator',
					),
					array(
						'type' => 'content',
						'content' => esc_html__( 'Leave a field empty to disable.', 'ts_sbp' ),
					),
					array(
						'title' => esc_html__( 'Rating field 1', 'ts_sbp' ),
						'id' => 'rating_field_1',
						'type' => 'text',
						'default' => esc_html__( 'Readability', 'ts_sbp' ),
					),
					array(
						'title' => esc_html__( 'Rating field 2', 'ts_sbp' ),
						'id' => 'rating_field_2',
						'type' => 'text',
						'default' => esc_html__( 'Helpfulness', 'ts_sbp' ),
					),
					array(
						'title' => esc_html__( 'Rating field 3', 'ts_sbp' ),
						'id' => 'rating_field_3',
						'type' => 'text',
						'default' => esc_html__( 'Word Selection', 'ts_sbp' ),
					),
				),
			),
			array(
				'menu_title' => esc_html__( 'Related Posts', 'ts_sbp' ),
				'menu_slug' => 'sbp-related-post',
				'fields' => array(
					array(
						'title' => esc_html__( 'Maximum number of related posts', 'ts_sbp' ),
						'id' => 'related_number',
						'type' => 'number',
						'default' => 3,
					),
					array(
						'title' => esc_html__( 'Display Related Posts only with a thumbnail', 'ts_sbp' ),
						'desc' => esc_html__( 'Posts that don\'t have featured images will show a fallback image if this option is unchecked.', 'ts_sbp' ),
						'id' => 'related_image_only',
						'type' => 'checkbox',
						'default' => false,
					),
				),
			),
		);

		$args = array(
			'menu_title' => esc_html__( 'Super Blog Pack', 'ts_sbp' ),
			'page_title' => esc_html__( 'Super Blog Pack', 'ts_sbp' ),
			'option_key' => '_ts_sbp_options',
			'menu_slug' => 'ts-sbp-options',
			'position' => 20,
		);

		$this->options = new TS_Options_API( $args, $templates );
	}

}

TS_SBP::getInstance();