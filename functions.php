<?php
/** Bloqueia acesso direto */
if (!defined('ABSPATH')) { exit; }

define('STC_VERSION', '0.2.0');
define('STC_PATH', get_template_directory());
define('STC_URI', get_template_directory_uri());

/**
 * Configura o tema
 */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script','navigation-widgets']);
  register_nav_menus([
    'primary' => __('Menu Principal', 'site-treinamento-comercial'),
    'footer'  => __('Menu do Rodapé', 'site-treinamento-comercial'),
  ]);
  if (!isset($GLOBALS['content_width'])) { $GLOBALS['content_width'] = 1100; }
});

/**
 * Enqueue — Bootstrap 5.3 (CDN) + tema
 */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('bootstrap-53', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
  $css = STC_PATH . '/assets/css/main.css';
  wp_enqueue_style('stc-main', STC_URI . '/assets/css/main.css', ['bootstrap-53'], file_exists($css) ? filemtime($css) : STC_VERSION);

  wp_enqueue_script('bootstrap-53', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
  $js  = STC_PATH . '/assets/js/main.js';
  wp_enqueue_script('stc-main', STC_URI . '/assets/js/main.js', ['bootstrap-53'], file_exists($js) ? filemtime($js) : STC_VERSION, true);

  // CSS do Owl
  wp_enqueue_style(
    'owl-carousel',
    get_template_directory_uri() . '/assets/owl.carousel.min.css',
    [],
    '2.3.4'
  );
  wp_enqueue_style(
    'owl-theme-default',
    get_template_directory_uri() . '/assets/owl.theme.default.min.css',
    ['owl-carousel'],
    '2.3.4'
  );

  // JS do Owl (depende de jQuery)
  wp_enqueue_script(
    'owl-carousel',
    get_template_directory_uri() . '/assets/owl.carousel.min.js',
    ['jquery'],
    '2.3.4',
    true
  );
  

  // Cache-buster: atualiza a versão quando o arquivo muda
  $ver = file_exists(get_stylesheet_directory() . '/style.css')
    ? filemtime(get_stylesheet_directory() . '/style.css')
    : null;

  wp_enqueue_style(
    'theme-style',
    get_stylesheet_uri(), // /style.css do tema ativo
    [],
    $ver
  );
});


/** Defer apenas para o JS do tema */
add_filter('script_loader_tag', function($tag, $handle, $src){
  if ($handle === 'stc-main') {
    return sprintf('<script src="%s" defer></script>', esc_url($src));
  }
  return $tag;
}, 10, 3);

/** Limpeza básica */
add_action('init', function () {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
});

/** Desabilita XML-RPC */
add_filter('xmlrpc_enabled', '__return_false');

/** Restringe endpoints sensíveis da REST API a visitantes */
add_filter('rest_endpoints', function($endpoints){
  if (!is_user_logged_in()) {
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
  }
  return $endpoints;
});

/** Cabeçalhos de segurança básicos */
add_action('send_headers', function(){
  header('X-Frame-Options: SAMEORIGIN');
  header('X-Content-Type-Options: nosniff');
  header('Referrer-Policy: no-referrer-when-downgrade');
  header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
});

/** Helpers */
function stc_safe_text($text){ return wp_kses_post($text); }
function stc_safe_url($url){ return esc_url($url); }

function stc_normalize_positive_int($value) {
  if ($value === '' || $value === null || is_array($value) || is_object($value)) {
    return '';
  }

  if (is_int($value)) {
    return $value < 0 ? abs($value) : $value;
  }

  if (is_float($value)) {
    $value = $value < 0 ? abs($value) : $value;
    return (int) floor($value);
  }

  $value = str_replace(["\xc2\xa0", ' '], '', (string) $value);
  $value = str_replace('.', '', $value);
  $value = str_replace(',', '.', $value);

  if (!is_numeric($value)) {
    return '';
  }

  $number = (float) $value;
  if (!is_finite($number)) {
    return '';
  }

  $number = $number < 0 ? abs($number) : $number;

  return (int) floor($number);
}

