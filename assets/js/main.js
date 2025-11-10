/**
 * JS básico do tema
 * - Melhorias leves de acessibilidade
 */
(function(){
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') document.documentElement.classList.add('user-is-tabbing');
  }, { once: true });
})();

// const anoSlug = $ano.options[$ano.selectedIndex]?.dataset.slug || '';

