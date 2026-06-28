<?php
// ABOUTME: Seitentemplate mit vollflächigem Hero; Menü liegt transparent darüber.
// ABOUTME: Hero-Titel unten links mit farbigen Schlusspunkten, Text aus Post-Meta.
/**
 * Template Name: Hero
 */

get_header();

$thumbnail_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$hero_photo     = $thumbnail_url ?: get_stylesheet_directory_uri() . '/assets/img/hero-haende.jpg';
$post_id        = get_the_ID();
$button_text    = get_post_meta( $post_id, 'hero_button_text', true );
$button_url     = get_post_meta( $post_id, 'hero_button_url',  true );
$title          = get_post_meta( $post_id, 'hero_title',       true ) ?: get_bloginfo( 'name' );

// Akzentfarben der Schlusspunkte: Türkis, Orange, Gelb (rotierend pro Satz).
$accent_colors = [ '#00ACA0', '#e76503', '#F7BC2F' ];
?>

<section
  class="site-hero"
  aria-label="<?php echo esc_attr( $title ); ?>"
  style="--hero-photo: url('<?php echo esc_url( $hero_photo ); ?>');"
>
  <div class="site-hero__overlay" aria-hidden="true"></div>

  <div class="site-hero__content">

    <h1 class="site-hero__title"><?php
      $parts = array_values( array_filter( array_map( 'trim', explode( '.', $title ) ) ) );
      foreach ( $parts as $i => $part ) {
          $color = $accent_colors[ $i % count( $accent_colors ) ];
          echo '<span>' . esc_html( $part )
             . '<span class="site-hero__dot" style="color:' . esc_attr( $color ) . '">.</span></span>';
      }
    ?></h1>

    <?php if ( $button_text && $button_url ) : ?>
      <div class="site-hero__actions">
        <a href="<?php echo esc_url( $button_url ); ?>"
           class="btn btn-crown btn--lg">
          <span class="btn-crown__icon" aria-hidden="true"></span>
          <?php echo esc_html( $button_text ); ?>
        </a>
      </div>
    <?php endif; ?>

  </div>

</section>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
