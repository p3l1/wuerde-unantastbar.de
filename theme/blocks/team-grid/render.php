<?php
// ABOUTME: Frontend-Rendering des Team-Raster-Blocks.
// ABOUTME: Gibt Profil-Karten für ausgewählte wuerde_person-Posts aus.

$layout      = sanitize_key( $attributes['layout'] ?? 'horizontal' );
$selected    = array_map( 'intval', (array) ( $attributes['selectedIds'] ?? [] ) );

$query_args = [
    'post_type'      => 'wuerde_person',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
];

if ( ! empty( $selected ) ) {
    $query_args['post__in'] = $selected;
    $query_args['orderby']  = 'post__in';
}

$persons = get_posts( $query_args );

if ( empty( $persons ) ) {
    return;
}

$card_class = 'horizontal' === $layout ? 'profile-card' : 'profile-card profile-card--vertical';
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'team-grid' ] ); ?>>
<?php foreach ( $persons as $person ) :
    $photo_url  = get_the_post_thumbnail_url( $person->ID, 'large' );
    $role       = esc_html( get_post_meta( $person->ID, 'person_role', true ) );
    $birthyear  = esc_html( get_post_meta( $person->ID, 'person_birthyear', true ) );
    $bio        = wp_kses_post( wpautop( $person->post_content ) );
    $position   = get_post_meta( $person->ID, 'person_photo_position', true ) ?: '50% 20%';

    $media_extra = $photo_url ? '' : ' profile-card__media--solid-teal';
    $no_photo    = $photo_url ? '' : ' profile-card--no-photo';
?>
    <article class="<?php echo esc_attr( trim( $card_class . $no_photo ) ); ?>">
        <div class="profile-card__media<?php echo esc_attr( $media_extra ); ?>" aria-hidden="true">
            <?php if ( $photo_url ) : ?>
                <img src="<?php echo esc_url( $photo_url ); ?>"
                     alt=""
                     style="object-position: <?php echo esc_attr( $position ); ?>"
                     loading="lazy">
            <?php else : ?>
                <span class="crown-watermark" aria-hidden="true"></span>
            <?php endif; ?>
        </div>
        <div class="profile-card__body">
            <?php if ( $birthyear ) : ?>
                <p class="profile-card__meta">
                    <span class="crown-accent" aria-hidden="true"></span>
                    Jahrgang <?php echo $birthyear; ?>
                </p>
            <?php endif; ?>
            <h3 class="profile-card__name"><?php echo esc_html( $person->post_title ); ?></h3>
            <?php if ( $role ) : ?>
                <p class="profile-card__title"><?php echo $role; ?></p>
            <?php endif; ?>
            <?php if ( $bio ) : ?>
                <div class="profile-card__text"><?php echo $bio; ?></div>
            <?php endif; ?>
        </div>
    </article>
<?php endforeach; ?>
</div>
