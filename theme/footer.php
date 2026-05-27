<?php
// ABOUTME: Site-Chrome Footer — schließt site-content, gibt globalen Footer und wp_footer() aus.
// ABOUTME: Eingebunden via get_footer() in allen Seiten-Templates.
?>
</div><!-- .site-content -->

<footer class="site-footer" role="contentinfo">
  <div class="site-footer__inner">

    <div class="site-footer__brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-text site-footer__brand-link">
        Verein für Menschenwürde und Demokratie&nbsp;e.V.
      </a>
      <p class="site-footer__tagline">Jeder Mensch trägt Würde in sich. Unantastbar.</p>
    </div>

    <div class="site-footer__nav">
      <?php
      wp_nav_menu( [
        'theme_location' => 'footer',
        'container'      => false,
        'menu_class'     => 'site-footer__nav-list',
        'fallback_cb'    => false,
      ] );
      ?>
    </div>

    <div class="site-footer__actions">
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'spenden' ) ) ?: home_url( '/spenden/' ) ); ?>"
         class="btn btn--primary site-footer__donate">
        Jetzt spenden
      </a>
    </div>

    <p class="site-footer__copy">
      &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> &mdash;
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'impressum' ) ) ?: '#' ); ?>">Impressum</a>
      &middot;
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'datenschutzerklaerung' ) ) ?: '#' ); ?>">Datenschutz</a>
    </p>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
