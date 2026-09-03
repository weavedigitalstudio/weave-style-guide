<?php
defined( 'ABSPATH' ) || exit;
$inner = '';
$fams = wsg_font_families();
if ( $fams ) {
	$inner .= '<div class="wsg-grid wsg-grid-families">';
	foreach ( $fams as $f ) {
		$var = wsg_css_var( 'font-family', $f['slug'] );
		$inner .= '<div class="wsg-card wsg-family" style="font-family:' . esc_attr( $var ) . '"><p class="wsg-family-name">' . esc_html( $f['name'] ) . '</p><p class="wsg-family-big">' . esc_html__( 'The quick brown fox jumps over the lazy dog', 'weave-style-guide' ) . '</p><p class="wsg-family-alpha">ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789 &amp; ? ! $ %</p><p class="wsg-meta wsg-family-meta"><code>' . esc_html( $var ) . '</code> <span class="wsg-small">' . esc_html( $f['fontFamily'] ?? '' ) . '</span></p></div>';
	}
	$inner .= '</div>';
}
$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Headings and body', 'weave-style-guide' ) . '</h3><div class="wsg-elements" aria-hidden="true">';
foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $el ) { $t = wsg_element_typography( $el ); $inner .= "<$el class=\"wsg-el\">" . esc_html( strtoupper( $el ) . ' ' . __( 'heading as the theme sets it', 'weave-style-guide' ) ) . "</$el><p class=\"wsg-small wsg-el-meta\">" . esc_html( implode( '  ·  ', array_filter( array( $t['fontSize'] ?? '', $t['fontWeight'] ?? '', $t['lineHeight'] ?? '', $t['letterSpacing'] ?? '' ) ) ) ) . ' <span class=\"wsg-now\" data-now=\"font-size\"></span></p>'; }
$inner .= '<p>' . esc_html__( 'Body copy. We build and host WordPress sites for funeral homes and local trades across New Zealand, and every site stays editable by the people who own it. This paragraph is the theme\'s default text style with a link to show the link colour.', 'weave-style-guide' ) . ' <a href="#">' . esc_html__( 'A text link', 'weave-style-guide' ) . '</a></p></div>';
$quote = '<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>' . esc_html__( 'They rebuilt our site so our own team can change it. Six months on, we have not needed to call them once.', 'weave-style-guide' ) . '</p><!-- /wp:paragraph --><cite>' . esc_html__( 'Sam Taylor, Taylor Family Funerals', 'weave-style-guide' ) . '</cite></blockquote><!-- /wp:quote -->';
$pull  = '<!-- wp:pullquote --><figure class="wp-block-pullquote"><blockquote><p>' . esc_html__( 'A pull quote lifts one line out of the copy and lets it breathe.', 'weave-style-guide' ) . '</p><cite>' . esc_html__( 'Pull quote block', 'weave-style-guide' ) . '</cite></blockquote></figure><!-- /wp:pullquote -->';
$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Quote and pull quote', 'weave-style-guide' ) . '</h3><div class="wsg-grid wsg-grid-quotes"><div>' . do_blocks( $quote ) . '</div><div>' . do_blocks( $pull ) . '</div></div>';
$sizes = wsg_font_sizes();
if ( $sizes ) {
	$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Size presets', 'weave-style-guide' ) . '</h3><p class="wsg-meta">' . esc_html__( 'The sizes the editor offers. Most are fluid: they scale with the screen between the mobile and desktop values shown, so the live size at this screen width sits beside each one. Resize the window and it changes.', 'weave-style-guide' ) . '</p><div class="wsg-rows">';
	foreach ( $sizes as $s ) {
		$val = is_array( $s['size'] ?? null ) ? '' : (string) ( $s['size'] ?? '' ); $fluid = $s['fluid'] ?? null;
		$range = is_array( $fluid ) ? sprintf( '%s to %s', $fluid['min'] ?? '', $fluid['max'] ?? '' ) : $val;
		$inner .= '<div class="wsg-row"><div class="wsg-row-meta"><span class="wsg-name">' . esc_html( $s['name'] ?? $s['slug'] ) . '</span><br><code>' . esc_html( wsg_css_var( 'font-size', $s['slug'] ) ) . '</code><br><span class="wsg-small">' . esc_html( $range ) . '</span> <span class="wsg-small wsg-now" data-now="font-size"></span></div><div class="wsg-row-sample" style="font-size:' . esc_attr( wsg_css_var( 'font-size', $s['slug'] ) ) . '">' . esc_html__( 'Build sites clients can edit', 'weave-style-guide' ) . '</div></div>';
	}
	$inner .= '</div>';
}
echo wsg_wrap( 'type', $inner, $attributes );
