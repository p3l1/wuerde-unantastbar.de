<?php
// ABOUTME: Archiv-Template für wuerde_kategorie-Terms.
// ABOUTME: Header zeigt Term-Bild oder gefilterte Karte; danach Karten-Grid aller Beiträge.

get_header();

$term      = get_queried_object();
$cat_color = get_term_meta( $term->term_id, 'wuerde_color_token', true );
if ( ! $cat_color ) {
    $cat_color = 'var(--color-cat-' . esc_attr( $term->slug ) . ')';
}

$image_id      = (int) get_term_meta( $term->term_id, 'wuerde_term_image', true );
$thumbnail_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

$posts = get_posts( [
    'post_type'      => 'wuerde_beitrag',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'tax_query'      => [ [
        'taxonomy' => 'wuerde_kategorie',
        'field'    => 'slug',
        'terms'    => $term->slug,
    ] ],
    'orderby' => 'title',
    'order'   => 'ASC',
] );

$mach_mit_url = get_permalink( get_page_by_path( 'mach-mit' ) ) ?: home_url( '/mach-mit/' );
?>

<?php if ( $thumbnail_url ) : ?>
<section
  class="page-banner"
  aria-label="<?php echo esc_attr( $term->name ); ?>"
  style="--page-banner-photo: url('<?php echo esc_url( $thumbnail_url ); ?>'); --page-banner-height: clamp(220px, 35vh, 380px);"
>
  <div class="page-banner__overlay" aria-hidden="true"></div>
  <div class="page-banner__content">
    <div class="kat-archive__breadcrumb">
      <a href="<?php echo esc_url( $mach_mit_url ); ?>">Mach mit</a>
      <span aria-hidden="true">›</span>
    </div>
    <h1 class="page-banner__title page-banner__title--small">
      <span class="kat-archive__dot" style="background:<?php echo esc_attr( $cat_color ); ?>"></span>
      <?php echo esc_html( $term->name ); ?>
    </h1>
  </div>
</section>
<?php else : ?>
<div class="kat-archive__map-header">
  <div class="kat-archive__map-meta">
    <div class="kat-archive__breadcrumb kat-archive__breadcrumb--dark">
      <a href="<?php echo esc_url( $mach_mit_url ); ?>">← Mach mit</a>
    </div>
    <h1 class="kat-archive__map-title">
      <span class="kat-archive__dot" style="background:<?php echo esc_attr( $cat_color ); ?>"></span>
      <?php echo esc_html( $term->name ); ?>
    </h1>
  </div>
  <div class="mitmach-map-wrapper">
    <div class="mitmach-map"
         id="mitmach-map"
         data-center-lat="51.2"
         data-center-lng="10.4"
         data-zoom="6"
         data-tile-style="osm"
         data-rest-url="<?php echo esc_url( rest_url( 'wuerde/v1/map-points?kategorie=' . urlencode( $term->slug ) ) ); ?>"
         style="height:380px"
         aria-label="Karte mit Beiträgen in der Kategorie <?php echo esc_attr( $term->name ); ?>">
    </div>
  </div>
</div>
<?php endif; ?>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">
  <div class="page-content__entry kat-archive">

    <?php if ( $thumbnail_url && $term->description ) : ?>
    <p class="kat-archive__description"><?php echo esc_html( $term->description ); ?></p>
    <?php endif; ?>

    <?php if ( ! $thumbnail_url && $term->description ) : ?>
    <p class="kat-archive__description"><?php echo esc_html( $term->description ); ?></p>
    <?php endif; ?>

    <?php if ( empty( $posts ) ) : ?>
    <p class="kat-archive__empty">Noch keine Beiträge in dieser Kategorie.</p>
    <?php else : ?>
    <ul class="mitmach-grid kat-archive__grid">
      <?php foreach ( $posts as $post ) :
          $thumbnail_url_card = get_the_post_thumbnail_url( $post->ID, 'medium' );
          $excerpt            = get_the_excerpt( $post );
          $post_ort_terms     = wp_get_post_terms( $post->ID, 'wuerde_ort', [ 'fields' => 'names' ] );
          $ort_label          = ! is_wp_error( $post_ort_terms ) && ! empty( $post_ort_terms ) ? $post_ort_terms[0] : '';
      ?>
      <li>
        <article class="mitmach-card" style="--cat-color:<?php echo esc_attr( $cat_color ); ?>">
          <?php if ( $thumbnail_url_card ) : ?>
          <div class="mitmach-card__image">
            <img src="<?php echo esc_url( $thumbnail_url_card ); ?>"
                 alt="<?php echo esc_attr( $post->post_title ); ?>"
                 loading="lazy">
          </div>
          <?php endif; ?>
          <h2 class="mitmach-card__title">
            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
              <?php echo esc_html( $post->post_title ); ?>
            </a>
          </h2>
          <?php if ( $excerpt ) : ?>
          <p class="mitmach-card__text"><?php echo esc_html( $excerpt ); ?></p>
          <?php endif; ?>
          <div class="mitmach-card__footer">
            <?php if ( $ort_label ) : ?>
            <span class="mitmach-card__tag"><?php echo esc_html( $ort_label ); ?></span>
            <?php endif; ?>
            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="mitmach-card__link">
              Details
            </a>
          </div>
        </article>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <div class="kat-archive__back">
      <a href="<?php echo esc_url( $mach_mit_url ); ?>" class="btn btn--secondary">
        ← Zurück zu „Mach mit"
      </a>
    </div>

  </div>
</main>

<?php get_footer(); ?>
