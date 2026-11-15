/* Movex · utilidades comuns: barra de cookies discreta e revelação suave ao rolar */
(function(){
  var k = 'mx_ck';
  try { if (localStorage.getItem(k)) return; } catch (e) {}
  var b = document.createElement('div'); b.className = 'ck'; b.setAttribute('role', 'region'); b.setAttribute('aria-label', 'Aviso de cookies');
  b.innerHTML = '<span>Usamos cookies para entender as visitas e melhorar o site.</span><button type="button">Entendi</button>';
  document.body.appendChild(b);
  setTimeout(function(){ b.classList.add('in'); }, 1200);
  b.querySelector('button').addEventListener('click', function(){ try { localStorage.setItem(k, '1'); } catch (e) {} b.classList.remove('in'); setTimeout(function(){ b.remove(); }, 400); });
})();
(function(){
  var els = document.querySelectorAll('.rv'); if (!els.length) return;
  if (!('IntersectionObserver' in window)) { els.forEach(function(el){ el.classList.add('in'); }); return; }
  var io = new IntersectionObserver(function(es){ es.forEach(function(e){ if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .15, rootMargin: '0px 0px -40px 0px' });
  els.forEach(function(el){ io.observe(el); });
})();
