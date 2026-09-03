<?php
defined( 'ABSPATH' ) || exit;
$palette = array_values( array_filter( wsg_palette(), fn( $c ) => (bool) wsg_hex_to_rgb( $c['color'] ) ) );
if ( count( $palette ) < 2 ) { echo wsg_wrap( 'contrast', '', $attributes ); return; }
$inner = '<p class="wsg-meta">' . esc_html__( 'What text can go on each colour. AA means 4.5:1, fine for body text; AA large means 3:1, headings only; AAA is 7:1. Colours that pass nothing are listed so nobody has to guess.', 'weave-style-guide' ) . '</p><div class="wsg-pairings">';
foreach ( $palette as $bg ) {
	$chips = '';
	foreach ( $palette as $fg ) {
		if ( $fg['slug'] === $bg['slug'] ) { continue; }
		$r = wsg_contrast( $fg['color'], $bg['color'] ); if ( $r < 3 ) { continue; }
		$rating = wsg_rating( $r );
		$chips .= '<span class="wsg-pair" style="background:' . esc_attr( $bg['color'] ) . ';color:' . esc_attr( $fg['color'] ) . '"><strong>' . esc_html( $fg['name'] ) . '</strong> ' . esc_html( number_format( $r, 1 ) . ':1 ' . $rating ) . '</span>';
	}
	$inner .= '<div class="wsg-pairing-row"><div class="wsg-pairing-bg"><span class="wsg-chip" style="background:' . esc_attr( $bg['color'] ) . ';color:' . esc_attr( wsg_readable_on( $bg['color'] ) ) . '">' . esc_html( $bg['name'] ) . '</span></div><div class="wsg-pairing-chips">' . ( $chips ?: '<span class="wsg-small">' . esc_html__( 'Nothing in the palette passes on this colour. Use it for surfaces, not behind text.', 'weave-style-guide' ) . '</span>' ) . '</div></div>';
}
$inner .= '</div>';
echo wsg_wrap( 'contrast', $inner, $attributes );
