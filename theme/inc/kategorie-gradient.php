<?php
// ABOUTME: Baut aus einer Liste von Kategoriefarben den CSS-Verlauf für den Beitrags-Header.
// ABOUTME: Gemeinsame Quelle für die Vorschau-Miniaturen im Backend und den Banner im Frontend.

/**
 * Die drei wählbaren Verlaufs-Varianten samt Beschriftung für die Einstellungsseite.
 *
 * @return array<string, array<string, string>>
 */
function wuerde_kategorie_gradient_varianten(): array {
    return [
        'bogen' => [
            'label'       => 'Bogen',
            'description' => 'Weicher Verlauf quer über alle Farben — ruhig und am nächsten am bisherigen Banner.',
        ],
        'fahne' => [
            'label'       => 'Fahne',
            'description' => 'Harte Bänder, eines je Kategorie und alle gleich breit — die Zuordnungen bleiben abzählbar.',
        ],
        'schnitt' => [
            'label'       => 'Schnitt',
            'description' => 'Diagonale Keile in ungleichen Breiten — grafisch und bewegt, ganz ohne Animation.',
        ],
    ];
}

function wuerde_kategorie_gradient_default_variant(): string {
    return 'bogen';
}

/**
 * Liest die im Backend gewählte Variante; unbekannte Werte fallen auf den Default zurück.
 */
function wuerde_kategorie_gradient_variant(): string {
    $variant = get_option( 'wuerde_header_gradient_variant', wuerde_kategorie_gradient_default_variant() );
    $variant = is_string( $variant ) ? $variant : '';

    return array_key_exists( $variant, wuerde_kategorie_gradient_varianten() )
        ? $variant
        : wuerde_kategorie_gradient_default_variant();
}

/**
 * Erzeugt den fertigen CSS-background-image-Wert für eine Liste von Kategoriefarben.
 *
 * @param string[]    $colors  Farbwerte in Term-Reihenfolge (Hex oder beliebiger CSS-Farbwert).
 * @param string|null $variant bogen|fahne|schnitt; null nimmt die Variante aus der Option.
 * @return string Leerer String, wenn keine Farbe übrig bleibt — dann greift der CSS-Fallback.
 */
function wuerde_kategorie_gradient( array $colors, ?string $variant = null ): string {
    $colors = wuerde_kategorie_gradient_filter_colors( $colors );
    if ( empty( $colors ) ) {
        return '';
    }

    $variant = null === $variant ? wuerde_kategorie_gradient_variant() : $variant;
    if ( ! array_key_exists( $variant, wuerde_kategorie_gradient_varianten() ) ) {
        $variant = wuerde_kategorie_gradient_default_variant();
    }

    switch ( $variant ) {
        case 'fahne':
            return wuerde_kategorie_gradient_fahne( $colors );
        case 'schnitt':
            return wuerde_kategorie_gradient_schnitt( $colors );
        default:
            return wuerde_kategorie_gradient_bogen( $colors );
    }
}

/**
 * Weicher Verlauf quer über alle Farben; bei einer Kategorie ein Aufhellen-Abdunkeln-Bogen.
 */
function wuerde_kategorie_gradient_bogen( array $colors ): string {
    $count = count( $colors );

    if ( 1 === $count ) {
        return sprintf(
            'linear-gradient(100deg, %s %s, %s %s, %s %s)',
            wuerde_kategorie_gradient_shade( $colors[0], 20 ),
            wuerde_kategorie_gradient_stop( 0 ),
            $colors[0],
            wuerde_kategorie_gradient_stop( 52 ),
            wuerde_kategorie_gradient_shade( $colors[0], -16 ),
            wuerde_kategorie_gradient_stop( 100 )
        );
    }

    $stops = [];
    foreach ( $colors as $index => $color ) {
        $stops[] = $color . ' ' . wuerde_kategorie_gradient_stop( $index / ( $count - 1 ) * 100 );
    }

    return 'linear-gradient(100deg, ' . implode( ', ', $stops ) . ')';
}

/**
 * Gleich breite Bänder, eines je Kategorie; bei einer Kategorie einfarbig wie bisher.
 */
