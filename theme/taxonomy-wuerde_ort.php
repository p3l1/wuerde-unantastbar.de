<?php
// ABOUTME: Archiv-Template für wuerde_ort-Terms.
// ABOUTME: Karten-Header und Kategorie-Accordion gefiltert auf diesen Ort.

get_header();

$term = get_queried_object();

// Alle Beiträge aus diesem Ort laden und nach Kategorie gruppieren.
$all_posts = get_posts( [
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

$posts_by_kat = [];
foreach ( $all_posts as $post ) {
    $kat_slugs = wp_get_post_terms( $post->ID, 'wuerde_kategorie', [ 'fields' => 'slugs' ] );
    if ( empty( $kat_slugs ) ) {
        $posts_by_kat['_ohne_kategorie'][] = $post;
    } else {
        foreach ( $kat_slugs as $slug ) {
            $posts_by_kat[ $slug ][] = $post;
        }
    }
}

// Nur Kategorien mit Beiträgen an diesem Ort anzeigen.
$all_terms = get_terms( [ 'taxonomy' => 'wuerde_kategorie', 'hide_empty' => false, 'orderby' => 'term_order', 'order' => 'ASC' ] );
$terms     = array_values( array_filter( is_wp_error( $all_terms ) ? [] : $all_terms, fn( $t ) => ! empty( $posts_by_kat[ $t->slug ] ) ) );

// Kartenzentrierung auf ersten Post mit Koordinaten.
$center_lat = '51.2';
$center_lng = '10.4';
$map_zoom   = '6';
foreach ( $all_posts as $post ) {
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
$ort_name     = $term->name;
?>

<section class="page-banner page-banner--map" aria-label="<?php echo esc_attr( $ort_name ); ?>">
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
       aria-label="Karte mit Beiträgen am Ort <?php echo esc_attr( $ort_name ); ?>">
  </div>
  <div class="page-banner__overlay" aria-hidden="true"></div>
  <div class="page-banner__content">
    <h1 class="page-banner__title page-banner__title--small">
      <?php echo esc_html( $ort_name ); ?>
    </h1>
  </div>
</section>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">
  <div class="page-content__entry kat-archive">

    <?php if ( empty( $all_posts ) ) : ?>
    <p class="kat-archive__empty">Noch keine Beiträge an diesem Ort.</p>
    <?php else : ?>

    <div class="category-accordion" id="mitmach-accordion" data-default-category="">

      <?php foreach ( $terms as $kat ) :
          $kat_posts   = $posts_by_kat[ $kat->slug ] ?? [];
          $total_count = count( $kat_posts );
          $color       = get_term_meta( $kat->term_id, 'wuerde_color_token', true );
          if ( ! $color ) {
              $color = 'var(--color-cat-' . esc_attr( $kat->slug ) . ')';
          }
          $panel_id = 'mitmach-cat-' . esc_attr( $kat->slug );
          $kat_url  = get_term_link( $kat, 'wuerde_kategorie' );
      ?>
      <div class="category-accordion__item" data-category="<?php echo esc_attr( $kat->slug ); ?>">
        <div class="category-accordion__header">
          <button class="category-accordion__trigger"
                  aria-expanded="true"
                  aria-controls="<?php echo esc_attr( $panel_id ); ?>">
            <span class="category-accordion__dot" style="background:<?php echo esc_attr( $color ); ?>"></span>
            <?php echo esc_html( $kat->name ); ?>
            <span class="category-accordion__count">(<?php echo esc_html( (string) $total_count ); ?>)</span>
            <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
              <path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            </svg>
          </button>
          <?php if ( ! is_wp_error( $kat_url ) ) : ?>
          <a class="category-accordion__page-link"
             href="<?php echo esc_url( $kat_url ); ?>"
             aria-label="Alle Beiträge in <?php echo esc_attr( $kat->name ); ?> anzeigen">
            Alle anzeigen
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
              <path d="M2 10L10 2M10 2H5M10 2v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <?php endif; ?>
        </div>
        <div class="category-accordion__panel" id="<?php echo esc_attr( $panel_id ); ?>">
          <ul class="category-accordion__list mitmach-grid">
            <?php foreach ( $kat_posts as $post ) :
                $thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
                $excerpt       = get_the_excerpt( $post );
            ?>
            <li>
              <article class="mitmach-card"
                       style="--cat-color:<?php echo esc_attr( $color ); ?>"
                       data-title="<?php echo esc_attr( strtolower( $post->post_title ) ); ?>"
                       data-text="<?php echo esc_attr( strtolower( $excerpt ) ); ?>"
                       data-category="<?php echo esc_attr( $kat->slug ); ?>"
                       data-ort="<?php echo esc_attr( strtolower( $ort_name ) ); ?>">
                <?php if ( $thumbnail_url ) : ?>
                <div class="mitmach-card__image">
                  <img src="<?php echo esc_url( $thumbnail_url ); ?>"
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
                  <span class="mitmach-card__ort">
                    <svg width="12" height="12" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1C4.79 1 3 2.79 3 5c0 3.5 4 8 4 8s4-4.5 4-8c0-2.21-1.79-4-4-4zm0 5.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" fill="currentColor"/></svg>
                    <?php echo esc_html( $ort_name ); ?>
                  </span>
                  <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="mitmach-card__link">
                    Details
                  </a>
                </div>
              </article>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <div class="kat-archive__back">
      <a href="<?php echo esc_url( $mach_mit_url ); ?>" class="btn btn--secondary">
        ← Zurück zu „Mach mit"
      </a>
    </div>

  </div>
</main>

<?php get_footer(); ?>
