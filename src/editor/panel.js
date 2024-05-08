/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	SelectControl,
	__experimentalToolsPanel as ToolsPanel, // eslint-disable-line
	__experimentalToolsPanelItem as ToolsPanelItem, // eslint-disable-line
	__experimentalInputControl as InputControl, // eslint-disable-line
} from '@wordpress/components';

export default function Panel( props ) {
	const { attributes, setAttributes } = props;
	const { metadata } = attributes;

	// Bail early if the Button block does not have the correct binding.
	if ( metadata?.bindings?.url?.source !== 'easy-plugin-stats/button' ) {
		return null;
	}

	const pluginSlug = metadata?.bindings?.url?.args?.slug ?? '';
	const field = metadata?.bindings?.url?.args?.field ?? 'download';
	const cache = metadata?.bindings?.url?.args?.cache ?? '';

	const fields = [
		{
			label: __( 'Plugin Homepage', 'easy-plugin-stats' ),
			value: 'homepage_link',
		},
		{
			label: __( 'Download Link', 'easy-plugin-stats' ),
			value: 'download_link',
		},
		{
			label: __( 'Live Preview', 'easy-plugin-stats' ),
			value: 'live_preview_link',
		},
		{
			label: __( 'Support Forum', 'easy-plugin-stats' ),
			value: 'support_link',
		},
		{
			label: __( 'Reviews', 'easy-plugin-stats' ),
			value: 'reviews_link',
		},
		{
			label: __( 'Author Profile', 'easy-plugin-stats' ),
			value: 'author_profile',
		},
		{
			label: __( 'Donate Link', 'easy-plugin-stats' ),
			value: 'donate_link',
		},
	];

	const setBindingArgs = ( arg, value ) => {
		setAttributes( {
			metadata: {
				...metadata,
				bindings: {
					...metadata.bindings,
					url: {
						...metadata.bindings.url,
						args: {
							...metadata.bindings.url.args,
							[ arg ]: value,
						},
					},
				},
			},
		} );
	};

	const resetAll = () => {
		setAttributes( {
			metadata: {
				...metadata,
				bindings: {
					...metadata.bindings,
					url: {
						...metadata.bindings.url,
						args: {
							field: 'homepage_link',
							slug: '',
							cache: '',
						},
					},
				},
			},
		} );
	};

	return (
		<InspectorControls group="settings">
			<ToolsPanel
				label={ __( 'Link settings', 'easy-plugin-stats' ) }
				resetAll={ () => resetAll() }
				dropdownMenuProps={ {
					popoverProps: {
						placement: 'left-start',
						offset: 259,
					},
				} }
			>
				<ToolsPanelItem
					label={ __( 'Plugin slug', 'easy-plugin-stats' ) }
					hasValue={ () => pluginSlug }
					onDeselect={ () => setBindingArgs( 'slug', '' ) }
					isShownByDefault
				>
					<InputControl
						label={ __( 'Plugin Slug', 'easy-plugin-stats' ) }
						value={ pluginSlug }
						onChange={ ( value ) =>
							setBindingArgs( 'slug', value )
						}
						type="text"
						help={ __(
							'The plugin slug on WordPress.org.',
							'easy-plugin-stats'
						) }
					/>
				</ToolsPanelItem>
				<ToolsPanelItem
					label={ __( 'Link to', 'easy-plugin-stats' ) }
					hasValue={ () => field && field !== 'homepage_link' }
					onDeselect={ () =>
						setBindingArgs( 'field', 'homepage_link' )
					}
					isShownByDefault
				>
					<SelectControl
						label={ __( 'Link to', 'easy-plugin-stats' ) }
						value={ field }
						options={ fields }
						onChange={ ( value ) =>
							setBindingArgs( 'field', value )
						}
					/>
				</ToolsPanelItem>
				<ToolsPanelItem
					label={ __( 'Cache' ) }
					hasValue={ () => cache }
					onDeselect={ () => setBindingArgs( 'cache', '' ) }
				>
					<InputControl
						label={ __( 'Cache (seconds)', 'easy-plugin-stats' ) }
						value={ cache }
						onChange={ ( value ) =>
							setBindingArgs( 'cache', value )
						}
						type="number"
						help={ __(
							'WordPress.org plugin data is cached for 43200 seconds (12 hours) by default.',
							'easy-plugin-stats'
						) }
						placeholder="43200"
						min="3600"
					/>
				</ToolsPanelItem>
			</ToolsPanel>
		</InspectorControls>
	);
}
