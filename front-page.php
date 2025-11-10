<?php get_header(); ?>

  <!-- -----------------------------------------------------
      TODO: Carrossel mostrando apenas a categoria destaque
  ----------------------------------------------------- -->
<section>
  <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <?php
        $hero_q = new WP_Query([
          'posts_per_page' => 5,
          'ignore_sticky_posts' => true,
          'meta_query' => [['key' => '_thumbnail_id','compare' => 'EXISTS']],
        ]);
        $i=0;
        while ($hero_q->have_posts()): $hero_q->the_post(); ?>
          <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo esc_attr($i); ?>" <?php if ($i===0) echo 'class="active" aria-current="true"'; ?> aria-label="<?php echo esc_attr(sprintf(__('Slide %d','site-treinamento-comercial'), $i+1)); ?>"></button>
      <?php $i++; endwhile; wp_reset_postdata(); ?>
    </div>

    <div class="carousel-inner">
      <?php
        $hero_q = new WP_Query([
          'posts_per_page' => 5,
          'ignore_sticky_posts' => true,
          'meta_query' => [['key' => '_thumbnail_id','compare' => 'EXISTS']],
        ]);
        $i=0;
        while ($hero_q->have_posts()): $hero_q->the_post();
          $is_active = $i===0 ? ' active' : '';
      ?> <?php
// pega a URL do thumbnail (use o tamanho que você criou, ex: 'capa-1000x860')
$bg = get_the_post_thumbnail_url(get_the_ID(), 'capa-1000x860');

// fallback opcional
if ( ! $bg ) {
  $bg = get_stylesheet_directory_uri() . '/img/place.png';
}
?>
      <div class="carousel-item<?php echo esc_attr($is_active); ?>" style="background-image: url('<?php echo esc_url($bg); ?>');">
        <div class="container" style="max-width: 80%;">
          <div class="row d-flex align-items-center" style="height:600px;">
            <div class="col-lg-6 order-1 order-lg-0 caixa_destaque" style="position: relative;">
              <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/top_destaques.png' ); ?>"
       alt="<?php echo esc_attr__( 'H1 Embaixador', 'site-treinamento-comercial' ); ?>" style="position: absolute;right: 4rem;top: -10rem;width: 130px;">
              <h2><?php the_title(); ?></h2>
              <div class="small">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
              </div>
              <hr>
              <p class="mb-3"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
              <a class="btn btn-primary" href="<?php the_permalink(); ?>"><?php esc_html_e('Ler mais','site-treinamento-comercial'); ?></a>
            </div>
            <!-- <div class="col-lg-6 order-0 order-lg-1">
            </div> -->
          </div>
        </div>
      </div>
      <?php $i++; endwhile; wp_reset_postdata(); ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden"><?php esc_html_e('Anterior','site-treinamento-comercial'); ?></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden"><?php esc_html_e('Próximo','site-treinamento-comercial'); ?></span>
    </button>
  </div>
</section>



  <!-- -----------------------------------------------------
      TODO: Query buscando apenas o post mais recente de cada uma das categorias regional-NOME
  ----------------------------------------------------- -->
<section class="bg-body-tertiary py-4">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="home">Giro <span>pelos canais</span></h1>
      <hr>
      <div class="d-none d-md-block text-muted small"><?php esc_html_e('Últimos posts','site-treinamento-comercial'); ?></div>
    </div>

    <div id="canaisOwl" class="owl-carousel owl-theme">
      <?php
        $canais_q = new WP_Query([
          'posts_per_page' => 12,
          'ignore_sticky_posts' => true,
        ]);

        if ($canais_q->have_posts()):
          while ($canais_q->have_posts()): $canais_q->the_post(); ?>
            <div class="item"> <!-- cada item do Owl -->
              <div class="card shadow-sm canais-card">
                <?php if (has_post_thumbnail()): ?>
                  <?php $url = get_the_post_thumbnail_url(get_the_ID(), 'thumb-300x200'); ?>
                    <div class="thumb-300x200" style="background-image:url('<?php echo esc_url($url); ?>'); height: 200px;background-size: cover;background-position: center; border-radius: 1.5rem 1.5rem 0 0;"></div>
                  <a href="<?php the_permalink(); ?>">
                  </a>
                <?php endif; ?>
                <div class="card-body">
                  <h3 class="card-title">
                    <a class="text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>
                  <p class="card-text">
                    <?php echo esc_html( wp_trim_words( wp_strip_all_tags(get_the_excerpt()), 18, '…' ) ); ?>
                  </p>
                </div>
                <div class="card-footer bg-transparent border-0">
                  <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/ver_mais.png' ); ?>" alt="Ver mais" style="max-width: 100px;float: right;">
                  </a>
                </div>
              </div>
            </div>
          <?php endwhile;
          wp_reset_postdata();
        else: ?>
          <div class="item">
            <div class="alert alert-info mb-0">
              <?php esc_html_e('Ainda não há posts para exibir.','site-treinamento-comercial'); ?>
            </div>
          </div>
        <?php endif; ?>
    </div>
  </div>
</section>


<section>
  <div class="container">
    <div class="row">
      <div class="col-sm-8">
        <h2>VOCÊ AINDA VENDE DO <span>MESMO JEITO</span> DE <span>3 ANOS ATRÁS</span>?</h2>
        <div class="row">
          <div class="col-sm-8">
            <p>O comportamento do consumidor evoluiu, os canais se diversificaram e a jornada de compra se tornou mais complexa. No post de hoje, analisamos os impactos dessas mudanças no desempenho comercial e apresentamos caminhos para atualizar sua abordagem de vendas com base em dados, tendências e boas práticas do mercado.<br><br>
          </div>
          <div class="col-sm-4 d-flex justify-content-center align-items-center">
            foto
          </div>
        </div>
        Quer saber mais? Acesse nosso instagram aqui:</p>
      </div>
      <div class="col-sm-4 d-flex justify-content-center align-items-center">
        foto
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4 d-flex justify-content-center align-items-center">
        foto
      </div>
      <div class="col-sm-8">
        <h2><span>Insights da ATD25 no ar!</span></h2>
        <p>Você já ouviu falar em polimatía? E sabe por que a aprendizagem contínua pode ser o seu maior diferencial no desenvolvimento de equipes comerciais? No nosso novo conteúdo de Treinamento Comercial, exploramos como esses conceitos podem transformar sua forma de atuar e liderar! Acesse agora pelo nosso Instagram e mergulhe nesse tema que vai turbinar sua jornada profissional:</p>
        <p>@hubdoconhecimento</p>
      </div>
    </div>
    <p>

</p>
  </div>
</section>

<?php get_footer(); ?>
