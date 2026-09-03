<?php
/**
 * A block template for the styles page: the theme's header and footer parts,
 * the title, and the content at the theme's full content width (the page
 * template usually constrains content to prose width, which is too narrow
 * for swatch grids and the contrast matrix).
 */
defined( 'ABSPATH' ) || exit;

function wsg_register_template(): void {
	if ( ! function_exists( 'register_block_template' ) ) { return; }
	$content = '<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->
<!-- wp:group {"tagName":"main","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:post-title {"level":1} /-->
<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->';
	register_block_template( 'weave-style-guide//style-guide', array(
		'title'       => __( 'Style guide', 'weave-style-guide' ),
		'description' => __( 'Header, title and content at full content width, footer. Used by the styles page.', 'weave-style-guide' ),
		'content'     => $content,
		'post_types'  => array( 'page' ),
	) );
}
add_action( 'init', 'wsg_register_template' );
