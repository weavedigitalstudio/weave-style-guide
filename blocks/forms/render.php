<?php
defined( 'ABSPATH' ) || exit;
$inner = '';
if ( class_exists( 'GFAPI' ) ) {
	$id = (int) ( $attributes['formId'] ?? 0 );
	if ( ! $id ) { foreach ( GFAPI::get_forms( true ) as $f ) { $id = (int) $f['id']; break; } }
	if ( $id ) {
		$form  = GFAPI::get_form( $id );
		$inner = '<p class="wsg-meta">' . esc_html( sprintf( __( 'Gravity Form "%1$s" (id %2$d) as the theme styles it. Fields, labels, help text, errors and the button all come from theme.json through the form defaults.', 'weave-style-guide' ), $form['title'] ?? '', $id ) ) . '</p><div class="wsg-form-stage">' . do_shortcode( '[gravityform id="' . $id . '" title="false" description="false" ajax="false"]' ) . '</div>';
	} else {
		$inner = '<p class="wsg-note">' . esc_html__( 'Gravity Forms is active but has no forms yet.', 'weave-style-guide' ) . '</p>';
	}
} else {
	$inner = '<p class="wsg-note">' . esc_html__( 'No form plugin active. Install Gravity Forms and this section renders the first form with the theme styling.', 'weave-style-guide' ) . '</p>';
}
echo wsg_wrap( 'forms', $inner, $attributes );
