<?php
/**
 * Archive (categorias) — “Regionais” vs “Canais”
 * - Cabeçalhos:
 *   - Regionais (se slug contiver "regional-") ou Canais
 *   - Regionais > {Nome da Categoria} (linha de apoio)
 *   - {Nome da Categoria} (H2 principal da listagem)
 * - Loop alternando grid 4/8 e 8/4 por linha (Bootstrap)
 */

get_header();

$term = get_queried_object();
$is_cat = is_category();
$slug   = ($is_cat && isset($term->slug)) ? (string) $term->slug : '';
$is_regional = $is_cat && (strpos($slug, 'regional-') !== false);

// rótulos base
$grupo_label = $is_regional ? __('Regionais', 'site-treinamento-comercial') : __('Canais', 'site-treinamento-comercial');
$cat_title   = single_cat_title('', false);
?>

<main id="conteudo" class="site-main py-5">
  <div class="container">

    <!-- Cabeçalhos -->
    <header class="mb-4">
      <h1 class="display-5 mb-1"><?php echo esc_html($grupo_label); ?></h1>

      <?php if ($is_cat): ?>
        <p class="text-muted mb-2">
          <?php echo esc_html($grupo_label . ' > ' . $cat_title); ?>
        </p>
      <?php endif; ?>

      <h2 class="h3 fw-semibold mb-0"><?php echo esc_html($cat_title); ?></h2>
    </header>

    <?php if (have_posts()): ?>

      <div class="archive-list">
        <?php
        $i = 0;
        while (have_posts()) :
          the_post();

          // Alternância: linha 0 => 4/8, linha 1 => 8/4, etc.
          $left_is_image = ($i % 2 === 0); // nas linhas pares, imagem à esquerda (4/8)
          $img_col = $left_is_image ? 4 : 8;
          $txt_col = $left_is_image ? 8 : 4;

          // Thumbnail (usa o tamanho que você criou; fallback para 'large')
          $thumb_size = image_get_intermediate_size(get_post_thumbnail_id(), 'capa-1000x860') ? 'capa-1000x860' : 'large';
          ?>

          <article <?php post_class('mb-4 pb-4 border-bottom'); ?>>

            <div class="row g-4 align-items-stretch">
              <?php if ($left_is_image): ?>
                <div class="col-12 col-sm-<?php echo (int)$img_col; ?>">
                  <a class="d-block h-100" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                    <?php if (has_post_thumbnail()): ?>
                      <?php the_post_thumbnail($thumb_size, ['class' => 'img-fluid w-100 h-auto rounded']); ?>
                    <?php else: ?>
                      <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="aspect-ratio: 1000/860;">
                        <span class="text-muted small"><?php esc_html_e('Sem imagem', 'site-treinamento-comercial'); ?></span>
                      </div>
                    <?php endif; ?>
                  </a>
                </div>
              <?php endif; ?>

              <div class="col-12 col-sm-<?php echo (int)$txt_col; ?>">
                <div class="h-100 d-flex flex-column">
                  <div class="mb-2">
                    <?php
                      // breadcrumb simples da categoria atual (opcional)
                      if ($is_cat) {
                        echo '<nav aria-label="'. esc_attr__('Trilha', 'site-treinamento-comercial') .'" class="small text-muted">';
                        echo esc_html($grupo_label) . ' › ' . esc_html($cat_title);
                        echo '</nav>';
                      }
                    ?>
                  </div>

                  <h3 class="h4 mb-2">
                    <a href="<?php the_permalink(); ?>" class="link-underline link-underline-opacity-0 link-dark">
                      <?php the_title(); ?>
                    </a>
                  </h3>

                  <div class="text-muted mb-2 small">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                      <?php echo esc_html(get_the_date()); ?>
                    </time>
                    <?php
                      $cats = get_the_category();
                      if (!empty($cats)) {
                        echo ' · ';
                        echo esc_html($cats[0]->name);
                      }
                    ?>
                  </div>

                  <div class="mb-3">
                    <?php the_excerpt(); ?>
                  </div>

                  <div class="mt-auto">
                    <a class="btn btn-outline-primary btn-sm" href="<?php the_permalink(); ?>">
                      <?php esc_html_e('Ler mais', 'site-treinamento-comercial'); ?>
                    </a>
                  </div>
                </div>
              </div>

              <?php if (!$left_is_image): ?>
                <div class="col-12 col-sm-<?php echo (int)$img_col; ?>">
                  <a class="d-block h-100" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                    <?php if (has_post_thumbnail()): ?>
                      <?php the_post_thumbnail($thumb_size, ['class' => 'img-fluid w-100 h-auto rounded']); ?>
                    <?php else: ?>
                      <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="aspect-ratio: 1000/860;">
                        <span class="text-muted small"><?php esc_html_e('Sem imagem', 'site-treinamento-comercial'); ?></span>
                      </div>
                    <?php endif; ?>
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </article>

          <?php
          $i++;
        endwhile;
        ?>
      </div>

      <!-- Paginação -->
      <nav class="mt-4" aria-label="<?php esc_attr_e('Paginação', 'site-treinamento-comercial'); ?>">
        <?php
          the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => __('« Anteriores', 'site-treinamento-comercial'),
            'next_text' => __('Próximos »', 'site-treinamento-comercial'),
          ]);
        ?>
      </nav>

    <?php else: ?>

      <div class="alert alert-info">
        <?php esc_html_e('Nenhum conteúdo encontrado.', 'site-treinamento-comercial'); ?>
      </div>

    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
