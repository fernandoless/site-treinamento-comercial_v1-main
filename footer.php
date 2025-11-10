</main>
<footer role="contentinfo">
  <div class="container d-flex align-items-center justify-content-between">
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/footer_claro.png' ); ?>"
       alt="<?php echo esc_attr__( 'Claro', 'site-treinamento-comercial' ); ?>" >
    <p>&copy; <?php echo esc_html( date('Y') ); ?> Copyright <?php bloginfo('name'); ?>. Todos os direitos reservados.</p>
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/footer_trn.png' ); ?>"
       alt="<?php echo esc_attr__( 'Treinamento Comercial', 'site-treinamento-comercial' ); ?>" >
  </div>
</footer>

<script>
  jQuery(function ($) {
    $('#canaisOwl').owlCarousel({
      loop: true,
      margin: 16,          // espaçamento entre cards
      stagePadding: 64,    // padding lateral (mostra “meio” do próximo anterior)
      dots: false,
      nav: true,           // setas
      navText: [
        '<span class="visually-hidden">Anterior</span>',
        '<span class="visually-hidden">Próximo</span>'
      ],
      autoplay: false,
      smartSpeed: 450,
      responsive: {
        0:   { items: 1, stagePadding: 24, margin: 12 },
        576: { items: 1, stagePadding: 40 },
        768: { items: 2, stagePadding: 56 },
        992: { items: 3, stagePadding: 72 },
        1200:{ items: 4, stagePadding: 88 }
      }
    });
  });
</script>


<?php wp_footer(); ?>
</body>
</html>
