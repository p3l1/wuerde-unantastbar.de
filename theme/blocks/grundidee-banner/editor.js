// ABOUTME: Block-Editor-Registrierung für wuerde/grundidee-banner.
// ABOUTME: Zeigt Vorschau und Felder für Titel, Button-Text, Button-URL und Farbe.
( function ( blocks, element, blockEditor, components ) {
  var el                = element.createElement;
  var registerBlock     = blocks.registerBlockType;
  var InspectorControls = blockEditor.InspectorControls;
  var RichText          = blockEditor.RichText;
  var PanelBody         = components.PanelBody;
  var TextControl       = components.TextControl;
  var SelectControl     = components.SelectControl;

  registerBlock( 'wuerde/grundidee-banner', {
    edit: function ( props ) {
      var attr    = props.attributes;
      var setAttr = props.setAttributes;

      return [
        el( InspectorControls, { key: 'controls' },
          el( PanelBody, { title: 'Banner-Einstellungen', initialOpen: true },
            el( SelectControl, {
              label: 'Farbe',
              value: attr.color,
              options: [
                { label: 'Türkis', value: 'teal' },
                { label: 'Gelb',   value: 'yellow' },
              ],
              onChange: function ( val ) { setAttr( { color: val } ); },
            } ),
            el( TextControl, {
              label: 'Button-Text',
              value: attr.buttonText,
              onChange: function ( val ) { setAttr( { buttonText: val } ); },
            } ),
            el( TextControl, {
              label: 'Button-URL',
              value: attr.buttonUrl,
              onChange: function ( val ) { setAttr( { buttonUrl: val } ); },
            } )
          )
        ),
        el( 'div', {
            key: 'preview',
            className: 'grundidee-banner-editor highlight-box highlight-box--' + attr.color,
          },
          el( RichText, {
            tagName: 'p',
            className: 'highlight-box__title grundidee-banner__title',
            value: attr.title,
            onChange: function ( val ) { setAttr( { title: val } ); },
            placeholder: 'Banner-Text eingeben …',
          } ),
          el( 'div', { className: 'grundidee-banner-editor__button' },
            el( 'span', { className: 'btn btn-crown' },
              attr.buttonText || 'Button-Text'
            )
          )
        ),
      ];
    },
    save: function () {
      return null; // server-side render
    },
  } );
} (
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components
) );
