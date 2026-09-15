<?php
defined( 'ABSPATH' ) || exit;
$inner = '<p class="wsg-version">' . esc_html__( 'Everything on this page is read from the site when the page loads, so it always matches the theme.', 'weave-style-guide' ) . '</p>';
echo wsg_wrap( 'version', $inner, $attributes );
