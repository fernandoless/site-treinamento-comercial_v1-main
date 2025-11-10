<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
  
  <!-- -----------------------------------------------------
  TODO: Verifique o não carregamento do style.css
  ----------------------------------------------------- -->
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="visually-hidden-focusable" href="#conteudo"><?php esc_html_e('Pular para o conteúdo','site-treinamento-comercial'); ?></a>

<header class="site-header fixed-top" role="banner">
  <nav id="principalNavbar" class="navbar navbar-dark">
    <div class="container">
      <div class="row">
        <div class="col-sm-4 text-start">
          <!-- Botão hamburger que abre o OFFCANVAS -->
          <button class="navbar-toggler" type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#primaryOffcanvas"
          aria-controls="primaryOffcanvas"
          aria-label="<?php esc_attr_e('Abrir menu', 'site-treinamento-comercial'); ?>">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
      <div class="col-sm-4 d-flex align-items-center justify-content-center">
        <a class="navbar-brand fw-semibold" href="<?php echo esc_url(home_url('/')); ?>">
          <strong>news</strong> treinamento comercial
        </a>
      </div>
      <div class="col-sm-4">
        <div class="d-flex justify-content-end" role="search">
          <?php get_search_form(); ?>
        </div>
      </div>
    </div>
  </div>



  </nav>

  <!-- OFFCANVAS: sai da lateral esquerda e cria overlay automaticamente -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="primaryOffcanvas" aria-labelledby="primaryOffcanvasLabel">
    <div class="offcanvas-header">
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php esc_attr_e('Fechar', 'site-treinamento-comercial'); ?>"></button>
    </div>

    <div class="offcanvas-body">
      <?php
        // menu em coluna (vertical)
        wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'menu_id'        => 'primary-menu',
          'menu_class'     => 'nav flex-column gap-1', // classes Bootstrap p/ lista vertical
          'fallback_cb'    => '__return_empty_string',
        ]);
      ?>
    </div>
  </div>
</header>


<main id="conteudo" class="site-main pt-5">
