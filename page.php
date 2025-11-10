<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class('container my-4'); ?>>
  <h1 class="mb-3"><?php the_title(); ?></h1>
  <div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
