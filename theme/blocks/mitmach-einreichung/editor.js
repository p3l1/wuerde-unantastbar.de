// ABOUTME: Editor-Registrierung des Mitmach-Einreichungs-Blocks.
// ABOUTME: Zeigt Formular-Texte als bearbeitbare Felder im Inspector-Panel.

( function ( blocks, element, blockEditor ) {
    var el            = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody    = window.wp.components.PanelBody;
    var TextControl  = window.wp.components.TextControl;

    function field( label, attrKey, attrs, setAttributes ) {
        return el( TextControl, {
            label: label,
            value: attrs[ attrKey ],
            onChange: function ( val ) {
                var update = {};
                update[ attrKey ] = val;
                setAttributes( update );
            },
        } );
    }

    blocks.registerBlockType( 'wuerde/mitmach-einreichung', {
        edit: function ( props ) {
            var attrs = props.attributes;
            var set   = props.setAttributes;
            var f     = function ( label, key ) { return field( label, key, attrs, set ); };

            return el(
                'div',
                useBlockProps( { style: { padding: '2rem', background: '#f0f0f0', borderRadius: '4px', textAlign: 'center' } } ),
                el( InspectorControls, null,
                    el( PanelBody, { title: 'Beschriftungen', initialOpen: true },
                        f( 'Name', 'labelName' ),
                        f( 'E-Mail', 'labelEmail' ),
                        f( 'Titel', 'labelTitel' ),
                        f( 'Beschreibung', 'labelBeschreibung' ),
                        f( 'Kategorie', 'labelKategorie' ),
                        f( 'Adresse', 'labelAdresse' ),
                        f( 'Ort', 'labelOrt' )
                    ),
                    el( PanelBody, { title: 'Platzhalter', initialOpen: false },
                        f( 'Titel', 'placeholderTitel' ),
                        f( 'Beschreibung', 'placeholderBeschreibung' ),
                        f( 'Adresse', 'placeholderAdresse' ),
                        f( 'Ort', 'placeholderOrt' ),
                        f( 'Kategorie-Dropdown', 'placeholderKategorie' )
                    ),
                    el( PanelBody, { title: 'Schaltflächen', initialOpen: false },
                        f( 'Adresssuche', 'btnAdresse' ),
                        f( 'Formular absenden', 'btnSubmit' )
                    ),
                    el( PanelBody, { title: 'Sonstige Texte', initialOpen: false },
                        f( 'Karten-Toggle', 'karteToggle' ),
                        f( 'Dateihinweis (vor E-Mail)', 'dateiHinweisVor' ),
                        f( 'Dateihinweis (nach E-Mail)', 'dateiHinweisNach' )
                    )
                ),
                el( 'p', { style: { margin: 0, color: '#555' } }, '❤️ Mitmach-Einreichungsformular — wird im Frontend angezeigt' )
            );
        },
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
