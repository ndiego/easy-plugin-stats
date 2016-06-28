<?php
/**
 * Plugin Name: Easy Plugin Stats
 * Plugin URI:  https://www.nickdiego.com/plugins/easy-plugin-stats
 * Description: Easily display stats from plugins hosted on WordPress.org
 * Author:      Nick Diego
 * Author URI:  http://www.nickdiego.com
 * Version:     1.0.0
 * Text Domain: easy-plugin-stats
 * Domain Path: languages
 *
 * Copyright 2016 Nick Diego
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, visit <http://www.gnu.org/licenses/>.
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class Easy_Plugin_Stats {

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		add_action( 'wp_loaded', array( $this, 'init') );
	}

	/**
	 * Initialize plugin.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		
		load_plugin_textdomain( 'geasy-plugin-stats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_action( 'admin_init', array( $this, 'add_tinymce_button' ) );
		
		add_shortcode( 'eps', array( $this, 'shortcode' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enable_dashicons' ) );
		
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}


	/**
	 * Initialize the columns shortcode tinymce button
	 *
	 * @since 1.0.0
	 */
	public function add_tinymce_button() {

		// Check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		// Check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_languages', array( $this, 'add_tinymce_translations' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tinymce_button' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'tinymce_popup' ), 100);
			add_action( 'admin_head', array( $this, 'tinymce_button_style' ) );
		}
	}


	/**
	 * Declare script for new button
	 *
	 * @since 1.0.0
	 *
	 * @param array $plugins An array of all current TinyMCE plugins
	 * @return array $plugins Return the plugins array with our plugin added
	 */
	function add_tinymce_plugin( $plugins ) {
		$plugins['eps_plugin'] = plugin_dir_url( __FILE__ ) . 'tinymce/js/plugin.js';
		return $plugins;
	}


	/**
	 * Add translations to TinyMCE button
	 *
	 * @since 1.0.0
	 *
	 * @param array $locales An array of all current TinyMCE translations
	 * @return array $locales Return the translations array with our translations added
	 */
	public function add_tinymce_translations( $locales ) {
		$locales['eps_translations'] = plugin_dir_path( __FILE__ ) . 'tinymce/plugin-translations.php';
		return $locales;
	}
	

	/**
	 * Register new button in the TinyMCE editor
	 *
	 * @since 1.0.0
	 *
	 * @param array $buttons An array of all current TinyMCE buttons
	 * @return array $buttons Return the buttons array with our button added
	 */
	function register_tinymce_button( $buttons ) {
		array_push( $buttons, 'eps_plugin' );
		return $buttons;
	}
	
	
	/**
	 * Add the popup and the popup backdrop to the footer of admin page
	 *
	 * @since 1.0.0
	 */
	public function tinymce_popup() {
		include_once dirname( __FILE__ ) . '/tinymce/popup.php';
	}
	
	
	/**
	 * Loads scripts/styles to the admin
	 *
	 * @since 1.0.0
	 */
	function admin_scripts_enqueue() {
		
		wp_register_script( 'eps-popup-scripts', plugin_dir_url( __FILE__ ) . 'tinymce/js/popup.js' );
		wp_enqueue_script( 'eps-popup-scripts' );
	
		// Used for adding translations to javascript
		wp_localize_script( 
			'eps-popup-scripts', 
			'eps_localize_scripts', 
			array(				
				'single_slug_title'	 	=> __( 'Plugin Slug', 'easy-plugin-stats' ),
				'aggregate_slug_title'	=> __( 'Plugin Slugs', 'easy-plugin-stats' ),
				'single_slug_desc' 		=> sprintf( __( 'Enter your plugin\'s slug and choose the stat field you wish to display. For help, visit this plugin\'s %sdocumentation%s.', 'easy-plugin-stats' ), '<a href="http://www.nickdiego.com/plugins/easy-plugin-stats">', '</a>' ),
				'aggregate_slug_desc' 	=> sprintf( __( 'Enter any number of %sspace separated%s plugin slugs and choose the stat field you wish to display. For help, visit this plugin\'s %sdocumentation%s.', 'easy-plugin-stats' ), '<strong>', '</strong>', '<a href="http://www.nickdiego.com/plugins/easy-plugin-stats">', '</a>' ),
				'advanced_show'			=> __( 'Show Advanced Settings', 'easy-plugin-stats' ),
				'advanced_hide'			=> __( 'Hide Advanced Settings', 'easy-plugin-stats' ),
				'missing_slug'         	=> __( 'You forgot to enter a plugin slug!', 'easy-plugin-stats' ),
			)
		);

		wp_enqueue_style( 'eps-popup-styles',  plugin_dir_url( __FILE__ ) . 'tinymce/css/popup.css' );
	}


	/**
	 * Add the plugin icon to the tinymce button
	 *
	 * @since 1.0.0
	 */
	function tinymce_button_style() {
		?>
		<style>
			.mce-i-dashicons-admin-plugins { font: 400 20px/1 dashicons !important; vertical-align: top; speak: none; -webkit-font-smoothing: antialiased; }
			.mce-i-dashicons-admin-plugins:before { content: "\f106"; }
		</style>
		<?php
	}


	/**
	 * Enqueue Dashicons style for frontend use (needed for stars)
	 *
	 * @since 1.0.0
	 */
	function enable_dashicons() {
		wp_enqueue_style( 'dashicons' );
	}


	/**
	 * Shortcode: Get plugin data from wp.org
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts  An array shortcode attributes
	 */
	public function shortcode( $atts ) {
	
		$atts = shortcode_atts( array( 
			'type'		 => 'single',
			'slug' 	  	 => '',
			'field'      => 'active_installs', 
			'before'	 => '',
			'after'		 => '',
			'cache_time' => 43200,
		), $atts );
	
		// The list of currently allowed fields
		$allowed_fields = array( 
			'single' => array( 
				'active_installs',
				'downloaded',
				'name',
				'slug',
				'version',
				'author',
				'author_profile',
				'contributors',
				'requires',
				'tested',
				//'compatibility',
				'rating',
				'five_rating',
				'star_rating',
				'num_ratings',
				//'ratings',
				'last_updated',
				'added',
				'homepage',
				'description',
				'installation',
				'screenshots',
				'changelog',
				'faq',
				'short_description',
				'download_link',
				'support_link',
				'tags',
				'donate_link',
			),
			'aggregate' => array( 
				'active_installs',
				'downloaded'
			)
		);
	
		// Return early is an incorrect field is passed
		if ( ! in_array( $atts['field'], $allowed_fields[ $atts['type'] ] ) ) {
			return;
		}
		
		
		if ( $atts['type'] == 'single' ) {
		
			// Get the plugin data if it has already been stored as a transient
			$plugin_data = get_transient( 'eps_' . esc_attr( $atts['slug'] ) );
		
			// If there is no transient, get the plugin data from wp.org
			if ( ! $plugin_data ) {

				$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . esc_attr( $atts['slug'] ) . '.json?fields=active_installs' );
		
				if ( is_wp_error( $response ) ) {
					return;
				} else {
					$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );
			
					// If someone typed in the plugin slug incorrectly, the body will return null
					if ( ! empty( $plugin_data ) ) {
						$cache_time  = is_int( $atts['cache_time'] ) ? $atts['cache_time'] : 43200; 
						set_transient( 'eps_' . esc_attr( $atts['slug'] ), $plugin_data, $cache_time );
					} else {
						return;
					}
				}
			}
	
			$final_output = html_entity_decode( $atts['before'] ) . $this->field_output( $atts, $plugin_data ) . html_entity_decode( $atts['after'] );
	
			return wp_kses_post( $final_output );
		
		} else if ( $atts['type'] == 'aggregate' ) {
			
			$data = array();

			$cleaned_slugs = preg_replace( '/[^\w\-\s]/', ' ', $atts['slug'] ); // remove all characters that are not allowed
			$cleaned_slugs = preg_replace( '/\s\s+/', ' ', $cleaned_slugs ); // trim all excess whitepace
	
			$slugs = explode( ' ', $cleaned_slugs );
	
			foreach ( $slugs as $slug ) {
		
				// Get the plugin data if it has already been stored as a transient
				$plugin_data = get_transient( 'eps_' . $slug );
		
				// If there is no transient, get the plugin data from wp.org
				if ( ! $plugin_data ) {

					$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . $slug . '.json?fields=active_installs' );
		
					if ( is_wp_error( $response ) ) {
						continue;
					} else {
						$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );
			
						// If someone typed in the plugin slug incorrectly, the body will return null
						if ( ! empty( $plugin_data ) ) {
							$cache_time  = is_int( $atts['cache_time'] ) ? $atts['cache_time'] : 43200; 
							set_transient( 'eps_' . esc_attr( $slug ), $plugin_data, $cache_time );
						} else {
							continue;
						}
					}
				}
		
				$data[] = $this->field_output( $atts, $plugin_data );
			}
	
			return number_format( array_sum( $data ) );
		}
	}
	
	
	/**
	 * Helper function for generating all field output
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts         An array shortcode attributes
	 * @param array $plugin_data  An array of all retrived plugin data from wp.org
	 */
	public function field_output( $atts, $plugin_data ) {
	
		// Generate the shortcode output, some fields need special handling
		switch ( $atts['field'] ) {
			case 'contributors':
				$contributors = (array) $plugin_data[ 'contributors' ];
		
				if ( ! empty ( $contributors ) ) {
					foreach ( $contributors as $contributor => $link ) {
						$output[] = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_attr( $contributor ) . '</a>';
					}
					$output = implode( ', ', $output );
				}
				break;
			case 'five_rating':
				$rating = $plugin_data[ 'rating' ];
		
				if ( ! empty ( $rating ) ) {
					$output = ( $rating / 100 ) * 5;
				}
				break;
			case 'star_rating':
				$rating = $plugin_data[ 'rating' ];
		
				if ( ! empty ( $rating ) ) {
					$five_rating = ( $rating / 100 ) * 5;
			
					$output = '<span class="eps-star-rating" title="' . $five_rating . " " . __( 'out of 5 stars', 'eps' ) . '">';
			
					if ( $rating < 5 ) {
						$stars = array( 0,0,0,0,0 );
					} else if ( $rating >= 5 && $rating < 15 ) {
						$stars = array( 5,0,0,0,0 );
					} else if ( $rating >= 15 && $rating < 25 ) {
						$stars = array( 1,0,0,0,0 );
					} else if ( $rating >= 25 && $rating < 35 ) {
						$stars = array( 1,5,0,0,0 );
					} else if ( $rating >= 35 && $rating < 45 ) {
						$stars = array( 1,1,0,0,0 );
					} else if ( $rating >= 45 && $rating < 55 ) {
						$stars = array( 1,1,5,0,0 );
					} else if ( $rating >= 55 && $rating < 65 ) {
						$stars = array( 1,1,1,0,0 );
					} else if ( $rating >= 65 && $rating < 75 ) {
						$stars = array( 1,1,1,5,0 );
					} else if ( $rating >= 75 && $rating < 85 ) {
						$stars = array( 1,1,1,1,0 );
					} else if ( $rating >= 85 && $rating < 95 ) {
						$stars = array( 1,1,1,1,5 );
					} else if ( $rating >= 95 ) {
						$stars = array( 1,1,1,1,1 );
					}
			
					foreach( $stars as $star ) {
						if ( $star == 0 ) {
							$output .= '<span class="dashicons dashicons-star-empty"></span>';
						} else if ( $star == 5 ) {
							$output .= '<span class="dashicons dashicons-star-half"></span>';
						} else if ( $star == 1 ) {
							$output .= '<span class="dashicons dashicons-star-filled"></span>';
						}
					}
			
					$output .= '</span>';
				}	
				break;
			case 'description':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['description'];
				break;
			case 'installation':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['installation'];
				break;
			case 'screenshots':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['screenshots'];
				break;
			case 'changelog':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['changelog'];
				break;
			case 'faq':
				$sections = (array) $plugin_data['sections'];
				$output   = $sections['faq'];
				break;
			case 'support_link':
				$slug = $plugin_data[ 'slug' ];
				$output = 'https://wordpress.org/support/plugin/' . $slug;
				break;
			case 'tags':
				$tags = (array) $plugin_data[ 'tags' ];

				if ( ! empty( $tags ) ) {
					$output = implode( ', ', $tags );
				}
				break;
			default:
				$output = $plugin_data[ $atts['field'] ];
		}
		
		return $output;
	}
	
	
	/**
	 * Adds additional links to the plugin row meta links
	 *
	 * @since 1.0.0
	 *
	 * @param array $links   Already defined meta links
	 * @param string $file   Plugin file path and name being processed
	 * @return array $links  The new array of meta links
	 */
	public function plugin_row_meta( $links, $file ) {

		// If we are not on the correct plugin, abort
		if ( $file != 'easy-plugin-stats/easy-plugin-stats.php' ) {
			return $links;
		}

		$docs_link = esc_url( add_query_arg( array(
				'utm_source'   => 'eps',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'eps_links',
				'utm_content'  => 'plugins-page-link'
			), 'http://www.nickdiego.com/plugins/easy-plugin-stats' )
		);

		$new_links = array(
			'<a href="' . $docs_link . '" target="_blank">' . esc_html__( 'Documentation', 'easy-plugin-stats' ) . '</a>',
		);

		$links = array_merge( $links, $new_links );

		return $links;
	}

}

new Easy_Plugin_Stats();
