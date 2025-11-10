<?php get_header(); ?>
<section class="container my-4">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/content', get_post_type()); ?>
  <?php endwhile; ?>
    <nav class="mt-3" aria-label="<?php esc_attr_e('Paginação','site-treinamento-comercial'); ?>">
      <?php the_posts_pagination(); ?>
    </nav>
  <?php else : ?>
    <div class="alert alert-info"><?php esc_html_e('Nada encontrado.', 'site-treinamento-comercial'); ?></div>
  <?php endif; ?>
</section>
<?php get_footer(); ?>
