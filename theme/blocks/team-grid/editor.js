// ABOUTME: Block-Editor-UI für den Team-Raster-Block.
// ABOUTME: Zeigt Personen-Auswahl und Layout-Einstellungen im Inspector-Panel.
( function () {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, CheckboxControl, Placeholder } = wp.components;
    const { useSelect } = wp.data;
    const { __ } = wp.i18n;

    registerBlockType( 'wuerde/team-grid', {
        edit( { attributes, setAttributes } ) {
            const { layout, selectedIds } = attributes;
            const blockProps = useBlockProps();

            const persons = useSelect( ( select ) => {
                return select( 'core' ).getEntityRecords( 'postType', 'wuerde_person', {
                    per_page: 100,
                    status: 'publish',
                    orderby: 'menu_order',
                    order: 'asc',
                } ) ?? [];
            }, [] );

            const togglePerson = ( id, checked ) => {
                if ( checked ) {
                    setAttributes( { selectedIds: [ ...selectedIds, id ] } );
                } else {
                    setAttributes( { selectedIds: selectedIds.filter( ( i ) => i !== id ) } );
                }
            };

            return wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement(
                    InspectorControls,
                    null,
                    wp.element.createElement(
                        PanelBody,
                        { title: __( 'Layout', 'wuerde-unantastbar' ), initialOpen: true },
                        wp.element.createElement( SelectControl, {
                            label: __( 'Karten-Stil', 'wuerde-unantastbar' ),
                            value: layout,
                            options: [
                                { label: 'Horizontal (breit)', value: 'horizontal' },
                                { label: 'Vertikal (kompakt)', value: 'vertical' },
                            ],
                            onChange: ( val ) => setAttributes( { layout: val } ),
                        } )
                    ),
                    wp.element.createElement(
                        PanelBody,
                        { title: __( 'Personen', 'wuerde-unantastbar' ), initialOpen: true },
                        persons.length === 0
                            ? wp.element.createElement( 'p', null, __( 'Keine veröffentlichten Personen gefunden.', 'wuerde-unantastbar' ) )
                            : persons.map( ( person ) =>
                                wp.element.createElement( CheckboxControl, {
                                    key: person.id,
                                    label: person.title.rendered,
                                    checked: selectedIds.includes( person.id ),
                                    onChange: ( checked ) => togglePerson( person.id, checked ),
                                } )
                            )
                    )
                ),
                wp.element.createElement( Placeholder, {
                    icon: 'groups',
                    label: __( 'Team-Raster', 'wuerde-unantastbar' ),
                    instructions: selectedIds.length === 0
                        ? __( 'Wähle im rechten Panel Personen aus.', 'wuerde-unantastbar' )
                        : selectedIds.length + __( ' Person(en) ausgewählt — Vorschau auf der Seite.', 'wuerde-unantastbar' ),
                } )
            );
        },
        save: () => null,
    } );
} )();
