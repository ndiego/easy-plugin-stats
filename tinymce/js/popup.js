// All of the js needed to run the advanced layouts TinyMCE popup
var eps_popup;

( function($) { 

	var editor, output, 
		inputs = {},
		singleSlugTitle		= eps_localize_scripts.single_slug_title,
		aggregateSlugTitle	= eps_localize_scripts.aggregate_slug_title,
		singleSlugDesc		= eps_localize_scripts.single_slug_desc,
		aggregateSlugDesc	= eps_localize_scripts.aggregate_slug_desc,
		advancedShow		= eps_localize_scripts.advanced_show,
		advancedHide		= eps_localize_scripts.advanced_hide,
		missingSlug			= eps_localize_scripts.missing_slug,
		fields = {
			active_installs 	: { name : 'Active Installs', aggregate : 1 },
			downloaded			: { name : 'Times Downloaded', aggregate : 1 },
			name 				: { name : 'Plugin Name', aggregate : 0 },
			slug 				: { name : 'Plugin Slug', aggregate : 0 },
			version				: { name : 'Version', aggregate : 0 },
			author 				: { name : 'Author', aggregate : 0 },
			author_profile 		: { name : 'Author Profile Link', aggregate : 0 },
			contributors 		: { name : 'Contributors', aggregate : 0 },
			requires 			: { name : 'Requires', aggregate : 0 },
			tested 				: { name : 'Tested', aggregate : 0 },
			//compatibility 	: { name : 'Compatibility', aggregate : 0 },
			rating				: { name : 'Rating out of 100', aggregate : 0 },
			five_rating 		: { name : 'Rating out of 5', aggregate : 0 },
			star_rating 		: { name : 'Star Rating', aggregate : 0 },
			num_ratings 		: { name : 'Number of Reviews', aggregate : 0 },
			//'ratings 			: { name : 'Ratings', aggregate : 0 },
			last_updated 		: { name : 'Last Updated', aggregate : 0 },
			added				: { name : 'Date Added', aggregate : 0 },
			homepage 			: { name : 'Plugin Homepage Link', aggregate : 0 },
			short_description  	: { name : 'Short Description', aggregate : 0 },
			description 		: { name : 'Description', aggregate : 0 },
			installation 		: { name : 'Installation', aggregate : 0 },
			screenshots 		: { name : 'Screenshots', aggregate : 0 },
			changelog 			: { name : 'Change Log', aggregate : 0 },
			faq 				: { name : 'FAQ', aggregate : 0 },
			download_link 		: { name : 'Download Link', aggregate : 0 },
			support_link 		: { name : 'Support Link', aggregate : 0 },
			tags 				: { name : 'Tags', aggregate : 0 },
			donate_link 		: { name : 'Donate Link', aggregate : 0 },
		};

	eps_popup = {
	
		init: function() {
			inputs.wrap     = $( '#eps_popup_wrap' );
			inputs.backdrop = $( '#eps_popup_backdrop' );
			inputs.insert   = $( '#eps_popup_insert' );
			inputs.reset    = $( '#eps_popup_reset' );
			inputs.close    = $( '#eps_popup_close' );
 
			inputs.statType     	 = $( '#eps_stat_type' );
			inputs.pluginSlug     	 = $( '#eps_plugin_slug' );
			inputs.pluginSlugTitle	 = $( '#eps_plugin_slug_wrap span' );
			inputs.pluginSlugDesc	 = $( '#eps_plugin_slug_field' );
			inputs.statField    	 = $( '#eps_stat_field' );	
			inputs.toggleAdvanced    = $( '#eps_toggle_advanced' );
			inputs.advancedContainer = $( '.eps-advanced-container' );
			inputs.cacheTime     	 = $( '#eps_cache_time' );
			inputs.beforeHTML     	 = $( '#eps_before_html' );
			inputs.afterHTML	 	 = $( '#eps_after_html' );
			
			
			// Insert shortcodes
			inputs.insert.click( function( event ) {
				event.preventDefault();
				eps_popup.insert();
			});
			
			// Reset all selection and classes in popup
			inputs.reset.click( function( event ) {
				event.preventDefault();
				eps_popup.reset();
			});
			
			// Close the popup
			inputs.close.add( inputs.backdrop ).add( '#eps_popup_cancel button' ).click( function( event ) {
				event.preventDefault();
				eps_popup.close();
			});
			
			// Toggle fields/titles/descriptions based on type of stat selected
			inputs.statType.change( function() {
				eps_popup.toggleType();
			});
			
			// Toggle advanced settings
			inputs.toggleAdvanced.click( function( event ) {
				event.preventDefault();
				inputs.advancedContainer.toggle();
				inputs.wrap.toggleClass( 'advanced' );
				
				if ( inputs.wrap.hasClass( 'advanced' ) ) {
					$(this).text( advancedHide );
				} else {
					$(this).text( advancedShow );
				}
			});
			
			// Make sure custom classes are actually custom classes
			inputs.pluginSlug.change( function() {
				var slug = $(this).val(),
					type = inputs.statType.val();
				
				// remove any unacceptable characters from slug
				if ( type == 'single' ) {
					slug = slug.replace( /[^\w\_\-]/g, '' ); // remove all characters that are not allowed, including spaces for single
				} else {
					slug = slug.replace( /[^\w\_\-\s]/g, '' ); // remove all characters that are not allowed
					slug = slug.replace( /\s\s+/g, ' ' ); // trim all excess whitepace
				}
				
				$(this).val( slug );
			});
		},
		
		open: function( editorId ) {
		
			if ( editorId ) {
				window.epsActiveEditor = editorId;
			}

			if ( ! window.epsActiveEditor ) {
				return;
			}
			
			if ( typeof window.tinymce !== 'undefined' ) {
				// Make sure the link wrapper is the last element in the body,
				// or the inline editor toolbar may show above the backdrop.
				$( document.body ).append( inputs.backdrop, inputs.wrap );
				
				editor = window.tinymce.get( window.epsActiveEditor );
				
				if ( editor ) {
					//selected = editor.selection.getContent();
					// Use the builtin WP class that prevents background from scrolling when modal is open
					$( document.body ).addClass( 'modal_open' );
					
					eps_popup.toggleType();
					
					inputs.wrap.show();
					inputs.backdrop.show();
				}
			}
		},
		
		insert: function() {
		
			var type, slug, field, cache, before, after, 
				
			// Reset output
			output = '';
			
			// Get all input values
			type   = inputs.statType.val();
			slug   = inputs.pluginSlug.val();
			field  = inputs.statField.val();
			cache  = inputs.cacheTime.val();
			before = inputs.beforeHTML.val();
			after  = inputs.afterHTML.val();
			
			if ( slug != '' ) {
				
				// If type is single don't bother adding this to the shortcode
				type = ( type == 'aggregate' ) ? ( 'type="aggregate" ' ) : '';
				
				slug  = 'slug="' + slug + '"';
				field = ' field="' + field + '"';
					
				// Test to make sure cache time is a number, an integer and greater than 5
				cache  = ( cache % 1 === 0 && cache >= 5 ) ? ( ' cache="' + cache + '"' ) : '';
			
				// For before and after html we need to encode the html and then remove all double quotes so that the shortcode will work
				before = ( before != '' ) ? ( ' before="' + escapeHtml( before.replace(/"/g, "'") ) + '"' ) : '';
				after  = ( after != '' ) ? ( ' after="' + escapeHtml( after.replace(/"/g, "'") ) + '"' ) : '';
		 
				// Generate the output			
				output = '[eps ' + type + slug + field + cache + before + after + ']';
					
				// Insert shortcodes and close popup
				editor.insertContent( output );
				eps_popup.close();

			} else {
			
				// Throw alert if no slug was entered
				alert( missingSlug );
			}
		},
		
		toggleType: function() {
				
			var type, 
			output = [];
			
			type = inputs.statType.val();
			
			if ( type === 'aggregate' ) {
				inputs.pluginSlugTitle.html( aggregateSlugTitle );
				inputs.pluginSlugDesc.html( aggregateSlugDesc );
			} else {
				inputs.pluginSlugTitle.html( singleSlugTitle );
				inputs.pluginSlugDesc.html( singleSlugDesc );
			}
			
			$.each( fields, function( value, atts ) {
				if ( type === 'aggregate' && atts['aggregate'] ) {
					output.push( '<option value="'+ value +'">'+ atts['name'] +'</option>' );
				} else if ( type === 'single' ) {
					output.push( '<option value="'+ value +'">'+ atts['name'] +'</option>' );
				}
			});
				
			inputs.statField.html( output.join('') );
		},
		
		reset: function() {
			
			// Reset all fields in popup
			inputs.statType.val('single');
			inputs.pluginSlug.val('');
			inputs.statField.val('name');
			inputs.cacheTime.val('');
			inputs.beforeHTML.val('');
			inputs.afterHTML.val('');
			
			// Switch back to single if on aggregate before reset
			eps_popup.toggleType();
		},
		
		close: function() {
			
			$( document.body ).removeClass( 'modal_open' );
			
			inputs.wrap.hide();
			inputs.backdrop.hide();
		} 
	}
	
	$( document ).ready( eps_popup.init );
	
	
	// Helper function for escaping HTML
	function escapeHtml(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	};
	
})( jQuery );
