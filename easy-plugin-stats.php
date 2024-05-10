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
		register_block_type( 
			__DIR__ . '/build/blocks/plugin-stats',
			array(
				'render_callback' => array( $this, 'render_block_outermost_plugin_stats' ),
			)
		);

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
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_scripts' ) );		
		add_shortcode( 'eps', array( $this, 'shortcode' ) );
		add_action( 'wp_head', array( $this, 'shortcode_inline_styles' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Renders the `outermost/plugin-stats` block.
	 *
	 * @since 2.0.0
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $content    The saved content.
	 * @param WP_Block $block      The parsed block.
	 */
	public function render_block_outermost_plugin_stats( $attributes, $content, $block ) {
		
		// Bail if there no plugin slugs.
		if ( ! ( $attributes['slugs'] ?? null ) ) {
			return null;
		}
		
		$slugs       = $attributes['slugs'];
		$field       = $attributes['field'] ?? 'homepage_link';
		$cache       = $attributes['cache'] ?? null;
		$prefix      = $attributes['prefix'] ?? null;
		$suffix      = $attributes['suffix'] ?? null;
		$link_text   = $attributes['link_text'] ?? null;
		$link_target = $attributes['link_target'] ?? null;
		$text_align  = $attributes['textAlign'] ?? null;

		$classes = array( 'field-' . $field );
		if ( $text_align ) {
			$classes[] = 'has-text-align-' . $text_align;
		}
		if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
			$classes[] = 'has-link-color';
		}

		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

		$prefix_output = '';
		if ( $prefix ) {
			$prefix_output .= '<span class="wp-block-post-terms__prefix">' . $prefix . '</span>';
		}

		$suffix_output = '';
		if ( $suffix ) {
			$suffix_output = '<span class="wp-block-post-terms__suffix">' . $suffix . '</span>';
		}

		$field_data = '';

		if ( 1 === count( $slugs ) ) {
			$plugin_data = $this->get_remote_plugin_data( $slugs[0], $cache );
			$field_data  = $this->field_output( $attributes, $plugin_data );
		} else if ( count( $slugs ) > 1 ) {
			$aggregate_data = array();

			foreach ( $slugs as $slug ) {
				$plugin_data      = $this->get_remote_plugin_data( $slug, $cache );
				$aggregate_data[] = $this->field_output( $attributes, $plugin_data, false, false );
			}

			$field_data = number_format( array_sum( $aggregate_data ), $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] );
		}

		$output  = $prefix_output;
		$output .= $field_data;
		$output .= $suffix_output;
		
		$html  = '<div ' . $wrapper_attributes . '>';
		$html .= $field === 'star_rating' ? $output : wp_kses_post( $output );
		$html .= '</div>';

		return $html;
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

		return $this->field_output( $source_args, $plugin_data, true, false );
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

	public function shortcode_inline_styles() {

		// Check if the shortcode exists
		if ( shortcode_exists( 'eps' ) ) {

			$shortcode_css = '
				.star-rating-container {
					display: inline-flex;
					align-items: center;
					gap: 0.4em;
				}
				.star-rating-container svg {
					margin-inline-start: -0.8em;
					fill: currentColor;
					width: 2em;
				}
				.star-rating-container svg:first-child {
					margin-inline-start: 0;
				}
			';
	
			// Output the inline styles directly in the HTML head section
			echo '<style id="eps-shortcode-styles" type="text/css">' . $shortcode_css . '</style>';
		}
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
			$output .= $this->field_output( $atts, $plugin_data, true, false );
			$output .= html_entity_decode( $atts['after'] );
	
			return $output;
		
		} else if ( $atts['type'] == 'aggregate' ) {
			$field_data    = array();
			$cleaned_slugs = preg_replace( '/[^\w\-\s]/', ' ', $atts['slug'] ); // remove all characters that are not allowed
			$cleaned_slugs = preg_replace( '/\s\s+/', ' ', $cleaned_slugs ); // trim all excess whitepace
			$slugs         = explode( ' ', $cleaned_slugs );
	
			foreach ( $slugs as $slug ) {
				$plugin_data  = $this->get_remote_plugin_data( $slug, $atts['cache_time'] );
				$field_data[] = $this->field_output( $atts, $plugin_data, false, false );
			}
	
			$output  = html_entity_decode( $atts['before'] );
			$output .= number_format( array_sum( $field_data ), $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] );
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
	public function field_output( $atts, $plugin_data, $single = true, $block = true ) {

		if ( ! ( $plugin_data['slug'] ?? null ) ) {
			return null;
		}

		$is_single = $single || ( isset( $atts['type'] ) && $atts['type'] === 'single' );
		$slug      = $plugin_data['slug'];
		$rating    = $plugin_data['rating'] ?? '';
		$sections  = (array) ( $plugin_data['sections'] ?? [] );
	
		// Generate the shortcode output, some fields need special handling
		switch ( $atts['field'] ) {
			case 'active_installs':
			case 'downloaded':
				$output = isset( $plugin_data[ $atts['field'] ] )
					? $is_single
						? number_format( $plugin_data[ $atts['field'] ], $this->number_format['decimals'], $this->number_format['dec_point'], $this->number_format['thousands_sep'] )
						: $plugin_data[ $atts['field'] ]
					: '';
				break;
			case 'contributors':
				$contributors = (array) $plugin_data[ 'contributors' ];
				$output       = '';
				if ( ! empty( $contributors ) ) {
					foreach ( $contributors as $contributor => $link ) {
						$output[] = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_attr( $contributor ) . '</a>';
					}
					$output = implode( ', ', $output );
				}
				break;
			case 'five_rating':
				$output = ! empty( $rating ) ? ( $rating / 100 ) * 5 : '';
				break;
			case 'star_rating':
				if ( ! empty( $rating ) ) {
					$five_rating = ( $rating / 100 ) * 5;
					$output      = '<span class="star-rating-container" title="' . $five_rating . ' ' . __( 'out of 5 stars', 'easy-plugin-stats' ) . '">';
					$filledStars = floor( $five_rating / 1 );
					$halfStar    = round( $five_rating - $filledStars, 1 );
					$star_empty  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z" clip-rule="evenodd"></path></svg>';
					$star_filled = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.072-.222l-3.348-3.263a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"></path></svg>';
					$star_half   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222l-3.348-3.263a.25.25 0 01.139-.426l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"></path></svg>';
					foreach ( range( 0, 4 ) as $i ) {
						if ( $i < $filledStars ) {
							$output .= $star_filled;
						} elseif ( $i === $filledStars && $halfStar >= 0.5 ) {
							$output .= $star_half;
						} else {
							$output .= $star_empty;
						}
					}
					$output .= '</span>';
				}
				break;
			case 'last_updated':
				$date   = date_create( $plugin_data['last_updated'] );
				$output = date_format( $date, $date_format );
				break;
			case 'tags':
				$tags = (array) $plugin_data['tags'];
				$output = ! empty( $tags ) ? implode( ', ', $tags ) : '';
				break;
			case 'description':
			case 'installation':
			case 'screenshots':
			case 'changelog':
			case 'faq':
				$output = $sections[ $atts['field'] ] ?? '';
				break;
			// @TODO the following needs refactoring.
			case 'homepage_link':
				$link   = 'https://wordpress.org/plugins/' . $slug;
				$output = ! $block ? $link : $this->formatLink( $atts, $link, __( 'Plugin Homepage', 'easy-plugin-stats' ) );
				break;
			case 'download_link':
				$link   = $plugin_data[ $atts['field'] ] ?? '';
				$output = ( $block && $link ) ? $this->formatLink( $atts, $link, __( 'Download', 'easy-plugin-stats' ) ) : $link;
				break;
			case 'live_preview_link':
				$link   = 'https://playground.wordpress.net/?plugin=' . $slug . '&blueprint-url=https://wordpress.org/plugins/wp-json/plugins/v1/plugin/' . $slug . '/blueprint.json';
				$output = ! $block ? $link : $this->formatLink( $atts, $link, __( 'Live Preview', 'easy-plugin-stats' ) );
				break;
			case 'support_link':
				$link   = 'https://wordpress.org/support/plugin/' . $slug;
				$output = ! $block ? $link : $this->formatLink( $atts, $link, __( 'Support', 'easy-plugin-stats' ) );
				break;
			case 'reviews_link':
				$link      = 'https://wordpress.org/support/plugin/' . $slug . '/reviews';
				$output = ! $block ? $link : $this->formatLink( $atts, $link, __( 'Reviews', 'easy-plugin-stats' ) );
				break;
			case 'author_profile':
				$link   = $plugin_data[ $atts['field'] ] ?? '';
				$output = ( $block && $link ) ? $this->formatLink( $atts, $link, __( 'Author Profile', 'easy-plugin-stats' ) ) : $link;
				break;
			case 'donate_link':
				$link   = $plugin_data[ $atts['field'] ] ?? '';
				$output = ( $block && $link ) ? $this->formatLink( $atts, $link, __( 'Donate', 'easy-plugin-stats' ) ) : $link;
				break;
			default:
				$output = $plugin_data[ $atts['field'] ] ?? '';
		}
		
		return $output;
	}

	/**
	 * Formats a hyperlink with provided attributes.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $atts         Array of attributes for the hyperlink.
	 * @param string $link         The URL of the hyperlink.
	 * @param string $text_default The default text for the hyperlink if not provided in the attributes.
	 * @return string              The formatted hyperlink HTML.
	 */
	public function formatLink( $atts, $link, $text_default ) {
		$output  = '<a href="' . $link . '"';
		$output .= $atts['linkTarget'] ? ' target="_blank">' : '>';
		$output .= $atts['linkText'] ? $atts['linkText'] : $text_default;
		$output .= '</a>';
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