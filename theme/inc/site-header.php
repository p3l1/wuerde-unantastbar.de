<?php
// ABOUTME: Wiederverwendbares Site-Header-Fragment (Markup ohne <html>/<head>).
// ABOUTME: Eingebunden in header.php und page-hero.php.
?>
<header class="site-header" role="banner">
  <div class="site-header__inner">

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__brand brand-text" rel="home">
      <img class="brand-crown brand-crown--white"
           src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/krone-white.png' ); ?>"
           alt="" width="40" height="40" aria-hidden="true" loading="eager">
      <img class="brand-crown brand-crown--teal"
           src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/krone-teal.png' ); ?>"
           alt="" width="40" height="40" aria-hidden="true" loading="eager">
      <span class="site-header__brand-name">Verein für Menschenwürde<span class="site-header__brand-break"> und Demokratie e.V.</span></span>
    </a>

    <?php
    wp_nav_menu( [
      'theme_location'  => 'primary',
      'container'       => 'nav',
      'container_class' => 'site-nav',
      'container_id'    => 'site-nav',
      'menu_class'      => 'site-nav__list',
      'items_wrap'      => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
      'fallback_cb'     => false,
      'walker'          => null,
    ] );
    ?>

    <button class="site-hamburger"
            aria-label="Menü öffnen"
            aria-expanded="false"
            aria-controls="site-mobile-nav"
            data-site-hamburger>
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div>

  <nav class="site-mobile-nav"
       id="site-mobile-nav"
       aria-label="Mobile Navigation"
       aria-hidden="true"
       data-site-mobile-nav>
    <?php
    wp_nav_menu( [
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'site-mobile-nav__list',
      'fallback_cb'    => false,
    ] );
    ?>
  </nav>
</header>
