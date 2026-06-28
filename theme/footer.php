<?php
// ABOUTME: Site-Chrome Footer — schließt site-content, gibt globalen Footer und wp_footer() aus.
// ABOUTME: Eingebunden via get_footer() in allen Seiten-Templates.
?>
</div><!-- .site-content -->

<?php
// Platzhalter-URLs — durch echte Social-Media-Profile ersetzen.
$social_links = [
  'instagram' => '#',
  'facebook'  => '#',
  'youtube'   => '#',
];
?>
<footer class="site-footer" role="contentinfo">
  <div class="site-footer__inner">

    <div class="site-footer__brand">
      <span class="site-footer__org">
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/krone-white.png' ); ?>"
             alt="" width="18" height="18" aria-hidden="true">
        <?php bloginfo( 'name' ); ?>
      </span>
      <div class="site-footer__social">
        <a href="<?php echo esc_url( $social_links['instagram'] ); ?>" aria-label="Instagram">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="<?php echo esc_url( $social_links['facebook'] ); ?>" aria-label="Facebook">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.5V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg>
        </a>
        <a href="<?php echo esc_url( $social_links['youtube'] ); ?>" aria-label="YouTube">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M23 7.5a3 3 0 0 0-2.1-2.1C19 5 12 5 12 5s-7 0-8.9.4A3 3 0 0 0 1 7.5 31 31 0 0 0 .6 12 31 31 0 0 0 1 16.5a3 3 0 0 0 2.1 2.1C5 19 12 19 12 19s7 0 8.9-.4a3 3 0 0 0 2.1-2.1A31 31 0 0 0 23.4 12 31 31 0 0 0 23 7.5zM9.8 15.3V8.7l5.7 3.3-5.7 3.3z"/></svg>
        </a>
      </div>
    </div>

    <nav class="site-footer__nav" aria-label="Footer-Navigation">
      <?php
      wp_nav_menu( [
        'theme_location' => 'footer',
        'container'      => false,
        'depth'          => 1,
        'fallback_cb'    => false,
      ] );
      ?>
    </nav>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
