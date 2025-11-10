<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class('my-4'); ?>>
  <div class="container">
    <h1><?php the_title(); ?>
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/top_embaixador.png' ); ?>"
       alt="<?php echo esc_attr__( 'H1 Embaixador', 'site-treinamento-comercial' ); ?>" ></h1>
    <hr style="max-width: 430px;margin: -2rem 0 2rem;">
    <div class="entry-content"><?php the_content(); ?>
    <h3>Em um jogo, eu nunca perco: Ou ganho, ou aprendo!</h3>
    <img src="" alt="foto">
    </div>
  </div>
</article>

  <!-- -----------------------------------------------------
      TODO: Query buscando apenas os posts na categoria "embaixador"
  ----------------------------------------------------- -->
<section class="bg-body-tertiary py-4">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between">
      <h2><?php esc_html_e('Confira abaixo algumas ações já realizadas pelo nosso Embaixador:','site-treinamento-comercial'); ?></h2>
      <div class="d-none d-md-block text-muted small"><?php esc_html_e('Últimos posts','site-treinamento-comercial'); ?></div>
    </div>

    <div id="canaisCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php
          $canais_q = new WP_Query([
            'posts_per_page' => 12,
            'ignore_sticky_posts' => true,
          ]);
          $count = 0;
          $slide_index = 0;
          if ($canais_q->have_posts()):
            while ($canais_q->have_posts()): $canais_q->the_post();
              if ($count % 3 === 0) {
                $active = $slide_index === 0 ? ' active' : '';
                echo '<div class="carousel-item'.$active.'"><div class="row g-3">';
              }
        ?>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm canais-card">
                  <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>">
                      <?php the_post_thumbnail('medium_large', ['class'=>'card-img-top','alt'=>esc_attr(get_the_title())]); ?>
                    </a>
                  <?php endif; ?>
                  <div class="card-body">
                    <h3 class="card-title"><a class="text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="card-text text-muted"><?php echo esc_html( wp_trim_words( wp_strip_all_tags(get_the_excerpt()), 18, '…' ) ); ?></p>
                  </div>
                  <div class="card-footer bg-transparent border-0">
                    <a class="btn btn-outline-primary btn-sm" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler','site-treinamento-comercial'); ?></a>
                  </div>
                </div>
              </div>
        <?php
              $count++;
              if ($count % 3 === 0) {
                echo '</div></div>';
                $slide_index++;
              }
            endwhile;
            if ($count % 3 !== 0) { echo '</div></div>'; }
            wp_reset_postdata();
          else:
        ?>
          <div class="carousel-item active">
            <div class="alert alert-info mb-0"><?php esc_html_e('Ainda não há posts para exibir.','site-treinamento-comercial'); ?></div>
          </div>
        <?php endif; ?>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#canaisCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><?php esc_html_e('Anterior','site-treinamento-comercial'); ?></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#canaisCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><?php esc_html_e('Próximo','site-treinamento-comercial'); ?></span>
      </button>
    </div>
  </div>
</section>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
