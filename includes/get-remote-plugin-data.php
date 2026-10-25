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
 * Every successful response is also stored in a long-lived backup. When the
 * API request fails (network error, non-200 status, or an unusable body), the
 * backup is returned so that a transient failure never drops the plugin from
 * the output. Failures are never written to the short-lived cache, so the next
 * request tries the API again.
 *
 * @since 2.0.0
 * @since 2.0.2 The slug is sanitized and the cache duration is cast to a positive integer.
 * @since 2.0.3 Falls back to the last successful response when the API request fails.
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

	if ( is_array( $plugin_data ) && ! empty( $plugin_data ) ) {
		return $plugin_data;
	}

	// No usable transient, fetch data from WordPress.org.
	$plugin_data = fetch_remote_plugin_data( $slug );

	if ( null !== $plugin_data ) {
		set_transient( $transient_key, $plugin_data, $cache );
		set_plugin_data_backup( $slug, $plugin_data );

		return $plugin_data;
	}

	// The request failed. Fall back to the last successful response, if any.
	return get_plugin_data_backup( $slug );
}

/**
 * Requests plugin data from the WordPress.org Plugins API.
 *
 * @since 2.0.3
 *
 * @param string $slug A sanitized plugin slug.
 * @return array|null  The decoded response, or null when the request failed or
 *                     the body did not decode to a non-empty array.
 */
function fetch_remote_plugin_data( $slug ) {
	$response = wp_remote_get(
		'https://api.wordpress.org/plugins/info/1.0/' . rawurlencode( $slug ) . '.json?fields=active_installs',
		array(
			/**
			 * Filters the timeout, in seconds, for requests to the WordPress.org Plugins API.
			 *
			 * @since 2.0.3
			 *
			 * @param int    $timeout The request timeout. Default 15.
			 * @param string $slug    The plugin slug being requested.
			 */
			'timeout' => (int) apply_filters( 'eps_request_timeout', 15, $slug ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return null;
	}

	if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$plugin_data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $plugin_data ) || empty( $plugin_data ) ) {
		return null;
	}

	return $plugin_data;
}

/**
 * Returns the transient key used for the long-lived backup of a plugin's data.
 *
 * @since 2.0.3
 *
 * @param string $slug A sanitized plugin slug.
 * @return string
 */
function get_plugin_data_backup_key( $slug ) {
	return 'eps_backup_' . $slug;
}

/**
 * Stores a successful API response as the long-lived backup for a plugin.
 *
 * @since 2.0.3
 *
 * @param string $slug        A sanitized plugin slug.
 * @param array  $plugin_data The plugin data returned by the API.
 * @return bool               Whether the backup was stored.
 */
function set_plugin_data_backup( $slug, $plugin_data ) {
	if ( ! is_array( $plugin_data ) || empty( $plugin_data ) ) {
		return false;
	}

	/**
	 * Filters how long, in seconds, the backup copy of plugin data is kept.
	 *
	 * The backup is only used when the WordPress.org API request fails. It is
	 * refreshed on every successful request.
	 *
	 * @since 2.0.3
	 *
	 * @param int    $expiration The backup lifetime. Default 30 days.
	 * @param string $slug       The plugin slug.
	 */
	$expiration = sanitize_cache_time( apply_filters( 'eps_backup_cache_time', 30 * DAY_IN_SECONDS, $slug ), 30 * DAY_IN_SECONDS );

	return set_transient( get_plugin_data_backup_key( $slug ), $plugin_data, $expiration );
}

/**
 * Retrieves the long-lived backup of a plugin's data, if one exists.
 *
 * @since 2.0.3
 *
 * @param string $slug A sanitized plugin slug.
 * @return array|null  The backed-up plugin data, or null if there is none.
 */
function get_plugin_data_backup( $slug ) {
	$plugin_data = get_transient( get_plugin_data_backup_key( $slug ) );

	return ( is_array( $plugin_data ) && ! empty( $plugin_data ) ) ? $plugin_data : null;
}
