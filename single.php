<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class('container my-4'); ?> style="background-color: #fff;">
  <h1 class="mb-2"><?php the_title(); ?></h1>
  <div class="text-muted small mb-3">
    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
  </div>
  <div class="entry-content"><?php the_content(); ?></div>
</article>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
