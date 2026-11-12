/*
 * Velaris — gradiente vivo com ruído simplex, brilho central, vinheta e granulado (WebGL).
 * Versão em JavaScript puro, sem React.
 * Uso: <div data-velaris data-bg="#05091a" data-colors="#93c5fd,#3d86ff,#0b4fd8,#000000" data-speed="1.2" data-grain=".3"></div>
 * O elemento precisa ter tamanho. Sem WebGL, fica o fundo CSS do próprio elemento.
 */
(function () {
  "use strict";
  var VERT = "attribute vec2 position;varying vec2 vUv;void main(){vUv=position*0.5+0.5;gl_Position=vec4(position,0.0,1.0);}";
  var FRAG = [
    "precision highp float;",
    "varying vec2 vUv;",
    "uniform vec2 u_resolution;uniform float u_time;uniform float u_grain;uniform vec3 u_colors[4];uniform vec3 u_bg;uniform float u_sharp;",
    "float ss(float a,float b,float x){float c=(a+b)*.5,h=(b-a)*.5/u_sharp;return smoothstep(c-h,c+h,x);}",
    "vec3 permute(vec3 x){return mod(((x*34.0)+1.0)*x,289.0);}",
    "float snoise(vec2 v){",
    " const vec4 C=vec4(0.211324865405187,0.366025403784439,-0.577350269189626,0.024390243902439);",
    " vec2 i=floor(v+dot(v,C.yy));vec2 x0=v-i+dot(i,C.xx);",
    " vec2 i1=(x0.x>x0.y)?vec2(1.0,0.0):vec2(0.0,1.0);",
    " vec4 x12=x0.xyxy+C.xxzz;x12.xy-=i1;i=mod(i,289.0);",
    " vec3 p=permute(permute(i.y+vec3(0.0,i1.y,1.0))+i.x+vec3(0.0,i1.x,1.0));",
    " vec3 m=max(0.5-vec3(dot(x0,x0),dot(x12.xy,x12.xy),dot(x12.zw,x12.zw)),0.0);m=m*m;m=m*m;",
    " vec3 x=2.0*fract(p*C.www)-1.0;vec3 h=abs(x)-0.5;vec3 ox=floor(x+0.5);vec3 a0=x-ox;",
    " m*=1.79284291400159-0.85373472095314*(a0*a0+h*h);",
    " vec3 g;g.x=a0.x*x0.x+h.x*x0.y;g.yz=a0.yz*x12.xz+h.yz*x12.yw;return 130.0*dot(m,g);}",
    "void main(){",
    " vec2 uv=vUv;float ratio=u_resolution.x/u_resolution.y;vec2 p=uv-0.5;p.x*=ratio;",
    " float t=u_time*0.1;",
    " float n1=snoise(p*0.4+vec2(t*0.2,-t*0.3));",
    " float n2=snoise(p*0.55+vec2(-t*0.15,t*0.25)+n1*0.25);",
    " float n3=snoise(p*0.75+vec2(t*0.1,-t*0.2)+n2*0.2);",
    " vec3 col=u_bg;float dist=length(p)*1.5;float vignette=1.0-smoothstep(0.3,1.2,dist);",
    " col=mix(col,u_colors[0],ss(-0.2,0.5,n1)*0.85);",
    " col=mix(col,u_colors[1],ss(-0.1,0.6,n2)*0.7);",
    " col=mix(col,u_colors[2],ss(-0.3,0.4,n3)*0.6);",
    " col=mix(col,u_colors[3],ss(0.0,0.7,n1*n2)*0.5);",
    " float glow=smoothstep(0.8,0.0,dist)*0.3;col+=u_colors[1]*glow;",
    " col=mix(col*0.2,col,vignette);",
    " float grain=fract(sin(dot(uv,vec2(12.9898,78.233)))*43758.5453+u_time);",
    " col+=(grain-0.5)*u_grain*0.1;",
    " gl_FragColor=vec4(col,1.0);",
    "}"].join("\n");

  function rgb(h) { h = h.replace("#", "").trim(); return [parseInt(h.slice(0, 2), 16) / 255, parseInt(h.slice(2, 4), 16) / 255, parseInt(h.slice(4, 6), 16) / 255]; }

  function mount(box) {
    var bg = rgb(box.getAttribute("data-bg") || "#000000");
    var cols = (box.getAttribute("data-colors") || "#86efac,#4ade80,#059669,#000000").split(",").slice(0, 4).map(rgb);
    var speed = parseFloat(box.getAttribute("data-speed") || "2"), grain = parseFloat(box.getAttribute("data-grain") || ".3");
    var cv = document.createElement("canvas");
    cv.setAttribute("aria-hidden", "true");
    cv.style.cssText = "position:absolute;inset:0;width:100%;height:100%;display:block;pointer-events:none";
    var gl = null;
    try { gl = cv.getContext("webgl", { antialias: false, alpha: false }); } catch (e) {}
    if (!gl) return;
    function sh(tp, src) { var s = gl.createShader(tp); gl.shaderSource(s, src); gl.compileShader(s); return gl.getShaderParameter(s, gl.COMPILE_STATUS) ? s : null; }
    var vs = sh(gl.VERTEX_SHADER, VERT), fs = sh(gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) return;
    var pr = gl.createProgram(); gl.attachShader(pr, vs); gl.attachShader(pr, fs); gl.linkProgram(pr);
    if (!gl.getProgramParameter(pr, gl.LINK_STATUS)) return;
    gl.useProgram(pr);
    var buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
    var pos = gl.getAttribLocation(pr, "position"); gl.enableVertexAttribArray(pos); gl.vertexAttribPointer(pos, 2, gl.FLOAT, false, 0, 0);
    var L = { res: gl.getUniformLocation(pr, "u_resolution"), time: gl.getUniformLocation(pr, "u_time"), grain: gl.getUniformLocation(pr, "u_grain"), colors: gl.getUniformLocation(pr, "u_colors"), bg: gl.getUniformLocation(pr, "u_bg"), sharp: gl.getUniformLocation(pr, "u_sharp") };
    gl.uniform1f(L.grain, grain);
    gl.uniform1f(L.sharp, parseFloat(box.getAttribute("data-sharp") || "1"));
    gl.uniform3f(L.bg, bg[0], bg[1], bg[2]);
    gl.uniform3fv(L.colors, new Float32Array([].concat.apply([], cols)));
    box.appendChild(cv); box.setAttribute("data-gl", "on");

    var reduce = !!(window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches);
    var raf = 0, visible = true, offset = 0;
    function resize() {
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      cv.width = Math.max(1, Math.round(box.clientWidth * dpr)); cv.height = Math.max(1, Math.round(box.clientHeight * dpr));
      gl.viewport(0, 0, cv.width, cv.height); gl.uniform2f(L.res, cv.width, cv.height);
      if (!raf) draw(performance.now());
    }
    function draw(t) { gl.uniform1f(L.time, offset + t * 0.001 * speed); gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4); }
    function tick(t) { draw(t); raf = visible && !document.hidden && !reduce ? requestAnimationFrame(tick) : 0; }
    function play() { if (!raf && !reduce && visible && !document.hidden) raf = requestAnimationFrame(tick); }
    resize();
    if ("ResizeObserver" in window) new ResizeObserver(resize).observe(box); else addEventListener("resize", resize);
    if ("IntersectionObserver" in window) new IntersectionObserver(function (e) { visible = e[0].isIntersecting; play(); }).observe(box);
    document.addEventListener("visibilitychange", play);
    play();
  }

  function init() { Array.prototype.forEach.call(document.querySelectorAll("[data-velaris]"), mount); }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
