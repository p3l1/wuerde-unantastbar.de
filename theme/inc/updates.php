<?php
// ABOUTME: Theme-Update-Checker über GitHub Releases.
// ABOUTME: Hängt sich in den WordPress-Update-Mechanismus ein und meldet neue Versionen im Admin.

define( 'WUERDE_GITHUB_REPO', 'p3l1/wuerde-unantastbar.de' );

add_filter( 'pre_set_site_transient_update_themes', 'wuerde_check_theme_update' );

function wuerde_check_theme_update( $transient ) {
    if ( empty( $transient->checked ) ) {
        return $transient;
    }

    $theme_slug      = get_template();
    $current_version = wp_get_theme()->get( 'Version' );

    $response = wp_remote_get(
        'https://api.github.com/repos/' . WUERDE_GITHUB_REPO . '/releases/latest',
        [
            'headers' => [
                'Accept'     => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
            ],
            'timeout' => 10,
        ]
    );

    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
        return $transient;
    }

    $release = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( empty( $release['tag_name'] ) || empty( $release['assets'] ) ) {
        return $transient;
    }

    $latest_version = ltrim( $release['tag_name'], 'v' );

    if ( ! version_compare( $latest_version, $current_version, '>' ) ) {
        return $transient;
    }

    $zip_url = '';
    foreach ( $release['assets'] as $asset ) {
        if ( substr( $asset['name'], -4 ) === '.zip' ) {
            $zip_url = $asset['browser_download_url'];
            break;
        }
    }

    if ( $zip_url ) {
        $transient->response[ $theme_slug ] = [
            'theme'       => $theme_slug,
            'new_version' => $latest_version,
            'url'         => $release['html_url'],
            'package'     => $zip_url,
        ];
    }

    return $transient;
}
