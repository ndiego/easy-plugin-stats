<?php
/**
 * Plugin Name:       Easy Plugin Stats
 * Description:       Easily display stats from plugins hosted on WordPress.org
 * Requires at least: 6.3
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Nick Diego
 * Author URI:        https://www.nickdiego.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-plugin-stats
 * 
 * @package           Easy Plugin Stats
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Easy_Plugin_Stats {

	// Define class properties for number format and date format.
    private $number_format;
    private $date_format;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		add_action( 'init', array( $this, 'editor_init' ) );
		add_action( 'wp_loaded', array( $this, 'init') );

		// Initialize class properties with default values using apply_filters
        $this->number_format = apply_filters( 'eps_number_format', array(
            'decimals'      => 0,
            'dec_point'     => '.',
            'thousands_sep' => ','
        ) );

        $this->date_format = apply_filters( 'eps_date_format', 'n/j/y' );
	}

	/**
	 * Registers blocks and bindings.
	 *
	 * @since 2.0.0
	 */
	public function editor_init() {
		register_block_type( __DIR__ . '/build/blocks/plugin-stats' );

		register_block_bindings_source( 'easy-plugin-stats/button', array(
			'label'              => __( 'Plugin Link Binding', 'easy-plugin-stats' ),
			'get_value_callback' => array( $this, 'bindings_callback' ),
		) );
	}

	/**
	 * Initialize the rest of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		
		load_plugin_textdomain( 'easy-plugin-stats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_shortcode( 'eps', array( $this, 'shortcode' ) );

		add_action( 'admin_init', array( $this, 'add_tinymce_button' ) );		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enable_dashicons' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_scripts' ) );
		
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Retrieves plugin data based on the provided source arguments and generates field output (links)
	 * using the fetched data. The returned string is bound to the Button block URL.
	 *
	 * @since 2.0.0
	 * 
	 * @param array $source_args An array containing source arguments such as 'slug', 'field', and 'cache'.
	 * @return string|null       The field output generated based on the plugin data, or null if plugin slugs are not provided.
	 */
	public function bindings_callback( $source_args ) {

		// Bail if there no plugin slugs.
		if ( ! ( $source_args['slug'] ?? null ) ) {
			return null;
		}

		$slug  = $source_args['slug'];
		$field = $source_args['field'] ?? 'homepage_link';
		$cache = $source_args['cache'] ?? null;
	
		// Fetch the plugin data.
		$plugin_data = $this->get_remote_plugin_data( $slug, $cache );

		return $this->field_output( $source_args, $plugin_data );
	}

	public function enqueue_editor_scripts() {

		$script_asset = include dirname( __FILE__ ) . '/build/editor/index.asset.php';
	
		wp_enqueue_script(
			'easy-plugin-stats-editor-scripts',
			plugin_dir_url( __FILE__ ) . 'build/editor/index.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
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
				'rating',
				'five_rating',
				'star_rating',
				'num_ratings',
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
			$plugin_data = $this->get_remote_plugin_data( $atts['slug'], $atts['cache_time'] );

			$output  = html_entity_decode( $atts['before'] );
			$output .= $this->field_output( $atts, $plugin_data );
			$output .= html_entity_decode( $atts['after'] );
	
			return $output;
		
		} else if ( $atts['type'] == 'aggregate' ) {
			$data          = array();
			$cleaned_slugs = preg_replace( '/[^\w\-\s]/', ' ', $atts['slug'] ); // remove all characters that are not allowed
			$cleaned_slugs = preg_replace( '/\s\s+/', ' ', $cleaned_slugs ); // trim all excess whitepace
			$slugs         = explode( ' ', $cleaned_slugs );
	
			foreach ( $slugs as $slug ) {
				$plugin_data = $this->get_remote_plugin_data( $slug, $atts['cache_time'] );
				$data[]      = $this->field_output( $atts, $plugin_data );
			}
	
			$output  = html_entity_decode( $atts['before'] );
			$output .= number_format( array_sum( $data ), $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] );
			$output .= html_entity_decode( $atts['after'] );
	
			return $output;
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

		if ( ! $plugin_data || ! isset( $plugin_data[ 'slug' ] ) ) {
			return null;
		}

		$slug     = $plugin_data[ 'slug' ];
		$rating   = isset( $plugin_data[ 'rating' ] ) ? $plugin_data[ 'rating' ] : '';
		$sections = isset( $plugin_data['sections'] ) ? (array) $plugin_data['sections'] : array();
	
		// Generate the shortcode output, some fields need special handling
		switch ( $atts['field'] ) {
			case 'active_installs':
				$output = ( $atts['type'] == 'single' ) ? number_format( $plugin_data[ 'active_installs' ], $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] ) : $plugin_data[ 'active_installs' ];
				break;
			case 'downloaded':
				$output = ( $atts['type'] == 'single' ) ? number_format( $plugin_data[ 'downloaded' ], $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] ) : $plugin_data[ 'downloaded' ];
				break;
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
				if ( ! empty ( $rating ) ) {
					$output = ( $rating / 100 ) * 5;
				}
				break;
			case 'star_rating':

				// @TODO update to use SVGs and not Dashicons.
				if ( ! empty( $rating ) ) {
					$five_rating = ( $rating / 100 ) * 5;
					$output      = '<span class="eps-star-rating" title="' . $five_rating . " " . __( 'out of 5 stars', 'easy-plugin-stats' ) . '">';
					$stars       = array_fill( 0, 5, 0 );
					$filledStars = floor( $five_rating / 1 );
					$halfStar    = round( $five_rating - $filledStars, 1 );
				
					for ( $i = 0; $i < $filledStars; $i++ ) {
						$stars[ $i ] = 1;
					}
				
					if ( $halfStar >= 0.5 ) {
						$stars[ $filledStars ] = 5;
					}
				
					foreach ( $stars as $star ) {
						switch ( $star ) {
							case 0:
								$output .= '<span class="dashicons dashicons-star-empty"></span>';
								break;
							case 5:
								$output .= '<span class="dashicons dashicons-star-half"></span>';
								break;
							case 1:
								$output .= '<span class="dashicons dashicons-star-filled"></span>';
								break;
						}
					}
				
					$output .= '</span>';
				}
				break;
			case 'last_updated':
				$date   = date_create( $plugin_data['last_updated'] );
				$output = date_format( $date, $date_format );
				break;
			case 'description':
				$output = $sections['description'];
				break;
			case 'installation':
				$output = $sections['installation'];
				break;
			case 'screenshots':
				$output = $sections['screenshots'];
				break;
			case 'changelog':
				$output = $sections['changelog'];
				break;
			case 'faq':
				$output = $sections['faq'];
				break;
			case 'live_preview_link':
				$output = 'https://playground.wordpress.net/?plugin=' . $slug . '&blueprint-url=https://wordpress.org/plugins/wp-json/plugins/v1/plugin/' . $slug . '/blueprint.json';
				break;
			case 'support_link':
				$output = 'https://wordpress.org/support/plugin/' . $slug;
				break;
			case 'reviews_link':
				$output = 'https://wordpress.org/support/plugin/' . $slug . '/reviews';
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
	 * Retrieves plugin data either from a transient cache or directly from WordPress.org API
	 * if it's not already cached.
	 *
	 * @since 2.0.0
	 * 
	 * @param string $slug  The slug of the plugin to retrieve data for.
	 * @param int    $cache Optional. The time in seconds to cache the plugin data. Default is 43200 seconds (12 hours).
	 * @return array|null   An array containing plugin data if retrieved successfully, or null if retrieval fails.
	 */
	public function get_remote_plugin_data( $slug, $cache ) {

		// Get the plugin data if it has already been stored as a transient.
		$plugin_data = get_transient( 'eps_' . $slug );

		// If there is no transient, get the plugin data from wp.org.
		if ( ! $plugin_data ) {

			// You need to manually include active installs.
			$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . $slug . '.json?fields=active_installs' );

			if ( ! is_wp_error( $response ) ) {
				$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );
	
				// If someone typed in the plugin slug incorrectly, the body will return null.
				if ( ! empty( $plugin_data ) ) {
					$cache_time  = is_int( $cache ) ? $cache : 43200; 
					set_transient( 'eps_' . esc_attr( $slug ), $plugin_data, $cache_time );

					return null;
				}
			}
		}

		return $plugin_data;
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