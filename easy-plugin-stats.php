<?php
/**
 * Plugin Name:       Easy Plugin Stats
 * Description:       Easily display stats from plugins hosted on WordPress.org.
 * Requires at least: 6.3
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Nick Diego
 * Author URI:        https://www.nickdiego.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-plugin-stats
 *
 * @package           EasyPluginStats
 */

namespace EasyPluginStats;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block and block bindings.
 */
function register_plugin_stats_block_and_block_bindings() {
	register_block_type(
		__DIR__ . '/build/blocks/plugin-stats',
		array(
			'render_callback' => __NAMESPACE__ . '\render_block_outermost_plugin_stats',
		)
	);

	register_block_bindings_source(
		'easy-plugin-stats/button',
		array(
			'label'              => __( 'Plugin Link Binding', 'easy-plugin-stats' ),
			'get_value_callback' => __NAMESPACE__ . '\bindings_callback',
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\register_plugin_stats_block_and_block_bindings' );

/**
 * Renders the plugin stats block.
 *
 * @param array $attributes Block attributes.
 * @return string|null The block content.
 */
function render_block_outermost_plugin_stats( $attributes ) {
	if ( ! isset( $attributes['slugs'] ) || empty( $attributes['slugs'] ) ) {
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

	$prefix_output = $prefix ? '<span class="wp-block-post-terms__prefix">' . esc_html( $prefix ) . '</span>' : '';
	$suffix_output = $suffix ? '<span class="wp-block-post-terms__suffix">' . esc_html( $suffix ) . '</span>' : '';

	$field_output = '';
	if ( count( $slugs ) === 1 ) {
		$plugin_data  = get_remote_plugin_data( $slugs[0], $cache );
		$field_output = get_field_output( $attributes, $plugin_data );
	} else {
		$aggregate_data = array();
		foreach ( $slugs as $slug ) {
			$plugin_data      = get_remote_plugin_data( $slug, $cache );
			$aggregate_data[] = get_field_output( $attributes, $plugin_data, false, false );
		}
		$field_output = format_numbers( $aggregate_data );
	}

	$output  = $prefix_output;
	$output .= $field_output;
	$output .= $suffix_output;

	$html  = '<div ' . $wrapper_attributes . '>';
	$html .= $field === 'star_rating' ? $output : wp_kses_post( $output );
	$html .= '</div>';

	return $html;
}

/**
 * Adds additional links to the plugin row meta.
 *
 * @since 1.0.0
 *
 * @param array  $links Already defined meta links.
 * @param string $file  Plugin file path and name being processed.
 * @return array $links The new array of meta links.
 */
function add_plugin_row_meta( $links, $file ) {
	if ( 'easy-plugin-stats/easy-plugin-stats.php' !== $file ) {
		return $links;
	}

	$docs_link = esc_url( add_query_arg(
		array(
			'utm_source'   => 'eps',
			'utm_medium'   => 'plugin',
			'utm_campaign' => 'eps_links',
			'utm_content'  => 'plugins-page-link',
		),
		'https://www.nickdiego.com/plugins/easy-plugin-stats'
	) );

	$new_links = array(
		'<a href="' . $docs_link . '" target="_blank">' . esc_html__( 'Documentation', 'easy-plugin-stats' ) . '</a>',
	);

	return array_merge( $links, $new_links );
}
add_filter( 'plugin_row_meta', __NAMESPACE__ . '\add_plugin_row_meta', 10, 2 );

// Require plugin files.
require_once __DIR__ . '/includes/get-field-output.php';
require_once __DIR__ . '/includes/get-remote-plugin-data.php';
require_once __DIR__ . '/includes/shortcode.php';
require_once __DIR__ . '/includes/block-bindings.php';
require_once __DIR__ . '/includes/utils.php';


// add_action( 'wp_loaded', array( $this, 'init') );
// load_plugin_textdomain( 'easy-plugin-stats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
