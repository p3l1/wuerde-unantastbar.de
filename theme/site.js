// ABOUTME: Site-weites JavaScript für Header-Navigation (Hamburger, Dropdown).
// ABOUTME: Lädt auf allen Seiten außer Lookbook/Hero-Demo (die haben lookbook.js).

( function () {
	// =========================================================================
	// Sticky Header — transparent über dem Hero, weiß darunter
	// =========================================================================

	var header = document.querySelector( '.site-header' );
	var hero   = document.querySelector( '.site-hero' );

	if ( header && hero ) {
		var onHeaderScroll = function () {
			var past = hero.getBoundingClientRect().bottom <= 80;
			header.classList.toggle( 'is-scrolled', past );
		};
		window.addEventListener( 'scroll', onHeaderScroll, { passive: true } );
		onHeaderScroll();
	}

	// =========================================================================
	// Hamburger / Mobile Nav
	// =========================================================================

	var hamburger = document.querySelector( '[data-site-hamburger]' );
	var mobileNav = document.querySelector( '[data-site-mobile-nav]' );

	if ( hamburger && mobileNav ) {
		hamburger.addEventListener( 'click', function () {
			var isOpen = hamburger.getAttribute( 'aria-expanded' ) !== 'true';
			hamburger.setAttribute( 'aria-expanded', String( isOpen ) );
			mobileNav.classList.toggle( 'is-open', isOpen );
			mobileNav.setAttribute( 'aria-hidden', String( ! isOpen ) );
		} );
	}

	// =========================================================================
	// Desktop Nav Dropdown
	// =========================================================================

	var dropdownItems = document.querySelectorAll( '.site-nav__item--dropdown' );

	dropdownItems.forEach( function ( item ) {
		var trigger  = item.querySelector( '.site-nav__link--has-dropdown' );
		var dropdown = item.querySelector( '.site-dropdown' );
		if ( ! trigger || ! dropdown ) return;

		trigger.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			var isOpen = item.classList.toggle( 'is-open' );
			trigger.setAttribute( 'aria-expanded', String( isOpen ) );
		} );
	} );

	document.addEventListener( 'click', function ( e ) {
		dropdownItems.forEach( function ( item ) {
			if ( ! item.contains( e.target ) ) {
				item.classList.remove( 'is-open' );
				var trigger = item.querySelector( '.site-nav__link--has-dropdown' );
				if ( trigger ) trigger.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	} );

	// =========================================================================
	// Mobile Sub-Menus
	// =========================================================================

	var subTriggers = document.querySelectorAll( '[data-site-sub-trigger]' );

	subTriggers.forEach( function ( trigger ) {
		var sub = trigger.nextElementSibling;
		if ( ! sub ) return;

		trigger.addEventListener( 'click', function () {
			var isOpen = sub.classList.toggle( 'is-open' );
			trigger.setAttribute( 'aria-expanded', String( isOpen ) );
		} );
	} );

	// =========================================================================
	// Profil-Karten — Custom Scrollbalken mit fester Thumb-Höhe
	// =========================================================================

	var THUMB_H = 40;

	document.querySelectorAll( '.profile-card__text' ).forEach( function ( el ) {
		var body = el.closest( '.profile-card__body' );
		if ( ! body ) return;

		var track = document.createElement( 'div' );
		track.className = 'card-scrollbar';
		var thumb = document.createElement( 'div' );
		thumb.className = 'card-scrollbar__thumb';
		track.appendChild( thumb );
		body.appendChild( track );

		// Bündig mit Oberkante des Text-Elements setzen
		track.style.top = el.offsetTop + 'px';

		function update() {
			var scrollable = el.scrollHeight > el.clientHeight + 2;
			track.style.display = scrollable ? '' : 'none';

			if ( scrollable ) {
				var ratio   = el.scrollTop / ( el.scrollHeight - el.clientHeight );
				var trackH  = track.clientHeight;
				thumb.style.top = ( ratio * ( trackH - THUMB_H ) ) + 'px';
			}

			var atEnd = ! scrollable || el.scrollTop + el.clientHeight >= el.scrollHeight - 2;
			body.classList.toggle( 'is-scroll-end', atEnd );
		}

		el.addEventListener( 'scroll', update, { passive: true } );
		update();

		// Drag
		var dragging = false, startY = 0, startScroll = 0;

		thumb.addEventListener( 'mousedown', function ( e ) {
			dragging    = true;
			startY      = e.clientY;
			startScroll = el.scrollTop;
			e.preventDefault();
		} );

		document.addEventListener( 'mousemove', function ( e ) {
			if ( ! dragging ) return;
			var trackH    = track.clientHeight;
			var thumbRange  = trackH - THUMB_H;
			var scrollRange = el.scrollHeight - el.clientHeight;
			el.scrollTop  = startScroll + ( e.clientY - startY ) / thumbRange * scrollRange;
		} );

		document.addEventListener( 'mouseup', function () {
			dragging = false;
		} );
	} );

	// =========================================================================
	// Bild-Ladeanimation — pulsierende Krone, solange img[loading=lazy] lädt
	// =========================================================================

	document.querySelectorAll( 'img[loading="lazy"]' ).forEach( function ( img ) {
		if ( img.complete && img.naturalWidth > 0 ) return;

		var parent = img.parentElement;
		if ( ! parent ) return;

		if ( getComputedStyle( parent ).position === 'static' ) {
			parent.style.position = 'relative';
		}

		var loader = document.createElement( 'span' );
		loader.className = 'img-loader';
		loader.setAttribute( 'aria-hidden', 'true' );
		parent.insertBefore( loader, img );
		img.classList.add( 'img-loader-pending' );

		var onSettle = function () {
			loader.remove();
			img.classList.remove( 'img-loader-pending' );
			img.classList.add( 'img-loader-loaded' );
		};

		img.addEventListener( 'load', onSettle, { once: true } );
		img.addEventListener( 'error', onSettle, { once: true } );
	} );

} )();
