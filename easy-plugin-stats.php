<?php
/**
 * Plugin Name: Easy Plugin Stats
 * Plugin URI:  https://www.nickdiego.com/plugins/easy-plugin-stats
 * Description: Easily display stats from plugins hosted on WordPress.org
 * Author:      Nick Diego
 * Author URI:  http://www.nickdiego.com
 * Version:     1.0.0
 * Text Domain: eps
 * Domain Path: languages
 *
 * Copyright 2016 Nick Diego
 *
 * Blox Lite is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Blox Lite is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Blox Lite. If not, visit <http://www.gnu.org/licenses/>.
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_shortcode( 'eps', 'eps_shortcode' );
/**
 * Shortcode: Get plugin data from wp.org
 *
 * @since 1.0.0
 *
 * @param array $atts  An array shortcode attributes
 */
function eps_shortcode( $atts ) {
	
	$atts = shortcode_atts( array( 
		'slug' 	  	 => '',
		'field'      => '', 
		'before'	 => '',
		'after'		 => '',
		'cache_time' => 60,
	), $atts );
	
	// The list of currently allowed fields
	$allowed_fields = array( 
		'name',
		'slug',
		'version',
		'author',
		'author_profile',
		'contributors',
		'requires',
		'tested',
		//'compatibility',
		'rating',
		'five_rating',
		'star_rating',
		'num_ratings',
		//'ratings',
		'active_installs',
		'downloaded',
		'last_updated',
		'added',
		'homepage',
		'description',
		'installation',
		'screenshots',
		'changelog',
		'faq',
		'short_description',
		'download_link',
		'support_link',
		'tags',
		'donate_link',
	);
	
	// Return early is an incorrect field is passed
	if ( ! in_array( $atts['field'], $allowed_fields ) ) {
		return;
	}
	
	// Get the plugin data if it has already been stored as a transient
	$plugin_data = get_transient( 'eps_' . esc_attr( $atts['slug'] ) );
	
	// If there is no transient, get the plugin data from wp.org
	if ( ! $plugin_data ) {

		$response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/' . $atts['slug'] . '.json?fields=active_installs' );
		
		if ( is_wp_error( $response ) ) {
			return;
		} else {
			$plugin_data = (array) json_decode( wp_remote_retrieve_body( $response ) );
			$cache_time  = is_int( $atts['cache_time'] ) ? $atts['cache_time'] : 60; 
			set_transient( 'eps_' . esc_attr( $atts['slug'] ), $plugin_data, $cache_time );
		}
	}
	
	// Generate the shortcode output, some fields need special handling
	switch ( $atts['field'] ) {
		case 'contributors':
			$contributors = (array) $plugin_data[ 'contributors' ];
			
			if ( ! empty ( $contributors ) ) {
				foreach ( $contributors as $contributor => $link ) {
					$output[] = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_attr( $contributor ) . '</a>';
				}
				$output = implode( ', ', $output );
			}
			break;
		case 'five_rating':
			$rating = $plugin_data[ 'rating' ];
			
			if ( ! empty ( $rating ) ) {
				$output = ( $rating / 100 ) * 5;
			}
			break;
		case 'star_rating':
			$rating = $plugin_data[ 'rating' ];
			
			if ( ! empty ( $rating ) ) {
				$five_rating = ( $rating / 100 ) * 5;
				
				$output = '<span class="eps-star-rating" title="' . $five_rating . " " . __( 'out of 5 stars', 'eps' ) . '">';
			    
			    if ( $rating < 5 ) {
			    	$stars = array( 0,0,0,0,0 );
				} else if ( $rating >= 5 && $rating < 15 ) {
					$stars = array( 5,0,0,0,0 );
			    } else if ( $rating >= 15 && $rating < 25 ) {
			    	$stars = array( 1,0,0,0,0 );
				} else if ( $rating >= 25 && $rating < 35 ) {
					$stars = array( 1,5,0,0,0 );
			    } else if ( $rating >= 35 && $rating < 45 ) {
			    	$stars = array( 1,1,0,0,0 );
				} else if ( $rating >= 45 && $rating < 55 ) {
					$stars = array( 1,1,5,0,0 );
			    } else if ( $rating >= 55 && $rating < 65 ) {
			    	$stars = array( 1,1,1,0,0 );
				} else if ( $rating >= 65 && $rating < 75 ) {
					$stars = array( 1,1,1,5,0 );
			    } else if ( $rating >= 75 && $rating < 85 ) {
			    	$stars = array( 1,1,1,1,0 );
				} else if ( $rating >= 85 && $rating < 95 ) {
					$stars = array( 1,1,1,1,5 );
				} else if ( $rating >= 95 ) {
					$stars = array( 1,1,1,1,1 );
				}
				
				foreach( $stars as $star ) {
					if ( $star == 0 ) {
						$output .= '<span class="dashicons dashicons-star-empty"></span>';
					} else if ( $star == 5 ) {
						$output .= '<span class="dashicons dashicons-star-half"></span>';
					} else if ( $star == 1 ) {
						$output .= '<span class="dashicons dashicons-star-filled"></span>';
					}
				}
				
				$output .= '</span>';
			}	
			break;
		case 'description':
			$sections = (array) $plugin_data['sections'];
			$output   = $sections['description'];
			break;
		case 'installation':
			$sections = (array) $plugin_data['sections'];
			$output   = $sections['installation'];
			break;
		case 'screenshots':
			$sections = (array) $plugin_data['sections'];
			$output   = $sections['screenshots'];
			break;
		case 'changelog':
			$sections = (array) $plugin_data['sections'];
			$output   = $sections['changelog'];
			break;
		case 'faq':
			$sections = (array) $plugin_data['sections'];
			$output   = $sections['faq'];
			break;
		case 'support_link':
			$slug = $plugin_data[ 'slug' ];
			$output = 'https://wordpress.org/support/plugin/' . $slug;
			break;
		case 'tags':
			$tags = (array) $plugin_data[ 'tags' ];

			if ( ! empty( $tags ) ) {
				$output = implode( ', ', $tags );
			}
			break;
		default:
			$output = $plugin_data[$atts['field']];
	}
	
	$final_output = html_entity_decode( $atts['before'] ) . $output . html_entity_decode( $atts['after'] );
	
	return wp_kses_post( $final_output );
}

