<?php
/**
 * Read the live theme: theme.json settings and styles, logo files, icons, patterns, block styles.
 */
defined( 'ABSPATH' ) || exit;

function wsg_settings(): array { static $s = null; return $s ??= (array) wp_get_global_settings(); }
function wsg_styles(): array { static $s = null; return $s ??= (array) wp_get_global_styles(); }

/** Theme palette only (core defaults are trimmed by the theme anyway). */
function wsg_palette(): array {
	$p = wsg_settings()['color']['palette']['theme'] ?? array();
	return array_values( array_filter( $p, fn( $c ) => ! empty( $c['slug'] ) && ! empty( $c['color'] ) ) );
}

/** A theme palette colour by slug, or '' when the theme doesn't have it. */
function wsg_palette_colour( string $slug ): string {
	foreach ( wsg_palette() as $c ) { if ( $c['slug'] === $slug ) { return (string) $c['color']; } }
	return '';
}

/**
 * The site's background colour as [ value, name ]: surface when the theme has it, otherwise the
 * global styles background (a preset reference followed to the palette), otherwise white.
 */
function wsg_site_background(): array {
	$surface = wsg_palette_colour( 'surface' );
	if ( '' !== $surface ) { return array( $surface, 'surface' ); }
	$bg = trim( (string) ( wsg_styles()['color']['background'] ?? '' ) );
	if ( preg_match( '/^(?:var\(--wp--preset--color--([a-z0-9-]+)\)|var:preset\|color\|([a-z0-9-]+))$/i', $bg, $m ) ) {
		$slug  = '' !== $m[1] ? $m[1] : ( $m[2] ?? '' );
		$value = wsg_palette_colour( $slug );
		return '' !== $value ? array( $value, $slug ) : array( '#ffffff', 'white' );
	}
	return '' !== $bg ? array( $bg, $bg ) : array( '#ffffff', 'white' );
}

function wsg_font_families(): array { return wsg_settings()['typography']['fontFamilies']['theme'] ?? array(); }
function wsg_font_sizes(): array { return wsg_settings()['typography']['fontSizes']['theme'] ?? array(); }
function wsg_spacing_sizes(): array { return wsg_settings()['spacing']['spacingSizes']['theme'] ?? array(); }
function wsg_custom(): array { return wsg_settings()['custom'] ?? array(); }

/** Logo files by convention: assets/logo/{primary,reversed,mark}.svg in the theme, plus the Site Logo. */
function wsg_logos(): array {
	$out = array();
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$out['site'] = array( 'label' => __( 'Site logo', 'weave-style-guide' ), 'html' => wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'wsg-logo-img' ) ), 'url' => wp_get_attachment_url( $logo_id ), 'surface' => 'surface' );
	}
	foreach ( array( 'primary' => 'surface', 'reversed' => 'surface-inverse', 'mark' => 'surface-subtle' ) as $slug => $surface ) {
		$file = get_theme_file_path( "assets/logo/$slug.svg" );
		if ( file_exists( $file ) ) {
			$out[ $slug ] = array( 'label' => ucfirst( $slug ), 'html' => (string) file_get_contents( $file ), 'url' => get_theme_file_uri( "assets/logo/$slug.svg" ), 'surface' => $surface );
		}
	}
	// The Site Logo is usually one of the theme's files uploaded again. When it is the same file (identical
	// bytes, or the same name give or take WordPress's -1 suffix), it shows once, on that file's card.
	$site_file = $logo_id ? (string) get_attached_file( $logo_id ) : '';
	if ( isset( $out['site'] ) && '' !== $site_file ) {
		$name = fn( string $path ): string => (string) preg_replace( '/-\d+(?=\.[a-z0-9]+$)/i', '', strtolower( wp_basename( $path ) ) );
		foreach ( array( 'primary', 'reversed', 'mark' ) as $slug ) {
			if ( ! isset( $out[ $slug ] ) ) { continue; }
			$theme_file = get_theme_file_path( "assets/logo/$slug.svg" );
			if ( $name( $site_file ) === $name( $theme_file ) || ( is_readable( $site_file ) && md5_file( $site_file ) === md5_file( $theme_file ) ) ) {
				$out[ $slug ]['also'] = __( 'Also the Site Logo', 'weave-style-guide' );
				unset( $out['site'] );
				break;
			}
		}
	}
	return $out;
}

/** Registered icons outside the core collection (the site's own set). */
function wsg_icons(): array {
	if ( ! class_exists( 'WP_Icons_Registry' ) ) { return array(); }
	$all = WP_Icons_Registry::get_instance()->get_registered_icons();
	return array_values( array_filter( $all, fn( $i ) => 'core' !== ( $i['collection'] ?? '' ) ) );
}

/** Patterns registered outside core (the theme's and any plugin's), or one category. */
function wsg_patterns( string $category = '' ): array {
	$all = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
	return array_values( array_filter( $all, function ( $p ) use ( $category ) {
		if ( str_starts_with( $p['name'], 'core/' ) || str_starts_with( $p['name'], 'weave-style-guide/' ) ) { return false; }
		return $category ? in_array( $category, $p['categories'] ?? array(), true ) : true;
	} ) );
}

function wsg_block_styles( string $block ): array {
	return WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( $block );
}

/** Element typography from global styles (h1..h6, p). */
function wsg_element_typography( string $element ): array {
	$t = wsg_styles()['elements'][ $element ]['typography'] ?? array();
	if ( 'p' === $element ) { $t = wsg_styles()['typography'] ?? array(); }
	return $t;
}

function wsg_css_var( string $type, string $slug ): string { return "var(--wp--preset--$type--$slug)"; }

/** The darkest solid colour in the palette, as a hex string (for dark stages). */
function wsg_darkest(): string {
	$best = '#111111'; $lum = 2;
	foreach ( wsg_palette() as $c ) { if ( ! wsg_hex_to_rgb( $c['color'] ) ) { continue; } $l = wsg_luminance( $c['color'] ); if ( $l < $lum ) { $lum = $l; $best = $c['color']; } }
	return $best;
}

/** The most saturated mid-tone in the palette (for the overlay demo band). */
function wsg_accent(): string {
	$best = '#888888'; $score = -1;
	foreach ( wsg_palette() as $c ) { $rgb = wsg_hex_to_rgb( $c['color'] ); if ( ! $rgb ) { continue; } $hsl = wsg_rgb_to_hsl( $rgb ); $l = wsg_luminance( $c['color'] ); if ( $l < 0.08 || $l > 0.7 ) { continue; } if ( $hsl[1] > $score ) { $score = $hsl[1]; $best = $c['color']; } }
	return $best;
}
