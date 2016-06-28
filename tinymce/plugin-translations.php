<?php 

defined( 'WPINC' ) or die;


// Make sure the _WP_Editors exists, if not, load it
if ( ! class_exists( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}


/**
 * Setup all of the text needed for the TinyMCE popup
 * Code borrowed heavily from: https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_external_languages
 *
 * @since 1.0.0
 *
 * @return array $translated An array of all translated strings
 */
function eps_translations() {
    $strings = array(
        'title' => __( 'Insert Plugin Stats', 'easy-plugin-stats' ),
    );
    
    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.eps_translations", ' . json_encode( $strings ) . ");\n";

    return $translated;
}


// Add our translated strings to the global $strings variable
$strings = eps_translations(); 
