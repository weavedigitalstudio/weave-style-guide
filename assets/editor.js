( function ( wp ) {
	var el = wp.element.createElement, SSR = wp.serverSideRender, useBlockProps = wp.blockEditor.useBlockProps;
	[ 'version', 'logo', 'colours', 'type', 'spacing', 'blocks', 'forms', 'icons', 'contrast', 'patterns' ].forEach( function ( name ) {
		wp.blocks.registerBlockType( 'weave-style-guide/' + name, {
			edit: function ( props ) { return el( 'div', useBlockProps(), el( SSR, { block: 'weave-style-guide/' + name, attributes: props.attributes } ) ); },
			save: function () { return null; }
		} );
	} );
} )( window.wp );
