<?php get_header(); ?>
<section class="container my-5">
  <h1 class="mb-2"><?php esc_html_e('Página não encontrada', 'site-treinamento-comercial'); ?></h1>
  <p class="mb-3"><?php esc_html_e('A página solicitada não existe.', 'site-treinamento-comercial'); ?></p>
  <a class="btn btn-primary" href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e('Voltar à página inicial','site-treinamento-comercial'); ?></a>
</section>
<?php get_footer(); ?>
