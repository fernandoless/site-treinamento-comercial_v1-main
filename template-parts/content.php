<article <?php post_class('card mb-3 shadow-sm'); ?>>
  <?php if (has_post_thumbnail()): ?>
    <a href="<?php the_permalink(); ?>">
      <?php the_post_thumbnail('medium_large', ['class'=>'card-img-top','alt'=>esc_attr(get_the_title())]); ?>
    </a>
  <?php endif; ?>
  <div class="card-body">
    <h2 class="h5 card-title mb-2"><a class="text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p class="card-text text-muted mb-2"><?php echo esc_html( wp_trim_words( wp_strip_all_tags(get_the_excerpt()), 24, '…' ) ); ?></p>
    <a class="btn btn-outline-primary btn-sm" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler mais', 'site-treinamento-comercial'); ?></a>
  </div>
</article>
