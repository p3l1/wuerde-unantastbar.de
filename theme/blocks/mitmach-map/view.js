// ABOUTME: Leaflet-Karte für den Mitmach-Karten-Block.
// ABOUTME: Lädt Marker-Daten via REST und zeichnet farbige circleMarker pro Kategorie.

( function () {
	var container = document.getElementById( 'mitmach-map' );
	if ( ! container || typeof L === 'undefined' ) return;

	var centerLat = parseFloat( container.dataset.centerLat ) || 51.2;
	var centerLng = parseFloat( container.dataset.centerLng ) || 10.4;
	var zoom      = parseInt( container.dataset.zoom, 10 ) || 6;
	var restUrl   = container.dataset.restUrl;

	var tileStyle = container.dataset.tileStyle || 'osm';

	var tileLayers = {
		osm: {
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>-Mitwirkende',
			maxZoom: 19,
		},
		topo: {
			url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>-Mitwirkende, &copy; <a href="https://opentopomap.org">OpenTopoMap</a>',
			maxZoom: 17,
		},
		humanitarian: {
			url: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>-Mitwirkende, Tiles &copy; <a href="https://hot.openstreetmap.org">HOT</a>',
			maxZoom: 19,
		},
		grayscale: {
			url: 'https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png',
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>-Mitwirkende',
			maxZoom: 18,
		},
	};

	var layer = tileLayers[ tileStyle ] || tileLayers.osm;

	var map = L.map( 'mitmach-map', {
		center: [ centerLat, centerLng ],
		zoom: zoom,
		scrollWheelZoom: false,
	} );

	L.tileLayer( layer.url, {
		attribution: layer.attribution,
		maxZoom: layer.maxZoom,
	} ).addTo( map );

	// Farbe per CSS-Variable aus dem Dokument lesen (Fallback: teal).
	function getCategoryColor( slug ) {
		var style = getComputedStyle( document.documentElement );
		var token = style.getPropertyValue( '--color-cat-' + slug ).trim();
		return token || style.getPropertyValue( '--color-teal' ).trim() || '#00aca0';
	}

	if ( ! restUrl ) return;

	fetch( restUrl )
		.then( function ( res ) { return res.json(); } )
		.then( function ( points ) {
			points.forEach( function ( point ) {
				if ( ! point.lat || ! point.lng ) return;

				var color  = getCategoryColor( point.category_slug );
				var marker = L.circleMarker( [ point.lat, point.lng ], {
					radius:      8,
					color:       color,
					fillColor:   color,
					fillOpacity: 0.85,
					weight:      2,
					className:   'mitmach-map__marker mitmach-map__marker--' + ( point.category_slug || 'sonstiges' ),
				} );

				marker.bindPopup(
					'<strong>' + point.title + '</strong>' +
					( point.permalink ? '<br><a href="' + point.permalink + '">Details</a>' : '' )
				);

				marker.addTo( map );
			} );
		} )
		.catch( function ( err ) {
			console.error( 'Mitmach-Karte: Fehler beim Laden der Marker.', err );
		} );
} )();
