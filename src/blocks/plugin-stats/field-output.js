/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { starEmpty, starFilled, starHalf } from '@wordpress/icons';
import { getSettings, format } from '@wordpress/date';

/**
 * Internal dependencies
 */
import { fields, linkedFields, fieldDefaults } from './fields';

export function getFieldOutput( attributes, fetchPluginData, error ) {
	const { field, slugs, linkText } = attributes;
	const isFieldValid = fields.some(
		( fieldObj ) => fieldObj.value === field
	);
	const setField = isFieldValid ? field : 'active_installs';

	let pluginData = fetchPluginData;

	// Display default message if there's nothing to display.
	if (
		error ||
		slugs.length === 0 ||
		Object.keys( pluginData ).length === 0
	) {
		if ( ! field || slugs.length === 0 ) {
			return (
				<span
					data-placeholder={ __(
						'Plugin Stat',
						'easy-plugin-stats'
					) }
				></span>
			);
		}

		// Return the field defaults.
		pluginData = { 'plugin-slug': fieldDefaults };
	}

	// Check if pluginData contains multiple items and the field is either 'active_installs' or 'downloaded'.
	if (
		( setField === 'active_installs' || setField === 'downloaded' ) &&
		Object.keys( pluginData ).length > 1
	) {
		let sum = 0;
		for ( const pluginSlug in pluginData ) {
			if ( pluginData.hasOwnProperty( pluginSlug ) ) {
				const fieldValue = pluginData[ pluginSlug ][ setField ];
				if ( fieldValue ) {
					sum += fieldValue;
				}
			}
		}
		// Format the sum with commas if it's greater than 1000.
		return sum.toLocaleString();
	}

	// If pluginData contains a single item, retrieve the value from the specified field.
	const value = pluginData[ Object.keys( pluginData )[ 0 ] ][ setField ];

	// If the field is 'author', return the value as HTML.
	if ( setField === 'author' ) {
		return <span dangerouslySetInnerHTML={ { __html: value } } />;
	}

	if ( setField === 'contributors' ) {
		let output = '';

		if ( Object.keys( value ).length > 0 ) {
			output = Object.entries( value )
				.map( ( [ contributor, link ] ) => {
					return `<a href="${ link }" target="_blank">${ contributor }</a>`;
				} )
				.join( ', ' );
		} else {
			output = __( 'No contributors', 'easy-plugin-stats' );
		}

		return <span dangerouslySetInnerHTML={ { __html: output } } />;
	}

	if ( setField === 'tags' ) {
		let output = '';

		if ( Object.keys( value ).length > 0 ) {
			output = Object.entries( value )
				.map( ( [ slug, name ] ) => {
					return `<a href="https://wordpress.org/plugins/tags/${ slug }" target="_blank">${ name }</a>`;
				} )
				.join( ', ' );
		}
		// @TODO add alternative display options (chit)
		return <span dangerouslySetInnerHTML={ { __html: output } } />;
	}

	if ( setField === 'five_rating' ) {
		const rating =
			pluginData[ Object.keys( pluginData )[ 0 ] ]?.rating ?? 100;
		return ( rating / 100 ) * 5;
	}

	if ( setField === 'last_updated' || setField === 'added' ) {
		const dateSettings = getSettings();
		const date = moment( value, 'YYYY-MM-DD' ); // eslint-disable-line

		// Ensure the date is in the format defined by the WordPress settings.
		return format( dateSettings.formats.date, date );
	}

	// If the field is 'star_rating', calculate and display star-shaped SVGs.
	if ( setField === 'star_rating' ) {
		// Default to 100 if no rating is found.
		const rating =
			pluginData[ Object.keys( pluginData )[ 0 ] ]?.rating ?? 100;
		const starRating = ( rating / 100 ) * 5;
		const remainder = ( starRating % 1 ).toFixed( 2 );

		// Determine how many full, half, and empty stars there are.
		const fullStars =
			remainder >= 0.8
				? Math.floor( starRating + 1 )
				: Math.floor( starRating );
		const halfStar = remainder < 0.8 && remainder > 0.2 ? 1 : 0;
		const emptyStars = 5 - fullStars - halfStar;

		const stars = [];
		for ( let i = 0; i < fullStars; i++ ) {
			stars.push( starFilled );
		}
		if ( halfStar === 1 ) {
			stars.push( starHalf );
		}
		for ( let i = 0; i < emptyStars; i++ ) {
			stars.push( starEmpty );
		}

		return (
			<span
				className="star-rating__container"
				title={ `${ starRating } out of 5 stars.` }
			>
				{ stars }
			</span>
		);
	}

	if ( linkedFields.includes( setField ) ) {
		const linkObject = fields.find(
			( fieldObj ) => fieldObj.value === setField
		);
		const text = linkText || linkObject.label.replace( ' Link', '' );
		const output = `<a href="${ value }" target="_blank">${ text }</a>`;

		return <span dangerouslySetInnerHTML={ { __html: output } } />;
	}

	// Format the value with commas if it's greater than 1000,
	return value >= 1000 ? value.toLocaleString() : value;
}
