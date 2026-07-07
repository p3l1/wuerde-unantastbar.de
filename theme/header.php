<?php
// ABOUTME: Site-Chrome Header — gibt <head>, Skiplink und globale Navigation aus.
// ABOUTME: Eingebunden via get_header() in allen Seiten-Templates.
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php // Browser-Chrome (Statusleiste/Toolbar) auf Mobilgeräten in Markengelb. ?>
  <meta name="theme-color" content="#F7BC2F">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e( 'Zum Inhalt springen', 'wuerde-unantastbar' ); ?></a>

<?php get_template_part( 'inc/site-header' ); ?>

<div class="site-content">
