<?php
if (!function_exists('stc_indicadores_meta_value')) {
  /**
   * Retorna um metadado numérico dos indicadores ou zero quando vazio.
   */
  function stc_indicadores_meta_value($post_id, $key) {
    if (function_exists('stc_get_indicator_int_meta')) {
      return stc_get_indicator_int_meta($post_id, $key, 0);
    }

    $value = get_post_meta($post_id, $key, true);
    if ($value === '' || $value === null) {
      return 0;
    }

    $value = str_replace(["\xc2\xa0", ' '], '', (string) $value);
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);

    $number = is_numeric($value) ? (float) $value : 0;
    if (!is_finite($number)) {
      return 0;
    }

    return (int) floor(abs($number));
  }
}

if (!function_exists('stc_format_indicadores_value')) {
  /**
   * Formata os valores numéricos para exibição seguindo o padrão brasileiro.
   */
  function stc_format_indicadores_value($value) {
    if (function_exists('stc_normalize_positive_int')) {
      $normalized = stc_normalize_positive_int($value);
      $number = $normalized === '' ? 0 : $normalized;
    } else {
      $number = is_numeric($value) ? (int) $value : (int) preg_replace('/\D+/', '', (string) $value);
    }

    return number_format((int) $number, 0, ',', '.');
  }
}

if (!function_exists('ind_render_box')) {
  /**
   * Renderiza o box de indicadores respeitando o layout existente.
   */
  function ind_render_box($post_id) {
    if (!$post_id) {
      echo '<p id="indicadores-box">Nenhum indicador disponível.</p>';
      return;
    }

    $meta = [
      'turmas'  => stc_indicadores_meta_value($post_id, 'turmas'),
      'participantes' => stc_indicadores_meta_value($post_id, 'participantes'),
      'horas'   => stc_indicadores_meta_value($post_id, 'horas'),
      'tema-capacitacao-turmas'    => stc_indicadores_meta_value($post_id, 'tema-capacitacao-turmas'),
      'tema-capacitacao-treinados' => stc_indicadores_meta_value($post_id, 'tema-capacitacao-treinados'),
      'tema-formacao-turmas'       => stc_indicadores_meta_value($post_id, 'tema-formacao-turmas'),
      'tema-formacao-treinados'    => stc_indicadores_meta_value($post_id, 'tema-formacao-treinados'),
      'tema-reciclagem-turmas'     => stc_indicadores_meta_value($post_id, 'tema-reciclagem-turmas'),
      'tema-reciclagem-treinados'  => stc_indicadores_meta_value($post_id, 'tema-reciclagem-treinados'),
    ];
    ?>
    <div class="indicadores" id="indicadores-box" data-post="<?php echo esc_attr($post_id); ?>">
      <div class="d-flex justify-content-between">
        <p>Total de <strong>turmas</strong> realizadas no mês</p>
        <span id="ind-turmas"><?php echo esc_html(stc_format_indicadores_value($meta['turmas'])); ?></span>
      </div>
      <hr>
      <div class="d-flex justify-content-between">
        <p>Total de <strong>participantes</strong> treinados por mês</p>
        <span id="ind-participantes"><?php echo esc_html(stc_format_indicadores_value($meta['participantes'])); ?></span>
      </div>
      <hr>
      <div class="d-flex justify-content-between">
        <p><strong>Horas</strong> em treinamento</p>
        <span id="ind-horas"><?php echo esc_html(stc_format_indicadores_value($meta['horas'])); ?></span>
      </div>
      <hr>
      <p><strong>Temas</strong> abordados:</p>

      <div class="row">
        <div class="col-sm-8"><p>Capacitação</p></div>
        <div class="col-sm-4">
          <div class="d-flex justify-content-between">
            <div>
              <p>Turmas</p>
              <span id="tema-capacitacao-turmas"><?php echo esc_html(stc_format_indicadores_value($meta['tema-capacitacao-turmas'])); ?></span>
            </div>
            <div>
              <p>Treinados</p>
              <span id="tema-capacitacao-treinados"><?php echo esc_html(stc_format_indicadores_value($meta['tema-capacitacao-treinados'])); ?></span>
            </div>
          </div>
        </div>
      </div>
      <hr>

      <div class="row">
        <div class="col-sm-8"><p>Formação inicial</p></div>
        <div class="col-sm-4">
          <div class="d-flex justify-content-between">
            <div>
              <p>Turmas</p>
              <span id="tema-formacao-turmas"><?php echo esc_html(stc_format_indicadores_value($meta['tema-formacao-turmas'])); ?></span>
            </div>
            <div>
              <p>Treinados</p>
              <span id="tema-formacao-treinados"><?php echo esc_html(stc_format_indicadores_value($meta['tema-formacao-treinados'])); ?></span>
            </div>
          </div>
        </div>
      </div>
      <hr>

      <div class="row">
        <div class="col-sm-8"><p>Reciclagem</p></div>
        <div class="col-sm-4">
          <div class="d-flex justify-content-between">
            <div>
              <p>Turmas</p>
              <span id="tema-reciclagem-turmas"><?php echo esc_html(stc_format_indicadores_value($meta['tema-reciclagem-turmas'])); ?></span>
            </div>
            <div>
              <p>Treinados</p>
              <span id="tema-reciclagem-treinados"><?php echo esc_html(stc_format_indicadores_value($meta['tema-reciclagem-treinados'])); ?></span>
            </div>
          </div>
        </div>
      </div>
      <hr>
    </div>
    <?php
  }
}

