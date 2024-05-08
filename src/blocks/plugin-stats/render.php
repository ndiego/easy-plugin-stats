<?php

$classes = array( 'field-' . $attributes['field'] );
if ( isset( $attributes['textAlign'] ) ) {
	$classes[] = 'has-text-align-' . $attributes['textAlign'];
}
if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
	$classes[] = 'has-link-color';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$prefix = '';
	if ( isset( $attributes['prefix'] ) && $attributes['prefix'] ) {
		$prefix .= '<span class="wp-block-post-terms__prefix">' . $attributes['prefix'] . '</span>';
	}

$suffix = '';
	if ( isset( $attributes['suffix'] ) && $attributes['suffix'] ) {
		$suffix = '<span class="wp-block-post-terms__suffix">' . $attributes['suffix'] . '</span>' . $suffix;
	}

$output = $prefix . esc_html( 'Plugin Stats', 'plugin-stats' ) . $suffix;
?>
<div <?php echo $wrapper_attributes; ?>>
	<?php echo wp_kses_post( $output ); ?>
</div>
