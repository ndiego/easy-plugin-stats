<?php
/**
 * Renders the `eps` shortcode and adds inline styles for the shortcode.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Shortcode: Get plugin data from wp.org
 *
 * @since 1.0.0
 * @since 2.0.2 All attributes are sanitized on input and escaped on output.
 *
 * @param array $atts An array shortcode attributes.
 */
function render_shortcode( $atts ) {

	// Prevent shortcodes from firing in the Editor.
	// This is a known issue, see https://github.com/WordPress/gutenberg/issues/45732.
	if ( is_admin() ) {
		return;
	}

	$atts = shortcode_atts( array(
		'type'       => 'single',
		'slug'       => '',
		'field'      => 'active_installs',
		'before'     => '',
		'after'      => '',
		'cache_time' => 43200,
	), $atts, 'eps' );

	// Confirm that the provided field type exists. Default to single.
	$field_type = array_key_exists( $atts['type'], get_allowed_fields() ) ? $atts['type'] : 'single';

	// Return early if an incorrect field is passed.
	if ( ! is_allowed_field( $atts['field'], $field_type ) ) {
		return;
	}

	$cache_time = sanitize_cache_time( $atts['cache_time'] );

	// The before/after attributes accept limited HTML. Shortcode attributes
	// cannot contain double quotes, so authors entity-encode markup; decode it
	// and then run it through the post allowlist so that no script, event
	// handler, or other active content can be injected.
	$before = wp_kses_post( html_entity_decode( (string) $atts['before'], ENT_QUOTES, 'UTF-8' ) );
	$after  = wp_kses_post( html_entity_decode( (string) $atts['after'], ENT_QUOTES, 'UTF-8' ) );

	if ( 'single' === $field_type ) {
		$plugin_data = get_remote_plugin_data( $atts['slug'], $cache_time );

		$output  = $before;
		$output .= (string) get_field_output( $atts, $plugin_data, true, false );
		$output .= $after;

		return $output;

	} elseif ( 'aggregate' === $field_type ) {
		$field_data = array();
		$slugs      = preg_split( '/\s+/', (string) $atts['slug'], -1, PREG_SPLIT_NO_EMPTY );

		foreach ( $slugs as $slug ) {
			$slug = sanitize_plugin_slug( $slug );
			if ( '' === $slug ) {
				continue;
			}
			$plugin_data  = get_remote_plugin_data( $slug, $cache_time );
			$field_data[] = (float) get_field_output( $atts, $plugin_data, false, false );
		}

		$output  = $before;
		$output .= format_numbers( array_sum( $field_data ) );
		$output .= $after;

		return $output;
	}
}
add_shortcode( 'eps', __NAMESPACE__ . '\render_shortcode' );

/**
 * Outputs inline styles for the shortcode if it exists.
 *
 * @since 2.0.0
 */
function render_shortcode_inline_styles() {

	// Check if the shortcode exists.
	if ( shortcode_exists( 'eps' ) ) {

		$shortcode_css = '
			.star-rating__container {
				display: inline-flex;
				align-items: center;
				gap: 0.4em;
			}
			.star-rating__container svg {
				margin-inline-start: -0.9em;
				fill: currentColor;
				width: 2em;
			}
			.star-rating__container svg:first-child {
				margin-inline-start: 0;
			}
		';

		// Output the inline styles directly in the HTML head section. The CSS is a static string defined above.
		echo '<style id="eps-shortcode-styles">' . $shortcode_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', __NAMESPACE__ . '\render_shortcode_inline_styles' );
