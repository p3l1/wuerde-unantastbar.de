// ABOUTME: Editor-Registrierung des Impressionen-Teaser-Blocks.
// ABOUTME: Ermöglicht Bildpool-Verwaltung via MediaUpload und Galerie-URL-Eingabe.

( function ( blocks, element, blockEditor ) {
	var el              = element.createElement;
	var useBlockProps   = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload     = blockEditor.MediaUpload;
	var MediaUploadCheck = blockEditor.MediaUploadCheck;
	var PanelBody       = window.wp.components.PanelBody;
	var Button          = window.wp.components.Button;
	var TextControl     = window.wp.components.TextControl;

	blocks.registerBlockType( 'wuerde/impressionen-teaser', {
		edit: function ( props ) {
			var images     = props.attributes.images     || [];
			var galleryUrl = props.attributes.galleryUrl || '';
			var heading    = props.attributes.heading    || 'Impressionen';
			var blockProps = useBlockProps( { className: 'impressionen-teaser-editor' } );

			function onSelectImages( selected ) {
				props.setAttributes( {
					images: selected.map( function ( img ) {
						return {
							id:  img.id,
							url: img.sizes && img.sizes.medium ? img.sizes.medium.url : img.url,
							alt: img.alt || img.title || '',
						};
					} ),
				} );
			}

			function removeImage( id ) {
				props.setAttributes( {
					images: images.filter( function ( img ) { return img.id !== id; } ),
				} );
			}

			var previewImages = images.slice( 0, 3 );

			return el(
				'div',
				blockProps,
				el( InspectorControls, null,
					el( PanelBody, { title: 'Einstellungen', initialOpen: true },
						el( TextControl, {
							label: 'Überschrift',
							value: heading,
							onChange: function ( val ) { props.setAttributes( { heading: val } ); },
						} ),
						el( TextControl, {
							label: 'Galerie-URL',
							value: galleryUrl,
							placeholder: '/galerie/',
							help: 'Zielseite beim Klick auf „Alle Impressionen"',
							onChange: function ( val ) { props.setAttributes( { galleryUrl: val } ); },
						} )
					),
					el( PanelBody, { title: 'Bilderpool (' + images.length + ' Bilder)', initialOpen: true },
						el( MediaUploadCheck, null,
							el( MediaUpload, {
								onSelect: onSelectImages,
								allowedTypes: [ 'image' ],
								multiple: true,
								gallery: false,
								value: images.map( function ( img ) { return img.id; } ),
								render: function ( ref ) {
									return el( Button, {
										onClick: ref.open,
										variant: 'secondary',
										style: { marginBottom: '12px' },
									}, images.length ? 'Bilder bearbeiten' : 'Bilder auswählen' );
								},
							} )
						),
						images.length > 0 && el( 'div', { className: 'impressionen-editor__thumbs' },
							images.map( function ( img ) {
								return el( 'div', { key: img.id, className: 'impressionen-editor__thumb' },
									el( 'img', { src: img.url, alt: img.alt } ),
									el( Button, {
										className: 'impressionen-editor__remove',
										isDestructive: true,
										onClick: function () { removeImage( img.id ); },
									}, '×' )
								);
							} )
						)
					)
				),

				el( 'div', { className: 'impressionen-teaser-preview' },
					el( 'h2', { className: 'impressionen-teaser-preview__heading' }, heading ),
					images.length === 0
						? el( 'div', { className: 'impressionen-editor__empty' },
							el( 'p', null, 'Noch keine Bilder ausgewählt. Wähle Bilder im Panel rechts aus.' )
						)
						: el( 'div', { className: 'impressionen-editor__grid' },
							previewImages.map( function ( img ) {
								return el( 'div', { key: img.id, className: 'impressionen-editor__preview-item' },
									el( 'img', { src: img.url, alt: img.alt } )
								);
							} ),
							images.length > 3 && el( 'p', { className: 'impressionen-editor__count' },
								images.length + ' Bilder im Pool — 3 werden zufällig ausgewählt'
							)
						),
					galleryUrl && el( 'p', { className: 'impressionen-editor__link-hint' },
						'Link: ', el( 'code', null, galleryUrl )
					)
				)
			);
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
