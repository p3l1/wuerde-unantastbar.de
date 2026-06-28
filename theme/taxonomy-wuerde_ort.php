<?php
// ABOUTME: Archiv-Template für wuerde_ort-Terms.
// ABOUTME: Karte mit gefilterten Beiträgen und Card-Grid, analog zu wuerde_kategorie.

get_header();

$term = get_queried_object();

$posts = get_posts( [
    'post_type'      => 'wuerde_beitrag',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'tax_query'      => [ [
        'taxonomy' => 'wuerde_ort',
        'field'    => 'slug',
        'terms'    => $term->slug,
    ] ],
    'orderby' => 'title',
    'order'   => 'ASC',
] );

// Kartenzentrierung: ersten Post mit Koordinaten suchen.
$center_lat = '51.2';
$center_lng = '10.4';
$map_zoom   = '6';
foreach ( $posts as $post ) {
    $lat = get_post_meta( $post->ID, 'wuerde_lat', true );
    $lng = get_post_meta( $post->ID, 'wuerde_lng', true );
    if ( $lat && $lng ) {
        $center_lat = $lat;
        $center_lng = $lng;
        $map_zoom   = '11';
        break;
    }
}

$mach_mit_url = get_post_type_archive_link( 'wuerde_beitrag' ) ?: get_home_url( null, '/mach-mit/' );
?>

<section class="page-banner page-banner--map" aria-label="<?php echo esc_attr( $term->name ); ?>">
  <div class="mitmach-map"
       id="mitmach-map"
       data-center-lat="<?php echo esc_attr( (string) $center_lat ); ?>"
       data-center-lng="<?php echo esc_attr( (string) $center_lng ); ?>"
       data-zoom="<?php echo esc_attr( $map_zoom ); ?>"
       data-interactive="false"
       data-tile-style="osm"
       data-rest-url="<?php echo esc_url( add_query_arg( 'ort', $term->slug, rest_url( 'wuerde/v1/map-points' ) ) ); ?>"
       data-crown-url="<?php echo esc_url( get_template_directory_uri() . '/assets/krone-white.png' ); ?>"
       style="height:clamp(220px, 35vh, 380px)"
       aria-label="Karte mit Beiträgen am Ort <?php echo esc_attr( $term->name ); ?>">
  </div>
  <div class="page-banner__overlay" aria-hidden="true"></div>
  <div class="page-banner__content">
    <h1 class="page-banner__title page-banner__title--small">
      <?php echo esc_html( $term->name ); ?>
    </h1>
  </div>
</section>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">
  <div class="page-content__entry kat-archive">

    <?php if ( $term->description ) : ?>
    <p class="kat-archive__description"><?php echo esc_html( $term->description ); ?></p>
    <?php endif; ?>

    <?php if ( empty( $posts ) ) : ?>
    <p class="kat-archive__empty">Noch keine Beiträge an diesem Ort.</p>
    <?php else : ?>
    <ul class="mitmach-grid kat-archive__grid">
      <?php foreach ( $posts as $post ) :
          $thumbnail_url_card = get_the_post_thumbnail_url( $post->ID, 'medium' );
          $excerpt            = get_the_excerpt( $post );
          $kat_terms          = wp_get_post_terms( $post->ID, 'wuerde_kategorie' );
          $kat_term           = ! is_wp_error( $kat_terms ) && ! empty( $kat_terms ) ? $kat_terms[0] : null;
          $cat_color          = $kat_term ? get_term_meta( $kat_term->term_id, 'wuerde_color_token', true ) : '';
          if ( ! $cat_color ) {
              $cat_color = $kat_term
                  ? 'var(--color-cat-' . esc_attr( $kat_term->slug ) . ')'
                  : 'var(--color-teal)';
          }
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
            <?php if ( $kat_term ) :
                $kat_url = get_term_link( $kat_term, 'wuerde_kategorie' );
            ?>
            <a href="<?php echo esc_url( ! is_wp_error( $kat_url ) ? $kat_url : '#' ); ?>"
               class="mitmach-card__tag">
              <?php echo esc_html( $kat_term->name ); ?>
            </a>
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
