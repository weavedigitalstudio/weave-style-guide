<?php
defined( 'ABSPATH' ) || exit;
$logos = wsg_logos();
$icon  = get_site_icon_url( 512 );
if ( ! $logos && ! $icon ) {
	echo wsg_wrap( 'logo', '<p class="wsg-note">' . esc_html__( 'No logo yet. Set the Site Logo in the header and the Site Icon in Settings, and put primary.svg, reversed.svg and mark.svg in the theme\'s assets/logo folder for the variants.', 'weave-style-guide' ) . '</p>', $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
	return;
}
// Logos made for light go on white and the site's background colour, as one panel when that background is white anyway.
// The reversed file is made for dark, so it sits on surface-inverse.
[ $bg, $bg_name ] = wsg_site_background();
$same = wsg_hex_to_rgb( $bg ) && wsg_contrast( $bg, '#ffffff' ) < 1.05;
/* translators: %s: where the site background comes from, e.g. surface or tertiary */
$site    = array( $bg, sprintf( __( 'Site background, %s', 'weave-style-guide' ), $bg_name ) );
$light   = $same ? array( $site ) : array( array( '#ffffff', __( 'White', 'weave-style-guide' ) ), $site );
$inverse = wsg_palette_colour( 'surface-inverse' );
$dark    = array( '' !== $inverse ? array( $inverse, 'surface-inverse' ) : array( wsg_darkest(), __( 'Darkest palette colour', 'weave-style-guide' ) ) );
$about   = $same ? __( 'Logos on the site\'s background colour, which is white.', 'weave-style-guide' ) : __( 'Logos on white and on the site\'s background colour.', 'weave-style-guide' );
if ( isset( $logos['reversed'] ) ) { $about .= ' ' . __( 'The reversed logo is made for dark, so it sits on the dark section colour.', 'weave-style-guide' ); }
$about .= ' ' . __( 'Download gives the original file.', 'weave-style-guide' );
$inner = '<p class="wsg-meta">' . esc_html( $about ) . '</p><div class="wsg-grid wsg-grid-logos">';
foreach ( $logos as $slug => $l ) {
	$stages = 'reversed' === $slug ? $dark : $light;
	$panels = '';
	foreach ( $stages as $i => [ $colour, $label ] ) {
		$panels .= '<div class="wsg-logo-stage" style="background:' . esc_attr( $colour ) . ';color:' . esc_attr( wsg_readable_on( $colour ) ) . '"><img src="' . esc_url( $l['url'] ) . '" alt="' . esc_attr( 0 === $i ? $l['label'] : '' ) . '" loading="lazy" decoding="async"><span class="wsg-stage-label">' . esc_html( $label ) . '</span></div>';
	}
	$inner .= '<figure class="wsg-card wsg-logo-card"><div class="wsg-logo-stages' . ( 1 === count( $stages ) ? ' is-single' : '' ) . '">' . $panels . '</div><figcaption class="wsg-meta"><span class="wsg-name">' . esc_html( $l['label'] ) . '</span>' . ( ! empty( $l['also'] ) ? ' <span class="wsg-small">' . esc_html( $l['also'] ) . '</span>' : '' ) . ( ! empty( $l['url'] ) ? ' <a class="wsg-small" href="' . esc_url( $l['url'] ) . '" download>' . esc_html__( 'Download', 'weave-style-guide' ) . '</a>' : '' ) . '</figcaption></figure>';
}
if ( $icon ) {
	$inner .= '<figure class="wsg-card wsg-logo-card wsg-site-icon"><div class="wsg-logo-stage wsg-icon-stage">' . implode( '', array_map( fn( $w ) => '<img class="wsg-icon-img" src="' . esc_url( $icon ) . '" style="width:' . $w . 'px;height:' . $w . 'px" width="' . $w . '" height="' . $w . '" alt="">', array( 96, 48, 32, 16 ) ) ) . '</div><figcaption class="wsg-meta"><span class="wsg-name">' . esc_html__( 'Site icon', 'weave-style-guide' ) . '</span> <span class="wsg-small">' . esc_html__( 'favicon and app icon, 96 to 16', 'weave-style-guide' ) . '</span> <a class="wsg-small" href="' . esc_url( $icon ) . '" download>' . esc_html__( 'Download', 'weave-style-guide' ) . '</a></figcaption></figure>';
}
$inner .= '</div>';
echo wsg_wrap( 'logo', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
