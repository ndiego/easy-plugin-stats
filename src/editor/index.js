/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import './variations';
import Panel from './panel';

/**
 * Filter the BlockEdit object and add plugins stats controls to Button blocks.
 *
 * @since 1.0.0
 * @param {Object} BlockEdit
 */
function addInspectorControls( BlockEdit ) {
	return ( props ) => {
		// Bail early if the Button block does not have the correct binding.
		if (
			props?.name === 'core/button' &&
			props?.attributes?.metadata?.bindings?.url?.source ===
				'easy-plugin-stats/button'
		) {
			return (
				<>
					<BlockEdit { ...props } />
					<Panel { ...props } />
				</>
			);
		}
		return <BlockEdit { ...props } />;
	};
}

addFilter(
	'editor.BlockEdit',
	'easy-plugin-stats/add-inspector-controls',
	addInspectorControls
);