function wuerde_kategorie_gradient_fahne( array $colors ): string {
    $count = count( $colors );

    if ( 1 === $count ) {
        return sprintf( 'linear-gradient(0deg, %s, %s)', $colors[0], $colors[0] );
    }

    $width = 100 / $count;
    $stops = [];
    foreach ( $colors as $index => $color ) {
        $to      = $index === $count - 1 ? 100 : ( $index + 1 ) * $width;
        $stops[] = $color . ' '
            . wuerde_kategorie_gradient_stop( $index * $width ) . ' '
            . wuerde_kategorie_gradient_stop( $to );
    }

    return 'linear-gradient(90deg, ' . implode( ', ', $stops ) . ')';
}

/**
 * Diagonale Keile in ungleichen, aus dem Index abgeleiteten Breiten.
 */
function wuerde_kategorie_gradient_schnitt( array $colors ): string {
    $count = count( $colors );

    if ( 1 === $count ) {
        return sprintf(
            'linear-gradient(112deg, %s %s %s, %s %s %s)',
            wuerde_kategorie_gradient_shade( $colors[0], 14 ),
            wuerde_kategorie_gradient_stop( 0 ),
            wuerde_kategorie_gradient_stop( 44 ),
            wuerde_kategorie_gradient_shade( $colors[0], -14 ),
            wuerde_kategorie_gradient_stop( 44 ),
            wuerde_kategorie_gradient_stop( 100 )
        );
    }

    $weights = [];
    $sum     = 0.0;
    foreach ( array_keys( $colors ) as $index ) {
        $weight    = 1 + ( ( $index * 7 ) % 5 ) * 0.22;
        $weights[] = $weight;
        $sum      += $weight;
    }

    $cursor = 0.0;
    $stops  = [];
    foreach ( $colors as $index => $color ) {
        $from    = $cursor;
        $cursor += $weights[ $index ] / $sum * 100;
        // Letzter Keil endet exakt bei 100 %, damit kein Rundungsspalt entsteht.
        $to      = $index === $count - 1 ? 100 : $cursor;
        $stops[] = $color . ' '
            . wuerde_kategorie_gradient_stop( $from ) . ' '
            . wuerde_kategorie_gradient_stop( $to );
    }

    return 'linear-gradient(112deg, ' . implode( ', ', $stops ) . ')';
}

/**
 * Drei Nachkommastellen halten die Stops bei sieben Kategorien lückenlos.
 */
function wuerde_kategorie_gradient_stop( float $percent ): string {
    return number_format( $percent, 3, '.', '' ) . '%';
}

/**
 * Hellt (positiver Prozentwert) oder dunkelt (negativer) einen Hex-Farbwert ab.
 *
 * @return string Nicht-Hex-Werte (z. B. var(--…)) kommen unverändert zurück.
 */
function wuerde_kategorie_gradient_shade( string $color, float $percent ): string {
    $rgb = wuerde_kategorie_gradient_hex_to_rgb( $color );
    if ( null === $rgb ) {
        return $color;
    }

    foreach ( $rgb as $index => $channel ) {
        $value = $percent >= 0
            ? $channel + ( 255 - $channel ) * ( $percent / 100 )
            : $channel * ( 1 + $percent / 100 );

        $rgb[ $index ] = (int) round( max( 0, min( 255, $value ) ) );
    }

    return sprintf( '#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2] );
}

/**
 * @return array<int, int>|null Drei Kanäle 0..255 oder null, wenn kein Hex-Wert vorliegt.
 */
function wuerde_kategorie_gradient_hex_to_rgb( string $color ): ?array {
    $hex = ltrim( trim( $color ), '#' );

    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
        return null;
    }

    return [
        (int) hexdec( substr( $hex, 0, 2 ) ),
        (int) hexdec( substr( $hex, 2, 2 ) ),
        (int) hexdec( substr( $hex, 4, 2 ) ),
    ];
}

/**
 * @param string[] $colors
 * @return string[] Leere und nicht-String-Werte entfallen, Reihenfolge bleibt erhalten.
 */
function wuerde_kategorie_gradient_filter_colors( array $colors ): array {
    $filtered = [];
    foreach ( $colors as $color ) {
        if ( ! is_string( $color ) ) {
            continue;
        }
        $color = trim( $color );
        if ( '' !== $color ) {
            $filtered[] = $color;
        }
    }

    return $filtered;
}
