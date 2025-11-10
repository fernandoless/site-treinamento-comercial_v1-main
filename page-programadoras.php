<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class('my-4'); ?>>
  <div class="container">
    <h1><?php the_title(); ?></h1>
    <hr style="max-width: 430px;margin: -2rem 0 2rem;">
    <div class="entry-content text-center">
      <?php the_content(); ?>
    </div>
  </div>
</article>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
