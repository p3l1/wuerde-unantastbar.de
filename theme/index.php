<?php
// ABOUTME: Main template file — WordPress falls back to this for all page types.
// ABOUTME: Replace with more specific templates (page.php, single.php, etc.) as needed.
get_header(); ?>

<main>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <h2><?php the_title(); ?></h2>
      <div><?php the_content(); ?></div>
    </article>
  <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
