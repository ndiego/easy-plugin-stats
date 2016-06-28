// Add the plugin stats shortcode Tinymce button
(function() {

	if ( typeof tinymce != 'undefined' ) {
		
		tinymce.PluginManager.add( 'eps_plugin', function( editor, url ) {
	
			// Get all the text translations
			var title = editor.getLang( 'eps_translations.title' );
			
			editor.addButton( 'eps_plugin', {
				icon: 'dashicons-admin-plugins',
				title: title,
				cmd: 'eps_plugin_function'
			}); // end addButton
			
			editor.addCommand( 'eps_plugin_function', function() { 
				
				// Launch the popup
				window.eps_popup.open( editor.id );				
			});
		});
	};
})();
