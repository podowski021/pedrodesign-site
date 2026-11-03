/* Meu Pet — interações compartilhadas */
(function () {
  var doc = document.documentElement;

  /* Header muda ao rolar */
  var header = document.querySelector('.header');
  function aoRolar() { if (header) header.classList.toggle('is-scrolled', window.scrollY > 24); }
  aoRolar();
  window.addEventListener('scroll', aoRolar, { passive: true });

  /* Menu mobile */
  var toggle = document.querySelector('.nav__toggle');
  var nav = document.getElementById('menu');
  function fecharMenu() {
    doc.classList.remove('menu-aberto');
    if (toggle) { toggle.setAttribute('aria-expanded', 'false'); toggle.setAttribute('aria-label', 'Abrir menu'); }
  }
  if (toggle) {
    toggle.addEventListener('click', function () {
      var aberto = doc.classList.toggle('menu-aberto');
      toggle.setAttribute('aria-expanded', String(aberto));
      toggle.setAttribute('aria-label', aberto ? 'Fechar menu' : 'Abrir menu');
    });
  }
  if (nav) nav.addEventListener('click', function (e) { if (e.target.closest('a')) fecharMenu(); });

  /* WhatsApp flutuante com escolha de unidade */
  var wpp = document.getElementById('wpp');
  if (wpp) {
    var botao = wpp.querySelector('.wpp__botao');
    var menu = wpp.querySelector('.wpp__menu');
    if (menu) {
      botao.addEventListener('click', function (e) {
        e.stopPropagation();
        var aberto = wpp.classList.toggle('is-aberto');
        botao.setAttribute('aria-expanded', String(aberto));
      });
      document.addEventListener('click', function (e) {
        if (!wpp.contains(e.target)) { wpp.classList.remove('is-aberto'); botao.setAttribute('aria-expanded', 'false'); }
      });
    }
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      fecharMenu();
      if (wpp) wpp.classList.remove('is-aberto');
    }
  });

  /* Animação de entrada ao rolar */
  var itens = document.querySelectorAll('.revela');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-visivel'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    itens.forEach(function (el) { io.observe(el); });
  } else {
    itens.forEach(function (el) { el.classList.add('is-visivel'); });
  }

  /* Rastreamento: envia ao dataLayer cada clique em WhatsApp, ligação, mapa etc.
     Ex.: { event: 'clique_whatsapp', unidade: 'resort', local: 'banner' } */
  window.dataLayer = window.dataLayer || [];
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-evento]');
    if (!el) return;
    window.dataLayer.push({
      event: 'clique_' + el.getAttribute('data-evento'),
      unidade: el.getAttribute('data-unidade') || '',
      local: el.getAttribute('data-local') || '',
      link_url: el.getAttribute('href') || ''
    });
  });

  /* Ano do rodapé */
  var ano = document.getElementById('ano');
  if (ano) ano.textContent = new Date().getFullYear();
})();

/* Números que contam ao aparecer */
(function () {
  var nums = document.querySelectorAll('[data-contar]');
  if (!nums.length) return;
  var reduz = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function anima(el) {
    var fim = parseInt(el.getAttribute('data-contar'), 10);
    var ini = parseInt(el.getAttribute('data-de') || '0', 10);
    var suf = el.getAttribute('data-sufixo') || '';
    if (reduz) { el.textContent = fim + suf; return; }
    var t0 = null, dur = 1400;
    function passo(t) {
      if (!t0) t0 = t;
      var p = Math.min(1, (t - t0) / dur);
      var e = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(ini + (fim - ini) * e) + suf;
      if (p < 1) requestAnimationFrame(passo);
    }
    requestAnimationFrame(passo);
  }
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (en) { if (en.isIntersecting) { anima(en.target); io.unobserve(en.target); } });
    }, { threshold: .5 });
    nums.forEach(function (n) { io.observe(n); });
  }
})();

/* Títulos ocupam o máximo da largura disponível: a linha mais longa enche a coluna */
(function () {
  var MAX = { H1: 132, H2: 118 };
  function fit() {
    document.querySelectorAll('.display').forEach(function (h) {
      var lines = h.querySelectorAll('.l1, .l2, .l3, .linha');
      if (!lines.length) return;
      var box = h.parentElement.clientWidth;
      if (!box) return;
      h.style.fontSize = '100px';
      var widest = 0;
      lines.forEach(function (l) {
        var d = l.style.display; l.style.display = 'inline-block'; l.style.whiteSpace = 'nowrap';
        widest = Math.max(widest, l.getBoundingClientRect().width);
        l.style.display = d;
      });
      if (!widest) { h.style.fontSize = ''; return; }
      var size = 100 * (box * 0.95) / (widest + 100 * 0.1);
      size = Math.max(26, Math.min(MAX[h.tagName] || 96, size));
      h.style.fontSize = size.toFixed(1) + 'px';
      lines.forEach(function (l) { l.style.whiteSpace = 'nowrap'; });
    });
  }
  var t; function later() { clearTimeout(t); t = setTimeout(fit, 80); }
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit); else window.addEventListener('load', fit);
  fit(); window.addEventListener('resize', later);
})();
