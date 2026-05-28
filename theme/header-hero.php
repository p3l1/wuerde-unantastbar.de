<?php
// ABOUTME: Header-Fragment für das Hero-Template — gibt <head> und Body-Open aus.
// ABOUTME: Ohne site-header, da das Hero-Template ihn nach dem Hero einbindet.
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

<div class="site-content">
