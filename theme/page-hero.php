<?php
// ABOUTME: Seitentemplate mit Fullscreen-Hero aus dem Beitragsbild.
// ABOUTME: Transparenter Header schwebt über dem Foto; danach normaler Seiteninhalt.
/**
 * Template Name: Hero
 */

get_header();

$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$hero_style    = $thumbnail_url
    ? ' style="--hero-photo: url(\'' . esc_url( $thumbnail_url ) . '\');"'
    : '';
?>

<section
  class="site-hero demo-hero demo-hero--fullscreen-photo"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  <?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>
></section>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
