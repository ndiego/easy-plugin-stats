<?php
/**
 * Renders the Plugin Stats block.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Renders the Plugin Stats block. 
 * 
 * Here we use a callback function instead of render.php. It makes things 
 * a bit easier to manage since the function uses other utility functions
 * shared by the block binding and shortcode.
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
	$prefixColor = $attributes['prefixColor'] ?? 'currentColor';
	$suffixColor = $attributes['suffixColor'] ?? 'currentColor';

	$classes = array( 'field-' . $field );
	if ( $text_align ) {
		$classes[] = 'has-text-align-' . $text_align;
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	// @TODO Ideally the color should be set as a class if a theme color is chosen, not the hardcoded hex value.
	$prefix_output = $prefix ? '<span class="wp-block-post-terms__prefix" style="color:' . $prefixColor . '">' . esc_html( $prefix ) . '</span>' : '';
	$suffix_output = $suffix ? '<span class="wp-block-post-terms__suffix" style="color:' . $suffixColor . '">' . esc_html( $suffix ) . '</span>' : '';

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
		$field_output = format_numbers( array_sum( $aggregate_data ) );
	}

	// Don't render the block if the field(s) have no output.
	if ( ! $field_output ) {
		return null;
	}

	$output  = $prefix_output;
	$output .= $field_output;
	$output .= $suffix_output;

	$html  = '<p ' . $wrapper_attributes . '><span class="wp-block-outermost-plugin-stats__container">';
	$html .= $field === 'star_rating' ? $output : wp_kses_post( $output );
	$html .= '</span></p>';

	return $html;
}