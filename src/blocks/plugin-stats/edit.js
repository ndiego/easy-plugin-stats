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
	CheckboxControl,
	FormTokenField,
	Notice,
	SelectControl,
	Spinner,
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
import { getFieldOutput } from './field-output';

// Allowed formats for the prefix and suffix fields.
export const ALLOWED_FORMATS = [
	'core/bold',
	'core/italic',
	'core/link',
	'core/strikethrough',
	'core/text-color',
];

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
	const [ isLoading, setIsLoading ] = useState( false );
	const [ error, setError ] = useState( null );
	const isAggregate = slugs?.length > 1;

	useEffect( () => {
		// No plugin slugs to fetch, bail early.
		if ( slugs.length === 0 ) {
			return;
		}

		const fetchDataForPluginSlugs = async () => {
			setIsLoading( true );
			try {
				const requests = slugs.map( ( slug ) =>
					fetch(
						`https://api.wordpress.org/plugins/info/1.0/${ slug }.json?fields=active_installs`
					)
						.then( ( response ) => {
							if ( ! response.ok ) {
								throw new Error(
									__(
										'An error occurred fetching plugin data.',
										'easy-plugin-stats'
									)
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
			} finally {
				setIsLoading( false );
			}
		};

		fetchDataForPluginSlugs();
	}, [ slugs ] );

	const colorGradientSettings = useMultipleOriginColorsAndGradients();

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
			<InspectorControls group="settings">
				<ToolsPanel
					className="outermost-plugin-stat__settings-panel"
					label={ __( 'Settings', 'easy-plugin-stats' ) }
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
						label={ __( 'Plugin slug', 'easy-plugin-stats' ) }
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
								if (
									value.length > 1 &&
									field !== 'active_installs' &&
									field !== 'downloaded'
								) {
									setAttributes( {
										field: 'active_installs',
									} );
								}
							} }
							tokenizeOnSpace={ true }
							__experimentalShowHowTo={ false }
						/>
						<p className='components-base-control__help'>
							{ __( 
								'The plugin slug on WordPress.org. For aggregate stats, separate multiple slugs with commas, spaces, or the Enter key.', 
								'easy-plugin-stats' 
							) }
						</p>
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
						label={ __( 'Stat', 'easy-plugin-stats' ) }
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
				<span class="wp-block-outermost-plugin-stat__container">
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
					<span className="stat-container" onClick={ preventLinkClicks }>
						{ isLoading ? (
							<Spinner />
						) : (
							getFieldOutput( attributes, pluginData, error )
						) }
					</span>
					{ ( isSelected || suffix ) && (
						<RichText
							identifier="suffix"
							allowedFormats={ ALLOWED_FORMATS }
							className="wp-block-post-terms__suffix"
							aria-label={ __( 'Suffix', 'easy-plugin-stats' ) }
							placeholder={ ' ' + __( 'Suffix', 'easy-plugin-stats' ) }
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
				</span>
			</p>
		</>
	);
}
