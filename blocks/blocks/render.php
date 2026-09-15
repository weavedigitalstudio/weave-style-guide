<?php
defined( 'ABSPATH' ) || exit;
$skip = fn( string $name ) => str_starts_with( $name, 'reveal' );
$btn = function ( string $style, string $label ) { $cls = $style ? '{"className":"is-style-' . $style . '"}' : ''; return '<!-- wp:button ' . $cls . ' --><div class="wp-block-button' . ( $style ? ' is-style-' . $style : '' ) . '"><a class="wp-block-button__link wp-element-button" href="#">' . esc_html( $label ) . '</a></div><!-- /wp:button -->'; };
$buttons = $btn( '', __( 'Default button', 'weave-style-guide' ) ); $dark_buttons = '';
$on_dark = fn( string $name, string $label ) => (bool) preg_match( '/inverse|dark|white|light/i', $name . ' ' . $label );
foreach ( wsg_block_styles( 'core/button' ) as $name => $s ) { if ( $skip( $name ) ) { continue; } $label = $s['label'] ?? $name; if ( $on_dark( $name, $label ) ) { $dark_buttons .= $btn( $name, $label ); } else { $buttons .= $btn( $name, $label ); } }
$inner = '<div class="wsg-blocks-buttons">' . do_blocks( '<!-- wp:buttons --><div class="wp-block-buttons">' . $buttons . '</div><!-- /wp:buttons -->' ) . '</div>';
if ( $dark_buttons ) { $inner .= '<p class="wsg-meta">' . esc_html__( 'Styles made for dark surfaces, shown on the darkest palette colour.', 'weave-style-guide' ) . '</p><div class="wsg-dark-stage" style="background:' . esc_attr( wsg_darkest() ) . '">' . do_blocks( '<!-- wp:buttons --><div class="wp-block-buttons">' . $dark_buttons . '</div><!-- /wp:buttons -->' ) . '</div>'; }
$lists = '';
foreach ( wsg_block_styles( 'core/list' ) as $name => $s ) { if ( $skip( $name ) ) { continue; } $lists .= '<div class="wsg-card"><p class="wsg-meta"><span class="wsg-name">' . esc_html( $s['label'] ?? $name ) . '</span> <code>is-style-' . esc_html( $name ) . '</code></p>' . do_blocks( '<!-- wp:list {"className":"is-style-' . $name . '"} --><ul class="wp-block-list is-style-' . $name . '"><!-- wp:list-item --><li>' . esc_html__( 'Editable by you', 'weave-style-guide' ) . '</li><!-- /wp:list-item --><!-- wp:list-item --><li>' . esc_html__( 'Fast on any device', 'weave-style-guide' ) . '</li><!-- /wp:list-item --></ul><!-- /wp:list -->' ) . '</div>'; }
if ( $lists ) { $inner .= '<h3 class="wsg-h3">' . esc_html__( 'List styles', 'weave-style-guide' ) . '</h3><div class="wsg-grid wsg-grid-lists">' . $lists . '</div>'; }
$rows = '';
foreach ( array( 'core/group', 'core/image', 'core/heading', 'core/paragraph', 'core/columns', 'core/column', 'core/cover', 'core/post-template', 'core/separator', 'core/quote' ) as $block ) { $names = array(); foreach ( wsg_block_styles( $block ) as $name => $s ) { $names[] = esc_html( $s['label'] ?? $name ) . ' <code>is-style-' . esc_html( $name ) . '</code>'; } if ( $names ) { $rows .= '<tr><th scope="row">' . esc_html( $block ) . '</th><td>' . implode( '<br>', $names ) . '</td></tr>'; } }
if ( $rows ) { $inner .= '<h3 class="wsg-h3">' . esc_html__( 'Other block styles', 'weave-style-guide' ) . '</h3><table class="wsg-table"><tbody>' . $rows . '</tbody></table>'; }
echo wsg_wrap( 'blocks', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
