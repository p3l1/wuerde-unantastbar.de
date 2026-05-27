// ABOUTME: Site-weites JavaScript für Header-Navigation (Hamburger, Dropdown).
// ABOUTME: Lädt auf allen Seiten außer Lookbook/Hero-Demo (die haben lookbook.js).

( function () {
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

} )();
