/*
 * Add the plugins stats shortcode Tinymce button
 */
(function() {

	if ( typeof tinymce != 'undefined' ) {
		
		tinymce.PluginManager.add( 'eps_button', function( editor, url ) {
	
			editor.addButton( 'eps_button', {
				type: 'button',
				title: 'Plugin Stats',
				icon: 'dashicons-admin-plugins',
				onclick: function() { 
					editor.windowManager.open({
						title: 'WP Plugin Stats',
						url: url + '/popup.html', 
						width: 550,
						height: 485,
						inline: 1
					}, {
						selectedContent: editor.selection.getContent(), 
						onInsert: function( layout ) { editor.insertContent( layout ); } 
					});
				}
			}); // end addButton
		}); // end PluginManager
	};
})();
