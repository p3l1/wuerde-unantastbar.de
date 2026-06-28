// ABOUTME: Kontaktformular-Submit-Handler.
// ABOUTME: Sendet Formulardaten per fetch an den REST-Endpoint und zeigt Feedback inline.

( function () {
    var form = document.querySelector( '.wuerde-kontakt-formular__form' );
    if ( ! form ) return;

    var status = form.querySelector( '.wuerde-kontakt-formular__status' );
    var btn    = form.querySelector( 'button[type="submit"]' );

    form.addEventListener( 'submit', function ( e ) {
        e.preventDefault();

        var captchaEl = form.querySelector( '[name="h-captcha-response"]' );
        var body = {
            name:          form.querySelector( '[name="name"]' ).value,
            email:         form.querySelector( '[name="email"]' ).value,
            nachricht:     form.querySelector( '[name="nachricht"]' ).value,
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
                if ( result.ok ) {
                    form.hidden = true;
                    setStatus( 'Vielen Dank! Deine Nachricht wurde gesendet. Wir melden uns bald bei dir.', false );
                    status.hidden = false;
                } else {
                    setStatus( result.data.error || 'Fehler beim Senden. Bitte versuche es erneut.', true );
                    btn.disabled    = false;
                    btn.textContent = 'Nachricht senden';
                }
            } )
            .catch( function () {
                setStatus( 'Netzwerkfehler. Bitte prüfe deine Internetverbindung.', true );
                btn.disabled    = false;
                btn.textContent = 'Nachricht senden';
            } );
    } );

    function setStatus( msg, isError ) {
        status.textContent = msg;
        status.hidden      = ! msg;
        status.className   = 'wuerde-kontakt-formular__status' + ( isError ? ' is-error' : ' is-success' );
    }
} )();
