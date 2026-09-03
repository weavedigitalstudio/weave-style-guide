<?php
defined( 'ABSPATH' ) || exit;
$logos = wsg_logos();
$icon  = get_site_icon_url( 512 );
if ( ! $logos && ! $icon ) {
	echo wsg_wrap( 'logo', '<p class="wsg-note">' . esc_html__( 'No logo yet. Set the Site Logo in the header and the Site Icon in Settings, and put primary.svg, reversed.svg and mark.svg in the theme\'s assets/logo folder for the variants.', 'weave-style-guide' ) . '</p>', $attributes );
	return;
}
$inner = '<div class="wsg-grid wsg-grid-logos">';
foreach ( $logos as $slug => $l ) {
	$inner .= '<figure class="wsg-card wsg-logo-card has-' . esc_attr( $l['surface'] ) . '-background-color has-background"><div class="wsg-logo-stage">' . $l['html'] . '</div><figcaption class="wsg-meta"><span class="wsg-name">' . esc_html( $l['label'] ) . '</span>' . ( ! empty( $l['url'] ) ? ' <a class="wsg-small" href="' . esc_url( $l['url'] ) . '" download>' . esc_html__( 'Download', 'weave-style-guide' ) . '</a>' : '' ) . '</figcaption></figure>';
}
if ( $icon ) {
	$inner .= '<figure class="wsg-card wsg-logo-card wsg-site-icon"><div class="wsg-logo-stage wsg-icon-stage">' . implode( '', array_map( fn( $w ) => '<img class="wsg-icon-img" src="' . esc_url( $icon ) . '" style="width:' . $w . 'px;height:' . $w . 'px" width="' . $w . '" height="' . $w . '" alt="">', array( 96, 48, 32, 16 ) ) ) . '</div><figcaption class="wsg-meta"><span class="wsg-name">' . esc_html__( 'Site icon', 'weave-style-guide' ) . '</span> <span class="wsg-small">' . esc_html__( 'favicon and app icon, 96 to 16', 'weave-style-guide' ) . '</span> <a class="wsg-small" href="' . esc_url( $icon ) . '" download>' . esc_html__( 'Download', 'weave-style-guide' ) . '</a></figcaption></figure>';
}
$inner .= '</div>';
echo wsg_wrap( 'logo', $inner, $attributes );
