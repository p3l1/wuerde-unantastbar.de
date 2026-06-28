<?php
// ABOUTME: Frontend-Rendering des Mitmach-Karten-Blocks.
// ABOUTME: Gibt den Leaflet-Container aus und enqueued CSS/JS nur wenn der Block aktiv ist.

wp_enqueue_style( 'leaflet' );
wp_enqueue_style( 'leaflet-markercluster' );
wp_enqueue_style( 'leaflet-markercluster-default' );
wp_enqueue_script( 'leaflet' );
wp_enqueue_script( 'leaflet-markercluster' );

$center_lat = (float) ( $attributes['centerLat'] ?? 51.2 );
$center_lng = (float) ( $attributes['centerLng'] ?? 10.4 );
$zoom       = (int)   ( $attributes['zoom']      ?? 7 );
$height     = sanitize_text_field( $attributes['height'] ?? '1000px' );
$tile_style = sanitize_key( $attributes['tileStyle'] ?? 'osm' );

$rest_url = rest_url( 'wuerde/v1/map-points' );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'mitmach-map-wrapper' ] ); ?>>
    <div class="mitmach-map"
         id="mitmach-map"
         data-center-lat="<?php echo esc_attr( $center_lat ); ?>"
         data-center-lng="<?php echo esc_attr( $center_lng ); ?>"
         data-zoom="<?php echo esc_attr( $zoom ); ?>"
         data-tile-style="<?php echo esc_attr( $tile_style ); ?>"
         data-rest-url="<?php echo esc_url( $rest_url ); ?>"
         data-crown-url="<?php echo esc_url( get_template_directory_uri() . '/assets/krone-white.png' ); ?>"
         style="height:<?php echo esc_attr( $height ); ?>"
         aria-label="Deutschlandkarte mit Mitmach-Möglichkeiten">
    </div>

</div>
