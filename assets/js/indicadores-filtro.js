(function(){
  if (!window.IndicadoresAPI || !window.fetch) return;

  const apiRoot = IndicadoresAPI.root; // ex: https://site.com/wp-json/indicadores/v1/

  const selAno = document.getElementById('filtro-ano');
  const selMes = document.getElementById('filtro-mes');
  const box   = document.getElementById('indicadores-box');

  const hasIntl = typeof Intl !== 'undefined' && typeof Intl.NumberFormat === 'function';
  const nfInt = hasIntl ? new Intl.NumberFormat('pt-BR') : null;

  function toNumber(value){
    if (typeof value === 'string') {
      value = value.replace(/\s+/g, '');
      value = value.replace(/\./g, '');
      value = value.replace(/,/g, '.');
    }

    const num = Number(value);
    if (typeof Number.isFinite === 'function') {
      return Number.isFinite(num) ? num : 0;
    }
    return isFinite(num) ? num : 0;
  }

  function formatInt(value){
    const parsed = toNumber(value);
    const num = Math.trunc(parsed);
    return nfInt ? nfInt.format(num) : String(num);
  }

  async function fetchJSON(url){
    const r = await fetch(url, { credentials: 'same-origin' });
    if (!r.ok) throw new Error('Erro na requisição');
    return r.json();
  }

  function fillBox(data){
    // Atualiza todos os spans
    box.dataset.post = ''; // opcional
    const tema = data.tema || {};
    const capacitacao = tema.capacitacao || {};
    const formacao = tema.formacao || {};
    const reciclagem = tema.reciclagem || {};

    const entries = [
      ['ind-turmas', data.turmas],
      ['ind-participantes', data.participantes],
      ['ind-horas', data.horas],
      ['tema-capacitacao-turmas', capacitacao.turmas],
      ['tema-capacitacao-treinados', capacitacao.treinados],
      ['tema-formacao-turmas', formacao.turmas],
      ['tema-formacao-treinados', formacao.treinados],
      ['tema-reciclagem-turmas', reciclagem.turmas],
      ['tema-reciclagem-treinados', reciclagem.treinados]
    ];

    entries.forEach(([id, value]) => {
      const el = document.getElementById(id);
      if (el) {
        el.textContent = formatInt(value);
      }
    });
  }

  async function carregarMeses(anoId){
    const url = apiRoot + 'meses?ano=' + encodeURIComponent(anoId);
    const meses = await fetchJSON(url);

    // Limpa e preenche o select de meses (ordenados por mes_num DESC já vêm da API)
    selMes.innerHTML = '';
    if (!meses.length){
      const op = document.createElement('option');
      op.value = '';
      op.textContent = '—';
      selMes.appendChild(op);
      return null;
    }
    meses.forEach((m,idx)=>{
      const op = document.createElement('option');
      op.value = m.id;
      op.textContent = m.title;
      selMes.appendChild(op);
    });
    // Seleciona o primeiro (maior mes_num)
    selMes.selectedIndex = 0;
    return meses[0].id;
  }

  async function carregarIndicador(postId){
    if (!postId) return;
    const url = apiRoot + 'indicador/' + encodeURIComponent(postId);
    const data = await fetchJSON(url);
    fillBox(data);
  }

  // Eventos
  selAno && selAno.addEventListener('change', async (e)=>{
    try{
      const anoId = e.target.value;
      const postId = await carregarMeses(anoId);
      await carregarIndicador(postId);
    }catch(err){ console.error(err); }
  });

  selMes && selMes.addEventListener('change', async (e)=>{
    try{
      await carregarIndicador(e.target.value);
    }catch(err){ console.error(err); }
  });

})();
