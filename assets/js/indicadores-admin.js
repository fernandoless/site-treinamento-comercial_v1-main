(function(){
  if (typeof window === 'undefined') return;

  function createNumberFormatter(locale) {
    if (typeof Intl !== 'undefined' && typeof Intl.NumberFormat === 'function') {
      return new Intl.NumberFormat(locale || 'pt-BR');
    }
    return null;
  }

  var formatter = createNumberFormatter('pt-BR');

  function formatThousands(value) {
    if (!value) return '';
    var cleaned = String(value).replace(/\D+/g, '');
    if (!cleaned) return '';
    var num = parseInt(cleaned, 10);
    if (!isFinite(num)) return '';
    if (formatter) {
      return formatter.format(num);
    }
    return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function applyIntMask(input) {
    var raw = String(input.value || '').replace(/\D+/g, '');
    input.dataset.rawValue = raw;
    input.value = raw ? formatThousands(raw) : '';
  }

  document.addEventListener('DOMContentLoaded', function(){
    var inputs = Array.prototype.slice.call(document.querySelectorAll('.stc-field[data-mask="int"]'));
    if (!inputs.length) return;

    inputs.forEach(applyIntMask);

    document.addEventListener('input', function(event){
      var target = event.target;
      if (!target || !target.dataset) return;
      if (target.dataset.mask === 'int') {
        applyIntMask(target);
      }
    }, true);

    var form = document.getElementById('post');
    if (form) {
      form.addEventListener('submit', function(){
        inputs.forEach(function(input){
          var raw = input.dataset.rawValue || '';
          input.value = raw;
        });
      });
    }
  });
})();
