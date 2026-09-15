<?php
defined( 'ABSPATH' ) || exit;
$inner = '<p class="wsg-version">' . esc_html__( 'Everything on this page is read from the site when the page loads, so it always matches the theme.', 'weave-style-guide' ) . '</p>';
echo wsg_wrap( 'version', $inner, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wsg_wrap() wraps inner markup escaped at build in get_block_wrapper_attributes().
