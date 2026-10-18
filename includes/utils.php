<?php
/**
 * Utility functions.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Formats a number based on provided or default settings.
 *
 * @since 2.0.0
 *
 * @param float|int $data The number to format.
 * @return string         The formatted number.
 */
function format_numbers( $data ) {
	// Apply filters to allow customization of number formatting.
	$number_format = apply_filters( 'eps_number_format', array(
		'decimals'      => 0,
		'dec_point'     => '.',
		'thousands_sep' => ','
	) );

	// Only ever format numeric values. Anything else from the API becomes 0.
	$data = is_numeric( $data ) ? (float) $data : 0;

	// Format the number based on the specified formatting options.
	return number_format(
		$data,
		(int) $number_format['decimals'],
		(string) $number_format['dec_point'],
		(string) $number_format['thousands_sep']
	);
}

/**
 * Returns the list of fields that may be displayed, keyed by output type.
 *
 * Shared by the shortcode, the block, and the block binding so that an
 * unknown field can never reach the output stage.
 *
 * @since 2.0.2
 *
 * @return array<string, string[]> Allowed fields for 'single' and 'aggregate'.
 */
function get_allowed_fields() {
	return array(
		'single'    => array(
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
			'downloaded',
		),
	);
}

/**
 * Checks whether a field name is allowed for the given output type.
 *
 * @since 2.0.2
 *
 * @param mixed  $field The requested field.
 * @param string $type  Either 'single' or 'aggregate'.
 * @return bool
 */
function is_allowed_field( $field, $type = 'single' ) {
	$allowed = get_allowed_fields();
	$type    = array_key_exists( $type, $allowed ) ? $type : 'single';

	return is_string( $field ) && in_array( $field, $allowed[ $type ], true );
}

/**
 * Sanitizes a WordPress.org plugin slug.
 *
 * Plugin slugs on WordPress.org only ever contain lowercase letters, digits,
 * hyphens, and underscores. Anything else is stripped so that the slug is safe
 * to use in a transient key and in the API request URL.
 *
 * @since 2.0.2
 *
 * @param mixed $slug The raw slug.
 * @return string     The sanitized slug, possibly empty.
 */
function sanitize_plugin_slug( $slug ) {
	if ( ! is_scalar( $slug ) ) {
		return '';
	}

	$slug = strtolower( trim( (string) $slug ) );

	return (string) preg_replace( '/[^a-z0-9_\-]/', '', $slug );
}

/**
 * Sanitizes a cache duration (in seconds).
 *
 * @since 2.0.2
 *
 * @param mixed $cache   The raw cache value.
 * @param int   $default The value to use when the input is not a positive integer.
 * @return int
 */
function sanitize_cache_time( $cache, $default = 43200 ) {
	if ( is_numeric( $cache ) && (int) $cache > 0 ) {
		return (int) $cache;
	}

	return (int) $default;
}

/**
 * Returns the HTML allowed in rendered field output.
 *
 * This is the standard post allowlist, extended with the handful of SVG
 * elements and attributes used by the star rating field. No scripts, event
 * handlers, or other active content are permitted.
 *
 * @since 2.0.2
 *
 * @return array
 */
function get_allowed_output_html() {
	$allowed = wp_kses_allowed_html( 'post' );

	$allowed['svg']  = array(
		'xmlns'       => true,
		'viewbox'     => true,
		'aria-hidden' => true,
		'focusable'   => true,
		'class'       => true,
		'width'       => true,
		'height'      => true,
		'fill'        => true,
	);
	$allowed['path'] = array(
		'd'         => true,
		'fill'      => true,
		'fill-rule' => true,
		'clip-rule' => true,
	);

	return $allowed;
}
