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
 * @since 2.0.0
 * @since 2.0.2 Block attributes are sanitized on input and escaped on output.
 *
 * @param array $attributes Block attributes.
 * @return string|null The block content.
 */
function render_block_outermost_plugin_stats( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();

	if ( empty( $attributes['slugs'] ) ) {
		return null;
	}

	// Sanitize every slug and drop any that are empty afterwards.
	$slugs = array_values( array_filter( array_map( __NAMESPACE__ . '\sanitize_plugin_slug', (array) $attributes['slugs'] ) ) );
	if ( empty( $slugs ) ) {
		return null;
	}

	$field = $attributes['field'] ?? 'homepage_link';
	if ( ! is_allowed_field( $field, count( $slugs ) === 1 ? 'single' : 'aggregate' ) ) {
		return null;
	}

	$cache        = isset( $attributes['cache'] ) ? sanitize_cache_time( $attributes['cache'] ) : null;
	$prefix       = isset( $attributes['prefix'] ) && is_scalar( $attributes['prefix'] ) ? (string) $attributes['prefix'] : '';
	$suffix       = isset( $attributes['suffix'] ) && is_scalar( $attributes['suffix'] ) ? (string) $attributes['suffix'] : '';
	$text_align   = isset( $attributes['textAlign'] ) && in_array( $attributes['textAlign'], array( 'left', 'center', 'right' ), true ) ? $attributes['textAlign'] : '';
	$prefix_color = isset( $attributes['prefixColor'] ) && is_scalar( $attributes['prefixColor'] ) ? (string) $attributes['prefixColor'] : '';
	$suffix_color = isset( $attributes['suffixColor'] ) && is_scalar( $attributes['suffixColor'] ) ? (string) $attributes['suffixColor'] : '';

	$classes = array( 'field-' . sanitize_html_class( $field ) );
	if ( $text_align ) {
		$classes[] = 'has-text-align-' . $text_align;
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	// @TODO Ideally the color should be set as a class if a theme color is chosen, not the hardcoded hex value.
	$prefix_output = $prefix ? '<span class="wp-block-post-terms__prefix"' . get_color_style_attribute( $prefix_color ) . '>' . esc_html( $prefix ) . '</span>' : '';
	$suffix_output = $suffix ? '<span class="wp-block-post-terms__suffix"' . get_color_style_attribute( $suffix_color ) . '>' . esc_html( $suffix ) . '</span>' : '';

	$field_output = '';
	if ( count( $slugs ) === 1 ) {
		$plugin_data  = get_remote_plugin_data( $slugs[0], $cache );
		$field_output = (string) get_field_output( $attributes, $plugin_data );
	} else {
		$aggregate_data = array();
		foreach ( $slugs as $slug ) {
			$plugin_data      = get_remote_plugin_data( $slug, $cache );
			$aggregate_data[] = (float) get_field_output( $attributes, $plugin_data, false, false );
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

	// The star rating field is the only one that produces SVG markup, so it
	// gets the extended allowlist. Everything else uses the post allowlist.
	$allowed_html = 'star_rating' === $field ? get_allowed_output_html() : wp_kses_allowed_html( 'post' );

	$html  = '<p ' . $wrapper_attributes . '><span class="wp-block-outermost-plugin-stats__container">';
	$html .= wp_kses( $output, $allowed_html );
	$html .= '</span></p>';

	return $html;
}

/**
 * Builds a safe inline style attribute for a single CSS color value.
 *
 * The value comes from a block attribute, so it is validated as CSS before it
 * is escaped for the attribute context. An empty string is returned when the
 * value is empty or is not a valid color declaration.
 *
 * @since 2.0.2
 *
 * @param string $color The raw color value.
 * @return string       Either an empty string or ` style="color:..."`.
 */
function get_color_style_attribute( $color ) {
	$color = trim( (string) $color );
	if ( '' === $color ) {
		return '';
	}

	$css = safecss_filter_attr( 'color:' . $color );
	if ( '' === $css ) {
		return '';
	}

	return ' style="' . esc_attr( $css ) . '"';
}
