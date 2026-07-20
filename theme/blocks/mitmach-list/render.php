<?php
// ABOUTME: Frontend-Rendering des Mitmach-Liste-Blocks.
// ABOUTME: Gibt Suchfeld, Filter-Chips und Kategorie-Accordion mit Mitmach-Karten aus.

$show_search       = (bool) ( $attributes['showSearch'] ?? true );
$default_category  = sanitize_key( $attributes['defaultCategory'] ?? '' );
$hidden_categories = array_map( 'sanitize_key', (array) ( $attributes['hiddenCategories'] ?? [] ) );

$terms = get_terms( [
    'taxonomy'   => 'wuerde_kategorie',
    'hide_empty' => false,
    'orderby'    => 'term_order',
    'order'      => 'ASC',
] );

if ( ! is_wp_error( $terms ) && ! empty( $hidden_categories ) ) {
    $terms = array_filter( $terms, function ( $term ) use ( $hidden_categories ) {
        return ! in_array( $term->slug, $hidden_categories, true );
    } );
}

if ( is_wp_error( $terms ) ) {
    $terms = [];
}

// Alle Beiträge in einem Query laden und nach Term gruppieren.
$all_posts = get_posts( [
    'post_type'      => 'wuerde_beitrag',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
] );

$posts_by_term = [];
foreach ( $all_posts as $post ) {
    $post_terms = wp_get_post_terms( $post->ID, 'wuerde_kategorie', [ 'fields' => 'slugs' ] );
    if ( empty( $post_terms ) ) {
        $posts_by_term['_ohne_kategorie'][] = $post;
    } else {
        foreach ( $post_terms as $term_slug ) {
            $posts_by_term[ $term_slug ][] = $post;
        }
    }
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'mitmach-liste' ] ); ?>>

    <?php if ( $show_search ) : ?>
    <form class="mitmach-search" role="search" aria-label="Mitmach-Suche" id="mitmach-search-form">
        <div class="mitmach-search__wrapper">
            <svg class="mitmach-search__icon" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="9" r="6"/>
                <path d="m15 15 3 3" stroke-linecap="round"/>
            </svg>
            <input class="mitmach-search__input" type="search"
                   placeholder="Anregungen finden…"
                   aria-label="Mitmach-Suche"
                   id="mitmach-search-input"
                   autocomplete="off">
            <button class="mitmach-search__btn btn btn--primary" type="submit">Suchen</button>
        </div>
    </form>
    <?php endif; ?>

    <?php if ( ! empty( $terms ) ) : ?>
    <div class="category-accordion" id="mitmach-accordion" data-default-category="<?php echo esc_attr( $default_category ); ?>">

        <?php foreach ( $terms as $term ) :
            $term_posts  = $posts_by_term[ $term->slug ] ?? [];
            $total_count = count( $term_posts );
            $color       = get_term_meta( $term->term_id, 'wuerde_color_token', true );
            if ( ! $color ) {
                $color = 'var(--color-cat-' . esc_attr( $term->slug ) . ')';
            }
            $panel_id    = 'mitmach-cat-' . esc_attr( $term->slug );
            $has_posts   = ! empty( $term_posts );
            // Kategorien starten immer zugeklappt (Akkordion öffnet sich erst per Klick).
            $is_open     = false;
            $term_url    = get_term_link( $term, 'wuerde_kategorie' );
            $shown_posts = array_slice( $term_posts, 0, 4 );
            $has_more    = $total_count > 4;
        ?>
        <div class="category-accordion__item" data-category="<?php echo esc_attr( $term->slug ); ?>">
            <div class="category-accordion__header">
                <button class="category-accordion__trigger"
                        aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                    <span class="category-accordion__dot" style="background:<?php echo esc_attr( $color ); ?>"></span>
                    <?php echo esc_html( $term->name ); ?>
                    <span class="category-accordion__count">(<?php echo $total_count; ?>)</span>
                    <svg class="category-accordion__chevron" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    </svg>
                </button>
                <?php if ( ! is_wp_error( $term_url ) ) : ?>
                <a class="category-accordion__page-link"
                   href="<?php echo esc_url( $term_url ); ?>"
                   aria-label="Alle Beiträge in <?php echo esc_attr( $term->name ); ?> anzeigen">
                    Alle anzeigen
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M2 10L10 2M10 2H5M10 2v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
            <div class="category-accordion__panel<?php echo $is_open ? '' : ' category-accordion__panel--closed'; ?>"
                 id="<?php echo esc_attr( $panel_id ); ?>">

                <?php if ( ! empty( $shown_posts ) ) : ?>
                <ul class="category-accordion__list mitmach-grid">
                    <?php foreach ( $shown_posts as $post ) :
                        $thumbnail_url  = get_the_post_thumbnail_url( $post->ID, 'medium' );
                        $excerpt        = get_the_excerpt( $post );
                        $post_ort_terms = wp_get_post_terms( $post->ID, 'wuerde_ort' );
                        $ort_term       = ! is_wp_error( $post_ort_terms ) && ! empty( $post_ort_terms ) ? $post_ort_terms[0] : null;
                        $ort_label      = $ort_term ? $ort_term->name : '';
                    ?>
                    <li>
                        <article class="mitmach-card"
                                 style="--cat-color:<?php echo esc_attr( $color ); ?>"
                                 data-title="<?php echo esc_attr( strtolower( $post->post_title ) ); ?>"
                                 data-text="<?php echo esc_attr( strtolower( $excerpt ) ); ?>"
                                 data-category="<?php echo esc_attr( $term->slug ); ?>"
                                 data-ort="<?php echo esc_attr( strtolower( $ort_label ) ); ?>">
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
                                <?php if ( $ort_term ) :
                                    $ort_url = get_term_link( $ort_term, 'wuerde_ort' );
                                ?>
                                <a href="<?php echo esc_url( ! is_wp_error( $ort_url ) ? $ort_url : '#' ); ?>"
                                   class="mitmach-card__ort">
                                  <svg width="12" height="12" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1C4.79 1 3 2.79 3 5c0 3.5 4 8 4 8s4-4.5 4-8c0-2.21-1.79-4-4-4zm0 5.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" fill="currentColor"/></svg>
                                  <?php echo esc_html( $ort_label ); ?>
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
                <?php if ( $has_more && ! is_wp_error( $term_url ) ) : ?>
                <div class="category-accordion__more">
                    <a href="<?php echo esc_url( $term_url ); ?>" class="category-accordion__more-link">
                        Alle <?php echo $total_count; ?> Beiträge in dieser Kategorie anzeigen →
                    </a>
                </div>
                <?php endif; ?>
                <?php else : ?>
                <p class="category-accordion__empty">Noch keine Beiträge in dieser Kategorie.</p>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

</div>
