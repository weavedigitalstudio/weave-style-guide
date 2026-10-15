<?php
/**
 * Dark mode: the colours that change, light and dark side by side; which sections go dark; text
 * contrast in both modes. Every swatch is painted with its literal value, never the live custom
 * property, so each column shows its own mode whichever mode the page is being viewed in.
 */
defined( 'ABSPATH' ) || exit;

$dark = wsg_dark_values();
if ( ! $dark ) {
	echo wsg_wrap( 'dark-mode', '<p class="wsg-note">' . esc_html__( 'This theme has no dark mode. It gets one from dark values in theme.json settings.custom.dark.', 'weave-style-guide' ) . '</p>', $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
	return;
}

$palette = wsg_palette();
$light   = array_column( $palette, 'color', 'slug' );
$names   = array_column( $palette, 'name', 'slug' );
$dark_of = fn( string $slug ): string => $dark[ $slug ] ?? ( $light[ $slug ] ?? '' );

// The palette name for a value, when the value is a palette colour.
$name_of = function ( string $value ) use ( $palette ): string {
	foreach ( $palette as $c ) {
		if ( strtolower( $c['color'] ) === strtolower( trim( $value ) ) ) { return $c['name']; }
	}
	return '';
};
$show  = fn( string $value ): string => wsg_hex_to_rgb( $value ) ? strtoupper( $value ) : $value;
$chip  = fn( string $value ): string => '<span class="wsg-dm-chip" style="background:' . esc_attr( $value ) . '" aria-hidden="true"></span>';
$cell  = fn( string $value ): string => '<td>' . $chip( $value ) . ( '' !== $name_of( $value ) ? '<span class="wsg-name">' . esc_html( $name_of( $value ) ) . '</span> ' : '' ) . '<code>' . esc_html( $show( $value ) ) . '</code></td>';

$inner = '<p class="wsg-meta">' . esc_html__( 'Visitors get these colours when their device is set to dark, or when they switch with the toggle. The editor stays light. Swatches show both values whichever mode you are viewing this page in.', 'weave-style-guide' ) . '</p>';

// 1. The colours that change.
$rows = '';
foreach ( $dark as $slug => $value ) {
	$rows .= '<tr><th scope="row">' . esc_html( $names[ $slug ] ) . ' <code class="wsg-small">' . esc_html( $slug ) . '</code></th>' . '<td>' . $chip( $light[ $slug ] ) . '<code>' . esc_html( $show( $light[ $slug ] ) ) . '</code></td>' . $cell( $value ) . '</tr>';
}
$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Colours that change', 'weave-style-guide' ) . '</h3>'
	. '<table class="wsg-table wsg-dm-table"><thead><tr><th scope="col">' . esc_html__( 'Colour', 'weave-style-guide' ) . '</th><th scope="col">' . esc_html__( 'Light', 'weave-style-guide' ) . '</th><th scope="col">' . esc_html__( 'Dark', 'weave-style-guide' ) . '</th></tr></thead><tbody>' . $rows . '</tbody></table>';

$fixed = array_diff_key( $names, $dark );
if ( $fixed ) {
	$inner .= '<p class="wsg-small">' . esc_html__( 'Every other colour is the same in both modes:', 'weave-style-guide' ) . ' ' . esc_html( implode( ', ', $fixed ) ) . '.</p>';
}

// 2. Which sections go dark. The same rule the weave-site-functions module uses: a solid, light
// background colour (relative luminance over 0.4) that has a dark value.
$flip = array();
foreach ( $dark as $slug => $value ) {
	if ( wsg_hex_to_rgb( $light[ $slug ] ) && wsg_luminance( $light[ $slug ] ) > 0.4 ) { $flip[] = $slug; }
}
$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Which sections go dark', 'weave-style-guide' ) . '</h3><p>'
	. ( $flip
		/* translators: %s: colour names */
		? esc_html( sprintf( __( 'The page, and sections with these backgrounds: %s. Everything else keeps its colours: dark bands, pastel and brand-coloured panels, pictures, Cover blocks and filled buttons.', 'weave-style-guide' ), implode( ', ', array_map( fn( $s ) => $names[ $s ], $flip ) ) ) )
		: esc_html__( 'The page only. Every section with its own background colour keeps its colours, as do pictures, Cover blocks and filled buttons.', 'weave-style-guide' ) )
	. '</p>';

// 3. Text contrast in both modes: the theme's text, heading and link colours on the page and on
// every section background that goes dark.
$styles = wsg_styles();
$texts  = array_filter(
	array(
		__( 'Body text', 'weave-style-guide' )  => array( wsg_style_slug( $styles['color']['text'] ?? '' ), 4.5 ),
		__( 'Headings', 'weave-style-guide' )   => array( wsg_style_slug( $styles['elements']['heading']['color']['text'] ?? '' ), 3.0 ),
		__( 'Links', 'weave-style-guide' )      => array( wsg_style_slug( $styles['elements']['link']['color']['text'] ?? '' ), 4.5 ),
		__( 'Link hover', 'weave-style-guide' ) => array( wsg_style_slug( $styles['elements']['link'][':hover']['color']['text'] ?? '' ), 4.5 ),
	),
	fn( $t ) => isset( $light[ $t[0] ] )
);
$grounds = array_unique( array_filter( array_merge( array( wsg_style_slug( $styles['color']['background'] ?? '' ) ), $flip ), fn( $s ) => isset( $light[ $s ] ) ) );

$verdict = function ( string $fg, string $bg, float $need ): string {
	if ( ! wsg_hex_to_rgb( $fg ) || ! wsg_hex_to_rgb( $bg ) ) { return '<td class="wsg-small">' . esc_html__( 'not a solid colour', 'weave-style-guide' ) . '</td>'; }
	$r    = wsg_contrast( $fg, $bg );
	$pass = $r >= $need;
	return '<td><span class="wsg-dm-sample" style="background:' . esc_attr( $bg ) . ';color:' . esc_attr( $fg ) . '">Aa</span>'
		. esc_html( number_format( $r, 1 ) . ':1 ' ) . '<strong class="' . ( $pass ? 'wsg-dm-pass' : 'wsg-dm-fail' ) . '">' . esc_html( $pass ? __( 'Pass', 'weave-style-guide' ) : __( 'Fail', 'weave-style-guide' ) ) . '</strong></td>';
};

$rows = '';
foreach ( $grounds as $bg ) {
	foreach ( $texts as $label => $t ) {
		list( $fg, $need ) = $t;
		$rows .= '<tr><th scope="row">' . esc_html( $label ) . ' <span class="wsg-small">' . esc_html( $names[ $fg ] ) . '</span></th><td>' . esc_html( $names[ $bg ] ) . '</td>'
			. $verdict( $light[ $fg ], $light[ $bg ], $need ) . $verdict( $dark_of( $fg ), $dark_of( $bg ), $need ) . '</tr>';
	}
}
if ( $rows ) {
	$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Text contrast in both modes', 'weave-style-guide' ) . '</h3>'
		. '<p class="wsg-meta">' . esc_html__( 'The theme\'s text, heading and link colours on the page and on each background that goes dark. Body text and links need 4.5:1, headings 3:1. Colours set on single blocks are not listed; check those on the pages.', 'weave-style-guide' ) . '</p>'
		. '<table class="wsg-table wsg-dm-table"><thead><tr><th scope="col">' . esc_html__( 'Text', 'weave-style-guide' ) . '</th><th scope="col">' . esc_html__( 'On', 'weave-style-guide' ) . '</th><th scope="col">' . esc_html__( 'Light', 'weave-style-guide' ) . '</th><th scope="col">' . esc_html__( 'Dark', 'weave-style-guide' ) . '</th></tr></thead><tbody>' . $rows . '</tbody></table>';
}

echo wsg_wrap( 'dark-mode', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
