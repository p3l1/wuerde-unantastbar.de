// ABOUTME: Einwilligungs-Gate für externe OpenStreetMap-Dienste (Kachel-Server, Nominatim).
// ABOUTME: Merkt die Einwilligung in localStorage und rendert einen Platzhalter mit Lade-Button.

( function () {
	var KEY = 'wuerde-osm-consent';

	window.wuerdeOsmConsent = {
		granted: function () {
			try {
				return localStorage.getItem( KEY ) === '1';
			} catch ( e ) {
				return false;
			}
		},

		grant: function () {
			try {
				localStorage.setItem( KEY, '1' );
			} catch ( e ) {}
		},

		// Ruft onConsent sofort auf wenn bereits eingewilligt wurde, sonst erst
		// nach Klick auf den Button im Platzhalter, der in container gerendert wird.
		// opts erlaubt abweichende Texte, z. B. für die Nominatim-Adresssuche.
		gate: function ( container, onConsent, opts ) {
			if ( this.granted() ) {
				onConsent();
				return;
			}
			if ( container.querySelector( '.osm-consent' ) ) return;
			opts = opts || {};

			var self = this;
			var overlay = document.createElement( 'div' );
			overlay.className = 'osm-consent';

			var text = document.createElement( 'p' );
			text.className = 'osm-consent__text';
			text.textContent = opts.text || 'Die Karte lädt Kartendaten von OpenStreetMap. Dabei wird deine IP-Adresse an Server der OpenStreetMap Foundation übertragen.';

			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'btn btn--primary osm-consent__btn';
			btn.textContent = opts.button || 'Karte laden';
			btn.addEventListener( 'click', function () {
				self.grant();
				overlay.remove();
				onConsent();
			} );

			overlay.appendChild( text );
			overlay.appendChild( btn );
			container.appendChild( overlay );
		},
	};
} )();
