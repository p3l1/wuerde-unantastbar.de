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
    <div id="wuerde_koordinaten_status" style="display:none;padding:6px 10px;border-radius:4px;font-size:12px;margin-bottom:6px"></div>
    <div id="wuerde_koordinaten_results"
         style="display:none;border:1px solid #ddd;border-radius:4px;max-height:160px;overflow-y:auto;margin-bottom:8px;background:#fff">
    </div>
    <div id="wuerde_koordinaten_map"
         style="height:320px;border-radius:4px;border:1px solid #ddd;margin-bottom:12px">
    </div>
    <table class="form-table" style="margin-top:0">
        <tr>
            <th style="width:120px;padding:4px 0"><label for="wuerde_ort_display">Ort</label></th>
            <td style="padding:4px 0">
                <input type="text" id="wuerde_ort_display" name="wuerde_ort_suggestion"
                       value="<?php
                           $ort_terms = wp_get_post_terms( $post->ID, 'wuerde_ort', [ 'fields' => 'names' ] );
                           echo esc_attr( ! is_wp_error( $ort_terms ) && ! empty( $ort_terms ) ? $ort_terms[0] : '' );
                       ?>"
                       placeholder="Wird automatisch bestimmt"
                       style="width:100%">
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
    <?php ob_start(); ?>
    ( function() {
        var mapEl     = document.getElementById( 'wuerde_koordinaten_map' );
        var latEl     = document.getElementById( 'wuerde_lat' );
        var lngEl     = document.getElementById( 'wuerde_lng' );
        var ortEl     = document.getElementById( 'wuerde_ort_display' );
        var searchEl  = document.getElementById( 'wuerde_koordinaten_search' );
        var searchBtn = document.getElementById( 'wuerde_koordinaten_search_btn' );
        var resultsEl = document.getElementById( 'wuerde_koordinaten_results' );
        var statusEl  = document.getElementById( 'wuerde_koordinaten_status' );

        if ( typeof L === 'undefined' || ! mapEl ) return;

        // ── Rate-Limiter ─────────────────────────────────────────────────────
        // Nominatim erlaubt max. 1 Anfrage/Sekunde. Anfragen werden sequenziell
        // in einer Queue abgearbeitet, mit mindestens 1,1 s Abstand.
        var MIN_INTERVAL = 1100;
        var lastRequest  = 0;
        var queue        = [];
        var queueRunning = false;

        function enqueue( fn ) {
            queue.push( fn );
            if ( ! queueRunning ) processQueue();
        }

        function processQueue() {
            if ( ! queue.length ) { queueRunning = false; return; }
            queueRunning = true;
            var fn    = queue.shift();
            var now   = Date.now();
            var delay = Math.max( 0, MIN_INTERVAL - ( now - lastRequest ) );
            setTimeout( function() {
                lastRequest = Date.now();
                fn().finally( processQueue );
            }, delay );
        }

        function nominatimFetch( url ) {
            return new Promise( function( resolve, reject ) {
                enqueue( function() {
                    return fetch( url )
                        .then( function( r ) {
                            if ( r.status === 429 ) throw new Error( 'rate_limit' );
                            if ( ! r.ok ) throw new Error( 'http_' + r.status );
                            return r.json();
                        } )
                        .then( resolve )
                        .catch( reject );
                } );
            } );
        }

        // ── Status-Anzeige ───────────────────────────────────────────────────
        var statusTimer = null;

        function showStatus( msg, type ) {
            var colors = {
                info:    { bg: '#e7f3fe', border: '#b3d7f7', text: '#0a4b8c' },
                error:   { bg: '#fce8e8', border: '#f5c2c2', text: '#8c1a1a' },
                success: { bg: '#edfaee', border: '#b8e5bc', text: '#1a5c1f' },
            };
            var c = colors[ type ] || colors.info;
            statusEl.textContent = msg;
            statusEl.style.cssText = 'display:block;padding:6px 10px;border-radius:4px;font-size:12px;margin-bottom:6px;'
                + 'background:' + c.bg + ';border:1px solid ' + c.border + ';color:' + c.text;
            clearTimeout( statusTimer );
            if ( type === 'success' ) {
                statusTimer = setTimeout( function() { statusEl.style.display = 'none'; }, 3000 );
            }
        }

        function hideStatus() { statusEl.style.display = 'none'; }

        function errorMessage( err ) {
            if ( err.message === 'rate_limit' ) return 'Zu viele Anfragen — bitte einen Moment warten.';
            if ( err.message === 'Failed to fetch' || err.name === 'TypeError' ) return 'Netzwerkfehler — bitte Internetverbindung prüfen.';
            return 'Fehler beim Abrufen der Geodaten (' + err.message + ').';
        }

        // ── Karte ────────────────────────────────────────────────────────────
        var initLat  = parseFloat( latEl.value ) || 51.2;
        var initLng  = parseFloat( lngEl.value ) || 10.4;
        var initZoom = ( latEl.value && lngEl.value ) ? 10 : 6;

        var map = L.map( mapEl, { scrollWheelZoom: false } )
                   .setView( [ initLat, initLng ], initZoom );

        L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap-Mitwirkende',
            maxZoom: 19,
        } ).addTo( map );

        // Admin-Layout kann den Container erst nach dem Rendering auf die
        // richtige Größe bringen — invalidateSize lädt fehlende Tiles nach.
        setTimeout( function() { map.invalidateSize(); }, 200 );

        var marker = null;

        var crownUrl = '<?php echo esc_url( get_template_directory_uri() . '/assets/krone-white.png' ); ?>';
        var pinIcon  = L.divIcon( {
            className:   'mitmach-map__pin',
            html:        '<div class="mitmach-map__pin-dot" style="background:#00ACA0"><img src="' + crownUrl + '" alt="" aria-hidden="true"></div>',
            iconSize:    [ 32, 32 ],
            iconAnchor:  [ 16, 16 ],
            popupAnchor: [ 0, -18 ],
        } );

        function setMarker( lat, lng ) {
            if ( marker ) {
                marker.setLatLng( [ lat, lng ] );
            } else {
                marker = L.marker( [ lat, lng ], { icon: pinIcon } ).addTo( map );
            }
            latEl.value = lat.toFixed( 6 );
            lngEl.value = lng.toFixed( 6 );
        }

        // Debounce: Karteklicks in schneller Folge lösen nur eine Rücksuche aus.
        var reverseTimer = null;

        function reverseGeocode( lat, lng ) {
            clearTimeout( reverseTimer );
            reverseTimer = setTimeout( function() {
                ortEl.value = '';
                showStatus( 'Ort wird bestimmt …', 'info' );
                var url = 'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=de';
                nominatimFetch( url )
                    .then( function( data ) {
                        if ( data.error ) { showStatus( 'Kein Ort an dieser Position gefunden.', 'error' ); return; }
                        var a    = data.address || {};
                        var city = a.city || a.town || a.village || a.municipality || a.county || '';
                        if ( city ) {
                            ortEl.value = city;
                            showStatus( 'Ort erkannt: ' + city, 'success' );
                        } else {
                            showStatus( 'Kein Ortsname gefunden — bitte manuell eintragen.', 'error' );
                        }
                    } )
                    .catch( function( err ) { showStatus( errorMessage( err ), 'error' ); } );
            }, 400 );
        }

        if ( latEl.value && lngEl.value ) {
            setMarker( initLat, initLng );
            reverseGeocode( initLat, initLng );
        }

        map.on( 'click', function( e ) {
            setMarker( e.latlng.lat, e.latlng.lng );
            reverseGeocode( e.latlng.lat, e.latlng.lng );
        } );

        // ── Adresssuche ──────────────────────────────────────────────────────
        function doSearch() {
            var q = searchEl.value.trim();
            if ( ! q ) { showStatus( 'Bitte einen Suchbegriff eingeben.', 'error' ); return; }
            searchBtn.disabled   = true;
            searchBtn.textContent = '…';
            resultsEl.style.display = 'none';
            showStatus( 'Suche läuft …', 'info' );

            var url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent( q ) + '&format=json&limit=5&accept-language=de';
            nominatimFetch( url )
                .then( function( results ) {
                    hideStatus();
                    resultsEl.innerHTML = '';
                    if ( ! results.length ) {
                        showStatus( 'Keine Ergebnisse für „' + q + '"', 'error' );
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
                .catch( function( err ) { showStatus( errorMessage( err ), 'error' ); } )
                .finally( function() {
                    searchBtn.disabled    = false;
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
    <?php wp_add_inline_script( 'leaflet', ob_get_clean(), 'after' );
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

    // Ort setzen: entweder aus dem Formularfeld oder server-seitig via Nominatim.
    $city = sanitize_text_field( wp_unslash( $_POST['wuerde_ort_suggestion'] ?? '' ) );

    if ( empty( $city ) ) {
        $lat = (float) ( $_POST['wuerde_lat'] ?? 0 );
        $lng = (float) ( $_POST['wuerde_lng'] ?? 0 );

        if ( $lat && $lng ) {
            $city = wuerde_reverse_geocode( $lat, $lng );
        }
    }

    if ( ! empty( $city ) ) {
        wp_set_post_terms( $post_id, [ $city ], 'wuerde_ort' );
    }
}

function wuerde_reverse_geocode( float $lat, float $lng ): string {
    $url = add_query_arg( [
        'lat'             => $lat,
        'lon'             => $lng,
        'format'          => 'json',
        'accept-language' => 'de',
    ], 'https://nominatim.openstreetmap.org/reverse' );

    $response = wp_remote_get( $url, [
        'timeout'    => 5,
        'user-agent' => 'wuerde-unantastbar.de/1.0 (WordPress; contact@wuerde-unantastbar.de)',
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return '';
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    $addr = $data['address'] ?? [];

    return (string) ( $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? $addr['county'] ?? '' );
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

// Einsender-Metadaten für öffentliche Einreichungen.
function wuerde_register_einreichung_meta() {
    $args = [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => false,
    ];
    register_post_meta( 'wuerde_beitrag', 'wuerde_einreichung_name',  $args );
    register_post_meta( 'wuerde_beitrag', 'wuerde_einreichung_email', $args );
}
add_action( 'init', 'wuerde_register_einreichung_meta' );

function wuerde_einreichung_meta_box() {
    add_meta_box(
        'wuerde_einreichung',
        'Einsender',
        'wuerde_einreichung_meta_box_html',
        'wuerde_beitrag',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wuerde_einreichung_meta_box' );

function wuerde_einreichung_meta_box_html( WP_Post $post ) {
    $name  = get_post_meta( $post->ID, 'wuerde_einreichung_name',  true );
    $email = get_post_meta( $post->ID, 'wuerde_einreichung_email', true );

    echo '<table style="border-collapse:collapse;width:100%"><tbody>';
    echo '<tr><th style="text-align:left;padding:3px 0;width:80px;font-weight:600">Ref.-Nr.</th>';
    echo '<td style="padding:3px 0"><strong>#' . esc_html( (string) $post->ID ) . '</strong></td></tr>';

    if ( $name ) {
        echo '<tr><th style="text-align:left;padding:3px 0;font-weight:600">Name</th>';
        echo '<td style="padding:3px 0">' . esc_html( $name ) . '</td></tr>';
    }
    if ( $email ) {
        echo '<tr><th style="text-align:left;padding:3px 0;font-weight:600">E-Mail</th>';
        echo '<td style="padding:3px 0"><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></td></tr>';
    }
    if ( ! $name && ! $email ) {
        echo '<tr><td colspan="2" style="padding:3px 0;color:#666;font-style:italic">Manuell erfasster Beitrag</td></tr>';
    }
    echo '</tbody></table>';
}
