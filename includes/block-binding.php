<?php
/**
 * Handles block binding callback for the Button block variation.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Retrieves plugin data based on the provided source arguments and generates field output (links)
 * using the fetched data. The returned string is bound to the Button block URL.
 *
 * @since 2.0.0
 *
 * @param array $source_args An array containing source arguments such as 'slug', 'field', and 'cache'.
 * @return string|null       The field output generated based on the plugin data, or null if plugin slugs are not provided.
 */
function bindings_callback( $source_args ) {
	
	// Bail if there is no plugin slug.
	if ( ! ( $source_args['slug'] ?? null ) ) {
		return null;
	}

	// Fetch the plugin data.
	$plugin_data = get_remote_plugin_data( $source_args['slug'], $source_args['cache'] ?? null );

	return get_field_output( $source_args, $plugin_data, true, false );
}