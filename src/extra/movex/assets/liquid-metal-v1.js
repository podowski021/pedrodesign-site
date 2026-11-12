/*
 * Liquid Metal — aro cromado com reflexos que escorrem, desenhado em WebGL.
 * Uso: <div data-liquid-metal data-ring="1.6"> (o elemento precisa de border-radius e position).
 * Sem WebGL, fica um aro prateado em CSS.
 */
(function () {
  "use strict";
  var CSS = '' +
    '.lm-ring{position:absolute;inset:0;border-radius:inherit;pointer-events:none;z-index:2;padding:var(--lm-w,1.6px);' +
    '-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;' +
    'background:linear-gradient(180deg,#f4f7fb,#8d96a6 45%,#e9eef5 55%,#6c7686)}' +
    '.lm-ring canvas{position:absolute;inset:0;width:100%;height:100%;display:block}' +
    '.lm-ring[data-gl="on"]{background:none}';

  var VERT = "attribute vec2 a;void main(){gl_Position=vec4(a,0.,1.);}";
  var FRAG = [
    "precision mediump float;",
    "uniform vec2 r;uniform float t;",
    "float h(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453);}",
    "float n(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);",
    " return mix(mix(h(i),h(i+vec2(1,0)),f.x),mix(h(i+vec2(0,1)),h(i+vec2(1,1)),f.x),f.y);}",
    "float fbm(vec2 p){float v=0.,a=.5;for(int i=0;i<4;i++){v+=a*n(p);p=p*2.03+vec2(1.7,9.2);a*=.5;}return v;}",
    "void main(){",
    " vec2 uv=gl_FragCoord.xy/r.y;",
    " float T=t*.22;",
    " float w=fbm(uv*vec2(.9,2.2)+vec2(T,-T*.4));",
    " float w2=fbm(uv*1.6-vec2(T*.7,0.)+w*1.8);",
    " float ph=uv.x*1.1+w2*3.2+T*1.6;",
    " float b=.5+.5*sin(ph*3.14159);",
    " float b2=.5+.5*sin(ph*6.28318+1.3);",
    " float v=mix(b,b2,.35);",
    " float hi=smoothstep(.68,.95,v);",
    " float lo=smoothstep(.0,.55,v);",
    " float y=gl_FragCoord.y/r.y;",
    " vec3 base=mix(vec3(.14,.16,.2),vec3(.82,.86,.92),lo);",
    " base*=.85+.3*y;",
    " vec3 col=mix(base,vec3(1.),hi);",
    " vec3 iri=.5+.5*cos(6.28318*(v*1.4+vec3(0.,.33,.67)+T*.3));",
    " col+=iri*.18*smoothstep(.55,.85,v)*(1.-hi);",
    " gl_FragColor=vec4(col,1.);",
    "}"].join("\n");

  function mount(host) {
    var ring = document.createElement("span");
    ring.className = "lm-ring"; ring.setAttribute("aria-hidden", "true");
    var w = host.getAttribute("data-ring"); if (w) ring.style.setProperty("--lm-w", w + "px");
    host.appendChild(ring);
    var reduce = !!(window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches);
    var cv = document.createElement("canvas"), gl = null;
    try { gl = cv.getContext("webgl", { alpha: false, antialias: false, premultipliedAlpha: false }); } catch (e) {}
    if (!gl) return;
    function sh(tp, src) { var s = gl.createShader(tp); gl.shaderSource(s, src); gl.compileShader(s); return gl.getShaderParameter(s, gl.COMPILE_STATUS) ? s : null; }
    var vs = sh(gl.VERTEX_SHADER, VERT), fs = sh(gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) return;
    var pr = gl.createProgram(); gl.attachShader(pr, vs); gl.attachShader(pr, fs); gl.linkProgram(pr);
    if (!gl.getProgramParameter(pr, gl.LINK_STATUS)) return;
    gl.useProgram(pr);
    var bf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, bf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    var loc = gl.getAttribLocation(pr, "a"); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
    var uR = gl.getUniformLocation(pr, "r"), uT = gl.getUniformLocation(pr, "t");
    ring.appendChild(cv); ring.setAttribute("data-gl", "on");

    var dpr = Math.min(window.devicePixelRatio || 1, 2), raf = 0, vis = true, t0 = performance.now(), off = Math.random() * 50;
    function size() {
      var W = Math.max(1, Math.round(ring.clientWidth * dpr)), H = Math.max(1, Math.round(ring.clientHeight * dpr));
      if (cv.width !== W || cv.height !== H) { cv.width = W; cv.height = H; gl.viewport(0, 0, W, H); }
      gl.uniform2f(uR, W, H);
    }
    function draw(now) { gl.uniform1f(uT, off + (now - t0) / 1000); gl.drawArrays(gl.TRIANGLES, 0, 3); }
    function tick(now) { draw(now); raf = vis && !document.hidden ? requestAnimationFrame(tick) : 0; }
    function play() { if (!raf && !reduce) raf = requestAnimationFrame(tick); }
    size(); draw(performance.now());
    if ("ResizeObserver" in window) new ResizeObserver(function () { size(); if (!raf) draw(performance.now()); }).observe(ring);
    if ("IntersectionObserver" in window) new IntersectionObserver(function (e) { vis = e[0].isIntersecting; if (vis) play(); }).observe(ring);
    document.addEventListener("visibilitychange", function () { if (!document.hidden) play(); });
    play();
  }

  var st = document.createElement("style"); st.textContent = CSS; document.head.appendChild(st);
  function init() { Array.prototype.forEach.call(document.querySelectorAll("[data-liquid-metal]"), mount); }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
