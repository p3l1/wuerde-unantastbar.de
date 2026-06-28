<?php
// ABOUTME: Template für die Detailseite eines einzelnen Mitmach-Beitrags.
// ABOUTME: Zeigt Titel, Kategorie, Ort, Beitragsbild und Content mit Page-Banner.

get_header();

$post_id       = get_the_ID();
$thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full' );

$kat_terms = wp_get_post_terms( $post_id, 'wuerde_kategorie' );
$ort_terms = wp_get_post_terms( $post_id, 'wuerde_ort' );
$kategorie = ! is_wp_error( $kat_terms ) && ! empty( $kat_terms ) ? $kat_terms[0] : null;
$ort_term  = ! is_wp_error( $ort_terms )  && ! empty( $ort_terms )  ? $ort_terms[0] : null;
$ort       = $ort_term ? $ort_term->name : '';

$cat_color = '';
if ( $kategorie ) {
    $cat_color = get_term_meta( $kategorie->term_id, 'wuerde_color_token', true );
    if ( ! $cat_color ) {
        $cat_color = 'var(--color-cat-' . esc_attr( $kategorie->slug ) . ')';
    }
}
?>

<?php
$crown_url     = get_template_directory_uri() . '/assets/krone-black.png';
$fallback_color = 'var(--color-teal)';
$banner_color   = $cat_color ?: $fallback_color;
?>

<?php if ( $thumbnail_url ) : ?>
<section
  class="page-banner"
  aria-label="<?php echo esc_attr( get_the_title() ); ?>"
  style="--page-banner-photo: url('<?php echo esc_url( $thumbnail_url ); ?>'); --page-banner-height: clamp(280px, 40vh, 420px);"
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
  style="--cat-color: <?php echo esc_attr( $banner_color ); ?>; --crown-url: url('<?php echo esc_url( $crown_url ); ?>'); --page-banner-height: clamp(280px, 40vh, 420px);"
>
  <div class="page-banner__content">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
  </div>
</section>
<?php endif; ?>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry beitrag-detail">


      <?php if ( $kategorie || $ort ) : ?>
      <div class="beitrag-detail__meta">
        <?php if ( $kategorie ) :
            $kat_url = get_term_link( $kategorie, 'wuerde_kategorie' );
        ?>
        <a href="<?php echo esc_url( ! is_wp_error( $kat_url ) ? $kat_url : '#' ); ?>"
           class="beitrag-detail__kat"
           style="--cat-color:<?php echo esc_attr( $cat_color ); ?>">
          <?php echo esc_html( $kategorie->name ); ?>
        </a>
        <?php endif; ?>
        <?php if ( $ort_term ) :
            $ort_url = get_term_link( $ort_term, 'wuerde_ort' );
        ?>
        <a href="<?php echo esc_url( ! is_wp_error( $ort_url ) ? $ort_url : '#' ); ?>"
           class="beitrag-detail__ort">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
            <path d="M7 1C4.79 1 3 2.79 3 5c0 3.5 4 8 4 8s4-4.5 4-8c0-2.21-1.79-4-4-4zm0 5.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"
                  fill="currentColor"/>
          </svg>
          <?php echo esc_html( $ort ); ?>
        </a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <div class="beitrag-detail__content">
        <?php the_content(); ?>
      </div>

      <div class="beitrag-detail__back">
        <a href="<?php echo esc_url( wuerde_machmit_url() ); ?>"
           class="btn btn--secondary">
          ← Zurück zu „Mach mit"
        </a>
      </div>

    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
