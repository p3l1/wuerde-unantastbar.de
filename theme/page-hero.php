<?php
// ABOUTME: Seitentemplate mit Fullscreen-Hero aus dem Beitragsbild.
// ABOUTME: Navbar hängt unter dem Hero und wird beim Hochscrollen sticky.
/**
 * Template Name: Hero
 */

get_header( 'hero' );

$thumbnail_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$hero_style     = $thumbnail_url
    ? ' style="--hero-photo: url(\'' . esc_url( $thumbnail_url ) . '\');"'
    : '';
$post_id        = get_the_ID();
$button_text    = get_post_meta( $post_id, 'hero_button_text', true );
$button_url     = get_post_meta( $post_id, 'hero_button_url',  true );
$title          = get_post_meta( $post_id, 'hero_title',       true ) ?: get_bloginfo( 'name' );
$subtitle       = get_post_meta( $post_id, 'hero_subtitle',    true ) ?: get_bloginfo( 'description' );
?>

<section
  class="site-hero demo-hero demo-hero--fullscreen-photo"
  aria-label="<?php echo esc_attr( $title ); ?>"
  <?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>
>
  <div class="demo-hero__content">

    <h1 class="demo-hero__title"><?php echo esc_html( $title ); ?></h1>

    <?php if ( $subtitle ) : ?>
      <p class="demo-hero__text"><?php echo esc_html( $subtitle ); ?></p>
    <?php endif; ?>

    <?php if ( $button_text && $button_url ) : ?>
      <div class="demo-hero__actions">
        <a href="<?php echo esc_url( $button_url ); ?>"
           class="btn btn-crown btn--lg">
          <span class="btn-crown__icon" aria-hidden="true"></span>
          <?php echo esc_html( $button_text ); ?>
        </a>
      </div>
    <?php endif; ?>

  </div>

  <div class="site-hero__scroll-indicator" aria-hidden="true">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

</section>

<?php get_template_part( 'inc/site-header' ); ?>

<div class="hero-intro">
  <div class="hero-intro__inner">
    <p class="hero-intro__text"><?php the_title(); ?></p>
  </div>
</div>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
