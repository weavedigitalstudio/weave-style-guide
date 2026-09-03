<?php
defined( 'ABSPATH' ) || exit;
$theme = wp_get_theme(); $json = get_stylesheet_directory() . '/theme.json';
$stamp = file_exists( $json ) ? wp_date( get_option( 'date_format' ) . ' H:i', filemtime( $json ) ) : '';
$inner = '<p class="wsg-version">' . esc_html( sprintf( __( '%1$s %2$s. theme.json updated %3$s. WordPress %4$s. Everything on this page is read from the theme when the page loads.', 'weave-style-guide' ), $theme->get( 'Name' ), $theme->get( 'Version' ), $stamp, get_bloginfo( 'version' ) ) ) . '</p>';
echo wsg_wrap( 'version', $inner, $attributes );