function stc_sanitize_positive_int_meta($value) {
  $normalized = stc_normalize_positive_int($value);
  return $normalized === '' ? '' : $normalized;
}

function stc_get_indicator_int_meta($post_id, $key, $default = 0) {
  $raw = get_post_meta($post_id, $key, true);
  $normalized = stc_normalize_positive_int($raw);

  if ($normalized === '') {
    return $default;
  }

  return (int) $normalized;
}

/** Classes do menu WP para Bootstrap 5 */
add_filter('nav_menu_css_class', function($classes, $item, $args){
  if (!empty($args->theme_location) && in_array($args->theme_location, ['primary','footer'], true)) {
    $classes[] = 'nav-item';
  }
  return $classes;
}, 10, 3);

add_filter('nav_menu_link_attributes', function($atts, $item, $args){
  if (!empty($args->theme_location) && in_array($args->theme_location, ['primary','footer'], true)) {
    $existing = isset($atts['class']) ? $atts['class'].' ' : '';
    $atts['class'] = trim($existing . 'nav-link');
  }
  return $atts;
}, 10, 3);

/** =====================
 * CPT 'indicadores' + Taxonomia 'ano'
 * ===================== */
add_action('init', function () {
  // Taxonomia 'ano'
  register_taxonomy('ano', 'indicadores', [
    'labels' => [
      'name'          => __('Anos','stc'),
      'singular_name' => __('Ano','stc'),
    ],
    'public'            => true,
    'hierarchical'      => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'rewrite'           => ['slug' => 'ano'],
    'show_in_rest'      => true,
  ]);

  // CPT 'indicadores'
  register_post_type('indicadores', [
    'labels' => [
      'name'          => __('Indicadores','stc'),
      'singular_name' => __('Indicador','stc'),
      'add_new_item'  => __('Adicionar Indicador','stc'),
      'edit_item'     => __('Editar Indicador','stc'),
    ],
    'public'        => true,
    'show_ui'       => true,
    'menu_position' => 20,
    'menu_icon'     => 'dashicons-chart-bar',
    'has_archive'   => false,
    'rewrite'       => ['slug' => 'indicadores'],
    'supports'      => ['title','editor','thumbnail'],
    'show_in_rest'  => true,
    'taxonomies'    => ['ano'],
  ]);
});

/** =====================
 * Metas do CPT 'indicadores'
 * - mes_num (1-12)
 * - turmas, participantes, horas
 * - tema-capacitacao-turmas, tema-capacitacao-treinados
 * - tema-formacao-turmas, tema-formacao-treinados
 * - tema-reciclagem-turmas, tema-reciclagem-treinados
 * ===================== */
add_action('init', function () {
  $args_int = [
    'type'              => 'integer',
    'single'            => true,
    'show_in_rest'      => true,
    'sanitize_callback' => 'stc_sanitize_positive_int_meta',
    'auth_callback'     => function() { return current_user_can('edit_posts'); },
  ];

  register_post_meta('indicadores', 'mes_num', $args_int);
  register_post_meta('indicadores', 'turmas',  $args_int);
  register_post_meta('indicadores', 'participantes', $args_int);
  register_post_meta('indicadores', 'horas', $args_int);

  register_post_meta('indicadores', 'tema-capacitacao-turmas',   $args_int);
  register_post_meta('indicadores', 'tema-capacitacao-treinados',$args_int);
  register_post_meta('indicadores', 'tema-formacao-turmas',      $args_int);
  register_post_meta('indicadores', 'tema-formacao-treinados',   $args_int);
  register_post_meta('indicadores', 'tema-reciclagem-turmas',    $args_int);
  register_post_meta('indicadores', 'tema-reciclagem-treinados', $args_int);
});

