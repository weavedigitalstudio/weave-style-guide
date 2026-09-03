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
echo wsg_wrap( 'patterns', $inner, $attributes );
