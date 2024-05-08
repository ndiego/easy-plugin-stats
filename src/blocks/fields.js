/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export const fields = [
	{
		label: __( 'Active Installs', 'easy-plugin-stats' ),
		value: 'active_installs',
	},
	{
		label: __( 'Downloaded', 'easy-plugin-stats' ),
		value: 'downloaded',
	},
	{
		label: __( 'Name', 'easy-plugin-stats' ),
		value: 'name',
	},
	{
		label: __( 'Slug', 'easy-plugin-stats' ),
		value: 'slug',
	},
	{
		label: __( 'Version', 'easy-plugin-stats' ),
		value: 'version',
	},
	{
		label: __( 'Author', 'easy-plugin-stats' ),
		value: 'author',
	},
	{
		label: __( 'Contributors', 'easy-plugin-stats' ),
		value: 'contributors',
	},
	{
		label: __( 'Tags', 'easy-plugin-stats' ),
		value: 'tags',
	},
	{
		label: __( 'Requires', 'easy-plugin-stats' ),
		value: 'requires',
	},
	{
		label: __( 'Tested', 'easy-plugin-stats' ),
		value: 'tested',
	},
	{
		label: __( 'Number of Reviews', 'easy-plugin-stats' ),
		value: 'num_ratings',
	},
	{
		label: __( 'Rating out of 100', 'easy-plugin-stats' ),
		value: 'rating',
	},
	{
		label: __( 'Rating out of 5', 'easy-plugin-stats' ),
		value: 'five_rating',
	},
	{
		label: __( 'Star Rating', 'easy-plugin-stats' ),
		value: 'star_rating',
	},
	{
		label: __( 'Last Updated', 'easy-plugin-stats' ),
		value: 'last_updated',
	},
	{
		label: __( 'Date Added', 'easy-plugin-stats' ),
		value: 'added',
	},
	{
		label: __( 'Plugin Homepage Link', 'easy-plugin-stats' ),
		value: 'homepage_link',
		type: 'link',
	},
	{
		label: __( 'Download Link', 'easy-plugin-stats' ),
		value: 'download_link',
		type: 'link',
	},
	{
		label: __( 'Live Preview Link', 'easy-plugin-stats' ),
		value: 'live_preview_link',
		type: 'link',
	},
	{
		label: __( 'Support Forum Link', 'easy-plugin-stats' ),
		value: 'support_link',
		type: 'link',
	},
	{
		label: __( 'Reviews Link', 'easy-plugin-stats' ),
		value: 'reviews_link',
		type: 'link',
	},
	{
		label: __( 'Author Profile Link', 'easy-plugin-stats' ),
		value: 'author_profile',
		type: 'link',
	},
	{
		label: __( 'Donate Link', 'easy-plugin-stats' ),
		value: 'donate_link',
		type: 'link',
	},
];

export const linkedFields = fields
	.filter( ( field ) => field.type === 'link' )
	.map( ( field ) => field.value );
