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
 *
 * @param string $slug  The slug of the plugin to retrieve data for.
 * @param int    $cache Optional. The time in seconds to cache the plugin data. Default is 43200 seconds (12 hours).
 * @return array|null   An array containing plugin data if retrieved successfully, or null if retrieval fails.
 */
function get_remote_plugin_data( $slug, $cache = 43200 ) {
	// Attempt to get the plugin data from the transient.
	$plugin_data = get_transient( 'eps_' . $slug );

	// If no transient, fetch data from WordPress.org.
	if ( false === $plugin_data ) {
		$response = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.0/' . $slug . '.json?fields=active_installs' );

		if ( ! is_wp_error( $response ) ) {
			$plugin_data = json_decode( wp_remote_retrieve_body( $response ), true );

			// If the response body is not empty, cache the data.
			if ( ! empty( $plugin_data ) ) {
				set_transient( 'eps_' . esc_attr( $slug ), $plugin_data, is_int( $cache ) ? $cache : 43200 );
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	return $plugin_data;
}