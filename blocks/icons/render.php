<?php
defined( 'ABSPATH' ) || exit;
$icons = wsg_icons();
if ( ! $icons ) { echo wsg_wrap( 'icons', '<p class="wsg-note">' . esc_html__( 'No icon set registered. The theme registers assets/icons/*.svg through the WordPress 7.1 Icons API.', 'weave-style-guide' ) . '</p>', $attributes ); return; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
$inner = '<p class="wsg-meta">' . esc_html( sprintf( _n( '%d icon in the site set. Click one to copy its name for the Icon block.', '%d icons in the site set. Click one to copy its name for the Icon block.', count( $icons ), 'weave-style-guide' ), count( $icons ) ) ) . '</p><div class="wsg-grid wsg-grid-icons">';
foreach ( $icons as $i ) { $inner .= '<button type="button" class="wsg-card wsg-icon wsg-copy" data-copy="' . esc_attr( $i['name'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Copy %s', 'weave-style-guide' ), $i['name'] ) ) . '">' . wp_get_icon( $i['name'], array( 'size' => 28 ) ) . '<span class="wsg-small">' . esc_html( $i['label'] ) . '</span></button>'; }
$inner .= '</div>';
echo wsg_wrap( 'icons', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
