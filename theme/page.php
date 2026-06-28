<?php
// ABOUTME: Generisches Seiten-Template für alle WordPress-Seiten.
// ABOUTME: Gibt den Block-Editor-Inhalt in einem Container aus, optional mit Bild-Banner.

get_header();

$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );

$banner_height_map = [
    'sm' => '280px',
    'md' => 'clamp(280px, 45vh, 440px)',
    'lg' => 'clamp(350px, 55vh, 560px)',
];
$banner_height_key = get_post_meta( get_the_ID(), 'banner_height', true ) ?: 'md';
$banner_height     = $banner_height_map[ $banner_height_key ] ?? $banner_height_map['md'];
$banner_pos        = get_post_meta( get_the_ID(), 'banner_image_pos', true ) ?: 'center 40%';

$banner_aspect = '';
$thumb_id      = get_post_thumbnail_id( get_the_ID() );
if ( $thumb_id ) {
    $meta = wp_get_attachment_metadata( $thumb_id );
    if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
        $banner_aspect = esc_attr( $meta['width'] ) . ' / ' . esc_attr( $meta['height'] );
    }
}
?>

<?php if ( $thumbnail_url ) : ?>
<section
  class="page-banner"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  style="--page-banner-photo: url('<?php echo esc_url( $thumbnail_url ); ?>'); --page-banner-height: <?php echo esc_attr( $banner_height ); ?>; --page-banner-pos: <?php echo esc_attr( $banner_pos ); ?>;<?php if ( $banner_aspect ) echo '--banner-aspect:' . $banner_aspect . ';'; ?>"
>
  <div class="page-banner__overlay" aria-hidden="true"></div>
  <div class="page-banner__content">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
  </div>
</section>
<?php else : ?>
<section
  class="page-banner page-banner--kategorie"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  style="--cat-color: var(--color-yellow); --crown-url: url('<?php echo esc_url( get_template_directory_uri() . '/assets/krone-black.png' ); ?>'); --page-banner-height: <?php echo esc_attr( $banner_height ); ?>;"
>
  <div class="page-banner__content">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
  </div>
</section>
<?php endif; ?>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
