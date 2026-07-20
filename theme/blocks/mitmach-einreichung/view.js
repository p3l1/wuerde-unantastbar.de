// ABOUTME: Mitmach-Einreichungsformular: Submit-Handler, Adresssuche, Leaflet-Karte und Erfolgsanzeige.
// ABOUTME: Die Karte wird erst beim Öffnen des <details>-Elements initialisiert.

( function () {
    var wrapper = document.querySelector( '.wuerde-mitmach-einreichung' );
    if ( ! wrapper ) return;

    var form    = wrapper.querySelector( '.wuerde-mitmach-einreichung__form' );
    var erfolg  = wrapper.querySelector( '.wuerde-mitmach-einreichung__erfolg' );
    var status  = form.querySelector( '.wuerde-mitmach-einreichung__status' );
    var btn     = form.querySelector( 'button[type="submit"]' );
    var latEl   = form.querySelector( '[name="lat"]' );
    var lngEl   = form.querySelector( '[name="lng"]' );
    var ortEl   = form.querySelector( '[name="ort"]' );
    var mapEl   = document.getElementById( 'wuerde-einr-map' );
    var details = form.querySelector( '.wuerde-mitmach-einreichung__karte-toggle' );

    var adresseEl   = document.getElementById( 'wuerde-einr-adresse' );
    var adresseBtn  = form.querySelector( '.wuerde-mitmach-einreichung__adresse-btn' );
    var adresseHint = form.querySelector( '.wuerde-mitmach-einreichung__adresse-hint' );

    // Karte und Marker im äußeren Scope, damit Adresssuche den Marker setzen kann.
    var leafletMap    = null;
    var leafletMarker = null;
    var pinIcon       = null;

    // Karte erst beim Öffnen des <details> laden.
    var mapReady = false;
    if ( details && mapEl && typeof L !== 'undefined' ) {
        details.addEventListener( 'toggle', function () {
            if ( details.open && ! mapReady ) {
                initMap();
            }
        } );
    }

    function initMap() {
        mapReady  = true;
        leafletMap = L.map( mapEl, { scrollWheelZoom: false } ).setView( [ 51.2, 10.4 ], 6 );
        L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap-Mitwirkende',
            maxZoom: 19,
        } ).addTo( leafletMap );
        setTimeout( function () { leafletMap.invalidateSize(); }, 200 );

        pinIcon = L.divIcon( {
            className:   'mitmach-map__pin',
            html:        '<div class="mitmach-map__pin-dot" style="background:#00ACA0"><img src="' + form.dataset.crown + '" alt="" aria-hidden="true"></div>',
            iconSize:    [ 32, 32 ],
            iconAnchor:  [ 16, 16 ],
        } );

        // Falls Koordinaten bereits gesetzt (z. B. durch Adresssuche vor Kartenöffnung).
        if ( latEl.value && lngEl.value ) {
            placeMarker( parseFloat( latEl.value ), parseFloat( lngEl.value ) );
            leafletMap.setView( [ parseFloat( latEl.value ), parseFloat( lngEl.value ) ], 13 );
        }

        leafletMap.on( 'click', function ( e ) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            setKoordinaten( lat, lng );
            reverseGeocode( lat, lng );
        } );
    }

    function placeMarker( lat, lng ) {
        if ( ! leafletMap || ! pinIcon ) return;
        var latlng = L.latLng( lat, lng );
        if ( leafletMarker ) {
            leafletMarker.setLatLng( latlng );
        } else {
            leafletMarker = L.marker( latlng, { icon: pinIcon } ).addTo( leafletMap );
        }
    }

    function setKoordinaten( lat, lng ) {
        latEl.value = lat.toFixed( 6 );
        lngEl.value = lng.toFixed( 6 );
        placeMarker( lat, lng );
    }

    // Adresssuche (Vorwärts-Geocodierung via Nominatim).
    function forwardGeocode( adresse ) {
        if ( ! adresse.trim() ) return;
        adresseHint.textContent = 'Suche …';
        adresseHint.className   = 'wuerde-mitmach-einreichung__adresse-hint';

        var url = 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent( adresse ) + '&format=json&limit=1&addressdetails=1&accept-language=de';
        fetch( url )
            .then( function ( r ) { return r.json(); } )
            .then( function ( data ) {
                if ( ! data || ! data.length ) {
                    adresseHint.textContent = 'Adresse nicht gefunden.';
                    adresseHint.className   = 'wuerde-mitmach-einreichung__adresse-hint is-error';
                    return;
                }
                var lat = parseFloat( data[0].lat );
                var lng = parseFloat( data[0].lon );
                setKoordinaten( lat, lng );

                if ( leafletMap ) {
                    leafletMap.setView( [ lat, lng ], 13 );
                }

                // Ort nur vorausfüllen wenn Feld noch leer — Stadt aus address-Objekt.
                if ( ! ortEl.value ) {
                    var a    = data[0].address || {};
                    var city = a.city || a.town || a.village || a.municipality || a.county || '';
                    if ( city ) ortEl.value = city;
                }

                adresseHint.textContent = 'Gefunden: ' + data[0].display_name;
                adresseHint.className   = 'wuerde-mitmach-einreichung__adresse-hint is-success';
            } )
            .catch( function () {
                adresseHint.textContent = 'Suche fehlgeschlagen.';
                adresseHint.className   = 'wuerde-mitmach-einreichung__adresse-hint is-error';
            } );
    }

    if ( adresseBtn && adresseEl ) {
        adresseBtn.addEventListener( 'click', function () {
            forwardGeocode( adresseEl.value );
        } );
        adresseEl.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Enter' ) {
                e.preventDefault();
                forwardGeocode( adresseEl.value );
            }
        } );
    }

    function reverseGeocode( lat, lng ) {
        var url = 'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=de';
        fetch( url )
            .then( function ( r ) { return r.json(); } )
            .then( function ( data ) {
                var a    = data.address || {};
                var city = a.city || a.town || a.village || a.municipality || a.county || '';
                // Ort nur vorausfüllen wenn Feld noch leer.
                if ( city && ! ortEl.value ) {
                    ortEl.value = city;
                }
            } )
            .catch( function () {} );
    }

    // Formular-Submit
    form.addEventListener( 'submit', function ( e ) {
        e.preventDefault();

        var kategorieIds = Array.from( form.querySelectorAll( '[name="kategorie_ids[]"]:checked' ) )
            .map( function ( el ) { return parseInt( el.value, 10 ); } )
            .filter( function ( id ) { return id > 0; } );

        if ( ! kategorieIds.length ) {
            setStatus( 'Bitte wähle mindestens eine Kategorie aus.', true );
            return;
        }

        var captchaEl = form.querySelector( '[name="h-captcha-response"]' );
        var body = {
            name:           form.querySelector( '[name="name"]' ).value,
            email:          form.querySelector( '[name="email"]' ).value,
            telefon:        form.querySelector( '[name="telefon"]' ).value,
            titel:          form.querySelector( '[name="titel"]' ).value,
            beschreibung:   form.querySelector( '[name="beschreibung"]' ).value,
            kurzbeschreibung: form.querySelector( '[name="kurzbeschreibung"]' ).value,
            kategorie_ids:  kategorieIds,
            ort:            ortEl.value,
            lat:            parseFloat( latEl.value ) || 0,
            lng:            parseFloat( lngEl.value ) || 0,
            email_public:   form.querySelector( '[name="email_public"]' ).checked,
            telefon_public: form.querySelector( '[name="telefon_public"]' ).checked,
            captcha_token:  captchaEl ? captchaEl.value : '',
            nonce:          form.dataset.nonce,
        };

        btn.disabled    = true;
        btn.textContent = '…';
        setStatus( '', false );

        fetch( form.dataset.endpoint, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify( body ),
        } )
            .then( function ( r ) {
                return r.json().then( function ( d ) { return { ok: r.ok, data: d }; } );
            } )
            .then( function ( result ) {
                if ( result.ok && result.data.post_id ) {
                    zeigErfolg( result.data.post_id );
                } else {
                    setStatus( result.data.error || 'Fehler. Bitte versuche es erneut.', true );
                    btn.disabled    = false;
                    btn.textContent = 'Beitrag einreichen';
                }
            } )
            .catch( function () {
                setStatus( 'Netzwerkfehler. Bitte prüfe deine Internetverbindung.', true );
                btn.disabled    = false;
                btn.textContent = 'Beitrag einreichen';
            } );
    } );

    function zeigErfolg( postId ) {
        var safeId = parseInt( postId, 10 );
        if ( ! safeId || safeId <= 0 ) return;
        form.hidden = true;
        var email   = form.dataset.notifyEmail;
        var subject = encodeURIComponent( 'Anhänge zu Einreichung #' + safeId );
        var bodyTxt = encodeURIComponent( 'Bitte füge diesem E-Mail deine Fotos oder Dokumente als Anhang bei.' );
        var mailto  = 'mailto:' + email + '?subject=' + subject + '&body=' + bodyTxt;

        var p1 = document.createElement( 'p' );
        p1.innerHTML = '<strong>Vielen Dank!</strong> Dein Beitrag wurde gespeichert (Referenz <strong>#' + safeId + '</strong>).';

        var p2 = document.createElement( 'p' );
        p2.textContent = 'Um Fotos oder Dokumente beizufügen, sende uns eine E-Mail:';

        var link = document.createElement( 'a' );
        link.setAttribute( 'href', mailto );
        link.className = 'btn btn--secondary';
        link.textContent = '📎 Dateien per E-Mail zusenden';

        erfolg.innerHTML = '';
        erfolg.appendChild( p1 );
        erfolg.appendChild( p2 );
        erfolg.appendChild( link );
        erfolg.hidden = false;
    }

    function setStatus( msg, isError ) {
        status.textContent = msg;
        status.hidden      = ! msg;
        status.className   = 'wuerde-mitmach-einreichung__status' + ( isError ? ' is-error' : ' is-success' );
    }
} )();
