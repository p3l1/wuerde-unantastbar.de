<?php
/**
 * Template Name: Hero Demo
 *
 * ABOUTME: Eigenstaendige Vorschau-Seite fuer Header, Hero (Vollbild-Foto) und Footer.
 * ABOUTME: Zeigt die CI-Komponenten im realistischen Seitenkontext ohne Lookbook-Rahmen.
 */

get_header();
?>
<div class="hero-demo-page">

  <!-- HERO SCENE: Transparenter Header schwebt ueber dem Foto -->
  <div class="hero-demo-scene">

    <header class="demo-header demo-header--transparent">
      <div class="demo-header__brand brand-text">Verein f&#252;r Menschenw&#252;rde und Demokratie e.V.</div>
      <nav class="demo-nav" aria-label="Demo Hauptnavigation">
        <ul class="demo-nav__list">
          <li><a href="#" class="demo-nav__link demo-nav__link--active">Startseite</a></li>
          <li class="demo-nav__item--dropdown">
            <a href="#" class="demo-nav__link demo-nav__link--has-dropdown">
              &Uuml;ber uns
              <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
            </a>
            <ul class="demo-dropdown">
              <li><a href="#" class="demo-dropdown__link">Verein &amp; Geschichte</a></li>
              <li><a href="#" class="demo-dropdown__link">Vorstand</a></li>
              <li><a href="#" class="demo-dropdown__link">Satzung</a></li>
            </ul>
          </li>
          <li><a href="#" class="demo-nav__link">Mach mit</a></li>
          <li><a href="#" class="demo-nav__link">Kontakt</a></li>
        </ul>
      </nav>
      <button class="demo-hamburger" aria-label="Menu oeffnen" aria-expanded="false" data-hamburger>
        <span></span><span></span><span></span>
      </button>
    </header>

    <div class="demo-mobile-nav demo-mobile-nav--dark demo-mobile-nav--fullscreen" aria-hidden="true" data-mobile-nav>
      <ul class="demo-mobile-nav__list">
        <li><a href="#">Startseite</a></li>
        <li class="demo-mobile-nav__item--sub">
          <button class="demo-mobile-nav__sub-trigger" aria-expanded="false" data-sub-trigger>
            &Uuml;ber uns
            <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
          </button>
          <ul class="demo-mobile-nav__sub">
            <li><a href="#">Verein &amp; Geschichte</a></li>
            <li><a href="#">Vorstand</a></li>
            <li><a href="#">Satzung</a></li>
          </ul>
        </li>
        <li><a href="#">Mach mit</a></li>
        <li><a href="#">Kontakt</a></li>
      </ul>
      <div class="demo-mobile-nav__brand brand-text">Verein f&#252;r Menschenw&#252;rde und Demokratie e.V.</div>
    </div>

    <section class="demo-hero demo-hero--fullscreen-photo"
             style="--hero-photo:url('https://wuerde-unantastbar.de/wp-content/uploads/2024/02/LS_02059-1024x683.jpg');"
             aria-label="Hero">
      <div class="demo-hero__content">
        <h1 class="demo-hero__title">Die W&#252;rde des Menschen ist unantastbar.</h1>
        <p class="demo-hero__text">Wir stehen auf f&#252;r eine Gesellschaft, in der jeder Mensch mit W&#252;rde leben kann. Mehr als 50&#8239;000 W&#252;rdetafeln sind bereits gebrannt und in Politik, Bildung, Kultur und Gesellschaft sichtbar.</p>
        <div class="demo-hero__actions">
          <button class="btn btn-crown btn--lg"><span class="btn-crown__icon" aria-hidden="true"></span>Jetzt mitmachen</button>
        </div>
      </div>
    </section>

  </div><!-- /.hero-demo-scene -->

  <!-- CONTENT -->
  <main class="hero-demo-content">
    <div class="hero-demo-content__inner">
      <h2>Wir setzen uns ein</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
      <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="demo-footer">
    <div class="demo-footer__container">
    <div class="demo-footer__brand brand-text">W&#252;rde Unantastbar</div>
    <p class="demo-footer__tagline">Verein f&#252;r Menschenw&#252;rde und Demokratie e.V.</p>
    <nav class="demo-footer__nav" aria-label="Footer Navigation">
      <ul>
        <li><a href="#">Impressum</a></li>
        <li><a href="#">Datenschutz</a></li>
        <li><a href="#">Satzung</a></li>
        <li><a href="#">Kontakt</a></li>
      </ul>
    </nav>
    <p class="demo-footer__copy">&copy; <?php echo esc_html( date( 'Y' ) ); ?> Verein f&#252;r Menschenw&#252;rde und Demokratie e.V.</p>
    </div><!-- /.demo-footer__container -->
  </footer>

</div>
<?php get_footer(); ?>
