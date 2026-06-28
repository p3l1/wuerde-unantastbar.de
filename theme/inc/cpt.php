<?php
// ABOUTME: Custom Post Type, Taxonomien, Post Meta und Admin-Oberfläche für Mitmach-Beiträge.
// ABOUTME: Registriert wuerde_beitrag mit wuerde_kategorie/wuerde_ort und Koordinaten-Meta-Box.

function wuerde_register_cpt() {
    register_post_type( 'wuerde_beitrag', [
        'labels'        => [
            'name'               => 'Mitmach-Beiträge',
            'singular_name'      => 'Mitmach-Beitrag',
            'add_new_item'       => 'Neuen Beitrag hinzufügen',
            'edit_item'          => 'Beitrag bearbeiten',
            'search_items'       => 'Beiträge suchen',
            'not_found'          => 'Keine Beiträge gefunden.',
            'not_found_in_trash' => 'Keine Beiträge im Papierkorb.',
        ],
        'public'        => true,
        'has_archive'   => false,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-heart',
        'rewrite'       => [ 'slug' => 'beitrag' ],
    ] );

    register_taxonomy( 'wuerde_kategorie', 'wuerde_beitrag', [
        'labels'            => [
            'name'          => 'Kategorien',
            'singular_name' => 'Kategorie',
            'edit_item'     => 'Kategorie bearbeiten',
            'add_new_item'  => 'Neue Kategorie hinzufügen',
        ],
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mitmach-kategorie' ],
    ] );

    register_taxonomy( 'wuerde_ort', 'wuerde_beitrag', [
        'labels'            => [
            'name'          => 'Orte',
            'singular_name' => 'Ort',
            'edit_item'     => 'Ort bearbeiten',
            'add_new_item'  => 'Neuen Ort hinzufügen',
        ],
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mitmach-ort' ],
    ] );

    register_post_meta( 'wuerde_beitrag', 'wuerde_lat', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
    ] );

    register_post_meta( 'wuerde_beitrag', 'wuerde_lng', [
        'type'         => 'number',
        'single'       => true,
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'wuerde_register_cpt' );

// Initiale Kategorie-Terms anlegen und veraltete Terms entfernen (idempotent).
function wuerde_seed_categories() {
    $obsolete = [
        'kirchengemeinden',
        'kommunal',
        'handwerk',
        'gesundheit',
        'sonstiges',
    ];

    foreach ( $obsolete as $slug ) {
        $term = get_term_by( 'slug', $slug, 'wuerde_kategorie' );
        if ( $term ) {
            wp_delete_term( $term->term_id, 'wuerde_kategorie' );
        }
    }

    $terms = [
        'kunst-kultur'     => 'Kunst und Kultur',
        'gespraech'        => 'Gespräch und Diskussion',
        'strasse'          => 'Auf der Straße',
        'spiel-spass'      => 'Spiel und Spaß',
        'bildung'          => 'Bildungseinrichtungen',
        'soziales'         => 'Soziale Einrichtungen',
        'betriebe'         => 'Betriebe und Unternehmen',
    ];

    foreach ( $terms as $slug => $name ) {
        if ( ! term_exists( $slug, 'wuerde_kategorie' ) ) {
            wp_insert_term( $name, 'wuerde_kategorie', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', 'wuerde_seed_categories' );

// Meta-Box für Koordinaten-Picker im Beitrags-Editor.
function wuerde_register_koordinaten_meta_box() {
    add_meta_box(
        'wuerde_koordinaten',
        'Kartenposition',
        'wuerde_render_koordinaten_meta_box',
        'wuerde_beitrag',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_register_koordinaten_meta_box' );

function wuerde_render_koordinaten_meta_box( WP_Post $post ) {
    wp_nonce_field( 'wuerde_koordinaten_save', 'wuerde_koordinaten_nonce' );

    $lat = get_post_meta( $post->ID, 'wuerde_lat', true );
    $lng = get_post_meta( $post->ID, 'wuerde_lng', true );
    ?>
    <div style="margin-bottom:8px;display:flex;gap:6px">
        <input type="text"
               id="wuerde_koordinaten_search"
               placeholder="Adresse oder Ort suchen …"
               style="flex:1;padding:4px 8px">
        <button type="button" id="wuerde_koordinaten_search_btn" class="button">Suchen</button>
    </div>
    <div id="wuerde_koordinaten_results"
         style="display:none;border:1px solid #ddd;border-radius:4px;max-height:160px;overflow-y:auto;margin-bottom:8px;background:#fff">
    </div>
    <div id="wuerde_koordinaten_map"
         style="height:320px;border-radius:4px;border:1px solid #ddd;margin-bottom:12px">
    </div>
    <table class="form-table" style="margin-top:0">
        <tr>
            <th style="width:120px;padding:4px 0"><label for="wuerde_lat">Erkannter Ort</label></th>
            <td style="padding:4px 0">
                <input type="text" id="wuerde_ort_display" name="wuerde_ort_suggestion"
                       placeholder="Wird automatisch bestimmt"
                       style="width:100%;background:#f6f7f7;color:#50575e"
                       readonly>
            </td>
        </tr>
        <tr>
            <th style="padding:4px 0"><label for="wuerde_lat">Breitengrad</label></th>
            <td style="padding:4px 0">
                <input type="number" id="wuerde_lat" name="wuerde_lat"
                       value="<?php echo esc_attr( $lat ); ?>"
                       step="0.000001" min="-90" max="90" style="width:100%">
            </td>
        </tr>
        <tr>
            <th style="padding:4px 0"><label for="wuerde_lng">Längengrad</label></th>
            <td style="padding:4px 0">
                <input type="number" id="wuerde_lng" name="wuerde_lng"
                       value="<?php echo esc_attr( $lng ); ?>"
                       step="0.000001" min="-180" max="180" style="width:100%">
            </td>
        </tr>
    </table>
    <p style="color:#666;font-size:12px;margin-top:4px">
        Klicke in die Karte oder suche nach einer Adresse. Ort und Koordinaten werden automatisch gesetzt.
    </p>
    <script>
    ( function() {
        var mapEl   = document.getElementById( 'wuerde_koordinaten_map' );
        var latEl   = document.getElementById( 'wuerde_lat' );
        var lngEl   = document.getElementById( 'wuerde_lng' );
        var ortEl   = document.getElementById( 'wuerde_ort_display' );
        var searchEl  = document.getElementById( 'wuerde_koordinaten_search' );
        var searchBtn = document.getElementById( 'wuerde_koordinaten_search_btn' );
        var resultsEl = document.getElementById( 'wuerde_koordinaten_results' );

        if ( typeof L === 'undefined' || ! mapEl ) return;

        var initLat = parseFloat( latEl.value ) || 51.2;
        var initLng = parseFloat( lngEl.value ) || 10.4;
        var initZoom = ( latEl.value && lngEl.value ) ? 10 : 6;

        var map = L.map( mapEl, { scrollWheelZoom: false } )
                   .setView( [ initLat, initLng ], initZoom );

        L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap-Mitwirkende',
            maxZoom: 19,
        } ).addTo( map );

        var marker = null;

        function setMarker( lat, lng ) {
            if ( marker ) {
                marker.setLatLng( [ lat, lng ] );
            } else {
                marker = L.marker( [ lat, lng ] ).addTo( map );
            }
            latEl.value = lat.toFixed( 6 );
            lngEl.value = lng.toFixed( 6 );
        }

        function reverseGeocode( lat, lng ) {
            var url = 'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=de';
            fetch( url )
                .then( function( r ) { return r.json(); } )
                .then( function( data ) {
                    var a = data.address || {};
                    var city = a.city || a.town || a.village || a.municipality || a.county || '';
                    ortEl.value = city;
                } )
                .catch( function() {} );
        }

        if ( latEl.value && lngEl.value ) {
            setMarker( initLat, initLng );
            reverseGeocode( initLat, initLng );
        }

        map.on( 'click', function( e ) {
            setMarker( e.latlng.lat, e.latlng.lng );
            reverseGeocode( e.latlng.lat, e.latlng.lng );
        } );

        function doSearch() {
            var q = searchEl.value.trim();
            if ( ! q ) return;
            searchBtn.disabled = true;
            searchBtn.textContent = '…';
            fetch( 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent( q ) + '&format=json&limit=5&accept-language=de' )
                .then( function( r ) { return r.json(); } )
                .then( function( results ) {
                    resultsEl.innerHTML = '';
                    if ( ! results.length ) {
                        resultsEl.innerHTML = '<p style="padding:8px;color:#666;margin:0">Keine Ergebnisse.</p>';
                        resultsEl.style.display = 'block';
                        return;
                    }
                    results.forEach( function( item ) {
                        var btn = document.createElement( 'button' );
                        btn.type = 'button';
                        btn.textContent = item.display_name;
                        btn.style.cssText = 'display:block;width:100%;text-align:left;padding:8px 10px;border:none;border-bottom:1px solid #eee;background:none;cursor:pointer;font-size:13px;line-height:1.3';
                        btn.addEventListener( 'mouseover', function() { btn.style.background = '#f0f0f0'; } );
                        btn.addEventListener( 'mouseout',  function() { btn.style.background = 'none'; } );
                        btn.addEventListener( 'click', function() {
                            var lat = parseFloat( item.lat );
                            var lng = parseFloat( item.lon );
                            map.setView( [ lat, lng ], 12 );
                            setMarker( lat, lng );
                            reverseGeocode( lat, lng );
                            resultsEl.style.display = 'none';
                        } );
                        resultsEl.appendChild( btn );
                    } );
                    resultsEl.style.display = 'block';
                } )
                .finally( function() {
                    searchBtn.disabled = false;
                    searchBtn.textContent = 'Suchen';
                } );
        }

        searchBtn.addEventListener( 'click', doSearch );
        searchEl.addEventListener( 'keydown', function( e ) {
            if ( e.key === 'Enter' ) { e.preventDefault(); doSearch(); }
        } );

        document.addEventListener( 'click', function( e ) {
            if ( ! resultsEl.contains( e.target ) && e.target !== searchEl && e.target !== searchBtn ) {
                resultsEl.style.display = 'none';
            }
        } );
    } )();
    </script>
    <?php
}

function wuerde_save_koordinaten_meta( int $post_id ) {
    if (
        ! isset( $_POST['wuerde_koordinaten_nonce'] ) ||
        ! wp_verify_nonce( sanitize_key( $_POST['wuerde_koordinaten_nonce'] ), 'wuerde_koordinaten_save' )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['wuerde_lat'] ) ) {
        update_post_meta( $post_id, 'wuerde_lat', (float) $_POST['wuerde_lat'] );
    }

    if ( isset( $_POST['wuerde_lng'] ) ) {
        update_post_meta( $post_id, 'wuerde_lng', (float) $_POST['wuerde_lng'] );
    }

    // Ort aus Reverse-Geocoding automatisch als Term setzen.
    if ( ! empty( $_POST['wuerde_ort_suggestion'] ) ) {
        $city = sanitize_text_field( wp_unslash( $_POST['wuerde_ort_suggestion'] ) );
        wp_set_post_terms( $post_id, [ $city ], 'wuerde_ort' );
    }
}
add_action( 'save_post_wuerde_beitrag', 'wuerde_save_koordinaten_meta' );

// Term-Meta für Kategorie-Farbe (CSS-Variable).
function wuerde_register_kategorie_color_meta() {
    register_term_meta( 'wuerde_kategorie', 'wuerde_color_token', [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => false,
    ] );
}
add_action( 'init', 'wuerde_register_kategorie_color_meta' );

// Term-Meta-Feld im Kategorie-Bearbeiten-Formular.
function wuerde_kategorie_color_field( WP_Term $term ) {
    $color = get_term_meta( $term->term_id, 'wuerde_color_token', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="wuerde_color_token">Farbe (CSS-Variable)</label></th>
        <td>
            <input type="text" id="wuerde_color_token" name="wuerde_color_token"
                   value="<?php echo esc_attr( $color ); ?>"
                   placeholder="z. B. var(--color-cat-kirche)">
            <p class="description">CSS-Variable aus style.css, z. B. <code>var(--color-cat-kirche)</code>.</p>
        </td>
    </tr>
    <?php
}
add_action( 'wuerde_kategorie_edit_form_fields', 'wuerde_kategorie_color_field' );

function wuerde_save_kategorie_color( int $term_id ) {
    if ( isset( $_POST['wuerde_color_token'] ) ) {
        update_term_meta( $term_id, 'wuerde_color_token', sanitize_text_field( $_POST['wuerde_color_token'] ) );
    }
}
add_action( 'edited_wuerde_kategorie', 'wuerde_save_kategorie_color' );

// Term-Meta für Kategorie-Header-Bild.
function wuerde_register_kategorie_image_meta() {
    register_term_meta( 'wuerde_kategorie', 'wuerde_term_image', [
        'type'         => 'integer',
        'single'       => true,
        'show_in_rest' => false,
    ] );
}
add_action( 'init', 'wuerde_register_kategorie_image_meta' );

// Bild-Picker im Kategorie-Bearbeiten-Formular.
function wuerde_kategorie_image_field( WP_Term $term ) {
    $image_id  = (int) get_term_meta( $term->term_id, 'wuerde_term_image', true );
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
    ?>
    <tr class="form-field">
        <th scope="row"><label>Header-Bild</label></th>
        <td>
            <div id="wuerde-term-image-wrap">
                <?php if ( $image_url ) : ?>
                <img id="wuerde-term-image-preview"
                     src="<?php echo esc_url( $image_url ); ?>"
                     style="max-width:200px;display:block;margin-bottom:8px">
                <?php else : ?>
                <img id="wuerde-term-image-preview"
                     src="" alt=""
                     style="max-width:200px;display:none;margin-bottom:8px">
                <?php endif; ?>
            </div>
            <input type="hidden" id="wuerde_term_image" name="wuerde_term_image"
                   value="<?php echo esc_attr( $image_id ); ?>">
            <button type="button" id="wuerde-term-image-btn" class="button">
                <?php echo $image_id ? 'Bild ändern' : 'Bild auswählen'; ?>
            </button>
            <?php if ( $image_id ) : ?>
            <button type="button" id="wuerde-term-image-remove" class="button" style="margin-left:4px">
                Entfernen
            </button>
            <?php endif; ?>
            <p class="description">Optionales Headerbild für die Kategorieseite. Ohne Bild wird die gefilterte Karte angezeigt.</p>
            <script>
            jQuery( function( $ ) {
                var frame;
                $( '#wuerde-term-image-btn' ).on( 'click', function() {
                    if ( frame ) { frame.open(); return; }
                    frame = wp.media( { title: 'Header-Bild auswählen', button: { text: 'Bild verwenden' }, multiple: false, library: { type: 'image' } } );
                    frame.on( 'select', function() {
                        var att = frame.state().get( 'selection' ).first().toJSON();
                        $( '#wuerde_term_image' ).val( att.id );
                        $( '#wuerde-term-image-preview' ).attr( 'src', att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url ).show();
                        $( '#wuerde-term-image-btn' ).text( 'Bild ändern' );
                    } );
                    frame.open();
                } );
                $( '#wuerde-term-image-remove' ).on( 'click', function() {
                    $( '#wuerde_term_image' ).val( '' );
                    $( '#wuerde-term-image-preview' ).attr( 'src', '' ).hide();
                    $( this ).hide();
                    $( '#wuerde-term-image-btn' ).text( 'Bild auswählen' );
                } );
            } );
            </script>
        </td>
    </tr>
    <?php
}
add_action( 'wuerde_kategorie_edit_form_fields', 'wuerde_kategorie_image_field' );

function wuerde_save_kategorie_image( int $term_id ) {
    if ( isset( $_POST['wuerde_term_image'] ) ) {
        $image_id = (int) $_POST['wuerde_term_image'];
        if ( $image_id ) {
            update_term_meta( $term_id, 'wuerde_term_image', $image_id );
        } else {
            delete_term_meta( $term_id, 'wuerde_term_image' );
        }
    }
}
add_action( 'edited_wuerde_kategorie', 'wuerde_save_kategorie_image' );