get_header();
if (have_posts()) : while (have_posts()) : the_post();

  $anos = stc_get_indicadores_years();
  $term_ids = !empty($anos) ? wp_list_pluck($anos, 'term_id') : [];
  $most_recent_year_id = !empty($term_ids) ? (int) $term_ids[0] : 0;

  $requested_year_id = isset($_GET['ano']) ? (int) $_GET['ano'] : 0;
  $selected_year_id = ($requested_year_id && in_array($requested_year_id, $term_ids, true))
    ? $requested_year_id
    : $most_recent_year_id;

  $default_meses = $selected_year_id ? stc_get_indicadores_posts_for_year($selected_year_id) : [];

  $default_post_id = !empty($default_meses) ? $default_meses[0]->ID : 0;
  $requested_post_id = isset($_GET['indicador']) ? (int) $_GET['indicador'] : 0;

  if ($requested_post_id && !empty($default_meses)) {
    foreach ($default_meses as $mes_post) {
      if ((int) $mes_post->ID === $requested_post_id) {
        $default_post_id = $requested_post_id;
        break;
      }
    }
  }
  $indicadores_grouped = stc_get_indicadores_grouped_by_year($anos);
  $anos_data = array_map(function($term) {
    return [
      'id'   => (int) $term->term_id,
      'name' => $term->name,
      'slug' => $term->slug,
    ];
  }, $anos);

  wp_enqueue_script('indicadores-filtro');
  wp_localize_script('indicadores-filtro', 'stcIndicadoresData', [
    'anos'          => $anos_data,
    'indicadores'   => $indicadores_grouped,
    'selectedYear'  => $selected_year_id,
    'selectedPost'  => $default_post_id,
  ]);
?>

<article <?php post_class('container my-4'); ?>>
  <div class="container">
    <h1><?php the_title(); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/h1_indicadores.png' ); ?>"
       alt="<?php echo esc_attr__( 'H1 Embaixador', 'site-treinamento-comercial' ); ?>"></h1>
    <hr style="max-width: 430px;margin: -2rem 0 2rem;">
    <form method="get" class="indicadores-filtro mb-4">
      <div class="caixa_indicadores">
        <h4>Indicadores Treinamento</h4>
        <h5>Todas as regionais</h5>
          <select id="filtro-mes" name="indicador" class="form-select" onchange="this.form.submit();">
            <?php if (!empty($default_meses)) : ?>
              <?php foreach ($default_meses as $p) : ?>
                <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($p->ID, $default_post_id); ?>>
                  <?php echo esc_html(get_the_title($p)); ?>
                </option>
              <?php endforeach; ?>
            <?php else : ?>
              <option value="">—</option>
            <?php endif; ?>
          </select>
          <select id="filtro-ano" name="ano" class="form-select" onchange="this.form.submit();">
            <?php if (!empty($anos)) : ?>
              <?php foreach ($anos as $t) : ?>
                <option value="<?php echo esc_attr($t->term_id); ?>" <?php selected($t->term_id, $selected_year_id); ?>>
                  <?php echo esc_html($t->name); ?>
                </option>
              <?php endforeach; ?>
            <?php else : ?>
              <option value="">—</option>
            <?php endif; ?>
          </select>
      </div>
      <noscript>
        <button type="submit" class="btn btn-primary mt-3"><?php esc_html_e('Filtrar', 'stc'); ?></button>
      </noscript>
    </form>

    <div class="entry-content"><?php the_content(); ?></div>

    <?php ind_render_box($default_post_id); ?>

  </div>
<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/imgs/bottom_indicadores.png' ); ?>"
       alt="<?php echo esc_attr__( 'Indicadores', 'site-treinamento-comercial' ); ?>" style="max-width: 200px;">
</article>

<?php endwhile; endif; ?>
<?php get_footer(); ?>
