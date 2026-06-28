// ABOUTME: Mitmach-Einreichungsformular: Submit-Handler, Leaflet-Karte und Erfolgsanzeige.
// ABOUTME: Die Karte wird erst beim Öffnen des <details>-Elements initialisiert.

( function () {
    var wrapper = document.querySelector( '.wuerde-mitmach-einreichung' );
    if ( ! wrapper ) return;

    var form   = wrapper.querySelector( '.wuerde-mitmach-einreichung__form' );
    var erfolg = wrapper.querySelector( '.wuerde-mitmach-einreichung__erfolg' );
    var status = form.querySelector( '.wuerde-mitmach-einreichung__status' );
    var btn    = form.querySelector( 'button[type="submit"]' );
    var latEl  = form.querySelector( '[name="lat"]' );
    var lngEl  = form.querySelector( '[name="lng"]' );
    var ortEl  = form.querySelector( '[name="ort"]' );
    var mapEl  = document.getElementById( 'wuerde-einr-map' );
    var details = form.querySelector( '.wuerde-mitmach-einreichung__karte-toggle' );

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
        mapReady = true;
        var map = L.map( mapEl, { scrollWheelZoom: false } ).setView( [ 51.2, 10.4 ], 6 );
        L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap-Mitwirkende',
            maxZoom: 19,
        } ).addTo( map );
        setTimeout( function () { map.invalidateSize(); }, 200 );

        var pinIcon = L.divIcon( {
            className:   'mitmach-map__pin',
            html:        '<div class="mitmach-map__pin-dot" style="background:#00ACA0"><img src="' + form.dataset.crown + '" alt="" aria-hidden="true"></div>',
            iconSize:    [ 32, 32 ],
            iconAnchor:  [ 16, 16 ],
        } );

        var marker = null;

        map.on( 'click', function ( e ) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            latEl.value = lat.toFixed( 6 );
            lngEl.value = lng.toFixed( 6 );
            if ( marker ) {
                marker.setLatLng( e.latlng );
            } else {
                marker = L.marker( e.latlng, { icon: pinIcon } ).addTo( map );
            }
            reverseGeocode( lat, lng );
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

        var captchaEl = form.querySelector( '[name="h-captcha-response"]' );
        var body = {
            name:          form.querySelector( '[name="name"]' ).value,
            email:         form.querySelector( '[name="email"]' ).value,
            titel:         form.querySelector( '[name="titel"]' ).value,
            beschreibung:  form.querySelector( '[name="beschreibung"]' ).value,
            kategorie_id:  parseInt( form.querySelector( '[name="kategorie_id"]' ).value, 10 ) || 0,
            ort:           ortEl.value,
            lat:           parseFloat( latEl.value ) || 0,
            lng:           parseFloat( lngEl.value ) || 0,
            captcha_token: captchaEl ? captchaEl.value : '',
            nonce:         form.dataset.nonce,
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

        erfolg.innerHTML =
            '<p><strong>Vielen Dank!</strong> Dein Beitrag wurde gespeichert (Referenz <strong>#' +
            safeId + '</strong>).</p>' +
            '<p>Um Fotos oder Dokumente beizufügen, sende uns eine E-Mail:</p>' +
            '<a href="' + mailto + '" class="btn btn--secondary">📎 Dateien per E-Mail zusenden</a>';
        erfolg.hidden = false;
    }

    function setStatus( msg, isError ) {
        status.textContent = msg;
        status.hidden      = ! msg;
        status.className   = 'wuerde-mitmach-einreichung__status' + ( isError ? ' is-error' : ' is-success' );
    }
} )();