add_action('admin_head', 'eps_mce_button');
/**
 * Adds the plugin stats shortcode tinymce button
 */
function eps_mce_button() {
    // Check user permissions
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
        return;
    }
    // Check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) ) {
        add_filter( 'mce_external_plugins', 'eps_add_tinymce_plugin' );
        add_filter( 'mce_buttons', 'eps_register_mce_button' );
    }
}

/**
 * Declare script for new button
 */
function eps_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['eps_button'] = plugin_dir_url( __FILE__ ) . 'tinymce/plugin.js';
    return $plugin_array;
}

/**
 * Register new button in the tinymce editor
 */
function eps_register_mce_button( $buttons ) {
    array_push( $buttons, 'eps_button' );
    return $buttons;
}

add_action( 'admin_head', 'eps_tinymce_button_style' );
/**
 * Add the plugin icon to the tinymce button
 */
function eps_tinymce_button_style() {
	?>
	<style>
		.mce-i-dashicons-admin-plugins { font: 400 20px/1 dashicons !important; vertical-align: top; speak: none; -webkit-font-smoothing: antialiased; }
		.mce-i-dashicons-admin-plugins:before { content: "\f106"; }
	</style>
	<?php
}

add_action( 'wp_enqueue_scripts', 'eps_scripts' );
/**
 * Enqueue Dashicons style for frontend use
 */
function eps_scripts() {
	wp_enqueue_style( 'dashicons' );
}
