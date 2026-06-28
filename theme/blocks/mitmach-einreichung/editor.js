// ABOUTME: Editor-Registrierung des Mitmach-Einreichungs-Blocks.
// ABOUTME: Zeigt einen statischen Placeholder — das Formular wird server-side gerendert.

( function ( blocks, element, blockEditor ) {
    var el            = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;

    blocks.registerBlockType( 'wuerde/mitmach-einreichung', {
        edit: function () {
            return el(
                'div',
                useBlockProps( { style: { padding: '2rem', background: '#f0f0f0', borderRadius: '4px', textAlign: 'center' } } ),
                el( 'p', { style: { margin: 0, color: '#555' } }, '❤️ Mitmach-Einreichungsformular — wird im Frontend angezeigt' )
            );
        },
    } );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
