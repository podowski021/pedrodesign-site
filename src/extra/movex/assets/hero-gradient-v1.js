/*
 * Animated Gradient — versão em JavaScript puro (sem React), shader WebGL2.
 * Uso: <div data-animated-gradient data-preset="Prism" data-color1="#09122b"></div>
 * O elemento precisa ter tamanho (ex.: position:absolute; inset:0). Sem WebGL2, fica o fundo CSS do próprio elemento.
 */
(function () {
  "use strict";
  var SHAPES = { Checks: 0, Stripes: 1, Edge: 2 };
  var PRESETS = {
    Prism: { color1: "#050505", color2: "#66B3FF", color3: "#FFFFFF", rotation: -50, proportion: 1, scale: 0.01, speed: 30, distortion: 0, swirl: 50, swirlIterations: 16, softness: 47, offset: -299, shape: "Checks", shapeSize: 45 },
    Lava: { color1: "#FF9F21", color2: "#FF0303", color3: "#000000", rotation: 114, proportion: 100, scale: 0.52, speed: 30, distortion: 7, swirl: 18, swirlIterations: 20, softness: 100, offset: 717, shape: "Edge", shapeSize: 12 },
    Plasma: { color1: "#B566FF", color2: "#000000", color3: "#000000", rotation: 0, proportion: 63, scale: 0.75, speed: 30, distortion: 5, swirl: 61, swirlIterations: 5, softness: 100, offset: -168, shape: "Checks", shapeSize: 28 },
    Pulse: { color1: "#66FF85", color2: "#000000", color3: "#000000", rotation: -167, proportion: 92, scale: 0, speed: 20, distortion: 54, swirl: 75, swirlIterations: 3, softness: 28, offset: -813, shape: "Checks", shapeSize: 79 },
    Vortex: { color1: "#000000", color2: "#FFFFFF", color3: "#000000", rotation: 50, proportion: 41, scale: 0.4, speed: 20, distortion: 0, swirl: 100, swirlIterations: 3, softness: 5, offset: -744, shape: "Stripes", shapeSize: 80 },
    Mist: { color1: "#050505", color2: "#FF66B8", color3: "#050505", rotation: 0, proportion: 33, scale: 0.48, speed: 39, distortion: 4, swirl: 65, swirlIterations: 5, softness: 100, offset: -235, shape: "Edge", shapeSize: 48 }
  };

  var VERT = "#version 300 es\nin vec4 a_position;\nvoid main(){gl_Position=a_position;}";
  var FRAG = [
    "#version 300 es",
    "precision highp float;",
    "uniform float u_time;uniform float u_pixelRatio;uniform vec2 u_resolution;",
    "uniform float u_scale;uniform float u_rotation;uniform vec4 u_color1;uniform vec4 u_color2;uniform vec4 u_color3;",
    "uniform float u_proportion;uniform float u_softness;uniform float u_shape;uniform float u_shapeScale;",
    "uniform float u_distortion;uniform float u_swirl;uniform float u_swirlIterations;",
    "out vec4 fragColor;",
    "#define TWO_PI 6.28318530718",
    "#define PI 3.14159265358979323846",
    "vec2 rotate(vec2 uv,float th){return mat2(cos(th),sin(th),-sin(th),cos(th))*uv;}",
    "float random(vec2 st){return fract(sin(dot(st.xy,vec2(12.9898,78.233)))*43758.5453123);}",
    "float noise(vec2 st){vec2 i=floor(st);vec2 f=fract(st);float a=random(i);float b=random(i+vec2(1.,0.));float c=random(i+vec2(0.,1.));float d=random(i+vec2(1.,1.));",
    " vec2 u=f*f*(3.-2.*f);return mix(mix(a,b,u.x),mix(c,d,u.x),u.y);}",
    "vec4 blend_colors(vec4 c1,vec4 c2,vec4 c3,float mixer,float edgesWidth,float edge_blur){",
    " vec3 color1=c1.rgb*c1.a;vec3 color2=c2.rgb*c2.a;vec3 color3=c3.rgb*c3.a;",
    " float r1=smoothstep(.0+.35*edgesWidth,.7-.35*edgesWidth+.5*edge_blur,mixer);",
    " float r2=smoothstep(.3+.35*edgesWidth,1.-.35*edgesWidth+edge_blur,mixer);",
    " vec3 b2=mix(color1,color2,r1);float o2=mix(c1.a,c2.a,r1);",
    " return vec4(mix(b2,color3,r2),mix(o2,c3.a,r2));}",
    "void main(){",
    " vec2 uv=gl_FragCoord.xy/u_resolution.xy;float t=.5*u_time;float noise_scale=.0005+.006*u_scale;",
    " uv-=.5;uv*=(noise_scale*u_resolution);uv=rotate(uv,u_rotation*.5*PI);uv/=u_pixelRatio;uv+=.5;",
    " float n1=noise(uv*1.+t);float n2=noise(uv*2.-t);float angle=n1*TWO_PI;",
    " uv.x+=4.*u_distortion*n2*cos(angle);uv.y+=4.*u_distortion*n2*sin(angle);",
    " float it=ceil(clamp(u_swirlIterations,1.,30.));",
    " for(float i=1.;i<=30.;i++){if(i>it)break;uv.x+=clamp(u_swirl,0.,2.)/i*cos(t+i*1.5*uv.y);uv.y+=clamp(u_swirl,0.,2.)/i*cos(t+i*1.*uv.x);}",
    " float proportion=clamp(u_proportion,0.,1.);float shape=0.;float mixer=0.;",
    " if(u_shape<.5){vec2 s=uv*(.5+3.5*u_shapeScale);shape=.5+.5*sin(s.x)*cos(s.y);mixer=shape+.48*sign(proportion-.5)*pow(abs(proportion-.5),.5);}",
    " else if(u_shape<1.5){vec2 s=uv*(.25+3.*u_shapeScale);float f=fract(s.y);shape=smoothstep(.0,.55,f)*smoothstep(1.,.45,f);mixer=shape+.48*sign(proportion-.5)*pow(abs(proportion-.5),.5);}",
    " else{float sh=1.-uv.y;sh-=.5;sh/=(noise_scale*u_resolution.y);sh+=.5;float ss=.2*(1.-u_shapeScale);shape=smoothstep(.45-ss,.55+ss,sh+.3*(proportion-.5));mixer=shape;}",
    " vec4 cm=blend_colors(u_color1,u_color2,u_color3,mixer,1.-clamp(u_softness,0.,1.),.01+.01*u_scale);",
    " fragColor=vec4(cm.rgb,cm.a);",
    "}"].join("\n");

  function hex(h) {
    var c = h.replace("#", "");
    if (c.length === 3) c = c[0] + c[0] + c[1] + c[1] + c[2] + c[2];
    return [parseInt(c.slice(0, 2), 16) / 255, parseInt(c.slice(2, 4), 16) / 255, parseInt(c.slice(4, 6), 16) / 255, c.length === 8 ? parseInt(c.slice(6, 8), 16) / 255 : 1];
  }

  function mount(box) {
    var base = PRESETS[box.getAttribute("data-preset")] || PRESETS.Prism, P = {};
    for (var k in base) P[k] = base[k];
    ["color1", "color2", "color3"].forEach(function (k) { var v = box.getAttribute("data-" + k); if (v) P[k] = v; });
    var sp = box.getAttribute("data-speed"); if (sp) P.speed = +sp;

    var canvas = document.createElement("canvas");
    canvas.setAttribute("aria-hidden", "true");
    canvas.style.cssText = "display:block;width:100%;height:100%";
    var gl = null;
    try { gl = canvas.getContext("webgl2", { premultipliedAlpha: true, alpha: true, antialias: true }); } catch (e) {}
    if (!gl) return;
    function sh(type, src) { var s = gl.createShader(type); gl.shaderSource(s, src); gl.compileShader(s); return gl.getShaderParameter(s, gl.COMPILE_STATUS) ? s : null; }
    var vs = sh(gl.VERTEX_SHADER, VERT), fs = sh(gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) return;
    var prog = gl.createProgram(); gl.attachShader(prog, vs); gl.attachShader(prog, fs); gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) return;
    gl.useProgram(prog);
    box.appendChild(canvas);
    box.setAttribute("data-gl", "on");

    var buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]), gl.STATIC_DRAW);
    var loc = gl.getAttribLocation(prog, "a_position"); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
    var U = {};
    ["u_time", "u_resolution", "u_pixelRatio", "u_scale", "u_rotation", "u_color1", "u_color2", "u_color3", "u_proportion", "u_softness", "u_shape", "u_shapeScale", "u_distortion", "u_swirl", "u_swirlIterations"]
      .forEach(function (n) { U[n] = gl.getUniformLocation(prog, n); });

    var c1 = hex(P.color1), c2 = hex(P.color2), c3 = hex(P.color3);
    gl.uniform1f(U.u_scale, P.scale);
    gl.uniform1f(U.u_rotation, P.rotation * Math.PI / 180);
    gl.uniform4f(U.u_color1, c1[0], c1[1], c1[2], c1[3]);
    gl.uniform4f(U.u_color2, c2[0], c2[1], c2[2], c2[3]);
    gl.uniform4f(U.u_color3, c3[0], c3[1], c3[2], c3[3]);
    gl.uniform1f(U.u_proportion, P.proportion / 100);
    gl.uniform1f(U.u_softness, P.softness / 100);
    gl.uniform1f(U.u_shape, SHAPES[P.shape]);
    gl.uniform1f(U.u_shapeScale, P.shapeSize / 100);
    gl.uniform1f(U.u_distortion, P.distortion / 50);
    gl.uniform1f(U.u_swirl, P.swirl / 100);
    gl.uniform1f(U.u_swirlIterations, P.swirl === 0 ? 0 : P.swirlIterations);

    var dpr = 1;
    function resize() {
      dpr = Math.min(window.devicePixelRatio || 1, 1.5);
      canvas.width = Math.max(1, Math.round(box.clientWidth * dpr));
      canvas.height = Math.max(1, Math.round(box.clientHeight * dpr));
      gl.viewport(0, 0, canvas.width, canvas.height);
      gl.uniform2f(U.u_resolution, canvas.width, canvas.height);
      gl.uniform1f(U.u_pixelRatio, dpr);
      if (!running) frame(performance.now());
    }

    var reduce = !!(window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches);
    var start = performance.now(), raf = 0, running = false, visible = true;
    function frame(now) {
      var elapsed = (now - start) / 1000, speed = (P.speed / 100) * 5;
      gl.uniform1f(U.u_time, elapsed * speed + P.offset * 0.01);
      gl.drawArrays(gl.TRIANGLES, 0, 6);
    }
    function tick(now) { frame(now); raf = requestAnimationFrame(tick); }
    function play() { if (running || reduce || !visible || document.hidden) return; running = true; raf = requestAnimationFrame(tick); }
    function pause() { running = false; cancelAnimationFrame(raf); }

    resize();
    if ("ResizeObserver" in window) new ResizeObserver(resize).observe(box); else addEventListener("resize", resize);
    if ("IntersectionObserver" in window) new IntersectionObserver(function (e) { visible = e[0].isIntersecting; visible ? play() : pause(); }).observe(box);
    document.addEventListener("visibilitychange", function () { document.hidden ? pause() : play(); });
    play();
  }

  function init() { Array.prototype.forEach.call(document.querySelectorAll("[data-animated-gradient]"), mount); }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();
})();
