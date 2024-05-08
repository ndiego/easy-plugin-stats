/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockVariation } from '@wordpress/blocks';
import { plugins } from '@wordpress/icons';

registerBlockVariation( 'core/button', {
	name: 'easy-plugin-stats/button',
	icon: plugins,
	title: __( 'Plugin Link', 'easy-plugin-stats' ),
	description: __(
		'Display a button that links to resources associated with plugins hosted on WordPress.org.',
		'easy-plugin-stats'
	),
	scope: [ 'inserter', 'transform' ],
	attributes: {
		metadata: {
			bindings: {
				url: {
					source: 'easy-plugin-stats/button',
					args: {
						field: 'download',
						slug: '',
					},
				},
			},
		},
	},
	isActive: ( blockAttributes ) =>
		'easy-plugin-stats/button' ===
		blockAttributes?.metadata?.bindings?.url?.source,
} );
