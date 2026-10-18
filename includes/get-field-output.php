<?php
/**
 * Handles the field output for the block, shortcode, and bindings.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Helper function for generating all field output.
 *
 * Every value that originates from shortcode attributes, block attributes, or
 * the WordPress.org Plugins API is escaped or run through an allowlist here
 * before it is returned.
 *
 * @since 2.0.0
 * @since 2.0.2 Unknown fields return an empty string; all API values are escaped.
 *
 * @param array   $atts         An array shortcode or block attributes.
 * @param array   $plugin_data  An array of all retrieved plugin data from wp.org.
 * @param boolean $single       Is it a single plugin (true) or an aggregate (false).
 * @param boolean $block        Is the output for a block (true) or binding/shortcode (false).
 * @return string|null          The generated field output or null if no plugin slug is found.
 */
function get_field_output( $atts, $plugin_data, $single = true, $block = true ) {

	if ( ! is_array( $plugin_data ) || empty( $plugin_data['slug'] ) || ! is_scalar( $plugin_data['slug'] ) ) {
		return null;
	}

	$atts      = is_array( $atts ) ? $atts : array();
	$is_single = $single || ( isset( $atts['type'] ) && 'single' === $atts['type'] );
	$field     = $atts['field'] ?? '';

	// Never output a field that is not on the allowlist.
	if ( ! is_allowed_field( $field, $is_single ? 'single' : 'aggregate' ) ) {
		return '';
	}

	$slug     = sanitize_plugin_slug( $plugin_data['slug'] );
	$rating   = isset( $plugin_data['rating'] ) && is_numeric( $plugin_data['rating'] ) ? (float) $plugin_data['rating'] : 0;
	$sections = (array) ( $plugin_data['sections'] ?? [] );

	// Define a filterable date format.
	$date_format = apply_filters( 'eps_date_format', 'n/j/y' );

	$default_links = array(
		'homepage_link'     => 'https://wordpress.org/plugins/' . $slug,
		'live_preview_link' => 'https://playground.wordpress.net/?plugin=' . $slug . '&blueprint-url=https://wordpress.org/plugins/wp-json/plugins/v1/plugin/' . $slug . '/blueprint.json',
		'support_link'      => 'https://wordpress.org/support/plugin/' . $slug,
		'reviews_link'      => 'https://wordpress.org/support/plugin/' . $slug . '/reviews',
	);

	$default_link_texts = array(
		'homepage_link'     => __( 'Plugin Homepage', 'easy-plugin-stats' ),
		'download_link'     => __( 'Download', 'easy-plugin-stats' ),
		'live_preview_link' => __( 'Live Preview', 'easy-plugin-stats' ),
		'support_link'      => __( 'Support', 'easy-plugin-stats' ),
		'reviews_link'      => __( 'Reviews', 'easy-plugin-stats' ),
		'author_profile'    => __( 'Author Profile', 'easy-plugin-stats' ),
		'donate_link'       => __( 'Donate', 'easy-plugin-stats' ),
	);

	// Star SVGs.
	$star_filled = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.072-.222l-3.348-3.263a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"></path></svg>';
	$star_half   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222l-3.348-3.263a.25.25 0 01.139-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"></path></svg>';
	$star_empty  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z" clip-rule="evenodd"></path></svg>';

	// Generate the output, some fields need special handling.
	switch ( $field ) {
		case 'active_installs':
		case 'downloaded':
			$value  = isset( $plugin_data[ $field ] ) && is_numeric( $plugin_data[ $field ] ) ? (float) $plugin_data[ $field ] : null;
			$output = '';
			if ( null !== $value ) {
				// Aggregates receive a raw number so they can be summed by the caller.
				$output = $is_single ? format_numbers( $value ) : (string) $value;
			}
			break;
		case 'contributors':
			$contributors = (array) ( $plugin_data['contributors'] ?? [] );
			$output       = '';
			if ( ! empty( $contributors ) ) {
				$output_array = array();
				foreach ( $contributors as $contributor => $link ) {
					if ( ! is_scalar( $link ) ) {
						continue;
					}
					$output_array[] = '<a href="' . esc_url( (string) $link ) . '" target="_blank">' . esc_html( (string) $contributor ) . '</a>';
				}
				$output = implode( ', ', $output_array );
			}
			break;
		case 'five_rating':
			$output = $rating > 0 ? (string) ( ( $rating / 100 ) * 5 ) : '';
			break;
		case 'star_rating':
			$output = '';
			if ( $rating > 0 ) {
				$five_rating = ( $rating / 100 ) * 5;
				/* translators: %s: rating out of 5 */
				$title       = sprintf( __( '%s out of 5 stars', 'easy-plugin-stats' ), $five_rating );
				$output      = '<span class="star-rating__container" title="' . esc_attr( $title ) . '">';
				$filledStars = floor( $five_rating );
				$halfStar    = round( $five_rating - $filledStars, 1 );

				foreach ( range( 0, 4 ) as $i ) {
					if ( $i < $filledStars ) {
						$output .= $star_filled;
					} elseif ( $i === (int) $filledStars && $halfStar >= 0.8 ) {
						$output .= $star_filled;
					} elseif ( $i === (int) $filledStars && $halfStar > 0.2 ) {
						$output .= $star_half;
					} else {
						$output .= $star_empty;
					}
				}
				$output .= '</span>';
			}
			break;
		case 'last_updated':
		case 'added':
			$raw_date = $plugin_data[ $field ] ?? '';
			$date     = is_scalar( $raw_date ) && '' !== $raw_date ? date_create( (string) $raw_date ) : false;
			$output   = $date ? esc_html( date_format( $date, (string) $date_format ) ) : '';
			break;
		case 'tags':
			$tags   = (array) ( $plugin_data['tags'] ?? [] );
			$output = '';
			if ( ! empty( $tags ) ) {
				$output_array = array();
				foreach ( $tags as $tag ) {
					if ( ! is_scalar( $tag ) ) {
						continue;
					}
					$tag_url        = 'https://wordpress.org/plugins/tags/' . sanitize_title_with_dashes( remove_accents( (string) $tag ) );
					$output_array[] = '<a href="' . esc_url( $tag_url ) . '" target="_blank">' . esc_html( (string) $tag ) . '</a>';
				}
				$output = implode( ', ', $output_array );
			}
			break;
		case 'homepage': // Legacy alias; the raw API URL, escaped as a URL.
		case 'homepage_link':
		case 'download_link':
		case 'live_preview_link':
		case 'support_link':
		case 'reviews_link':
		case 'author_profile':
		case 'donate_link':
			$link   = $default_links[ $field ] ?? $plugin_data[ $field ] ?? '';
			$link   = is_scalar( $link ) ? esc_url( (string) $link ) : '';
			$output = '';

			if ( $link ) {
				$default_link_text = $default_link_texts[ $field ] ?? ucfirst( str_replace( '_', ' ', $field ) );
				$output            = $block ? format_link( $atts, $link, $default_link_text ) : $link;
			}
			break;
		case 'description':
		case 'installation':
		case 'screenshots':
		case 'changelog':
		case 'faq':
			// These sections are HTML supplied by the WordPress.org API. Restrict them to the post allowlist.
			$section = $sections[ $field ] ?? '';
			$output  = is_scalar( $section ) ? wp_kses_post( (string) $section ) : '';
			break;
		default:
			$value  = $plugin_data[ $field ] ?? '';
			$output = is_scalar( $value ) ? esc_html( (string) $value ) : '';
	}

	return $output;
}

/**
 * Formats a hyperlink with provided attributes.
 *
 * @since 2.0.0
 *
 * @param array  $atts              Array of attributes for the hyperlink.
 * @param string $link              The URL of the hyperlink.
 * @param string $default_link_text The default text for the hyperlink if not provided in the attributes.
 * @return string                   The formatted hyperlink HTML.
 */
function format_link( $atts, $link, $default_link_text ) {
	$link_text   = isset( $atts['linkText'] ) && is_scalar( $atts['linkText'] ) && '' !== $atts['linkText'] ? (string) $atts['linkText'] : $default_link_text;
	$link_target = ! empty( $atts['linkTarget'] ) ? ' target="_blank" rel="noopener"' : '';

	return '<a href="' . esc_url( $link ) . '"' . $link_target . '>' . esc_html( $link_text ) . '</a>';
}
