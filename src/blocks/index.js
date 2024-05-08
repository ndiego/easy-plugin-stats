/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { plugins } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';
import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	icon: plugins,
	edit: Edit,
} );
