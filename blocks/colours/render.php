<?php
defined( 'ABSPATH' ) || exit;
$palette = wsg_palette();
if ( ! $palette ) { echo wsg_wrap( 'colours', '<p class="wsg-note">' . esc_html__( 'No theme palette found in theme.json.', 'weave-style-guide' ) . '</p>', $attributes ); return; }
$copy = fn( string $value, string $label = '' ) => '<button type="button" class="wsg-copy" data-copy="' . esc_attr( $value ) . '" aria-label="' . esc_attr( sprintf( __( 'Copy %s', 'weave-style-guide' ), $label ?: $value ) ) . '"><code>' . esc_html( $value ) . '</code></button>';
$slugs = array_column( $palette, 'slug' );
$is_variant = function ( string $slug ) use ( $slugs ): bool { return (bool) preg_match( '/^(.+)-(light|lighter|dark|darker|strong|soft|muted|tint|shade|[0-9]{1,3})$/', $slug, $m ) && in_array( $m[1], $slugs, true ); };
$card = function ( array $c ) use ( &$css, &$json, $copy ): string {
	$hex = strtoupper( $c['color'] ); $rgb = wsg_hex_to_rgb( $hex ); $hsl = $rgb ? wsg_rgb_to_hsl( $rgb ) : null; $var = wsg_css_var( 'color', $c['slug'] );
	$border = $rgb && wsg_needs_border( $hex ) ? ' wsg-has-border' : '';
	$css .= "  --wp--preset--color--{$c['slug']}: $hex;\n"; $json[ $c['slug'] ] = array( 'name' => $c['name'], 'hex' => $hex, 'rgb' => $rgb, 'hsl' => $hsl );
	return '<div class="wsg-card wsg-colour' . $border . '"><div class="wsg-swatch" style="background:' . esc_attr( $var ) . '"></div><p class="wsg-colour-name wsg-name">' . esc_html( $c['name'] ) . '</p><div class="wsg-values">' . $copy( $hex ) . ( $rgb ? $copy( sprintf( 'rgb(%d %d %d)', ...$rgb ) ) : '' ) . ( $hsl ? $copy( sprintf( 'hsl(%d %d%% %d%%)', ...$hsl ) ) : '' ) . $copy( $var, $c['slug'] ) . '</div></div>';
};
$css = ''; $json = array(); $overlays = array(); $mains = ''; $variants = '';
foreach ( $palette as $c ) {
	if ( ! wsg_hex_to_rgb( $c['color'] ) ) { $overlays[] = $c; continue; }
	if ( $is_variant( $c['slug'] ) ) { $variants .= $card( $c ); } else { $mains .= $card( $c ); }
}
$inner = '<div class="wsg-grid wsg-grid-colours">' . $mains . '</div>';
if ( $variants ) { $inner .= '<h3 class="wsg-h3">' . esc_html__( 'Tints and variants', 'weave-style-guide' ) . '</h3><p class="wsg-meta">' . esc_html__( 'Lighter, darker and hover versions of the main colours. Use the main colour first; reach for these for hover states, soft backgrounds and borders.', 'weave-style-guide' ) . '</p><div class="wsg-grid wsg-grid-colours">' . $variants . '</div>'; }
if ( $overlays ) {
	$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Overlays', 'weave-style-guide' ) . '</h3><p class="wsg-meta">' . esc_html__( 'Translucent helpers for panels over photos and dark bands. Not brand colours. Shown as a panel over a dark to light band so the transparency reads.', 'weave-style-guide' ) . '</p><div class="wsg-grid wsg-grid-overlays">';
	$dark = wsg_darkest();
	foreach ( $overlays as $c ) { $var = wsg_css_var( 'color', $c['slug'] ); $inner .= '<div class="wsg-card wsg-colour"><div class="wsg-overlay-stage" style="background:linear-gradient(90deg,' . esc_attr( $dark ) . ' 0%,' . esc_attr( $dark ) . ' 30%,' . esc_attr( wsg_accent() ) . ' 60%,#ffffff 100%)"><span class="wsg-overlay-panel" style="background:' . esc_attr( $var ) . '"></span></div><p class="wsg-colour-name wsg-name">' . esc_html( $c['name'] ) . '</p><div class="wsg-values">' . $copy( $c['color'] ) . $copy( $var, $c['slug'] ) . '</div></div>'; }
	$inner .= '</div>';
}
$inner .= '<p class="wsg-actions">' . $copy( ":root {\n$css}", __( 'palette as CSS', 'weave-style-guide' ) ) . ' ' . $copy( wp_json_encode( $json, JSON_PRETTY_PRINT ), __( 'palette as JSON', 'weave-style-guide' ) ) . '</p>';
$inner = str_replace( array( '<code>:root {', '<code>{' ), array( '<code>' . esc_html__( 'Copy palette as CSS', 'weave-style-guide' ) . '<span hidden>:root {', '<code>' . esc_html__( 'Copy palette as JSON', 'weave-style-guide' ) . '<span hidden>{' ), $inner );
$inner = preg_replace( '/(<span hidden>.*?)<\/code>/s', '$1</span></code>', $inner );
echo wsg_wrap( 'colours', $inner, $attributes );
