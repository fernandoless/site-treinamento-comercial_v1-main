<?php
/**
 * Template de formulário de busca
 * Salve como: /wp-content/themes/seu-tema/searchform.php
 */

$unique_id = wp_unique_id('search-form-');
?>

<form role="search" method="get" class="search-form d-flex gap-2" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="visually-hidden" for="<?php echo esc_attr($unique_id); ?>">
    <?php esc_html_e('Pesquisar por:', 'site-treinamento-comercial'); ?>
  </label>

  <input
    type="search"
    id="<?php echo esc_attr($unique_id); ?>"
    class="search-field form-control"
    placeholder="<?php echo esc_attr_x('Pesquisar...', 'placeholder', 'site-treinamento-comercial'); ?>"
    value="<?php echo esc_attr(get_search_query()); ?>"
    name="s"
    />

  <button type="submit" class="search-submit btn btn-outline-secondary" aria-label="<?php esc_attr_e('Pesquisar', 'site-treinamento-comercial'); ?>">
    <img
      src="<?php echo esc_url(get_stylesheet_directory_uri() . '/imgs/lupa.png'); ?>"
      alt=""
      width="20"
      height="20"
      loading="lazy"
      />
    <span class="visually-hidden"><?php esc_html_e('Pesquisar', 'site-treinamento-comercial'); ?></span>
  </button>
</form>