/** Metabox para metas */
add_action('add_meta_boxes', function () {
  add_meta_box(
    'stc_indicadores_meta',
    __('Indicadores do Mês', 'stc'),
    function($post){
      wp_nonce_field('stc_indicadores_meta','stc_indicadores_meta_nonce');

      $mes_num  = get_post_meta($post->ID,'mes_num', true);
      $turmas   = get_post_meta($post->ID,'turmas', true);
      $particip = get_post_meta($post->ID,'participantes', true);
      $horas    = get_post_meta($post->ID,'horas', true);

      $cap_tur  = get_post_meta($post->ID,'tema-capacitacao-turmas', true);
      $cap_tre  = get_post_meta($post->ID,'tema-capacitacao-treinados', true);
      $for_tur  = get_post_meta($post->ID,'tema-formacao-turmas', true);
      $for_tre  = get_post_meta($post->ID,'tema-formacao-treinados', true);
      $rec_tur  = get_post_meta($post->ID,'tema-reciclagem-turmas', true);
      $rec_tre  = get_post_meta($post->ID,'tema-reciclagem-treinados', true);
      ?>
      <style>
        .stc-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
        .stc-grid label{display:block;font-weight:600;margin-bottom:4px;}
        .stc-field{background:#fff;border:1px solid #ccd0d4;padding:8px;border-radius:4px;width:100%;}
        @media (max-width: 782px){ .stc-grid{grid-template-columns:1fr;} }
      </style>

      <div class="stc-grid">
        <div>
          <label for="stc_mes_num"><?php _e('Mês (1–12)','stc'); ?></label>
          <input id="stc_mes_num" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_mes_num" value="<?php echo esc_attr($mes_num); ?>">
        </div>
        <div>
          <label for="stc_turmas"><?php _e('Turmas','stc'); ?></label>
          <input id="stc_turmas" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_turmas" value="<?php echo esc_attr($turmas); ?>">
        </div>
        <div>
          <label for="stc_participantes"><?php _e('Participantes','stc'); ?></label>
          <input id="stc_participantes" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_participantes" value="<?php echo esc_attr($particip); ?>">
        </div>
        <div>
          <label for="stc_horas"><?php _e('Horas','stc'); ?></label>
          <input id="stc_horas" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_horas" value="<?php echo esc_attr($horas); ?>">
        </div>

        <div>
          <label for="stc_cap_tur"><?php _e('Tema: Capacitação — Turmas','stc'); ?></label>
          <input id="stc_cap_tur" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_cap_tur" value="<?php echo esc_attr($cap_tur); ?>">
        </div>
        <div>
          <label for="stc_cap_tre"><?php _e('Tema: Capacitação — Treinados','stc'); ?></label>
          <input id="stc_cap_tre" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_cap_tre" value="<?php echo esc_attr($cap_tre); ?>">
        </div>
        <div>
          <label for="stc_for_tur"><?php _e('Tema: Formação — Turmas','stc'); ?></label>
          <input id="stc_for_tur" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_for_tur" value="<?php echo esc_attr($for_tur); ?>">
        </div>
        <div>
          <label for="stc_for_tre"><?php _e('Tema: Formação — Treinados','stc'); ?></label>
          <input id="stc_for_tre" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_for_tre" value="<?php echo esc_attr($for_tre); ?>">
        </div>
        <div>
          <label for="stc_rec_tur"><?php _e('Tema: Reciclagem — Turmas','stc'); ?></label>
          <input id="stc_rec_tur" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_rec_tur" value="<?php echo esc_attr($rec_tur); ?>">
        </div>
        <div>
          <label for="stc_rec_tre"><?php _e('Tema: Reciclagem — Treinados','stc'); ?></label>
          <input id="stc_rec_tre" class="stc-field" type="text" inputmode="numeric" pattern="[0-9]*" data-mask="int" name="stc_rec_tre" value="<?php echo esc_attr($rec_tre); ?>">
        </div>
      </div>
      <p class="description"><?php _e('Preencha os valores deste mês; o título do post continua sendo o nome do mês.','stc'); ?></p>
      <?php
    },
    'indicadores',
    'normal',
    'default'
  );
});

add_action('admin_enqueue_scripts', function($hook){
  if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
    return;
  }

  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || $screen->post_type !== 'indicadores') {
    return;
  }

  $handle = 'stc-indicadores-admin';
  $path   = STC_PATH . '/assets/js/indicadores-admin.js';
  $ver    = file_exists($path) ? filemtime($path) : STC_VERSION;

  wp_enqueue_script($handle, STC_URI . '/assets/js/indicadores-admin.js', [], $ver, true);
});

