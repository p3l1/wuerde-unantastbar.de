( function ( blocks, element, blockEditor ) {
	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = window.wp.components.PanelBody;
	var RangeControl = window.wp.components.RangeControl;
	var TextControl = window.wp.components.TextControl;
	var SelectControl = window.wp.components.SelectControl;

	blocks.registerBlockType( 'wuerde/mitmach-map', {
		edit: function ( props ) {
			var blockProps = useBlockProps( { className: 'wuerde-block-placeholder' } );
			return el(
				'div',
				blockProps,
				el( InspectorControls, null,
					el( PanelBody, { title: 'Karteneinstellungen', initialOpen: true },
						el( RangeControl, {
							label: 'Zoom-Stufe',
							value: props.attributes.zoom,
							min: 4,
							max: 12,
							onChange: function ( val ) { props.setAttributes( { zoom: val } ); },
						} ),
						el( TextControl, {
							label: 'Höhe (CSS)',
							value: props.attributes.height,
							onChange: function ( val ) { props.setAttributes( { height: val } ); },
							help: 'z. B. 480px oder 60vh',
						} ),
						el( SelectControl, {
							label: 'Kartenstil',
							value: props.attributes.tileStyle,
							options: [
								{ label: 'OpenStreetMap (Standard)',  value: 'osm' },
								{ label: 'Topografisch',              value: 'topo' },
								{ label: 'Humanitarian (HOT)',        value: 'humanitarian' },
								{ label: 'Graustufen',                value: 'grayscale' },
							],
							onChange: function ( val ) { props.setAttributes( { tileStyle: val } ); },
						} )
					)
				),
				el( 'div', { className: 'wuerde-map-placeholder', style: { height: props.attributes.height } },
				el( 'div', { className: 'wuerde-map-placeholder__icon' },
					el( 'svg', {
						width: '64', height: '64', viewBox: '0 0 64 64',
						fill: 'none', xmlns: 'http://www.w3.org/2000/svg',
						'aria-hidden': 'true',
					},
						el( 'rect', { width: '64', height: '64', rx: '8', fill: 'currentColor', opacity: '0.08' } ),
						el( 'path', { d: 'M32 12C23.16 12 16 19.16 16 28c0 12 16 24 16 24s16-12 16-24c0-8.84-7.16-16-16-16zm0 21a5 5 0 1 1 0-10 5 5 0 0 1 0 10z', fill: 'currentColor', opacity: '0.5' } )
					)
				),
				el( 'p', { className: 'wuerde-map-placeholder__title' }, 'Interaktive Deutschlandkarte' ),
				el( 'p', { className: 'wuerde-map-placeholder__meta' },
					'Höhe: ', el( 'strong', null, props.attributes.height ),
					' · Zoom: ', el( 'strong', null, String( props.attributes.zoom ) ),
					' · Stil: ', el( 'strong', null, props.attributes.tileStyle )
				),
				el( 'p', { className: 'wuerde-map-placeholder__hint' },
					'Einstellungen im Block-Panel rechts · Karte wird nur im Frontend geladen'
				)
			)
			);
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
