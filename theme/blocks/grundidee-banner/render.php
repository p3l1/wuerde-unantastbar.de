<?php
// ABOUTME: Rendert den Grundidee-Banner mit Krone-Wasserzeichen und CTA.
// ABOUTME: Attributes: title, buttonText, buttonUrl, color (teal|yellow).

$title       = $attributes['title']      ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url  = $attributes['buttonUrl']  ?? '';
$color       = $attributes['color']      ?? 'teal';

$box_class = 'highlight-box highlight-box--' . esc_attr( $color );
?>
<div class="<?php echo $box_class; ?> grundidee-banner">
  <span class="crown-watermark" aria-hidden="true"></span>
  <?php if ( $title ) : ?>
    <p class="highlight-box__title grundidee-banner__title"><?php echo wp_kses_post( $title ); ?></p>
  <?php endif; ?>
  <?php if ( $button_text && $button_url ) : ?>
    <div>
      <a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-crown">
        <span class="btn-crown__icon" aria-hidden="true"></span>
        <?php echo esc_html( $button_text ); ?>
      </a>
    </div>
  <?php endif; ?>
</div>