/** Salvar metadados */
add_action('save_post_indicadores', function($post_id){
  if (!isset($_POST['stc_indicadores_meta_nonce']) || !wp_verify_nonce($_POST['stc_indicadores_meta_nonce'], 'stc_indicadores_meta')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $fields = [
    'mes_num'      => isset($_POST['stc_mes_num']) ? stc_normalize_positive_int($_POST['stc_mes_num']) : '',
    'turmas'       => isset($_POST['stc_turmas']) ? stc_normalize_positive_int($_POST['stc_turmas']) : '',
    'participantes'=> isset($_POST['stc_participantes']) ? stc_normalize_positive_int($_POST['stc_participantes']) : '',
    'horas'        => isset($_POST['stc_horas']) ? stc_normalize_positive_int($_POST['stc_horas']) : '',
    'tema-capacitacao-turmas'       => isset($_POST['stc_cap_tur']) ? stc_normalize_positive_int($_POST['stc_cap_tur']) : '',
    'tema-capacitacao-treinados'    => isset($_POST['stc_cap_tre']) ? stc_normalize_positive_int($_POST['stc_cap_tre']) : '',
    'tema-formacao-turmas'          => isset($_POST['stc_for_tur']) ? stc_normalize_positive_int($_POST['stc_for_tur']) : '',
    'tema-formacao-treinados'       => isset($_POST['stc_for_tre']) ? stc_normalize_positive_int($_POST['stc_for_tre']) : '',
    'tema-reciclagem-turmas'        => isset($_POST['stc_rec_tur']) ? stc_normalize_positive_int($_POST['stc_rec_tur']) : '',
    'tema-reciclagem-treinados'     => isset($_POST['stc_rec_tre']) ? stc_normalize_positive_int($_POST['stc_rec_tre']) : '',
  ];
  foreach($fields as $k=>$v){
    if ($v === '' || $v === null) {
      delete_post_meta($post_id, $k);
    } else {
      update_post_meta($post_id, $k, $v);
    }
  }
});

/**
 * Recupera os termos de ano dos indicadores, ordenando do mais recente para o mais antigo.
 */
function stc_get_indicadores_years() {
  $anos = get_terms([
    'taxonomy'   => 'ano',
    'hide_empty' => true,
  ]);

  if (is_wp_error($anos) || empty($anos)) {
    return [];
  }

  usort($anos, function($a, $b) {
    $a_val = (int) $a->name;
    $b_val = (int) $b->name;
    if ($a_val === $b_val) {
      return strcasecmp($b->name, $a->name);
    }
    return $b_val <=> $a_val;
  });

  return $anos;
}

/**
 * Busca todos os indicadores de um ano específico, ordenados do mês mais recente para o mais antigo.
 */
function stc_get_indicadores_posts_for_year($term_id) {
  $term_id = (int) $term_id;
  if ($term_id <= 0) {
    return [];
  }

  return get_posts([
    'post_type'      => 'indicadores',
    'numberposts'    => -1,
    'tax_query'      => [[
      'taxonomy' => 'ano',
      'field'    => 'term_id',
      'terms'    => [$term_id],
    ]],
    'meta_key'       => 'mes_num',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
    'post_status'    => 'publish',
    'no_found_rows'  => true,
  ]);
}

/**
 * Normaliza os dados dos indicadores para exibição (usado tanto no template quanto no AJAX).
 */
function stc_prepare_indicadores_payload($posts) {
  $out = [];

  foreach ($posts as $p) {
    $id  = $p instanceof WP_Post ? $p->ID : (int) $p;

    $out[] = [
      'id'      => $id,
      'title'   => get_the_title($id),
      'mes_num' => stc_get_indicator_int_meta($id, 'mes_num'),
      'meta'    => [
        'turmas'  => stc_get_indicator_int_meta($id, 'turmas'),
        'participantes' => stc_get_indicator_int_meta($id, 'participantes'),
        'horas'   => stc_get_indicator_int_meta($id, 'horas'),
        'tema-capacitacao-turmas'    => stc_get_indicator_int_meta($id, 'tema-capacitacao-turmas'),
        'tema-capacitacao-treinados' => stc_get_indicator_int_meta($id, 'tema-capacitacao-treinados'),
        'tema-formacao-turmas'       => stc_get_indicator_int_meta($id, 'tema-formacao-turmas'),
        'tema-formacao-treinados'    => stc_get_indicator_int_meta($id, 'tema-formacao-treinados'),
        'tema-reciclagem-turmas'     => stc_get_indicator_int_meta($id, 'tema-reciclagem-turmas'),
        'tema-reciclagem-treinados'  => stc_get_indicator_int_meta($id, 'tema-reciclagem-treinados'),
      ],
    ];
  }

  return $out;
}

/**
 * Agrupa os indicadores por ano, retornando todos os posts disponíveis.
 */
function stc_get_indicadores_grouped_by_year($years = null) {
  if ($years === null) {
    $years = stc_get_indicadores_years();
  }

  $grouped = [];

  foreach ($years as $year) {
    $grouped[$year->term_id] = stc_prepare_indicadores_payload(
      stc_get_indicadores_posts_for_year($year->term_id)
    );
  }

  return $grouped;
}

add_action('wp_ajax_stc_indicadores_por_ano', 'stc_indicadores_por_ano');
add_action('wp_ajax_nopriv_stc_indicadores_por_ano', 'stc_indicadores_por_ano');

function stc_indicadores_por_ano() {
  // Segurança opcional: checar referer/nonce aqui se quiser
  $ano = isset($_GET['ano']) ? sanitize_text_field($_GET['ano']) : '';
  if (!$ano) {
    wp_send_json_error('Parâmetro "ano" obrigatório', 400);
  }

  // Aceita ID ou slug
  if (ctype_digit($ano)) {
    $term = get_term((int)$ano, 'ano');
  } else {
    $term = get_term_by('slug', $ano, 'ano');
  }

  if (!$term || is_wp_error($term)) {
    wp_send_json_error('Ano inválido', 404);
  }

  $posts = stc_get_indicadores_posts_for_year($term->term_id);
  $out   = stc_prepare_indicadores_payload($posts);

  wp_send_json_success($out);
}


// 1) Habilita thumbnails e define o tamanho padrão do "post thumbnail"
add_action('after_setup_theme', function () {
  add_theme_support('post-thumbnails');

  // Tamanho padrão do the_post_thumbnail()
  // set_post_thumbnail_size(
  //   1000,
  //   860,
  //   ['center', 'center'] // hard crop central
  // );

  // Tamanho 300x200 com hard crop central
  add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');

    // tamanho nomeado
    add_image_size(
      'thumb-300x200',
      300,
      200,
      ['center', 'center'] // hard crop central
    );
  });

  // (Opcional) tamanho nomeado, caso queira usar explicitamente
  add_image_size(
    'capa-1000x860',
    1000,
    860,
    ['center', 'center'] // hard crop central
  );
});


add_filter('body_class', function ($classes) {
  if (is_page()) {
    $id   = get_queried_object_id();
    $slug = get_post_field('post_name', $id);

    if ($slug) {
      // remove classes com ID numérico de página
      foreach ($classes as $i => $class) {
        if (preg_match('/^page-id-\d+$/', $class)) {
          unset($classes[$i]);
        }
      }
      // adiciona "page-{slug}"
      $classes[] = 'page-' . sanitize_title($slug);
    }
  }

  return array_values(array_unique($classes));
});
