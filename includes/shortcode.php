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
		'type'		 => 'single',
		'slug' 	  	 => '',
		'field'      => 'active_installs',
		'before'	 => '',
		'after'		 => '',
		'cache_time' => 43200,
	), $atts );

	// The list of currently allowed fields.
	$allowed_fields = array(
		'single' => array(
			'active_installs',
			'downloaded',
			'name',
			'slug',
			'version',
			'author',
			'contributors',
			'tags',
			'requires',
			'tested',
			'rating',
			'five_rating',
			'star_rating',
			'num_ratings',
			'last_updated',
			'added',
			'homepage', // For backward compatibility.
			'homepage_link',
			'download_link',
			'live_preview_link',
			'support_link',
			'reviews_link',
			'author_profile',
			'donate_link',
			'description',
			'installation',
			'screenshots',
			'changelog',
			'faq',
		),
		'aggregate' => array(
			'active_installs',
			'downloaded'
		)
	);

	// Confirm that the provided field type exists. Default to single.
	$field_type = array_key_exists( $atts['type'], $allowed_fields ) ? $atts['type'] : 'single';

	// Return early is an incorrect field is passed.
	if ( ! in_array( $atts['field'], $allowed_fields[ $field_type ] ) ) {
		return;
	}

	if ( $field_type == 'single' ) {
		$plugin_data = get_remote_plugin_data( $atts['slug'], $atts['cache_time'] );

		$output  = html_entity_decode( $atts['before'] );
		$output .= get_field_output( $atts, $plugin_data, true, false );
		$output .= html_entity_decode( $atts['after'] );

		return $output;

	} else if ( $field_type == 'aggregate' ) {
		$field_data    = array();
		$cleaned_slugs = preg_replace( '/[^\w\-\s]/', ' ', $atts['slug'] ); // Remove all characters that are not allowed.
		$cleaned_slugs = preg_replace( '/\s\s+/', ' ', $cleaned_slugs ); // Trim all excess whitepace.
		$slugs         = explode( ' ', $cleaned_slugs );

		foreach ( $slugs as $slug ) {
			$plugin_data  = get_remote_plugin_data( $slug, $atts['cache_time'] );
			$field_data[] = get_field_output( $atts, $plugin_data, false, false );
		}

		$output  = html_entity_decode( $atts['before'] );
		$output .= format_numbers( array_sum( $field_data ) );
		$output .= html_entity_decode( $atts['after'] );

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

		// Output the inline styles directly in the HTML head section.
		echo '<style id="eps-shortcode-styles" type="text/css">' . $shortcode_css . '</style>';
	}
}
add_action( 'wp_head', __NAMESPACE__ . '\render_shortcode_inline_styles' );