// ABOUTME: Client-side Suche und Kategorie-Accordion-Toggle für den Mitmach-Liste-Block.
// ABOUTME: Progressive Enhancement: alle Karten sind im HTML gerendert, JS blendet nur aus.

( function () {
	var accordion = document.getElementById( 'mitmach-accordion' );
	if ( ! accordion ) return;

	var searchInput = document.getElementById( 'mitmach-search-input' );
	var searchForm  = document.getElementById( 'mitmach-search-form' );
	var cards       = Array.from( accordion.querySelectorAll( '.mitmach-card' ) );
	var triggers    = Array.from( accordion.querySelectorAll( '.category-accordion__trigger' ) );

	// Accordion-Toggle: Trigger öffnet/schließt zugehöriges Panel.
	triggers.forEach( function ( trigger ) {
		trigger.addEventListener( 'click', function () {
			var expanded = trigger.getAttribute( 'aria-expanded' ) === 'true';
			var panelId  = trigger.getAttribute( 'aria-controls' );
			var panel    = document.getElementById( panelId );
			if ( ! panel ) return;

			trigger.setAttribute( 'aria-expanded', String( ! expanded ) );
			panel.classList.toggle( 'category-accordion__panel--closed', expanded );
		} );
	} );

	// Suche: filtert Karten live, öffnet automatisch Kategorien mit Treffern.
	function applyFilter( query ) {
		query = query.trim().toLowerCase();

		cards.forEach( function ( card ) {
			var title    = card.dataset.title || '';
			var text     = card.dataset.text  || '';
			var ort      = card.dataset.ort   || '';
			var matches  = ! query || title.includes( query ) || text.includes( query ) || ort.includes( query );
			card.closest( 'li' ).hidden = ! matches;
		} );

		// Kategorien mit mindestens einem sichtbaren Treffer aufklappen.
		var items = Array.from( accordion.querySelectorAll( '.category-accordion__item' ) );
		items.forEach( function ( item ) {
			var visibleCards = item.querySelectorAll( 'li:not([hidden])' );
			var hasMatches   = visibleCards.length > 0;
			item.hidden      = query && ! hasMatches;

			if ( query && hasMatches ) {
				var itemTrigger = item.querySelector( '.category-accordion__trigger' );
				var panelId     = itemTrigger && itemTrigger.getAttribute( 'aria-controls' );
				var panel       = panelId && document.getElementById( panelId );
				if ( itemTrigger && panel ) {
					itemTrigger.setAttribute( 'aria-expanded', 'true' );
					panel.classList.remove( 'category-accordion__panel--closed' );
				}
			}
		} );
	}

	if ( searchInput ) {
		searchInput.addEventListener( 'input', function () {
			applyFilter( searchInput.value );
		} );
	}

	if ( searchForm ) {
		searchForm.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( searchInput ) applyFilter( searchInput.value );
		} );
	}
} )();
