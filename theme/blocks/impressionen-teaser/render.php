<?php
// ABOUTME: Frontend-Rendering des Impressionen-Teaser-Blocks.
// ABOUTME: Wählt 3 zufällige Bilder aus dem kuratierten Pool und zeigt sie als verlinktes Grid.

$images      = (array) ( $attributes['images']     ?? [] );
$gallery_url = sanitize_url( $attributes['galleryUrl'] ?? '' );
$heading     = sanitize_text_field( $attributes['heading'] ?? 'Impressionen' );

if ( empty( $images ) ) {
    return;
}

// 3 zufällige Bilder aus dem Pool
$pool = $images;
shuffle( $pool );
$displayed = array_slice( $pool, 0, 3 );
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'impressionen-teaser' ] ); ?>>

  <div class="impressionen-teaser__header">
    <h2 class="impressionen-teaser__heading"><?php echo esc_html( $heading ); ?></h2>
    <?php if ( $gallery_url ) : ?>
    <a href="<?php echo esc_url( $gallery_url ); ?>" class="impressionen-teaser__all-link">
      Alle Impressionen →
    </a>
    <?php endif; ?>
  </div>

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
