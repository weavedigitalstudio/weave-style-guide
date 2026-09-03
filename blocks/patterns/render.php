<?php
defined( 'ABSPATH' ) || exit;
$patterns = wsg_patterns( (string) ( $attributes['category'] ?? '' ) );
$patterns = array_filter( $patterns, fn( $p ) => ! str_starts_with( $p['name'], 'weave-style-guide/' ) );
if ( ! $patterns ) { echo wsg_wrap( 'patterns', '<p class="wsg-note">' . esc_html__( 'No theme patterns registered.', 'weave-style-guide' ) . '</p>', $attributes ); return; }
$skipped = array();
$patterns = array_filter( $patterns, function ( $p ) use ( &$skipped ) { if ( preg_match( '/<!-- wp:(post-content|template-part|comments|post-comments-form)\b/', $p['content'] ) ) { $skipped[] = $p['title']; return false; } return true; } );
$inner = '<p class="wsg-meta">' . esc_html( sprintf( _n( '%d pattern. Open one to see it rendered with the live styles.', '%d patterns. Open one to see it rendered with the live styles.', count( $patterns ), 'weave-style-guide' ), count( $patterns ) ) ) . '</p>';
foreach ( $patterns as $p ) { $inner .= '<details class="wsg-pattern"' . ( empty( $attributes['collapsed'] ) ? ' open' : '' ) . '><summary class="wsg-h3">' . esc_html( $p['title'] ) . ' <code class="wsg-small">' . esc_html( $p['name'] ) . '</code></summary><div class="wsg-pattern-render">' . do_blocks( $p['content'] ) . '</div></details>'; }
if ( $skipped ) { $inner .= '<p class="wsg-small">' . esc_html( sprintf( __( 'Not shown because they render the current page or a template part: %s.', 'weave-style-guide' ), implode( ', ', $skipped ) ) ) . '</p>'; }
// Patterns created in the editor (stored as wp_block posts), synced or not.
$user = get_posts( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'numberposts' => 100, 'orderby' => 'title', 'order' => 'ASC' ) );
if ( $user ) {
	$inner .= '<h3 class="wsg-h3">' . esc_html__( 'Patterns created in the editor', 'weave-style-guide' ) . '</h3><p class="wsg-meta">' . esc_html( sprintf( _n( '%d pattern made in the editor. Synced patterns update everywhere they are used; not synced ones are a starting point.', '%d patterns made in the editor. Synced patterns update everywhere they are used; not synced ones are a starting point.', count( $user ), 'weave-style-guide' ), count( $user ) ) ) . '</p>';
	foreach ( $user as $u ) {
		$sync = get_post_meta( $u->ID, 'wp_pattern_sync_status', true ) === 'unsynced' ? __( 'not synced', 'weave-style-guide' ) : __( 'synced', 'weave-style-guide' );
		$inner .= '<details class="wsg-pattern"' . ( empty( $attributes['collapsed'] ) ? ' open' : '' ) . '><summary class="wsg-h3">' . esc_html( $u->post_title ) . ' <code class="wsg-small">' . esc_html( $sync ) . '</code></summary><div class="wsg-pattern-render">' . do_blocks( $u->post_content ) . '</div></details>';
	}
}
echo wsg_wrap( 'patterns', $inner, $attributes );
