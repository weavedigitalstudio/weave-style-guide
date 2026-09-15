<?php
defined( 'ABSPATH' ) || exit;
$sizes = wsg_spacing_sizes();
if ( ! $sizes ) { echo wsg_wrap( 'spacing', '<p class="wsg-note">' . esc_html__( 'No spacing presets in theme.json.', 'weave-style-guide' ) . '</p>', $attributes ); return; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
$inner = '<div class="wsg-rows">';
foreach ( $sizes as $s ) { $var = wsg_css_var( 'spacing', $s['slug'] ); $inner .= '<div class="wsg-row"><div class="wsg-row-meta"><span class="wsg-name">' . esc_html( $s['name'] ?? $s['slug'] ) . '</span><br><code>' . esc_html( $var ) . '</code><br><span class="wsg-small">' . esc_html( (string) ( $s['size'] ?? '' ) ) . '</span></div><div class="wsg-row-sample"><span class="wsg-bar" style="width:' . esc_attr( $var ) . '"></span></div></div>'; }
$inner .= '</div>';
$radii = wsg_custom()['radius'] ?? array();
if ( is_array( $radii ) && $radii ) { $inner .= '<h3 class="wsg-h3">' . esc_html__( 'Radius', 'weave-style-guide' ) . '</h3><div class="wsg-grid wsg-grid-radius">'; foreach ( $radii as $k => $v ) { $inner .= '<div class="wsg-card wsg-radius"><div class="wsg-radius-sample" style="border-radius:var(--wp--custom--radius--' . esc_attr( $k ) . ')"></div><p class="wsg-meta"><span class="wsg-name">' . esc_html( $k ) . '</span><br><code>' . esc_html( (string) $v ) . '</code></p></div>'; } $inner .= '</div>'; }
echo wsg_wrap( 'spacing', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
