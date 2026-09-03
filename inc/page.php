<?php
/**
 * The styles page: one pattern, one creator, noindex.
 */
defined( 'ABSPATH' ) || exit;

function wsg_page_content(): string {
	$h = fn( string $t ) => '<!-- wp:heading {"level":2,"align":"wide","className":"wsg-h2"} --><h2 class="wp-block-heading alignwide wsg-h2">' . esc_html( $t ) . '</h2><!-- /wp:heading -->';
	return implode( "\n", array(
		'<!-- wp:weave-style-guide/version {"align":"wide"} /-->',
		$h( __( 'Logo', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/logo {"align":"wide"} /-->',
		$h( __( 'Colours', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/colours {"align":"wide"} /-->',
		$h( __( 'Fonts', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/type {"align":"wide"} /-->',
		$h( __( 'Spacing', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/spacing {"align":"wide"} /-->',
		$h( __( 'Buttons and block styles', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/blocks {"align":"wide"} /-->',
		$h( __( 'Forms', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/forms {"align":"wide"} /-->',
		$h( __( 'Icons', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/icons {"align":"wide"} /-->',
		$h( __( 'Contrast', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/contrast {"align":"wide"} /-->',
		$h( __( 'Patterns', 'weave-style-guide' ) ), '<!-- wp:weave-style-guide/patterns {"align":"wide"} /-->',
	) );
}

function wsg_register_pattern(): void {
	register_block_pattern_category( 'weave-style-guide', array( 'label' => __( 'Style guide', 'weave-style-guide' ) ) );
	register_block_pattern( 'weave-style-guide/page', array(
		'title'      => __( 'Style guide page', 'weave-style-guide' ),
		'categories' => array( 'weave-style-guide' ),
		'content'    => wsg_page_content(),
		'inserter'   => true,
	) );
}
add_action( 'init', 'wsg_register_pattern', 20 );

/** Create or refresh the /styles/ page. Returns the page ID. */
function wsg_create_page( string $slug = 'styles', bool $refresh = false ): int {
	$existing = get_page_by_path( $slug );
	$args = array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => __( 'Style guide', 'weave-style-guide' ), 'post_name' => $slug, 'post_content' => wsg_page_content() );
	if ( $existing ) {
		if ( $refresh ) { wp_update_post( array_merge( array( 'ID' => $existing->ID ), $args ) ); }
		$id = $existing->ID;
	} else {
		$id = (int) wp_insert_post( $args );
	}
	update_post_meta( $id, '_wsg_noindex', '1' );
	if ( function_exists( 'register_block_template' ) ) {
		update_post_meta( $id, '_wp_page_template', 'style-guide' ); // the template slug, not the registered id
	}
	return $id;
}

function wsg_noindex(): void {
	if ( is_page() && get_post_meta( get_queried_object_id(), '_wsg_noindex', true ) ) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}
}
add_action( 'wp_head', 'wsg_noindex', 1 );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'weave-style-guide create', function ( $args, $assoc ) {
		$id = wsg_create_page( $assoc['slug'] ?? 'styles', ! empty( $assoc['refresh'] ) );
		WP_CLI::success( 'Style guide page ' . $id . ': ' . get_permalink( $id ) );
	} );
}

/** Ability for the build sweep, when weave-abilities (WP Abilities API) is present. */
function wsg_register_ability(): void {
	if ( ! function_exists( 'wp_register_ability' ) ) { return; }
	wp_register_ability( 'weave/style-guide-create', array(
		'label'       => __( 'Create the style guide page', 'weave-style-guide' ),
		'description' => __( 'Creates or refreshes the noindex /styles/ page built from the live theme.', 'weave-style-guide' ),
		'category'    => 'weave',
		'input_schema'  => array( 'type' => 'object', 'properties' => array( 'slug' => array( 'type' => 'string' ), 'refresh' => array( 'type' => 'boolean' ) ) ),
		'output_schema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'integer' ), 'url' => array( 'type' => 'string' ) ) ),
		'execute_callback' => function ( $input ) { $id = wsg_create_page( $input['slug'] ?? 'styles', ! empty( $input['refresh'] ) ); return array( 'id' => $id, 'url' => get_permalink( $id ) ); },
		'permission_callback' => fn() => current_user_can( 'edit_pages' ),
	) );
}
add_action( 'wp_abilities_api_init', 'wsg_register_ability' );
