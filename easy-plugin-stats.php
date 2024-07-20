<?php
/**
 * Plugin Name:       Easy Plugin Stats
 * Description:       Easily display stats associated with plugins hosted on WordPress.org.
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Version:           2.0.1
 * Author:            Nick Diego
 * Author URI:        https://www.nickdiego.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-plugin-stats
 *
 * @package           EasyPluginStats
 */

namespace EasyPluginStats;

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and block bindings.
 */
function register_plugin_stats_block_and_block_bindings() {
	register_block_type(
		__DIR__ . '/build/blocks/plugin-stats',
		array(
			'render_callback' => __NAMESPACE__ . '\render_block_outermost_plugin_stats',
		)
	);

	register_block_bindings_source(
		'easy-plugin-stats/button',
		array(
			'label'              => __( 'Plugin Button Binding', 'easy-plugin-stats' ),
			'get_value_callback' => __NAMESPACE__ . '\bindings_callback',
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\register_plugin_stats_block_and_block_bindings' );

/**
 * Enqueue Editor assets.
 */
function enqueue_block_editor_assets() {
	$asset_file  = include plugin_dir_path( __FILE__ ) . 'build/editor/index.asset.php';

	wp_enqueue_script(
		'easy-plugin-stats-editor-scripts',
		plugin_dir_url( __FILE__ ) . 'build/editor/index.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);

    wp_set_script_translations(
        'easy-plugin-stats-editor-scripts',
        'easy-plugin-stats',
        plugin_dir_path( __FILE__ ) . 'languages'
    );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

// Require plugin files.
require_once __DIR__ . '/includes/block.php';
require_once __DIR__ . '/includes/block-binding.php';
require_once __DIR__ . '/includes/shortcode.php';
require_once __DIR__ . '/includes/get-field-output.php';
require_once __DIR__ . '/includes/get-remote-plugin-data.php';
require_once __DIR__ . '/includes/utils.php';
