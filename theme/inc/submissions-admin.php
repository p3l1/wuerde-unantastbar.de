<?php
// ABOUTME: Admin-Erweiterungen für den Einreichungs-Workflow.
// ABOUTME: Warteliste-Untermenü, Dashboard-Widget und Mail-Fehler-Benachrichtigung.

function wuerde_warteliste_submenu() {
    $count = (int) ( wp_count_posts( 'wuerde_beitrag' )->pending ?? 0 );
    $label = $count > 0
        ? 'Warteliste <span class="awaiting-mod count-' . $count . '"><span class="pending-count">' . $count . '</span></span>'
        : 'Warteliste';

    add_submenu_page(
        'edit.php?post_type=wuerde_beitrag',
        'Warteliste — Ausstehende Einreichungen',
        $label,
        'edit_posts',
        'edit.php?post_type=wuerde_beitrag&post_status=pending'
    );
}
add_action( 'admin_menu', 'wuerde_warteliste_submenu' );

function wuerde_register_dashboard_widget() {
    wp_add_dashboard_widget(
        'wuerde_einreichungen',
        'Mitmach-Einreichungen',
        'wuerde_dashboard_widget_html'
    );
}
add_action( 'wp_dashboard_setup', 'wuerde_register_dashboard_widget' );

function wuerde_dashboard_widget_html() {
    $count = (int) ( wp_count_posts( 'wuerde_beitrag' )->pending ?? 0 );
    $url   = admin_url( 'edit.php?post_type=wuerde_beitrag&post_status=pending' );

    if ( $count === 0 ) {
        echo '<p>Keine ausstehenden Einreichungen.</p>';
        return;
    }

    $singular = $count === 1 ? 'ausstehende Einreichung' : 'ausstehende Einreichungen';
    echo '<p><strong>' . esc_html( (string) $count ) . '</strong> ' . esc_html( $singular ) . '</p>';
    echo '<p><a href="' . esc_url( $url ) . '" class="button button-primary">Warteliste anzeigen</a></p>';
}

function wuerde_beitrag_columns( array $columns ): array {
    $new = [];
    foreach ( $columns as $key => $label ) {
        if ( $key === 'title' ) {
            $new['wuerde_ref'] = 'Ref.-Nr.';
        }
        $new[ $key ] = $label;
    }
    return $new;
}
add_filter( 'manage_wuerde_beitrag_posts_columns', 'wuerde_beitrag_columns' );

function wuerde_beitrag_column_content( string $column, int $post_id ): void {
    if ( $column === 'wuerde_ref' ) {
        echo '<strong style="font-family:monospace">#' . esc_html( (string) $post_id ) . '</strong>';
    }
}
add_action( 'manage_wuerde_beitrag_posts_custom_column', 'wuerde_beitrag_column_content', 10, 2 );

function wuerde_show_mail_error_notice() {
    $notice = get_transient( 'wuerde_mail_fehler' );
    if ( ! $notice ) {
        return;
    }
    delete_transient( 'wuerde_mail_fehler' );
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
}
add_action( 'admin_notices', 'wuerde_show_mail_error_notice' );
