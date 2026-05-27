// ABOUTME: Editor-Registrierung des Mitmach-Liste-Blocks.
// ABOUTME: Lädt Kategorien via wp.data und zeigt Einstellungen im Inspector-Panel.

( function ( blocks, element, blockEditor, data ) {
	var el = element.createElement;
	var useState = element.useState;
	var useEffect = element.useEffect;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = window.wp.components.PanelBody;
	var ToggleControl = window.wp.components.ToggleControl;
	var CheckboxControl = window.wp.components.CheckboxControl;
	var Spinner = window.wp.components.Spinner;
	var useSelect = data.useSelect;

	blocks.registerBlockType( 'wuerde/mitmach-list', {
		edit: function ( props ) {
			var blockProps = useBlockProps( { className: 'wuerde-list-placeholder' } );
			var hiddenCategories = props.attributes.hiddenCategories || [];

			var terms = useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'taxonomy', 'wuerde_kategorie', {
					per_page: -1,
					orderby: 'name',
					order: 'asc',
				} );
			}, [] );

			function toggleCategory( slug, checked ) {
				var next = checked
					? hiddenCategories.filter( function ( s ) { return s !== slug; } )
					: hiddenCategories.concat( [ slug ] );
				props.setAttributes( { hiddenCategories: next } );
			}

			var visibleCount = terms
				? terms.filter( function ( t ) { return ! hiddenCategories.includes( t.slug ); } ).length
				: 0;

			return el(
				'div',
				blockProps,
				el( InspectorControls, null,
					el( PanelBody, { title: 'Einstellungen', initialOpen: true },
						el( ToggleControl, {
							label: 'Suchfeld anzeigen',
							checked: props.attributes.showSearch,
							onChange: function ( val ) { props.setAttributes( { showSearch: val } ); },
						} )
					),
					el( PanelBody, { title: 'Kategorien', initialOpen: true },
						! terms
							? el( Spinner )
							: terms.map( function ( term ) {
								return el( CheckboxControl, {
									key: term.slug,
									label: term.name + ' (' + term.count + ')',
									checked: ! hiddenCategories.includes( term.slug ),
									onChange: function ( checked ) { toggleCategory( term.slug, checked ); },
								} );
							} )
					)
				),
				el( 'div', { className: 'wuerde-list-placeholder__inner' },
					el( 'div', { className: 'wuerde-list-placeholder__icon' },
						el( 'svg', {
							width: '56', height: '56', viewBox: '0 0 56 56',
							fill: 'none', xmlns: 'http://www.w3.org/2000/svg',
							'aria-hidden': 'true',
						},
							el( 'rect', { width: '56', height: '56', rx: '8', fill: 'currentColor', opacity: '0.08' } ),
							el( 'rect', { x: '12', y: '14', width: '32', height: '4', rx: '2', fill: 'currentColor', opacity: '0.4' } ),
							el( 'rect', { x: '12', y: '24', width: '24', height: '4', rx: '2', fill: 'currentColor', opacity: '0.3' } ),
							el( 'rect', { x: '12', y: '34', width: '28', height: '4', rx: '2', fill: 'currentColor', opacity: '0.3' } ),
							el( 'rect', { x: '12', y: '44', width: '20', height: '4', rx: '2', fill: 'currentColor', opacity: '0.2' } )
						)
					),
					el( 'p', { className: 'wuerde-list-placeholder__title' }, 'Mitmach-Liste' ),
					el( 'p', { className: 'wuerde-list-placeholder__meta' },
						terms
							? ( visibleCount + ' von ' + terms.length + ' Kategorien · ' + ( props.attributes.showSearch ? 'Suche aktiv' : 'Suche ausgeblendet' ) )
							: 'Kategorien werden geladen …'
					),
					el( 'p', { className: 'wuerde-list-placeholder__hint' },
						'Einstellungen im Block-Panel rechts · Liste wird nur im Frontend geladen'
					)
				)
			);
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.data );
