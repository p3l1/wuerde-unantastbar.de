<?php
// ABOUTME: Generisches Seiten-Template für alle WordPress-Seiten.
// ABOUTME: Gibt den Block-Editor-Inhalt in einem Container aus.

get_header();
?>

<main class="page-content" id="main-content" aria-label="Seiteninhalt" tabindex="-1">

  <?php while ( have_posts() ) : the_post(); ?>

    <div class="page-content__entry">
      <?php the_content(); ?>
    </div>

  <?php endwhile; ?>

</main>

<?php get_footer(); ?>
