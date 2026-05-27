<?php
// ABOUTME: Site-Chrome Header — gibt <head>, Skiplink und globale Navigation aus.
// ABOUTME: Eingebunden via get_header() in allen Seiten-Templates.
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e( 'Zum Inhalt springen', 'wuerde-unantastbar' ); ?></a>

<header class="site-header" role="banner">
  <div class="site-header__inner">

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__brand brand-text" rel="home">
      <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/krone-teal.png' ); ?>"
           alt="" width="32" height="32" aria-hidden="true" loading="eager">
      <span>Verein für Menschenwürde<span class="site-header__brand-break"> und Demokratie e.V.</span></span>
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

<div class="site-content">
