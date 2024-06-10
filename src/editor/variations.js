/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockVariation } from '@wordpress/blocks';
import { plugins } from '@wordpress/icons';

registerBlockVariation( 'core/button', {
	name: 'easy-plugin-stats/button',
	icon: plugins,
	title: __( 'Plugin Button', 'easy-plugin-stats' ),
	description: __(
		'Prompt visitors to take action with a button-style link. This button links to resources associated with a plugin hosted on WordPress.org.',
		'easy-plugin-stats'
	),
	scope: [ 'inserter' ],
	example: {
		attributes: {
			className: 'is-style-fill',
			text: __( 'Download', 'easy-plugin-stats' ),
		},
	},
	attributes: {
		metadata: {
			bindings: {
				url: {
					source: 'easy-plugin-stats/button',
					args: {
						field: 'download_link',
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
