/* Contador de visitas do Pedro Design — anônimo, sem cookies de terceiros.
   Para não contar as suas próprias visitas neste aparelho: abra qualquer página com ?naocontar=1 (ou o painel). */
(function () {
  try {
    var q = new URLSearchParams(location.search);
    if (q.get('naocontar') === '1') localStorage.setItem('pd-nao-contar', '1');
    if (q.get('naocontar') === '0') localStorage.removeItem('pd-nao-contar');
    if (localStorage.getItem('pd-nao-contar') === '1') return;
  } catch (e) {}
  if (navigator.webdriver) return;
  var END = /(^|\.)pedrodesign\.store$/.test(location.hostname) ? 'https://pedrodesign.store/painel/api.php?a=t' : '/painel/api.php?a=t';
  var PAGE = document.documentElement.getAttribute('data-pagina') || 'home';
  function rid() { var a = new Uint8Array(9); (window.crypto || window.msCrypto).getRandomValues(a); return Array.prototype.map.call(a, function (b) { return ('0' + b.toString(16)).slice(-2); }).join(''); }
  var vid, sid, novo = false;
  /* o mesmo id vale para pedrodesign.store e bio.pedrodesign.store (cookie do domínio), para a pessoa contar uma vez só */
  var DOM = /(^|\.)pedrodesign\.store$/.test(location.hostname) ? '; domain=.pedrodesign.store' : '';
  function ck() { var m = document.cookie.match(/(?:^|; )pd_v=([a-f0-9]{8,32})/); return m ? m[1] : ''; }
  try { vid = ck() || localStorage.getItem('pd-v') || ''; } catch (e) { vid = ck(); }
  if (!vid) { vid = rid(); novo = true; }
  try { localStorage.setItem('pd-v', vid); } catch (e) {}
  try { document.cookie = 'pd_v=' + vid + DOM + '; path=/; max-age=34560000; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : ''); } catch (e) {}
  try { sid = sessionStorage.getItem('pd-s'); if (!sid) { sid = rid(); sessionStorage.setItem('pd-s', sid); } } catch (e) { sid = rid(); }
  var pv = rid(), queue = [];
  function send(force) {
    if (!queue.length) return;
    var body = JSON.stringify({ v: vid, s: sid, ev: queue.splice(0, 20) });
    var ok = false;
    try { ok = navigator.sendBeacon && navigator.sendBeacon(END, new Blob([body], { type: 'text/plain' })); } catch (e) {}
    if (!ok) { try { fetch(END, { method: 'POST', body: body, keepalive: true, mode: 'no-cors', headers: { 'Content-Type': 'text/plain' } }); } catch (e) {} }
  }
  function ev(o) { o.pv = pv; o.p = PAGE; queue.push(o); }
  /* visita */
  var ref = document.referrer || '';
  try { if (ref && new URL(ref).hostname === location.hostname) ref = ''; } catch (e) {}
  var tz = ''; try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (e) {}
  ev({ e: 'v', r: ref, us: q.get('utm_source') || q.get('origem') || '', um: q.get('utm_medium') || '', uc: q.get('utm_campaign') || '',
       lg: (navigator.language || ''), tz: tz, w: screen.width || 0, n: novo ? 1 : 0 });
  send();
  /* tempo ativo (só conta com a aba visível) e rolagem máxima */
  var act = 0, last = document.visibilityState === 'visible' ? Date.now() : 0, sc = 0, sentDu = -1;
  function tick() { if (last) { act += Date.now() - last; last = Date.now(); } else if (document.visibilityState !== 'hidden') last = Date.now(); }
  ['pointerdown', 'keydown', 'touchstart'].forEach(function (n) { addEventListener(n, function () { if (!last && document.visibilityState !== 'hidden') last = Date.now(); }, { passive: true }); });
  function scroll() { var d = document.documentElement, m = d.scrollHeight - innerHeight; var p = m > 0 ? Math.round(100 * scrollY / m) : 100; if (p > sc) sc = Math.min(100, p); }
  addEventListener('scroll', scroll, { passive: true }); scroll();
  function leave() { tick(); var du = Math.round(act / 1000); if (du === sentDu && !queue.length) return; sentDu = du; ev({ e: 'l', du: du, sc: sc }); send(); }
  document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'hidden') { leave(); last = 0; } else { last = Date.now(); } });
  addEventListener('pagehide', leave);
  setInterval(function () { if (document.visibilityState === 'visible') leave(); }, 30000);
  /* cliques */
  function label(el) {
    var mv = el.closest('.mvx'); if (mv) { var pjm = mv.closest('.pj'), sm = pjm && pjm.querySelector('strong'); return 'Movex' + (sm ? ' · ' + sm.textContent.trim() : ''); }
    var a = el.closest('a,button'); if (!a) return '';
    var href = a.getAttribute('href') || '';
    var where = a.closest('#fwa') ? 'botão flutuante' : a.closest('#hd,header') ? 'menu' : a.closest('.hero') ? 'topo' : a.closest('.card,#planos') ? 'orçamento' : a.closest('nav') && PAGE !== 'home' ? 'links' : a.closest('footer') ? 'rodapé' : PAGE;
    if (/wa\.me|whatsapp/i.test(href)) return 'WhatsApp · ' + where;
    var pj = a.closest('.pj'); if (pj) { var s = pj.querySelector('strong'); return 'Projeto: ' + (s ? s.textContent.trim() : '?'); }
    if (/^mailto:/.test(href)) return 'E-mail · ' + where;
    if (/instagram\.com/.test(href)) return 'Instagram · ' + where;
    if (a.closest('.au')) { var w = a.closest('.pp'); var n = w && w.querySelector('strong'); return (a.classList.contains('sp') ? 'Velocidade do áudio' : 'Ouviu áudio') + (n ? ' · ' + n.textContent.trim() : ''); }
    if (/#projetos/.test(href)) return 'Ver projetos · ' + where;
    if (/#feedbacks/.test(href)) return 'Ver feedbacks · ' + where;
    if (/#planos/.test(href)) return 'Ver orçamento · ' + where;
    if (PAGE !== 'home' && /pedrodesign\.store\/?$|^\/$/.test(href)) return 'Abriu o site · ' + where;
    return '';
  }
  document.addEventListener('click', function (e) {
    var t = e.target; if (!t || !t.closest) return;
    var k = label(t); if (!k) return;
    ev({ e: 'c', k: k }); send();
  }, true);
})();
