<?php
/**
 * Fetches the plugin data from WordPress.org and caches it.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Retrieves plugin data either from a transient cache or directly from WordPress.org API
 * if it's not already cached.
 *
 * @since 2.0.0
 * @since 2.0.2 The slug is sanitized and the cache duration is cast to a positive integer.
 *
 * @param string $slug  The slug of the plugin to retrieve data for.
 * @param int    $cache Optional. The time in seconds to cache the plugin data. Default is 43200 seconds (12 hours).
 * @return array|null   An array containing plugin data if retrieved successfully, or null if retrieval fails.
 */
function get_remote_plugin_data( $slug, $cache = 43200 ) {
	$slug = sanitize_plugin_slug( $slug );

	// Bail if there is nothing left after sanitization.
	if ( '' === $slug ) {
		return null;
	}

	$cache         = sanitize_cache_time( $cache );
	$transient_key = 'eps_' . $slug;

	// Attempt to get the plugin data from the transient.
	$plugin_data = get_transient( $transient_key );

	// If no transient, fetch data from WordPress.org.
	if ( false === $plugin_data ) {
		$response = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.0/' . rawurlencode( $slug ) . '.json?fields=active_installs' );

		if ( ! is_wp_error( $response ) ) {
			$plugin_data = json_decode( wp_remote_retrieve_body( $response ), true );

			// If the response body is a non-empty array, cache the data.
			if ( is_array( $plugin_data ) && ! empty( $plugin_data ) ) {
				set_transient( $transient_key, $plugin_data, $cache );
			} else {
				$plugin_data = null;
			}
		} else {
			$plugin_data = null;
		}
	}

	return is_array( $plugin_data ) ? $plugin_data : null;
}
