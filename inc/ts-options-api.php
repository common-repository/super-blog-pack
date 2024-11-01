<?php
/**
 * The core class that handles all the functionality of this plugin
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
if( !class_exists( 'TS_Options_API' ) ) :
	/**
	 * A simple api that creates an options page
	 * Used by ThemeStones officially
	 * 
	 * @package  SuperBlogPack
	 * @package  TS Options API
	 * @version  1.0.0
	 */
	class TS_Options_API {

		/**
		 * Holds the options array
		 * Forbids to call get_options() functions multiple time
		 * 
		 * @var null
		 */
		public $options = null;

		/**
		 * Main constructor that builds options page
		 * 
		 * @param array $args     Parameters for option pages
		 * @param array $template Template that build fields
		 * @return object $this
		 */
		function __construct( $args = array(), $template = array() ) {

			$this->args = wp_parse_args( $args, array(
				'menu_title' => 'TS Options API',
				'page_title' => 'TS Options API',
				'menu_slug' => 'ts-options-api',
				'option_key' => '_ts_options_api',
				'parent_slug' => '',
				'capability' => 'manage_options',
				'icon' => '',
				'callback' => array( $this, 'menu_callback' ),
				'position' => null,
			) );

			$this->template = $template;

			add_action( 'admin_init', array( $this, 'update_options' ), 10 );

			add_action( 'admin_menu', array( $this, 'add_menu' ), 10 );

			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 10 );

			add_action( 'admin_footer', array( $this, 'footer' ), 10 );

			if( get_option( $this->args['option_key'] ) === false ) {
				$this->set_defaults();
			}

			return $this;

		}

		/**
		 * Enqueue required scripts
		 * 
		 * @param  string $hook hook_suffix
		 * @return void
		 */
		function scripts( $hook ) {

			if( strpos( $hook, $this->slugify( $this->args['menu_title'] ) ) !== false  ) {
				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
			}

		}

		/**
		 * Add the administration menu page
		 * Or submenu page if parent slug is given
		 * 
		 * @return void
		 */
		function add_menu() {

			$page_title = $this->args['page_title'];
			$menu_title = $this->args['menu_title'];
			$capability = $this->args['capability'];
			$menu_slug = $this->args['menu_slug'];
			$parent_slug = $this->args['parent_slug'];
			$icon = $this->args['icon'];
			$callback = $this->args['callback'];
			$position = $this->args['position'];

			if( !empty( $parent_slug ) ) {
				add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
				return;
			}

			$this->_suffix = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon, $position );

			$this->add_submenu( $menu_slug );

			remove_submenu_page( $menu_slug, $menu_slug );

		}

		/**
		 * Add the submenus
		 * 
		 * @param string $parent_slug slug of the parent menu item
		 * @return void
		 */
		function add_submenu( $parent_slug ) {

			$template = $this->template;

			foreach( $template as $key => $args ) {

				$args = wp_parse_args( $args, array(
					'menu_title' => 'Sample Subpage',
					'page_title' => $this->args['page_title'],
					'menu_slug' => 'sample-subpage',
					'capability' => $this->args['capability'],
					'callback' => array( $this, 'menu_callback' ),
					'fields' => array(),
				) );
				
				add_submenu_page( $parent_slug, $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['callback'] );

			}

		}

		/**
		 * Get the option
		 * 
		 * @param  string  $key     The option key
		 * @param  mixed   $default Default option when ther's nothing to show
		 * @return mixed            The option value
		 */
		function get_option( $key = '', $default = false ) {

			if( is_null( $this->options ) ) {
				$this->options = get_option( $this->args['option_key'] );
			}

			$options = $this->options;

			if( is_array( $options ) && isset( $options[ $key ] ) ) {
				return $options[ $key ];
			} else {
				return $default;
			}

		}

		/**
		 * Update the options on save
		 * 
		 * @return void
		 */
		function update_options() {
			$option_key = $this->args['option_key'];

			if( isset( $_POST['_ts_options_api_nonce'] ) && wp_verify_nonce( $_POST['_ts_options_api_nonce'], '_ts_options_api_nonce' ) && isset( $_POST[ $option_key ] ) ) {

				$all_options = $_POST[ $option_key ];

				if( !is_array( $all_options ) ) {
					return;
				}

				$template = $this->template;

				$sanitized_options = array();

				foreach( $template as $args ) {

					$args = wp_parse_args( $args, array(
						'fields' => array(),
					) );

					foreach( $args['fields'] as $field ) {

						$field_args = wp_parse_args( $field, array(
							'id' => '',
							'type' => 'text',
							'sanitize' => '',
						) );

						$field_id = $field_args['id'];

						if( empty( $field_id ) ) {
							continue;
						}

						$orig_value = isset( $all_options[ $field_id ] ) ? $all_options[ $field_id ] : '';

						switch( $field_args['type'] ) {
							case 'check':
							case 'checkbox':
								$sanitize = is_array( $orig_value ) ? 'esc_attr' : 'absint';
								break;
							case 'radio':
							case 'select':
							case 'dropdown':
							case 'number':
								$sanitize = 'esc_attr';
								break;
							case 'email':
								$sanitize = 'sanitize_email';
								break;
							case 'url':
								$sanitize = 'esc_url';
								break;
							case 'color':
								$sanitize = 'sanitize_hex_color';
								break;
							default:
								$sanitize = 'esc_html';
								break;
						}

						if( is_callable( $field_args['sanitize'] ) ) {
							$callable = $field_args['sanitize'];
						} else {
							$callable = $sanitize;
						}

						if( is_array( $orig_value ) ) {
							$proceed_val = array();
							foreach( $orig_value as $key => $value ) {
								$proceed_val[ $key ] = call_user_func( $callable, $value );
							}
						} else {
							$proceed_val = call_user_func( $callable, $orig_value );
						}

						$sanitized_options[ $field_id ] = $proceed_val;

					}

				}

				update_option( $option_key, $sanitized_options );
			}
		}

		/**
		 * Set the default option when there's nothing
		 * 
		 * @return void
		 */
		function set_defaults() {

			$template = $this->template;

			$all_options = array();

			foreach( $template as $args ) {

				$args = wp_parse_args( $args, array(
					'fields' => array(),
				) );

				foreach( $args['fields'] as $field ) {

					$field_args = wp_parse_args( $field, array(
						'id' => '',
						'type' => 'text',
						'sanitize' => '',
						'default' => '',
					) );

					$field_id = $field_args['id'];

					if( empty( $field_id ) ) {
						continue;
					}

					$all_options[ $field_id ] = $field_args['default'];

				}

			}
			
			$option_key = $this->args['option_key'];

			update_option( $option_key, $all_options );

		}

		/**
		 * Build markup for the options page
		 * 
		 * @return void
		 */
		function menu_callback() {

			$this->options = null;

			global $ts_options_api;

			if( !is_array( $ts_options_api ) ) {
				$ts_options_api = array();
			}

			$ts_options_api['api_showing'] = true;

			$template = $this->template;

			echo '<form method="post"><div class="ts-options-api">';

			echo '<h2 class="nav-tab-wrapper">';

			foreach( $template as $key => $args ) {

				$args = wp_parse_args( $args, array(
					'menu_title' => 'Sample Subpage',
					'page_title' => '',
					'menu_slug' => 'sample-subpage',
					'capability' => $this->args['capability'],
					'callback' => array( $this, 'menu_callback' ),
					'fields' => array(),
				) );

				$active_class = '';

				$hook = current_filter();

				if( strpos( $hook, $args['menu_slug'] ) !== false ) {
					$active_class = ' nav-tab-active';
				}

				echo sprintf( '<a href="#%s" class="nav-tab%s">%s</a>', esc_attr( $args['menu_slug'] ), esc_attr( $active_class ), esc_html( $args['menu_title'] ) );

			}

			echo '</h2>';

			foreach( $template as $key => $args ) {

				$args = wp_parse_args( $args, array(
					'menu_title' => 'Sample Subpage',
					'page_title' => '',
					'description' => '',
					'menu_slug' => 'sample-subpage',
					'capability' => $this->args['capability'],
					'callback' => array( $this, 'menu_callback' ),
					'fields' => array(),
				) );

				$active_class = 'display: none;';

				$hook = current_filter();

				if( strpos( $hook, $args['menu_slug'] ) !== false ) {
					$active_class = 'display: block;';
				}

				echo sprintf( '<div class="options-section" style="%s" id="%s">', esc_attr( $active_class ), esc_attr( $args['menu_slug'] ) );

				if( !empty( $args['description'] ) ) {
					echo wp_kses_post( wpautop( $args['description'] ) );
				}

				echo '<table class="form-table">';

				foreach( $args['fields'] as $key => $field ) {

					$field_name = isset( $field['id'] ) ? $this->args['option_key'] . '[' . $field['id'] . ']' : '';

					$set_option = $this->get_option( isset( $field['id'] ) ? $field['id'] : '', null );

					$default = isset( $field['default'] ) ? $field['default'] : '';

					$value = is_null( $set_option ) ? $default : $set_option;
					
					echo '<tr>';
					$this->show_field( $field_name, $field, $value );
					echo '</tr>';
				}

				echo '</table></div>';

			}

			wp_nonce_field( '_ts_options_api_nonce', '_ts_options_api_nonce' );

			echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></div></form>';
		}

		/**
		 * Show a field
		 * 
		 * @param  string $field_name The unique ID of the field with a valid name for submitting
		 * @param  array  $args       Field parameters
		 * @param  string $val        Current value of the field
		 * @return void
		 */
		function show_field( $field_name, $args, $val = '' ) {

			$args = wp_parse_args( $args, array(
				'type' => 'text',
				'title' => '',
				'info' => '',
				'desc' => '',
			) );

			$info = '';

			if( !empty( $args['info'] ) ) {
				$info = sprintf( '<br/><p><em>%s</em></p>', wp_kses( $args['info'], array() ) );
			}

			switch( $args['type'] ) {
				case 'seperator':
					echo '<th colspan="2" style="padding: 0;"><hr /></th>';
					break;
				case 'heading':
					echo sprintf( '<td colspan="2" style="padding: 0;"><h3 style="margin-bottom: 0;">%s</h3></td>', $args['title'] );
					break;
				case 'paragraph':
				case 'content':
				case 'description':
					echo sprintf( '<td colspan="2" style="padding: 0;">%s</td>', wp_kses_post( wpautop( $args['content'] ) ) );
					break;
				case 'color':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					echo sprintf( '<td><input name="%s" type="text" id="%s" value="%s" class="color-field" data-default-color="%s" />%s</td>', esc_attr( $field_name ), esc_attr( $field_name ), esc_attr( $val ), esc_attr( $val ), wp_kses_post( $info ) );
					break;
				case 'check':
				case 'checkbox':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th><td>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					if( isset( $args['options'] ) ) {
						$val = (array)$val;
						foreach( (array)$args['options'] as $key => $element_title ) {
							$value = in_array( $key, $val ) ? 1 : 0;
							$name = $field_name . '[]';
							echo sprintf( '<label><input type="checkbox" name="%s" value="%s" %s/> %s</label><br />', esc_attr( $name ), esc_attr( $key ), checked( $value, 1, false ), esc_html( $element_title ) );
						}
					} else {
						echo sprintf( '<label><input type="checkbox" name="%s" id="%s" value="1" %s/> %s</label>', esc_attr( $field_name ), esc_attr( $field_name ), checked( $val, 1, false ), esc_html( $args['title'] ) );
					}
					echo wp_kses_post( $info );
					echo '</td>';
					break;
				case 'radio':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th><td>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					if( isset( $args['options'] ) ) {
						foreach( (array)$args['options'] as $key => $element_title ) {
							$value = $val == $key ? 1 : 0;
							echo sprintf( '<label><input type="radio" name="%s" value="%s" %s/> %s</label><br />', esc_attr( $field_name ), esc_attr( $key ), checked( $value, 1, false ), esc_html( $element_title ) );
						}
					}
					echo wp_kses_post( $info );
					echo '</td>';
					break;
				case 'select':
				case 'dropdown':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					if( isset( $args['options'] ) ) {
						echo sprintf( '<td><select name="%s" id="%s">', esc_attr( $field_name ), esc_attr( $field_name ) );
						foreach( (array)$args['options'] as $key => $element_title ) {
							echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $val, $key, false ), esc_html( $element_title ) );
						}
						echo '</select>';
						echo wp_kses_post( $info );
						echo '</td>';
					}
					break;
				case 'text':
				case 'number':
				case 'email':
				case 'url':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					echo sprintf( '<td><input name="%s" type="%s" id="%s" value="%s" class="regular-text" />%s</td>', esc_attr( $field_name ), esc_attr( $args['type'] ), esc_attr( $field_name ), esc_attr( $val ), wp_kses_post( $info ) );
					break;
				case 'textarea':
					echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', esc_attr( $field_name ), esc_html( $args['title'] ) );
					echo sprintf( '<td><textarea id="%s" name="%s" rows="10" cols="90">%s</textarea>%s</td>', esc_attr( $field_name ), esc_attr( $field_name ), esc_textarea( $val ), wp_kses_post( $info ) );
					break;
			}

		}

		/**
		 * Conver a string into slug (url friendly)
		 * 
		 * @param  string $text String to be slugified
		 * @return string       Slugified string
		 */
		function slugify( $text ) {

			$text = preg_replace('~[^\pL\d]+~u', '-', $text);
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
			$text = preg_replace('~[^-\w]+~', '', $text);
			$text = trim($text, '-');
			$text = preg_replace('~-+~', '-', $text);
			$text = strtolower($text);

			return $text;
		}

		/**
		 * Admin footer
		 *
		 * Basically prints javascript code.
		 * For special reason, it's not enqueued but printed directly.
		 * 
		 * @return void
		 */
		function footer() {

			global $ts_options_api, $pagenow, $hook_suffix;

			if( $pagenow == 'admin.php' && strpos( $hook_suffix, $this->slugify( $this->args['menu_title'] ) ) !== false ) {
				if( isset( $ts_options_api['api_showing'] ) && $ts_options_api['api_showing'] === true ) {
					?>
					<script type="text/javascript">
						(function($) {
							var a, b, c, d;

							function changeTabTo( hash, footstep, lastHash ) {

								footstep = typeof footstep === 'undefined' ? false : footstep;

								d = $('.ts-options-api .nav-tab-wrapper .nav-tab-active').attr('href');

								if( d.endsWith( hash ) ) {
									$(':focus').blur();
									return;
								}

								a = $('.ts-options-api .nav-tab-wrapper a[href$="' + hash + '"]').addClass('nav-tab-active').blur();
								a.siblings().removeClass('nav-tab-active');
								b = a.attr('href');
								$(b).fadeIn().siblings('.options-section').hide();
								b = b.replace('#', '');

								c = $('.wp-has-current-submenu ul li a[href$="' + b + '"]');

								$('.wp-has-current-submenu ul').find('.current').removeClass('current');

								c.parent().andSelf().addClass('current');

								$(':focus').blur();

								if( footstep === true && window.history && window.history.pushState ) {
									history.pushState( null, null, c.attr('href') );
								}

							}

							$(document).on('click', '.ts-options-api .nav-tab-wrapper a', function() {
								a = $(this);

								changeTabTo( a.attr('href'), true );

								return false;
							});

							$(document).on('click', '#<?php echo esc_attr( $this->_suffix ); ?> a', function() {
								a = $(this).attr('href').split('=');

								changeTabTo( a[1], true );

								return false;
							});

							window.addEventListener('popstate', function(e) {
								a = location.search.split('=');
								changeTabTo( a[1] );
							});

							$(document).ready(function() {
								$('.ts-options-api .color-field').each(function() {
									$(this).wpColorPicker({
										hide: true
									});
								});
							});

						})(jQuery);
					</script>
					<?php
					$ts_options_api['api_showing'] = false;
				}
			}

		}

	}
endif;