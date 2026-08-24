<?php
// ABOUTME: Frontend-Rendering des Impressionen-Teaser-Blocks.
// ABOUTME: Zeigt 3 zufällige oder alle Bilder des kuratierten Pools als verlinktes Grid.

$images      = (array) ( $attributes['images'] ?? [] );
$gallery_url = sanitize_url( $attributes['galleryUrl'] ?? '' );
$show_all    = ! empty( $attributes['showAllImages'] );

if ( empty( $images ) ) {
    return;
}

if ( $show_all ) {
    $displayed = $images;
} else {
    $pool = $images;
    shuffle( $pool );
    $displayed = array_slice( $pool, 0, 3 );
}

$wrapper_class = 'impressionen-teaser' . ( $show_all ? ' impressionen-teaser--all' : '' );
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => $wrapper_class ] ); ?>>

  <div class="impressionen-teaser__grid">
    <?php foreach ( $displayed as $image ) :
        $id  = (int) ( $image['id'] ?? 0 );
        $alt = $image['alt'] ?? '';
        if ( ! $id ) continue;
    ?>
    <div class="impressionen-teaser__item">
      <?php if ( $gallery_url ) : ?>
      <a href="<?php echo esc_url( $gallery_url ); ?>" class="impressionen-teaser__link" tabindex="-1" aria-hidden="true">
      <?php endif; ?>
        <?php echo wp_get_attachment_image( $id, 'large', false, [
            'class'   => 'impressionen-teaser__img',
            'alt'     => esc_attr( $alt ),
            'loading' => 'lazy',
        ] ); ?>
      <?php if ( $gallery_url ) : ?>
      </a>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if ( $gallery_url ) : ?>
  <div class="impressionen-teaser__cta">
    <a href="<?php echo esc_url( $gallery_url ); ?>" class="btn btn--secondary">
      Alle Impressionen ansehen
    </a>
  </div>
  <?php endif; ?>

</section>
