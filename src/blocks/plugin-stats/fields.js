/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const currentDate = new Date();

export const fields = [
	{
		label: __( 'Active installs', 'easy-plugin-stats' ),
		value: 'active_installs',
		default: 1000,
	},
	{
		label: __( 'Downloaded', 'easy-plugin-stats' ),
		value: 'downloaded',
		default: 12345,
	},
	{
		label: __( 'Name', 'easy-plugin-stats' ),
		value: 'name',
		default: __( 'Plugin Name', 'easy-plugin-stats' ),
	},
	{
		label: __( 'Version', 'easy-plugin-stats' ),
		value: 'version',
		default: '1.0.0',
	},
	{
		label: __( 'Author', 'easy-plugin-stats' ),
		value: 'author',
		default: __( 'Plugin Author', 'easy-plugin-stats' ),
	},
	{
		label: __( 'Contributors', 'easy-plugin-stats' ),
		value: 'contributors',
		default: {
			'example-contributor': 'https://profiles.wordpress.org/me/',
		},
	},
	{
		label: __( 'Tags', 'easy-plugin-stats' ),
		value: 'tags',
		default: { 'example-tag': 'example-tag' },
	},
	{
		label: __( 'Requires', 'easy-plugin-stats' ),
		value: 'requires',
		default: '6.5',
	},
	{
		label: __( 'Tested up to', 'easy-plugin-stats' ),
		value: 'tested',
		default: '6.5',
	},
	{
		label: __( 'Number of reviews', 'easy-plugin-stats' ),
		value: 'num_ratings',
		default: '12',
	},
	{
		label: __( 'Rating out of 100', 'easy-plugin-stats' ),
		value: 'rating',
		default: '100',
	},
	{
		label: __( 'Rating out of 5', 'easy-plugin-stats' ),
		value: 'five_rating',
		default: '5',
	},
	{
		label: __( 'Star rating', 'easy-plugin-stats' ),
		value: 'star_rating',
	},
	{
		label: __( 'Last updated', 'easy-plugin-stats' ),
		value: 'last_updated',
		default: currentDate,
	},
	{
		label: __( 'Date added', 'easy-plugin-stats' ),
		value: 'added',
		default: currentDate,
	},
	{
		label: __( 'Plugin homepage link', 'easy-plugin-stats' ),
		value: 'homepage_link',
		type: 'link',
	},
	{
		label: __( 'Download link', 'easy-plugin-stats' ),
		value: 'download_link',
		type: 'link',
	},
	{
		label: __( 'Live preview link', 'easy-plugin-stats' ),
		value: 'live_preview_link',
		type: 'link',
	},
	{
		label: __( 'Support forum link', 'easy-plugin-stats' ),
		value: 'support_link',
		type: 'link',
	},
	{
		label: __( 'Reviews link', 'easy-plugin-stats' ),
		value: 'reviews_link',
		type: 'link',
	},
	{
		label: __( 'Author profile link', 'easy-plugin-stats' ),
		value: 'author_profile',
		type: 'link',
	},
	{
		label: __( 'Donate link', 'easy-plugin-stats' ),
		value: 'donate_link',
		type: 'link',
	},
];

export const linkedFields = fields
	.filter( ( field ) => field.type === 'link' )
	.map( ( field ) => field.value );

export const fieldDefaults = fields
	.filter( ( field ) => !! field.default )
	.reduce( ( acc, field ) => {
		acc[ field.value ] = field.default;
		return acc;
	}, {} );
