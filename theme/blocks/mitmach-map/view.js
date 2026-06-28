// ABOUTME: Leaflet-Karte für den Mitmach-Karten-Block.
// ABOUTME: Lädt Marker-Daten via REST und zeichnet farbige circleMarker pro Kategorie.

( function () {
	var container = document.getElementById( 'mitmach-map' );
	if ( ! container || typeof L === 'undefined' ) return;

	var centerLat = parseFloat( container.dataset.centerLat ) || 51.2;
	var centerLng = parseFloat( container.dataset.centerLng ) || 10.4;
	var zoom      = parseInt( container.dataset.zoom, 10 ) || 6;
	var restUrl   = container.dataset.restUrl;

	var tileStyle   = container.dataset.tileStyle || 'osm';
	var interactive = container.dataset.interactive !== 'false';
	var crownUrl    = container.dataset.crownUrl || '';

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
		center:              [ centerLat, centerLng ],
		zoom:                zoom,
		scrollWheelZoom:     false,
		zoomControl:         interactive,
		attributionControl:  interactive,
		dragging:            interactive,
		touchZoom:           interactive,
		doubleClickZoom:     interactive,
		boxZoom:             interactive,
		keyboard:            interactive,
	} );

	L.tileLayer( layer.url, {
		attribution: layer.attribution,
		maxZoom: layer.maxZoom,
	} ).addTo( map );

	if ( ! restUrl ) return;

	var clusterGroup = L.markerClusterGroup( {
		maxClusterRadius: 48,
		iconCreateFunction: function ( cluster ) {
			return L.divIcon( {
				className:  'mitmach-map__cluster',
				html:       '<div class="mitmach-map__cluster-dot">' + cluster.getChildCount() + '</div>',
				iconSize:   [ 32, 32 ],
				iconAnchor: [ 16, 16 ],
			} );
		},
	} );

	fetch( restUrl )
		.then( function ( res ) { return res.json(); } )
		.then( function ( points ) {
			points.forEach( function ( point ) {
				if ( ! point.lat || ! point.lng ) return;

				var color = point.color || '#00aca0';
				var icon  = L.divIcon( {
					className:   'mitmach-map__pin',
					html:        '<div class="mitmach-map__pin-dot" style="background:' + color + '">'
					             + ( crownUrl ? '<img src="' + crownUrl + '" alt="" aria-hidden="true">' : '' )
					             + '</div>',
					iconSize:    [ 32, 32 ],
					iconAnchor:  [ 16, 16 ],
					popupAnchor: [ 0, -18 ],
				} );

				var popup = '<div class="mitmach-map__popup" style="--popup-color:' + color + '">'
				          + '<strong>' + point.title + '</strong>'
				          + ( point.ort ? '<span class="mitmach-card__tag mitmach-map__popup-ort">' + point.ort + '</span>' : '' )
				          + ( point.permalink ? '<a href="' + point.permalink + '" class="mitmach-map__popup-link">Details →</a>' : '' )
				          + '</div>';

				L.marker( [ point.lat, point.lng ], { icon: icon } )
				 .bindPopup( popup, { className: 'mitmach-map__popup-wrap' } )
				 .addTo( clusterGroup );
			} );

			clusterGroup.addTo( map );
		} )
		.catch( function ( err ) {
			console.error( 'Mitmach-Karte: Fehler beim Laden der Marker.', err );
		} );
} )();
