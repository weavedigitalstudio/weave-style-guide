<?php
/**
 * Plugin Name:       Weave Style Guide
 * Plugin URI:        https://github.com/weavedigitalstudio/weave-style-guide
 * Description:       An auto-generated styles page for block themes: logo, colours, type, spacing, buttons and block styles, icons, patterns, contrast and helper classes, all read from the live theme so nothing on the page is typed in.
 * Version:           0.1.3
 * Requires at least: 7.1
 * Requires PHP:      8.1
 * Author:            Weave Digital Studio
 * License:           MIT
 * Text Domain:       weave-style-guide
 * GitHub Plugin URI: weavedigitalstudio/weave-style-guide
 * Primary Branch:    main
 */

defined( 'ABSPATH' ) || exit;

define( 'WSG_VERSION', '0.1.3' );
define( 'WSG_DIR', plugin_dir_path( __FILE__ ) );
define( 'WSG_URL', plugin_dir_url( __FILE__ ) );

require_once WSG_DIR . 'inc/data.php';
require_once WSG_DIR . 'inc/colour.php';
require_once WSG_DIR . 'inc/page.php';
require_once WSG_DIR . 'inc/template.php';
require_once WSG_DIR . 'inc/github-updater.php';
\WeaveStyleGuide\Updater\GitHubUpdater::init( __FILE__ );

/**
 * Register the guide blocks. Each block is server-rendered from blocks/<name>/render.php.
 */
function wsg_register_blocks(): void {
	foreach ( glob( WSG_DIR . 'blocks/*/block.json' ) as $json ) {
		register_block_type( dirname( $json ) );
	}
}
add_action( 'init', 'wsg_register_blocks' );

/**
 * One editor script for all blocks (no build step): registers each block with a
 * server-side preview.
 */
function wsg_editor_assets(): void {
	wp_enqueue_script( 'wsg-editor', WSG_URL . 'assets/editor.js', array( 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-block-editor', 'wp-components' ), WSG_VERSION, true );
}
add_action( 'enqueue_block_editor_assets', 'wsg_editor_assets' );

/**
 * Front-end and editor styles plus the click-to-copy script, only when a guide block renders.
 */
function wsg_register_assets(): void {
	wp_register_style( 'wsg', WSG_URL . 'assets/style.css', array(), WSG_VERSION );
	wp_style_add_data( 'wsg', 'path', WSG_DIR . 'assets/style.css' );
	wp_register_script( 'wsg-copy', WSG_URL . 'assets/copy.js', array(), WSG_VERSION, array( 'strategy' => 'defer' ) );
}
add_action( 'init', 'wsg_register_assets', 9 ); // before the blocks register, so block.json "style": "wsg" resolves

function wsg_enqueue_assets(): void {
	wp_enqueue_style( 'wsg' );
	wp_enqueue_script( 'wsg-copy' );
}

/** Wrap a block's output with the shared classes. */
function wsg_wrap( string $section, string $inner, array $attributes = array() ): string {
	wsg_enqueue_assets();
	$attrs = get_block_wrapper_attributes( array( 'class' => 'wsg wsg-' . $section ) );
	return "<section $attrs>$inner</section>";
}
