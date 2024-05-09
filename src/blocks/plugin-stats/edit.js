/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	AlignmentToolbar,
	InspectorControls,
	BlockControls,
	useBlockProps,
	RichText,
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown, // eslint-disable-line
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients, // eslint-disable-line
} from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import {
	FormTokenField,
	Notice,
	SelectControl,
	CheckboxControl,
	__experimentalToolsPanel as ToolsPanel, // eslint-disable-line
	__experimentalToolsPanelItem as ToolsPanelItem, // eslint-disable-line
	__experimentalInputControl as InputControl, // eslint-disable-line
	__experimentalVStack as VStack, // eslint-disable-line
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './editor.scss';
import { fields, linkedFields } from './fields';
import { ALLOWED_FORMATS } from './constants';
import { getFieldValue } from './output';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @since 2.0.0
 *
 * @param {Object} props All the props passed to this component.
 * @return {Element} Element to render.
 */
export default function Edit( props ) {
	const {
		clientId,
		attributes,
		isSelected,
		setAttributes,
		insertBlocksAfter,
	} = props;
	const {
		field,
		slugs,
		textAlign,
		cache,
		prefix,
		suffix,
		prefixColor,
		suffixColor,
		linkText,
		linkTarget,
	} = attributes;
	const [ pluginData, setPluginData ] = useState( {} );
	const [ error, setError ] = useState( null );
	const isAggregate = slugs?.length > 1;

	useEffect( () => {
		// No plugin slugs to fetch, bail early.
		if ( slugs.length === 0 ) {
			return;
		}

		const fetchDataForPluginSlugs = async () => {
			try {
				const requests = slugs.map( ( slug ) =>
					fetch(
						`https://api.wordpress.org/plugins/info/1.0/${ slug }.json?fields=active_installs`
					)
						.then( ( response ) => {
							if ( ! response.ok ) {
								throw new Error(
									'An error occurred fetching plugin data.',
									'easy-plugin-stats'
								);
							}
							return response.json();
						} )
						.then( ( jsonData ) => ( { slug, data: jsonData } ) )
				);

				const responses = await Promise.all( requests );

				const pluginDataObject = {};
				responses.forEach( ( { slug, data } ) => {
					pluginDataObject[ slug ] = data;
				} );

				setPluginData( pluginDataObject );
			} catch ( fetchError ) {
				setError( fetchError.message );
				setPluginData( {} );
			}
		};

		fetchDataForPluginSlugs();
	}, [ slugs ] );

	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// In WordPress <=6.2 this will return null, so default to true in those cases.
	const hasColorsOrGradients =
		colorGradientSettings?.hasColorsOrGradients ?? true;

	// Define custom color settings.
	const colorSettings = [
		{
			colorLabel: __( 'Prefix', 'easy-plugin-stats' ),
			colorValue: prefixColor || '',
			onChange: ( value ) =>
				setAttributes( {
					prefixColor: value,
				} ),
			resetAllFilter: () =>
				setAttributes( {
					prefixColor: '',
				} ),
		},
		{
			colorLabel: __( 'Suffix', 'easy-plugin-stats' ),
			colorValue: suffixColor || '',
			onChange: ( value ) =>
				setAttributes( {
					suffixColor: value,
				} ),
			resetAllFilter: () =>
				setAttributes( {
					suffixColor: '',
				} ),
		},
	];

	// Some output values are links and we don't want them to be clickable.
	const preventLinkClicks = ( event ) => {
        if ( event.target.tagName === 'A' ) {
            event.preventDefault();
        }
    };

	// Disable fields not supported when multiple plugins are chosen.
	const availableFields = ! isAggregate
		? fields
		: fields.map( ( fieldObj ) => {
				if (
					fieldObj.value !== 'downloaded' &&
					fieldObj.value !== 'active_installs'
				) {
					return { ...fieldObj, disabled: true };
				}
				return fieldObj;
		  } );

	const blockProps = useBlockProps( {
		className: classnames( {
			[ `has-text-align-${ textAlign }` ]: textAlign,
			[ `field-${ field }` ]: field,
		} ),
	} );

	return (
		<>
			<BlockControls>
				<AlignmentToolbar
					value={ textAlign }
					onChange={ ( nextAlign ) => {
						setAttributes( { textAlign: nextAlign } );
					} }
				/>
			</BlockControls>
			{ hasColorsOrGradients && (
				<InspectorControls group="color">
					{ colorSettings.map(
						( {
							colorLabel,
							colorValue,
							onChange,
							resetAllFilter,
						} ) => (
							<ColorGradientSettingsDropdown
								key={ `icon-block-color-${ colorLabel }` }
								__experimentalIsRenderedInSidebar
								settings={ [
									{
										label: colorLabel,
										colorValue,
										onColorChange: onChange,
										isShownByDefault: false,
										resetAllFilter,
										enableAlpha: true,
									},
								] }
								panelId={ clientId }
								{ ...colorGradientSettings }
							/>
						)
					) }
				</InspectorControls>
			) }
			<InspectorControls group="settings">
				<ToolsPanel
					label={ __( 'Settings' ) }
					resetAll={ () =>
						setAttributes( {
							field: 'active_installs',
							slugs: [],
							cache: '',
							prefix: '',
							suffix: '',
						} )
					}
					dropdownMenuProps={ {
						popoverProps: {
							placement: 'left-start',
							offset: 259,
						},
					} }
				>
					<ToolsPanelItem
						label={ __( 'Plugin slug' ) }
						hasValue={ () => slugs.length > 0 }
						onDeselect={ () => {
							setError(); // Clear any errors.
							setAttributes( { slugs: [] } );
						} }
						isShownByDefault
					>
						<FormTokenField
							label={ __( 'Plugin slug', 'easy-plugin-stats' ) }
							value={ slugs }
							onChange={ ( value ) => {
								setError(); // Clear any errors.
								setAttributes( { slugs: value } );

								// If multiple slugs are entered, reset the field value.
								if ( value.length > 1 ) {
									setAttributes( {
										field: 'active_installs',
									} );
								}
							} }
						/>
						{ error && (
							<Notice status="error" isDismissible={ false }>
								{ __(
									'Stats for the corresponding plugin slug(s) could not be found. Check for typos and try again.',
									'easy-plugin-stats'
								) }
							</Notice>
						) }
					</ToolsPanelItem>
					<ToolsPanelItem
						label={ __( 'Stat' ) }
						hasValue={ () => field && field !== 'active_installs' }
						onDeselect={ () => setAttributes( { field: '' } ) }
						isShownByDefault
					>
						<VStack spacing={ 4 }>
							<SelectControl
								label={ __( 'Stat', 'easy-plugin-stats' ) }
								value={ field }
								options={ availableFields }
								onChange={ ( value ) =>
									setAttributes( { field: value } )
								}
							/>

							{ linkedFields.includes( field ) && (
								<>
									<InputControl
										label={ __(
											'Link Text',
											'easy-plugin-stats'
										) }
										value={ linkText }
										onChange={ ( value ) =>
											setAttributes( {
												linkText: value,
											} )
										}
										type="text"
									/>
									<CheckboxControl
										label={ __(
											'Open in new tab.',
											'easy-plugin-stats'
										) }
										checked={ linkTarget }
										onChange={ () =>
											setAttributes( {
												linkTarget: ! linkTarget,
											} )
										}
									/>
								</>
							) }
						</VStack>
					</ToolsPanelItem>
					<ToolsPanelItem
						label={ __( 'Cache' ) }
						hasValue={ () => cache }
						onDeselect={ () => setAttributes( { cache: '' } ) }
					>
						<InputControl
							label={ __(
								'Cache (seconds)',
								'easy-plugin-stats'
							) }
							value={ cache }
							onChange={ ( value ) =>
								setAttributes( { cache: value } )
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
			<p { ...blockProps }>
				{ ( isSelected || prefix ) && (
					<RichText
						identifier="prefix"
						allowedFormats={ ALLOWED_FORMATS }
						className="wp-block-easy-plugin-stats__prefix"
						aria-label={ __( 'Prefix', 'easy-plugin-stats' ) }
						placeholder={
							__( 'Prefix', 'easy-plugin-stats' ) + ' '
						}
						value={ prefix }
						style={ { color: prefixColor || 'currentColor' } }
						onChange={ ( value ) =>
							setAttributes( { prefix: value } )
						}
						tagName="span"
					/>
				) }
				<span class="stat-container" onClick={ preventLinkClicks }>
					{ getFieldValue( attributes, pluginData, error ) }
				</span>
				{ ( isSelected || suffix ) && (
					<RichText
						identifier="suffix"
						allowedFormats={ ALLOWED_FORMATS }
						className="wp-block-post-terms__suffix"
						aria-label={ __( 'Suffix' ) }
						placeholder={ ' ' + __( 'Suffix' ) }
						value={ suffix }
						style={ { color: suffixColor || 'currentColor' } }
						onChange={ ( value ) =>
							setAttributes( { suffix: value } )
						}
						tagName="span"
						__unstableOnSplitAtEnd={ () =>
							insertBlocksAfter(
								createBlock( getDefaultBlockName() )
							)
						}
					/>
				) }
			</p>
		</>
	);
}
