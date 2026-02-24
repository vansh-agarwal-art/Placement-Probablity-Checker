<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PlaceIQ — Career Intelligence</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
:root{
  --bg:#0c0a06;
  --surface:#13110c;
  --card:#1a1710;
  --border:rgba(212,175,100,0.18);
  --border-bright:rgba(212,175,100,0.5);
  --gold:#d4af64;
  --gold-light:#f0d08a;
  --gold-dim:rgba(212,175,100,0.12);
  --cream:#f5edd8;
  --red:#e05c4a;
  --green:#4caf7d;
  --blue:#5b9bd5;
  --text:#e8dfc8;
  --muted:rgba(232,223,200,0.45);
  --glow:0 0 30px rgba(212,175,100,0.25);
}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{
  font-family:'DM Sans',sans-serif;
  background:var(--bg);color:var(--text);
  min-height:100vh;overflow-x:hidden;
  cursor:none;
}

/* ══ CROSSHAIR CURSOR ══ */
#xhair{
  position:fixed;pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
  width:32px;height:32px;
}
#xhair::before,#xhair::after{
  content:'';position:absolute;background:var(--gold);
  transition:opacity 0.2s;
}
#xhair::before{width:1px;height:100%;left:50%;transform:translateX(-50%)}
#xhair::after{width:100%;height:1px;top:50%;transform:translateY(-50%)}
#xhair-dot{
  position:fixed;pointer-events:none;z-index:10000;
  width:4px;height:4px;background:var(--gold);border-radius:50%;
  transform:translate(-50%,-50%);
  box-shadow:0 0 8px var(--gold);
}
#xhair-ring{
  position:fixed;pointer-events:none;z-index:9998;
  width:56px;height:56px;
  border:1px solid rgba(212,175,100,0.3);
  border-radius:50%;
  transform:translate(-50%,-50%);
  transition:transform 0.18s cubic-bezier(0.2,0,0,1),width 0.25s,height 0.25s,border-color 0.2s;
}
body.hover-active #xhair-ring{
  width:44px;height:44px;
  border-color:rgba(212,175,100,0.7);
}
body.hover-active #xhair{opacity:0.5}

/* ══ AMBIENT BG ══ */
#bg-canvas{position:fixed;inset:0;z-index:0;opacity:0.5}

/* ══ NOISE ══ */
body::before{
  content:'';position:fixed;inset:0;z-index:1;pointer-events:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.06'/%3E%3C/svg%3E");
  opacity:0.35;
}

/* ══ PAGES ══ */
.page{display:none;min-height:100vh;position:relative;z-index:10}
.page.active{display:flex;flex-direction:column}

/* ══════════════════════════════════
   TOP NAV BAR
══════════════════════════════════ */
.topbar{
  position:fixed;top:0;left:0;right:0;z-index:200;
  display:flex;justify-content:space-between;align-items:center;
  padding:0 48px;height:56px;
  background:rgba(12,10,6,0.85);
  backdrop-filter:blur(16px);
  border-bottom:1px solid var(--border);
}
.logo-mark{
  font-family:'Playfair Display',serif;
  font-size:1.15rem;font-weight:700;
  color:var(--gold);letter-spacing:0.08em;
  display:flex;align-items:center;gap:10px;
}
.logo-mark::before{
  content:'';width:20px;height:20px;
  border:1px solid var(--gold);
  transform:rotate(45deg);display:inline-block;
  flex-shrink:0;
}
.topbar-right{
  font-family:'JetBrains Mono',monospace;
  font-size:0.65rem;color:var(--muted);letter-spacing:0.1em;
  display:flex;align-items:center;gap:18px;
}
.live-dot{
  width:5px;height:5px;border-radius:50%;
  background:var(--green);display:inline-block;
  animation:pulse 2s ease-in-out infinite;
}
@keyframes pulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(76,175,125,0.4)}50%{opacity:0.7;box-shadow:0 0 0 4px rgba(76,175,125,0)}}

/* ══════════════════════════════════
   LANDING
══════════════════════════════════ */
#landing{
  align-items:center;justify-content:center;
  text-align:center;padding:80px 24px 60px;
  gap:0;
}

.eyebrow{
  font-family:'JetBrains Mono',monospace;
  font-size:0.7rem;letter-spacing:0.3em;color:var(--gold);
  text-transform:uppercase;margin-bottom:28px;
  opacity:0;animation:rise 0.7s 0.2s forwards;
}
.hero-title{
  font-family:'Playfair Display',serif;
  font-size:clamp(3.5rem,9vw,7.5rem);
  font-weight:900;line-height:0.9;
  color:var(--cream);margin-bottom:6px;
  opacity:0;animation:rise 0.7s 0.35s forwards;
}
.hero-title em{
  font-style:italic;
  background:linear-gradient(135deg,var(--gold),var(--gold-light),var(--gold));
  background-size:200%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;
  animation:shimmer 5s linear infinite;
}
@keyframes shimmer{0%{background-position:0%}100%{background-position:200%}}

.hero-rule{
  width:80px;height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent);
  margin:28px auto;opacity:0;animation:rise 0.7s 0.5s forwards;
}
.hero-sub{
  max-width:500px;font-size:1.1rem;color:var(--muted);
  line-height:1.8;margin:0 auto 52px;font-weight:300;
  opacity:0;animation:rise 0.7s 0.6s forwards;
}

.cta-wrap{opacity:0;animation:rise 0.7s 0.7s forwards;display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.btn-gold{
  padding:15px 44px;
  background:var(--gold);color:#0c0a06;
  border:none;font-family:'DM Sans',sans-serif;
  font-size:0.9rem;font-weight:600;letter-spacing:0.06em;
  cursor:pointer;transition:all 0.3s;position:relative;overflow:hidden;
  text-transform:uppercase;
}
.btn-gold::after{content:'';position:absolute;inset:0;background:white;opacity:0;transition:0.3s}
.btn-gold:hover{transform:translateY(-2px);box-shadow:var(--glow)}
.btn-gold:hover::after{opacity:0.12}
.btn-outline{
  padding:15px 44px;
  background:transparent;color:var(--gold);
  border:1px solid var(--border-bright);
  font-family:'DM Sans',sans-serif;font-size:0.9rem;font-weight:600;
  letter-spacing:0.06em;cursor:pointer;transition:all 0.3s;
  text-transform:uppercase;
}
.btn-outline:hover{background:var(--gold-dim);transform:translateY(-2px)}

.metrics-row{
  display:flex;gap:0;margin-top:80px;
  border:1px solid var(--border);
  opacity:0;animation:rise 0.7s 0.85s forwards;
}
.metric{
  padding:28px 48px;text-align:center;
  border-right:1px solid var(--border);flex:1;
  transition:background 0.3s;
}
.metric:last-child{border-right:none}
.metric:hover{background:var(--gold-dim)}
.metric-val{
  font-family:'Playfair Display',serif;
  font-size:2.5rem;font-weight:700;color:var(--gold);
  line-height:1;
}
.metric-lbl{font-size:0.7rem;color:var(--muted);letter-spacing:0.15em;text-transform:uppercase;margin-top:6px}

@keyframes rise{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}

/* ══════════════════════════════════
   FORM PAGE
══════════════════════════════════ */
#form-page{padding:80px 24px 80px;align-items:center}
.form-wrap{width:100%;max-width:680px}

/* Progress */
.progress-bar-wrap{margin-bottom:40px}
.pb-meta{display:flex;justify-content:space-between;margin-bottom:10px}
.pb-label{font-family:'JetBrains Mono',monospace;font-size:0.65rem;color:var(--gold);letter-spacing:0.15em}
.pb-step{font-family:'JetBrains Mono',monospace;font-size:0.65rem;color:var(--muted)}
.pb-track{height:2px;background:rgba(212,175,100,0.1);position:relative}
.pb-fill{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-light));transition:width 0.5s cubic-bezier(0.4,0,0.2,1);position:relative}
.pb-fill::after{content:'◆';position:absolute;right:-6px;top:-7px;font-size:0.5rem;color:var(--gold)}

/* Panel */
.panel{
  background:var(--card);border:1px solid var(--border);
  position:relative;overflow:hidden;
  animation:panelIn 0.4s cubic-bezier(0.4,0,0.2,1);
}
@keyframes panelIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
.panel::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);
  opacity:0.6;
}
.panel-head{
  padding:28px 32px 24px;
  border-bottom:1px solid var(--border);
  display:flex;gap:18px;align-items:flex-start;
}
.panel-num{
  font-family:'Playfair Display',serif;
  font-size:2rem;font-weight:900;color:var(--gold);
  opacity:0.25;line-height:1;flex-shrink:0;width:32px;
}
.panel-category{font-family:'JetBrains Mono',monospace;font-size:0.6rem;letter-spacing:0.2em;color:var(--gold);text-transform:uppercase;margin-bottom:6px}
.panel-q{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--cream);line-height:1.3}
.panel-body{padding:32px}
.panel-foot{padding:20px 32px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}

/* Steps */
.step{display:none}
.step.active{display:block}

/* Text input */
.inp-wrap{position:relative}
.fancy-input{
  width:100%;padding:16px 20px;
  background:rgba(212,175,100,0.04);
  border:1px solid var(--border);
  color:var(--cream);font-family:'DM Sans',sans-serif;font-size:1.05rem;
  outline:none;transition:all 0.3s;letter-spacing:0.02em;
}
.fancy-input:focus{border-color:var(--gold-light);background:rgba(212,175,100,0.08);box-shadow:0 0 0 2px rgba(212,175,100,0.15)}
.fancy-input::placeholder{color:var(--muted)}
.inp-corner{position:absolute;width:8px;height:8px}
.inp-corner.tl{top:0;left:0;border-top:1px solid var(--gold);border-left:1px solid var(--gold)}
.inp-corner.br{bottom:0;right:0;border-bottom:1px solid var(--gold);border-right:1px solid var(--gold)}

/* CGPA */
.cgpa-center{text-align:center;padding:10px 0 24px}
.cgpa-num{
  font-family:'Playfair Display',serif;
  font-size:8rem;font-weight:900;
  color:var(--gold);line-height:1;
  transition:color 0.3s;
  text-shadow:0 0 60px rgba(212,175,100,0.3);
}
.cgpa-denom{font-size:1rem;color:var(--muted);margin-top:4px;font-weight:300;letter-spacing:0.1em}
.grade-pill{
  display:inline-block;margin-top:16px;
  padding:5px 18px;border:1px solid var(--border);
  font-family:'JetBrains Mono',monospace;font-size:0.7rem;letter-spacing:0.15em;
  transition:all 0.3s;color:var(--gold);background:var(--gold-dim);
}

/* Range */
input[type=range]{
  -webkit-appearance:none;width:100%;height:2px;
  outline:none;cursor:pointer;
  background:linear-gradient(90deg,var(--gold) var(--pct,75%),rgba(212,175,100,0.12) var(--pct,75%));
}
input[type=range]::-webkit-slider-thumb{
  -webkit-appearance:none;width:18px;height:18px;
  background:var(--gold);cursor:grab;
  clip-path:polygon(50% 0%,100% 50%,50% 100%,0% 50%);
  transition:transform 0.2s;
}
input[type=range]::-webkit-slider-thumb:active{cursor:grabbing;transform:scale(1.4)}
.range-ends{display:flex;justify-content:space-between;margin-top:8px}
.range-end{font-family:'JetBrains Mono',monospace;font-size:0.6rem;color:var(--muted)}

/* Skill cards */
.skill-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.skill-card{
  background:rgba(212,175,100,0.03);border:1px solid var(--border);
  padding:18px;transition:all 0.25s;
}
.skill-card:hover{border-color:var(--border-bright);background:rgba(212,175,100,0.06)}
.sc-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.sc-name{font-size:0.75rem;color:var(--muted);letter-spacing:0.08em;text-transform:uppercase}
.sc-val{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--gold)}
.sc-bar{height:2px;background:rgba(212,175,100,0.1);overflow:hidden;margin-bottom:10px}
.sc-fill{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-light));transition:width 0.25s}
.skill-card input[type=range]{height:1px;margin-top:2px}
.skill-card input[type=range]::-webkit-slider-thumb{width:12px;height:12px}

/* Counters */
.counter-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.counter-card{
  border:1px solid var(--border);
  background:rgba(212,175,100,0.03);
  padding:28px 20px;text-align:center;
  position:relative;transition:all 0.25s;
}
.counter-card:hover{border-color:var(--border-bright)}
.counter-card::before,.counter-card::after{content:'';position:absolute;width:10px;height:10px}
.counter-card::before{top:0;left:0;border-top:1px solid var(--gold);border-left:1px solid var(--gold)}
.counter-card::after{bottom:0;right:0;border-bottom:1px solid var(--gold);border-right:1px solid var(--gold)}
.cc-ico{font-size:1.8rem;margin-bottom:8px}
.cc-lbl{font-size:0.7rem;color:var(--muted);letter-spacing:0.12em;text-transform:uppercase;margin-bottom:18px}
.cc-ctrl{display:flex;align-items:center;justify-content:center;gap:18px}
.cc-btn{
  width:32px;height:32px;border:1px solid var(--border);
  background:none;color:var(--gold);font-size:1.1rem;
  cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;
}
.cc-btn:hover{background:var(--gold);color:#0c0a06;border-color:var(--gold)}
.cc-val{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:var(--cream);min-width:36px}

/* Backlog options */
.bl-wrap{display:grid;grid-template-columns:repeat(5,1fr);gap:8px}
.bl-btn{
  padding:14px 6px;border:1px solid var(--border);
  background:rgba(212,175,100,0.03);color:var(--muted);
  cursor:pointer;text-align:center;transition:all 0.25s;
  font-family:'JetBrains Mono',monospace;font-size:0.75rem;font-weight:500;
}
.bl-btn:hover{border-color:var(--border-bright);color:var(--text)}
.bl-btn.sel{border-color:var(--gold);color:var(--gold);background:var(--gold-dim)}
.bl-btn.danger.sel{border-color:var(--red);color:var(--red);background:rgba(224,92,74,0.08)}

/* Nav buttons */
.btn-back{
  background:none;border:1px solid var(--border);color:var(--muted);
  padding:11px 24px;cursor:pointer;font-family:'DM Sans',sans-serif;
  font-size:0.8rem;letter-spacing:0.06em;transition:all 0.2s;text-transform:uppercase;
}
.btn-back:hover{border-color:var(--text);color:var(--text)}
.btn-fwd{
  background:var(--gold);color:#0c0a06;border:none;
  padding:12px 32px;cursor:pointer;font-family:'DM Sans',sans-serif;
  font-size:0.8rem;font-weight:600;letter-spacing:0.08em;
  transition:all 0.25s;text-transform:uppercase;
}
.btn-fwd:hover{background:var(--gold-light);transform:translateY(-1px);box-shadow:var(--glow)}

.hint-txt{font-size:0.78rem;color:var(--muted);line-height:1.6;font-style:italic}

/* ══════════════════════════════════
   RESULT PAGE
══════════════════════════════════ */
#result-page{padding:80px 24px 80px;align-items:center}
.result-wrap{width:100%;max-width:760px}

/* Score hero card */
.score-card{
  background:var(--card);border:1px solid var(--border);
  padding:50px 40px;text-align:center;margin-bottom:20px;
  position:relative;overflow:hidden;
}
.score-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--score-c,var(--gold)),transparent);
}
.score-label{
  font-family:'JetBrains Mono',monospace;font-size:0.65rem;
  letter-spacing:0.25em;color:var(--muted);margin-bottom:20px;text-transform:uppercase;
}

/* Score ring SVG */
.ring-wrap{width:200px;height:200px;position:relative;margin:0 auto 28px}
.ring-wrap svg{width:200px;height:200px;transform:rotate(-90deg)}
.ring-bg{fill:none;stroke:rgba(212,175,100,0.08);stroke-width:8}
.ring-track{fill:none;stroke-width:8;stroke-linecap:round;stroke-dasharray:502;stroke-dashoffset:502;transition:stroke-dashoffset 1.6s cubic-bezier(0.4,0,0.2,1)}
.ring-center{
  position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;
}
.ring-num{
  font-family:'Playfair Display',serif;font-size:3.2rem;
  font-weight:900;line-height:1;
}
.ring-of{font-size:0.75rem;color:var(--muted);margin-top:3px;font-family:'JetBrains Mono',monospace;letter-spacing:0.1em}

.verdict-text{
  font-family:'Playfair Display',serif;
  font-size:1.6rem;font-weight:700;margin-bottom:12px;
}
.verdict-desc{color:var(--muted);font-size:0.95rem;max-width:480px;margin:0 auto;line-height:1.75;font-weight:300}

/* Skill breakdown */
.section-head{
  font-family:'JetBrains Mono',monospace;font-size:0.65rem;
  letter-spacing:0.2em;color:var(--gold);text-transform:uppercase;
  margin-bottom:16px;padding-bottom:10px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:8px;
}
.section-head::before{content:'◆';font-size:0.45rem}

.breakdown-card{
  background:var(--card);border:1px solid var(--border);
  padding:28px;margin-bottom:20px;
}
.bk-item{margin-bottom:16px}
.bk-row{display:flex;justify-content:space-between;margin-bottom:6px}
.bk-name{font-size:0.85rem;color:var(--text);font-weight:500}
.bk-val{font-family:'JetBrains Mono',monospace;font-size:0.8rem;color:var(--gold)}
.bk-bar{height:2px;background:rgba(212,175,100,0.08);overflow:hidden}
.bk-fill{height:100%;width:0;background:linear-gradient(90deg,var(--gold),var(--gold-light));transition:width 1.1s cubic-bezier(0.4,0,0.2,1)}

/* Tips */
.tips-card{
  background:var(--card);border:1px solid var(--border);
  padding:28px;margin-bottom:20px;
}
.tip-item{
  display:flex;gap:16px;padding:16px 0;
  border-bottom:1px solid var(--border);align-items:flex-start;
}
.tip-item:last-child{border-bottom:none;padding-bottom:0}
.tip-tag{
  flex-shrink:0;padding:4px 10px;
  font-family:'JetBrains Mono',monospace;font-size:0.6rem;letter-spacing:0.1em;
  border:1px solid;white-space:nowrap;margin-top:2px;
}
.tip-tag.high{border-color:rgba(224,92,74,0.5);color:var(--red);background:rgba(224,92,74,0.07)}
.tip-tag.med{border-color:var(--border-bright);color:var(--gold);background:var(--gold-dim)}
.tip-tag.ok{border-color:rgba(76,175,125,0.4);color:var(--green);background:rgba(76,175,125,0.07)}
.tip-body{font-size:0.9rem;color:var(--muted);line-height:1.7}
.tip-body strong{color:var(--text);font-weight:600}

/* Actions */
.actions-row{display:flex;gap:12px;flex-wrap:wrap}
.act-btn{
  flex:1;min-width:160px;padding:16px;text-align:center;
  font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:600;
  letter-spacing:0.07em;text-transform:uppercase;cursor:pointer;
  transition:all 0.25s;border:none;
}
.act-btn.primary{background:var(--gold);color:#0c0a06}
.act-btn.primary:hover{background:var(--gold-light);transform:translateY(-2px);box-shadow:var(--glow)}
.act-btn.secondary{background:none;color:var(--muted);border:1px solid var(--border)}
.act-btn.secondary:hover{border-color:var(--border-bright);color:var(--text)}
.act-btn.pdf{
  background:rgba(91,155,213,0.1);color:var(--blue);
  border:1px solid rgba(91,155,213,0.3);flex:1.4;
}
.act-btn.pdf:hover{background:rgba(91,155,213,0.18);border-color:var(--blue);transform:translateY(-2px)}

/* ══ LOADER ══ */
#loader{
  display:none;position:fixed;inset:0;z-index:1000;
  background:var(--bg);align-items:center;justify-content:center;flex-direction:column;gap:28px;
}
#loader.show{display:flex}
.loader-icon{
  width:64px;height:64px;border:1px solid var(--border);
  transform:rotate(45deg);position:relative;
  animation:loaderSpin 3s linear infinite;
}
.loader-icon::before{
  content:'';position:absolute;inset:8px;border:1px solid rgba(212,175,100,0.3);
  animation:loaderSpin 2s linear infinite reverse;
}
.loader-icon::after{
  content:'◆';position:absolute;inset:0;display:flex;align-items:center;
  justify-content:center;font-size:0.7rem;color:var(--gold);transform:rotate(-45deg);
}
@keyframes loaderSpin{to{transform:rotate(calc(45deg + 360deg))}}
.loader-txt{font-family:'JetBrains Mono',monospace;font-size:0.7rem;letter-spacing:0.2em;color:var(--gold);text-transform:uppercase}
.loader-sub{font-family:'JetBrains Mono',monospace;font-size:0.6rem;color:var(--muted);letter-spacing:0.1em;margin-top:6px;min-height:1em;text-align:center}
.loader-progress{width:240px;height:1px;background:rgba(212,175,100,0.1)}
.loader-prog-fill{height:100%;background:var(--gold);animation:lpFill 1.8s ease forwards}
@keyframes lpFill{from{width:0}to{width:100%}}

/* ══ CONFETTI ══ */
.cf{position:fixed;pointer-events:none;z-index:999;animation:cfFall linear forwards}
@keyframes cfFall{0%{opacity:1;transform:translate(0,0) rotate(0deg)}100%{opacity:0;transform:translate(var(--tx),110vh) rotate(var(--r))}}

@media(max-width:580px){
  .skill-grid,.counter-grid,.bl-wrap{grid-template-columns:1fr 1fr}
  .bl-wrap{grid-template-columns:repeat(3,1fr)}
  .metrics-row{flex-direction:column}
  .metric{border-right:none;border-bottom:1px solid var(--border)}
  .metric:last-child{border-bottom:none}
  .topbar{padding:0 20px}
  .cgpa-num{font-size:6rem}
}
</style>
</head>
<body>

<!-- Cursor Elements -->
<div id="xhair"></div>
<div id="xhair-dot"></div>
<div id="xhair-ring"></div>

<!-- Ambient BG -->
<canvas id="bg-canvas"></canvas>

<!-- Loader -->
<div id="loader">
  <div class="loader-icon"></div>
  <div>
    <div class="loader-txt">Analyzing Profile</div>
    <div class="loader-sub" id="lsub">Initializing assessment engine...</div>
  </div>
  <div class="loader-progress"><div class="loader-prog-fill" id="lpf"></div></div>
</div>

<!-- Confetti container -->
<div id="cfwrap"></div>

<!-- ════════════════════════════════
     LANDING
════════════════════════════════ -->
<div class="page active" id="landing">
  <div class="topbar">
    <div class="logo-mark">PlaceIQ</div>
    <div class="topbar-right">
      <span class="live-dot"></span>
      <span>SYSTEM ONLINE</span>
      <span id="live-time"></span>
    </div>
  </div>

  <div class="eyebrow">Career Intelligence System — v3.0</div>
  <h1 class="hero-title">Know Your<br><em>Placement Odds</em></h1>
  <div class="hero-rule"></div>
  <p class="hero-sub">A precise, data-driven assessment of your campus placement probability based on 8 real-world parameters used by top recruiters.</p>
  <div class="cta-wrap">
    <button class="btn-gold" onclick="startAssessment()">Begin Assessment</button>
    <button class="btn-outline" onclick="">How it works ↓</button>
  </div>
  <div class="metrics-row">
    <div class="metric"><div class="metric-val">8</div><div class="metric-lbl">Parameters</div></div>
    <div class="metric"><div class="metric-val">100</div><div class="metric-lbl">Score Points</div></div>
    <div class="metric"><div class="metric-val">&lt;60s</div><div class="metric-lbl">To Complete</div></div>
    <div class="metric"><div class="metric-val">PDF</div><div class="metric-lbl">Report Included</div></div>
  </div>
</div>

<!-- ════════════════════════════════
     FORM PAGE
════════════════════════════════ -->
<div class="page" id="form-page">
  <div class="topbar">
    <div class="logo-mark">PlaceIQ</div>
    <button onclick="showPage('landing')" class="btn-back" style="border:none;background:none;cursor:pointer">← Home</button>
  </div>

  <div class="form-wrap">
    <!-- Progress -->
    <div class="progress-bar-wrap">
      <div class="pb-meta">
        <span class="pb-label">Assessment Progress</span>
        <span class="pb-step" id="step-counter">Step 1 of 5</span>
      </div>
      <div class="pb-track"><div class="pb-fill" id="pb" style="width:0%"></div></div>
    </div>

    <!-- S1: Name -->
    <div class="step active" id="s1">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-num">01</div>
          <div>
            <div class="panel-category">Identity</div>
            <div class="panel-q">What's your name, candidate?</div>
          </div>
        </div>
        <div class="panel-body">
          <div class="inp-wrap">
            <div class="inp-corner tl"></div>
            <input type="text" class="fancy-input" id="inp-name" placeholder="Enter your full name" maxlength="60" autocomplete="off">
            <div class="inp-corner br"></div>
          </div>
        </div>
        <div class="panel-foot">
          <span class="hint-txt">Personalizes your assessment report.</span>
          <button class="btn-fwd" onclick="next(1)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- S2: CGPA -->
    <div class="step" id="s2">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-num">02</div>
          <div>
            <div class="panel-category">Academic Record</div>
            <div class="panel-q">What is your current CGPA?</div>
          </div>
        </div>
        <div class="panel-body">
          <div class="cgpa-center">
            <div class="cgpa-num" id="cgpa-num">7.5</div>
            <div class="cgpa-denom">out of 10.0</div>
            <div class="grade-pill" id="grade-pill">First Class</div>
          </div>
          <input type="range" id="inp-cgpa" min="0" max="10" step="0.1" value="7.5" oninput="onCgpa(this.value)" style="--pct:75%">
          <div class="range-ends"><span class="range-end">0.0</span><span class="range-end">5.0</span><span class="range-end">10.0</span></div>
        </div>
        <div class="panel-foot">
          <button class="btn-back" onclick="prev(2)">← Back</button>
          <button class="btn-fwd" onclick="next(2)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- S3: Skills -->
    <div class="step" id="s3">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-num">03</div>
          <div>
            <div class="panel-category">Skill Matrix</div>
            <div class="panel-q">Rate your technical & soft skills.</div>
          </div>
        </div>
        <div class="panel-body">
          <div class="skill-grid">
            <div class="skill-card">
              <div class="sc-top"><span class="sc-name">💻 Coding</span><span class="sc-val" id="sv-code">5</span></div>
              <div class="sc-bar"><div class="sc-fill" id="sf-code" style="width:50%"></div></div>
              <input type="range" min="0" max="10" value="5" id="inp-code" oninput="skillUpdate('code',this.value)" style="--pct:50%">
            </div>
            <div class="skill-card">
              <div class="sc-top"><span class="sc-name">🗣️ Communication</span><span class="sc-val" id="sv-comm">5</span></div>
              <div class="sc-bar"><div class="sc-fill" id="sf-comm" style="width:50%"></div></div>
              <input type="range" min="0" max="10" value="5" id="inp-comm" oninput="skillUpdate('comm',this.value)" style="--pct:50%">
            </div>
            <div class="skill-card">
              <div class="sc-top"><span class="sc-name">🧮 Aptitude</span><span class="sc-val" id="sv-apt">5</span></div>
              <div class="sc-bar"><div class="sc-fill" id="sf-apt" style="width:50%"></div></div>
              <input type="range" min="0" max="10" value="5" id="inp-apt" oninput="skillUpdate('apt',this.value)" style="--pct:50%">
            </div>
            <div class="skill-card">
              <div class="sc-top"><span class="sc-name">🧩 DSA / Problem Solving</span><span class="sc-val" id="sv-dsa">5</span></div>
              <div class="sc-bar"><div class="sc-fill" id="sf-dsa" style="width:50%"></div></div>
              <input type="range" min="0" max="10" value="5" id="inp-dsa" oninput="skillUpdate('dsa',this.value)" style="--pct:50%">
            </div>
          </div>
        </div>
        <div class="panel-foot">
          <button class="btn-back" onclick="prev(3)">← Back</button>
          <button class="btn-fwd" onclick="next(3)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- S4: Experience -->
    <div class="step" id="s4">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-num">04</div>
          <div>
            <div class="panel-category">Field Experience</div>
            <div class="panel-q">Projects built & internships completed.</div>
          </div>
        </div>
        <div class="panel-body">
          <div class="counter-grid">
            <div class="counter-card">
              <div class="cc-ico">🛠️</div>
              <div class="cc-lbl">Projects Built</div>
              <div class="cc-ctrl">
                <button class="cc-btn" onclick="cnt('proj',-1)">−</button>
                <div class="cc-val" id="cv-proj">0</div>
                <button class="cc-btn" onclick="cnt('proj',1)">+</button>
              </div>
            </div>
            <div class="counter-card">
              <div class="cc-ico">🏢</div>
              <div class="cc-lbl">Internships Done</div>
              <div class="cc-ctrl">
                <button class="cc-btn" onclick="cnt('intern',-1)">−</button>
                <div class="cc-val" id="cv-intern">0</div>
                <button class="cc-btn" onclick="cnt('intern',1)">+</button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-foot">
          <button class="btn-back" onclick="prev(4)">← Back</button>
          <button class="btn-fwd" onclick="next(4)">Continue →</button>
        </div>
      </div>
    </div>

    <!-- S5: Backlogs -->
    <div class="step" id="s5">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-num">05</div>
          <div>
            <div class="panel-category">Academic Risk</div>
            <div class="panel-q">How many active backlogs do you have?</div>
          </div>
        </div>
        <div class="panel-body">
          <div class="bl-wrap">
            <button class="bl-btn sel" onclick="blSelect(this,0)">None</button>
            <button class="bl-btn" onclick="blSelect(this,1)">1</button>
            <button class="bl-btn" onclick="blSelect(this,2)">2</button>
            <button class="bl-btn danger" onclick="blSelect(this,3)">3</button>
            <button class="bl-btn danger" onclick="blSelect(this,5)">4+</button>
          </div>
          <div style="margin-top:20px;padding:16px;border-left:2px solid rgba(224,92,74,0.5);background:rgba(224,92,74,0.05)">
            <p class="hint-txt" style="color:rgba(224,92,74,0.8)">Note: Most top companies auto-reject profiles with even one active backlog at the screening stage.</p>
          </div>
        </div>
        <div class="panel-foot">
          <button class="btn-back" onclick="prev(5)">← Back</button>
          <button class="btn-fwd" onclick="calculate()">Run Analysis ◆</button>
        </div>
      </div>
    </div>

  </div><!-- /form-wrap -->
</div>

<!-- ════════════════════════════════
     RESULT PAGE
════════════════════════════════ -->
<div class="page" id="result-page">
  <div class="topbar">
    <div class="logo-mark">PlaceIQ</div>
    <button onclick="showPage('landing')" class="btn-back" style="border:none;background:none;cursor:pointer">← Home</button>
  </div>

  <div class="result-wrap">

    <!-- Score Card -->
    <div class="score-card" id="score-card">
      <div class="score-label">Assessment Report — <span id="res-name">Candidate</span></div>
      <div class="ring-wrap">
        <svg viewBox="0 0 200 200">
          <circle class="ring-bg" cx="100" cy="100" r="80"/>
          <circle class="ring-track" id="ring-track" cx="100" cy="100" r="80"/>
        </svg>
        <div class="ring-center">
          <div class="ring-num" id="res-num" style="color:var(--gold)">0</div>
          <div class="ring-of">out of 100</div>
        </div>
      </div>
      <div class="verdict-text" id="res-verdict" style="color:var(--gold)">—</div>
      <div class="verdict-desc" id="res-desc"></div>
    </div>

    <!-- Skill Breakdown -->
    <div class="breakdown-card">
      <div class="section-head">Skill Breakdown</div>
      <div id="bk-items"></div>
    </div>

    <!-- Tips -->
    <div class="tips-card">
      <div class="section-head">Improvement Roadmap</div>
      <div id="tips-list"></div>
    </div>

    <!-- Actions -->
    <div class="actions-row">
      <button class="act-btn primary" onclick="restart()">Retake Assessment</button>
      <button class="act-btn secondary" onclick="copyResult()">Copy Result</button>
      <button class="act-btn pdf" onclick="downloadPDF()" id="pdf-btn">⬇ Download PDF Report</button>
    </div>

  </div>
</div>

<script>
/* ════════════════════════════════
   CURSOR — CROSSHAIR
════════════════════════════════ */
const xh=document.getElementById('xhair');
const xhd=document.getElementById('xhair-dot');
const xhr=document.getElementById('xhair-ring');
let mx=window.innerWidth/2,my=window.innerHeight/2,rx=mx,ry=my;

document.addEventListener('mousemove',e=>{
  mx=e.clientX;my=e.clientY;
  xh.style.left=mx+'px';xh.style.top=my+'px';
  xhd.style.left=mx+'px';xhd.style.top=my+'px';
});
(function ringFollow(){
  rx+=(mx-rx)*0.1;ry+=(my-ry)*0.1;
  xhr.style.left=rx+'px';xhr.style.top=ry+'px';
  requestAnimationFrame(ringFollow);
})();
document.querySelectorAll('button,input,.bl-btn,.skill-card,.counter-card,.metric').forEach(el=>{
  el.addEventListener('mouseenter',()=>document.body.classList.add('hover-active'));
  el.addEventListener('mouseleave',()=>document.body.classList.remove('hover-active'));
});

/* ════════════════════════════════
   AMBIENT PARTICLES
════════════════════════════════ */
const c=document.getElementById('bg-canvas');
const ctx=c.getContext('2d');
let W,H,pts=[];
function resize(){
  W=c.width=window.innerWidth;H=c.height=window.innerHeight;
  pts=[];
  for(let i=0;i<60;i++){
    pts.push({x:Math.random()*W,y:Math.random()*H,vx:(Math.random()-.5)*0.25,vy:(Math.random()-.5)*0.25,r:Math.random()*1+0.3});
  }
}
window.addEventListener('resize',resize);resize();
(function draw(){
  ctx.clearRect(0,0,W,H);
  pts.forEach(p=>{
    p.x=(p.x+p.vx+W)%W;p.y=(p.y+p.vy+H)%H;
    ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
    ctx.fillStyle='rgba(212,175,100,0.35)';ctx.fill();
  });
  for(let i=0;i<pts.length;i++){
    for(let j=i+1;j<pts.length;j++){
      const d=Math.hypot(pts[i].x-pts[j].x,pts[i].y-pts[j].y);
      if(d<130){
        ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);
        ctx.strokeStyle=`rgba(212,175,100,${0.1*(1-d/130)})`;ctx.lineWidth=0.4;ctx.stroke();
      }
    }
  }
  requestAnimationFrame(draw);
})();

/* ════════════════════════════════
   LIVE TIME
════════════════════════════════ */
function updateTime(){
  const t=new Date();
  const s=t.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  const el=document.getElementById('live-time');
  if(el)el.textContent=s;
}
setInterval(updateTime,1000);updateTime();

/* ════════════════════════════════
   STATE
════════════════════════════════ */
const S={name:'',cgpa:7.5,code:5,comm:5,apt:5,dsa:5,proj:0,intern:0,backlogs:0};
const counts={proj:0,intern:0};
let blVal=0,curStep=1;

/* ════════════════════════════════
   ROUTING
════════════════════════════════ */
function showPage(id){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo(0,0);
}
function startAssessment(){showPage('form-page');showStep(1);}

/* ════════════════════════════════
   STEPS
════════════════════════════════ */
function showStep(n){
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('s'+n).classList.add('active');
  document.getElementById('pb').style.width=((n-1)/5*100)+'%';
  document.getElementById('step-counter').textContent=`Step ${n} of 5`;
  curStep=n;
}
function next(from){
  if(from===1){
    const v=document.getElementById('inp-name').value.trim();
    if(!v){shake('inp-name');return}
    S.name=v;
  }
  if(from===2)S.cgpa=parseFloat(document.getElementById('inp-cgpa').value);
  if(from===3){S.code=+document.getElementById('inp-code').value;S.comm=+document.getElementById('inp-comm').value;S.apt=+document.getElementById('inp-apt').value;S.dsa=+document.getElementById('inp-dsa').value}
  if(from===4){S.proj=counts.proj;S.intern=counts.intern}
  showStep(from+1);
}
function prev(from){showStep(from-1)}

function shake(id){
  const el=document.getElementById(id);
  el.style.borderColor='var(--red)';
  el.animate([{transform:'translateX(-6px)'},{transform:'translateX(6px)'},{transform:'translateX(-4px)'},{transform:'translateX(4px)'},{transform:'none'}],{duration:350});
  setTimeout(()=>el.style.borderColor='',1000);
}

/* ════════════════════════════════
   CGPA SLIDER
════════════════════════════════ */
function onCgpa(v){
  v=parseFloat(v);
  document.getElementById('cgpa-num').textContent=v.toFixed(1);
  document.getElementById('inp-cgpa').style.setProperty('--pct',(v/10*100)+'%');
  let grade,col;
  if(v>=9){grade='Distinction ★';col='var(--gold-light)'}
  else if(v>=7.5){grade='First Class';col='var(--gold)'}
  else if(v>=6.5){grade='Second Class';col='#c8a04a'}
  else if(v>=5){grade='Pass';col='var(--muted)'}
  else{grade='Below Threshold';col='var(--red)'}
  const p=document.getElementById('grade-pill');
  p.textContent=grade;p.style.color=col;p.style.borderColor=col;
  document.getElementById('cgpa-num').style.color=col;
  document.getElementById('cgpa-num').style.textShadow=`0 0 60px ${col}`;
}

/* ════════════════════════════════
   SKILLS
════════════════════════════════ */
function skillUpdate(key,val){
  document.getElementById('sv-'+key).textContent=val;
  document.getElementById('sf-'+key).style.width=(val*10)+'%';
  document.getElementById('inp-'+key).style.setProperty('--pct',(val*10)+'%');
}

/* ════════════════════════════════
   COUNTERS
════════════════════════════════ */
function cnt(key,d){
  const max=key==='proj'?20:5;
  counts[key]=Math.max(0,Math.min(max,counts[key]+d));
  document.getElementById('cv-'+key).textContent=counts[key];
}

/* ════════════════════════════════
   BACKLOGS
════════════════════════════════ */
function blSelect(el,val){
  document.querySelectorAll('.bl-btn').forEach(b=>b.classList.remove('sel'));
  el.classList.add('sel');blVal=val;
}

/* ════════════════════════════════
   CALCULATE
════════════════════════════════ */
function calculate(){
  S.backlogs=blVal;S.proj=counts.proj;S.intern=counts.intern;
  const ld=document.getElementById('loader');
  ld.classList.add('show');
  // Reset progress bar
  const lpf=document.getElementById('lpf');
  lpf.style.animation='none';void lpf.offsetWidth;lpf.style.animation='lpFill 1.8s ease forwards';
  const msgs=['Scanning academic record...','Calibrating skill vectors...','Comparing against industry benchmarks...','Computing placement probability...','Preparing your report...'];
  let i=0;
  const iv=setInterval(()=>{
    const el=document.getElementById('lsub');
    if(el&&i<msgs.length)el.textContent=msgs[i++];
    else clearInterval(iv);
  },350);
  setTimeout(()=>{ld.classList.remove('show');showResult(computeScore())},1900);
}

function computeScore(){
  let s=0;
  const g=S.cgpa;
  if(g>=9)s+=28;else if(g>=8.5)s+=25;else if(g>=8)s+=22;else if(g>=7.5)s+=18;else if(g>=7)s+=14;else if(g>=6.5)s+=10;else if(g>=6)s+=6;else s+=2;
  s+=S.code*1.8;s+=S.comm*1.2;s+=S.apt*1.0;s+=S.dsa*1.4;
  s+=Math.min(S.proj,5)*2;s+=Math.min(S.intern,2)*4;
  s-=S.backlogs*7;
  return Math.max(0,Math.min(100,Math.round(s)));
}

/* ════════════════════════════════
   SHOW RESULT
════════════════════════════════ */
function showResult(score){
  showPage('result-page');
  document.getElementById('res-name').textContent=S.name;

  let color,verdict,desc;
  if(score>=80){color='#4caf7d';verdict='Elite Candidate';desc='Exceptional profile. You are well-positioned for top companies. Focus on cracking interviews — your resume will get noticed.';fireConfetti()}
  else if(score>=65){color='var(--gold)';verdict='Strong Profile';desc='Solid standing across parameters. Target mid-to-large companies and refine your weaker areas to break into the top tier.'}
  else if(score>=50){color='#d4af64';verdict='Developing Profile';desc='A fair shot at placement. Several areas need improvement. Follow the roadmap below consistently for the next 3–6 months.'}
  else if(score>=35){color='#e08a4a';verdict='Needs Improvement';desc='Your profile will be filtered at many companies. Focused effort on the directives below can significantly change your outcome.'}
  else{color='var(--red)';verdict='Immediate Action Required';desc="Don't be discouraged — placement is still possible with disciplined effort. Start with clearing backlogs and daily coding practice."}

  // Color everything
  document.getElementById('res-num').style.color=color;
  document.getElementById('res-verdict').style.color=color;
  document.getElementById('score-card').style.setProperty('--score-c',color);

  // Ring
  const ring=document.getElementById('ring-track');
  ring.style.stroke=color;
  const circumference=2*Math.PI*80;
  ring.style.strokeDasharray=circumference;
  ring.style.strokeDashoffset=circumference;
  setTimeout(()=>{ring.style.strokeDashoffset=circumference-(score/100)*circumference;},150);

  // Number counter
  animateNum('res-num',0,score,1500);
  document.getElementById('res-verdict').textContent=verdict;
  document.getElementById('res-desc').textContent=desc;

  // Breakdown
  const bkData=[
    {name:'CGPA',pct:Math.min(100,Math.round(S.cgpa*10))},
    {name:'Coding Skills',pct:S.code*10},
    {name:'Communication',pct:S.comm*10},
    {name:'Aptitude',pct:S.apt*10},
    {name:'DSA / Problem Solving',pct:S.dsa*10},
    {name:'Projects',pct:Math.min(100,S.proj*20)},
    {name:'Internships',pct:Math.min(100,S.intern*50)},
  ];
  const bkEl=document.getElementById('bk-items');
  bkEl.innerHTML='';
  bkData.forEach((b,i)=>{
    bkEl.innerHTML+=`<div class="bk-item"><div class="bk-row"><span class="bk-name">${b.name}</span><span class="bk-val">${b.pct}%</span></div><div class="bk-bar"><div class="bk-fill" id="bkf${i}" style="transition-delay:${i*0.08}s"></div></div></div>`;
  });
  setTimeout(()=>bkData.forEach((b,i)=>{document.getElementById('bkf'+i).style.width=b.pct+'%'}),300);

  // Tips
  const tips=[];
  if(S.cgpa<7)tips.push({level:'high',text:'<strong>Improve your CGPA above 7.5.</strong> Many companies have strict 7+ cutoffs. Speak with professors, attend extra sessions, and prioritise scoring subjects.'});
  if(S.code<6)tips.push({level:'high',text:'<strong>Daily coding practice is essential.</strong> Solve 2–3 problems on LeetCode every day. Start with Easy, move to Medium over 8 weeks.'});
  if(S.dsa<6)tips.push({level:'high',text:'<strong>Master DSA fundamentals.</strong> Arrays, Linked Lists, Trees, Graphs, and Dynamic Programming are asked in every product company interview. Use Striver\'s SDE Sheet.'});
  if(S.comm<6)tips.push({level:'med',text:'<strong>Build communication confidence.</strong> Practice mock GDs, record yourself answering HR questions, and review. Join clubs or take a public speaking course.'});
  if(S.apt<5)tips.push({level:'med',text:'<strong>Strengthen aptitude skills.</strong> Dedicate 30 minutes daily to IndiaBix or PrepInsta. Cover Quant, Logical Reasoning, and Verbal in rotation.'});
  if(S.proj<2)tips.push({level:'med',text:'<strong>Build 2–3 showcase projects.</strong> A deployed full-stack app, ML model, or mobile app with a clear GitHub README will stand out to every recruiter.'});
  if(S.intern===0)tips.push({level:'med',text:'<strong>Seek at least one internship.</strong> Apply via Internshala, LinkedIn, and AngelList. Even a 4-week stint adds credibility and real-world context to your resume.'});
  if(S.backlogs>0)tips.push({level:'high',text:'<strong>Clear all backlogs — this is Priority #1.</strong> Most companies auto-reject profiles with active backlogs at the screening stage, before a human ever reads your resume.'});
  if(tips.length===0)tips.push({level:'ok',text:'<strong>Excellent foundation!</strong> Now focus on competitive programming, FAANG-style mock interviews, LinkedIn networking, and polishing your resume and portfolio.'});

  const tlEl=document.getElementById('tips-list');
  tlEl.innerHTML=tips.map(t=>`
    <div class="tip-item">
      <span class="tip-tag ${t.level}">${t.level==='high'?'Priority':t.level==='ok'?'Great':'Action'}</span>
      <div class="tip-body">${t.text}</div>
    </div>`).join('');
}

function animateNum(id,from,to,dur){
  const el=document.getElementById(id);
  const start=performance.now();
  (function step(now){
    const p=Math.min((now-start)/dur,1),e=1-Math.pow(1-p,4);
    el.textContent=Math.round(from+(to-from)*e);
    if(p<1)requestAnimationFrame(step);
  })(performance.now());
}

/* ════════════════════════════════
   CONFETTI
════════════════════════════════ */
function fireConfetti(){
  const colors=['#d4af64','#f0d08a','#4caf7d','#5b9bd5','#e8dfc8','#e05c4a'];
  const wrap=document.getElementById('cfwrap');
  wrap.innerHTML='';
  for(let i=0;i<90;i++){
    const el=document.createElement('div');el.className='cf';
    const sz=5+Math.random()*7;
    el.style.cssText=`left:${Math.random()*100}vw;top:-20px;width:${sz}px;height:${sz}px;background:${colors[Math.floor(Math.random()*colors.length)]};border-radius:${Math.random()>.5?'50%':'2px'};--tx:${(Math.random()-.5)*180}px;--r:${Math.random()*720}deg;animation-duration:${2.2+Math.random()*2}s;animation-delay:${Math.random()*0.7}s`;
    wrap.appendChild(el);setTimeout(()=>el.remove(),5000);
  }
}

/* ════════════════════════════════
   COPY RESULT
════════════════════════════════ */
function copyResult(){
  const score=document.getElementById('res-num').textContent;
  const verdict=document.getElementById('res-verdict').textContent;
  const txt=`PlaceIQ Assessment Report\nCandidate: ${S.name}\nPlacement Score: ${score}/100\nVerdict: ${verdict}\n\nGenerated by PlaceIQ — Career Intelligence System`;
  navigator.clipboard.writeText(txt).then(()=>{
    const b=document.querySelector('.act-btn.secondary');
    b.textContent='✓ Copied!';
    setTimeout(()=>b.textContent='Copy Result',2200);
  });
}

/* ════════════════════════════════
   RESTART
════════════════════════════════ */
function restart(){
  Object.assign(counts,{proj:0,intern:0});blVal=0;
  document.getElementById('inp-name').value='';
  document.getElementById('inp-cgpa').value=7.5;onCgpa(7.5);
  ['code','comm','apt','dsa'].forEach(k=>{document.getElementById('inp-'+k).value=5;skillUpdate(k,5)});
  document.getElementById('cv-proj').textContent='0';document.getElementById('cv-intern').textContent='0';
  document.querySelectorAll('.bl-btn').forEach(b=>b.classList.remove('sel'));
  document.querySelector('.bl-btn').classList.add('sel');
  document.getElementById('ring-track').style.strokeDashoffset=2*Math.PI*80;
  showPage('form-page');showStep(1);
}

/* ════════════════════════════════
   ENTER KEY
════════════════════════════════ */
document.addEventListener('keydown',e=>{
  if(e.key==='Enter'){
    const btn=document.querySelector('.step.active .btn-fwd');
    if(btn)btn.click();
  }
});

/* ════════════════════════════════════════════════════
   PDF REPORT — CLEAN, PROFESSIONAL, FULLY READABLE
════════════════════════════════════════════════════ */
function downloadPDF(){
  const btn=document.getElementById('pdf-btn');
  btn.textContent='⏳ Generating...';btn.disabled=true;

  setTimeout(()=>{
    try{
      const {jsPDF}=window.jspdf;
      const doc=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
      const PW=210,PH=297;
      const score=parseInt(document.getElementById('res-num').textContent)||0;

      // Color palette for PDF
      const GOLD=[160,120,40];
      const DARK=[25,22,15];
      const MID=[50,45,35];
      const LIGHT=[232,223,200];
      const MUTED=[130,120,100];
      const WHITE=[255,255,255];
      const scoreCol=score>=80?[60,160,100]:score>=65?[160,120,40]:score>=50?[180,140,50]:score>=35?[200,120,60]:[190,70,60];

      // ─────────────────────────────────────────
      // PAGE 1: COVER PAGE
      // ─────────────────────────────────────────
      // White background
      doc.setFillColor(255,255,255);doc.rect(0,0,PW,PH,'F');

      // Gold top band
      doc.setFillColor(...GOLD);doc.rect(0,0,PW,28,'F');

      // Logo in band
      doc.setFont('helvetica','bold');doc.setFontSize(22);doc.setTextColor(255,255,255);
      doc.text('PlaceIQ',PW/2,16,{align:'center'});
      doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(255,240,200);
      doc.text('Career Intelligence System',PW/2,23,{align:'center'});

      // Report title area
      doc.setFillColor(248,245,238);doc.rect(0,28,PW,60,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(28);doc.setTextColor(...DARK);
      doc.text('Placement Probability',PW/2,58,{align:'center'});
      doc.text('Assessment Report',PW/2,70,{align:'center'});

      // Gold rule
      doc.setDrawColor(...GOLD);doc.setLineWidth(1);
      doc.line(40,78,PW-40,78);

      // Candidate info box
      doc.setFillColor(255,255,255);doc.rect(30,92,PW-60,52,'F');
      doc.setDrawColor(...GOLD);doc.setLineWidth(0.5);doc.rect(30,92,PW-60,52,'S');
      // Gold left accent on box
      doc.setFillColor(...GOLD);doc.rect(30,92,3,52,'F');

      doc.setFont('helvetica','bold');doc.setFontSize(9);doc.setTextColor(...MUTED);
      doc.text('CANDIDATE NAME',38,104);
      doc.setFont('helvetica','bold');doc.setFontSize(18);doc.setTextColor(...DARK);
      doc.text(S.name,38,116);

      doc.setDrawColor(220,210,190);doc.setLineWidth(0.3);doc.line(38,120,PW-38,120);

      doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(...MUTED);
      const now=new Date();
      const dateStr=now.toLocaleDateString('en-IN',{year:'numeric',month:'long',day:'numeric'});
      const timeStr=now.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'});
      doc.text(`Assessment Date: ${dateStr}  |  ${timeStr}`,38,128);
      doc.text(`Report ID: PIQ-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}-${Math.random().toString(36).substr(2,6).toUpperCase()}`,38,135);

      // BIG SCORE CIRCLE
      const cx=PW/2,cy=195,R=40;
      // Shadow/glow ring
      doc.setFillColor(240,235,220);doc.circle(cx,cy,R+4,'F');
      // Score fill arc background
      doc.setDrawColor(230,220,200);doc.setLineWidth(6);doc.circle(cx,cy,R,'S');
      // Score arc
      doc.setDrawColor(...scoreCol);doc.setLineWidth(6);
      // Draw arc as polyline
      const startA=-Math.PI/2;const endA=startA+(score/100)*Math.PI*2;
      const steps=60;
      for(let i=0;i<steps;i++){
        const a1=startA+(endA-startA)*(i/steps);
        const a2=startA+(endA-startA)*((i+1)/steps);
        doc.setDrawColor(...scoreCol);doc.setLineWidth(6);
        doc.line(cx+R*Math.cos(a1),cy+R*Math.sin(a1),cx+R*Math.cos(a2),cy+R*Math.sin(a2));
      }

      // Inner white circle
      doc.setFillColor(255,255,255);doc.circle(cx,cy,R-4,'F');

      // Score number
      doc.setFont('helvetica','bold');doc.setFontSize(32);doc.setTextColor(...scoreCol);
      doc.text(String(score),cx,cy+6,{align:'center'});
      doc.setFont('helvetica','normal');doc.setFontSize(9);doc.setTextColor(...MUTED);
      doc.text('out of 100',cx,cy+14,{align:'center'});

      // Label above circle
      doc.setFont('helvetica','bold');doc.setFontSize(9);doc.setTextColor(...MUTED);
      doc.text('PLACEMENT PROBABILITY SCORE',cx,cy-R-8,{align:'center'});

      // Verdict below circle
      const verdictStr=score>=80?'Elite Candidate':score>=65?'Strong Profile':score>=50?'Developing Profile':score>=35?'Needs Improvement':'Immediate Action Required';
      doc.setFont('helvetica','bold');doc.setFontSize(14);doc.setTextColor(...scoreCol);
      doc.text(verdictStr,cx,cy+R+14,{align:'center'});

      // Progress bar below
      const barX=40,barY=cy+R+24,barW=PW-80,barH=5;
      doc.setFillColor(230,220,200);doc.roundedRect(barX,barY,barW,barH,2,2,'F');
      doc.setFillColor(...scoreCol);doc.roundedRect(barX,barY,(score/100)*barW,barH,2,2,'F');
      // Scale labels
      doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(...MUTED);
      ['0','25','50','75','100'].forEach((l,i)=>{doc.text(l,barX+(i/4)*barW,barY+barH+5,{align:'center'})});
      doc.text('Poor',barX,barY-2);doc.text('Excellent',barX+barW,barY-2,{align:'right'});

      // Footer
      doc.setFillColor(...DARK);doc.rect(0,PH-20,PW,20,'F');
      doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(180,170,150);
      doc.text('PlaceIQ — Career Intelligence System  |  Confidential — For Candidate Use Only',PW/2,PH-11,{align:'center'});
      doc.setTextColor(...GOLD);doc.text('Page 1 of 2',PW-20,PH-11,{align:'right'});

      // ─────────────────────────────────────────
      // PAGE 2: DETAILED REPORT
      // ─────────────────────────────────────────
      doc.addPage();
      doc.setFillColor(255,255,255);doc.rect(0,0,PW,PH,'F');

      // Gold header strip
      doc.setFillColor(...GOLD);doc.rect(0,0,PW,14,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(255,255,255);
      doc.text('PlaceIQ — Detailed Assessment Report',14,9);
      doc.setFont('helvetica','normal');doc.setTextColor(255,240,200);
      doc.text(`${S.name}  |  Score: ${score}/100`,PW-14,9,{align:'right'});

      let Y=26;

      // ── SECTION A: INPUT DATA ──────────────────
      // Section header
      doc.setFillColor(...GOLD);doc.rect(14,Y,2,8,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(...DARK);
      doc.text('A. Input Parameters',20,Y+6);
      Y+=14;

      const inputs=[
        ['CGPA',`${S.cgpa.toFixed(1)} / 10.0`,S.cgpa/10],
        ['Coding Skills',`${S.code} / 10`,S.code/10],
        ['Communication',`${S.comm} / 10`,S.comm/10],
        ['Aptitude',`${S.apt} / 10`,S.apt/10],
        ['DSA / Problem Solving',`${S.dsa} / 10`,S.dsa/10],
        ['Projects Completed',String(S.proj),Math.min(1,S.proj/5)],
        ['Internships Done',String(S.intern),Math.min(1,S.intern/2)],
        ['Active Backlogs',S.backlogs===0?'None':String(S.backlogs),0],
      ];

      inputs.forEach((row,i)=>{
        const ry=Y+i*11;
        if(i%2===0){doc.setFillColor(250,248,243);doc.rect(14,ry-3,PW-28,11,'F')}
        doc.setFont('helvetica','normal');doc.setFontSize(8.5);doc.setTextColor(...MID);
        doc.text(row[0],18,ry+4);
        // Value
        const isBacklog=row[0]==='Active Backlogs'&&S.backlogs>0;
        doc.setFont('helvetica','bold');doc.setFontSize(8.5);
        doc.setTextColor(...(isBacklog?[190,70,60]:DARK));
        doc.text(row[1],PW-18,ry+4,{align:'right'});
        // Mini bar (skip backlogs)
        if(row[0]!=='Active Backlogs'){
          const bx=95,bw=60,bh=2;
          doc.setFillColor(225,215,195);doc.rect(bx,ry+1,bw,bh,'F');
          doc.setFillColor(...GOLD);doc.rect(bx,ry+1,bw*row[2],bh,'F');
        }
      });

      Y+=inputs.length*11+10;
      doc.setDrawColor(220,210,190);doc.setLineWidth(0.3);doc.line(14,Y,PW-14,Y);
      Y+=10;

      // ── SECTION B: SCORE BREAKDOWN ─────────────
      doc.setFillColor(...GOLD);doc.rect(14,Y,2,8,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(...DARK);
      doc.text('B. Score Contribution Analysis',20,Y+6);
      Y+=14;

      const breakdown=[
        {name:'CGPA',pts:S.cgpa>=9?28:S.cgpa>=8.5?25:S.cgpa>=8?22:S.cgpa>=7.5?18:S.cgpa>=7?14:S.cgpa>=6.5?10:S.cgpa>=6?6:2,max:28},
        {name:'Coding Skills',pts:Math.round(S.code*1.8),max:18},
        {name:'Communication',pts:Math.round(S.comm*1.2),max:12},
        {name:'Aptitude',pts:Math.round(S.apt),max:10},
        {name:'DSA / Problem Solving',pts:Math.round(S.dsa*1.4),max:14},
        {name:'Projects',pts:Math.min(S.proj,5)*2,max:10},
        {name:'Internships',pts:Math.min(S.intern,2)*4,max:8},
        {name:'Backlog Penalty',pts:-S.backlogs*7,max:0,penalty:true},
      ];

      breakdown.forEach((b,i)=>{
        const ry=Y+i*11;
        if(i%2===0){doc.setFillColor(250,248,243);doc.rect(14,ry-3,PW-28,11,'F')}
        doc.setFont('helvetica','normal');doc.setFontSize(8.5);doc.setTextColor(...MID);
        doc.text(b.name,18,ry+4);
        // pts
        const isNeg=b.pts<0;
        doc.setFont('helvetica','bold');doc.setFontSize(8.5);
        doc.setTextColor(...(isNeg?[190,70,60]:b.pts>0?[60,140,90]:MID));
        doc.text((isNeg?'':'+')+(b.pts)+' pts',PW-18,ry+4,{align:'right'});
        if(!b.penalty){
          const bx=95,bw=55,bh=2;
          doc.setFillColor(225,215,195);doc.rect(bx,ry+1,bw,bh,'F');
          doc.setFillColor(...GOLD);doc.rect(bx,ry+1,bw*(b.pts/b.max),bh,'F');
          doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(...MUTED);
          doc.text(`${b.pts}/${b.max}`,bx+bw+3,ry+4);
        }
      });

      Y+=breakdown.length*11+4;
      // Total row
      doc.setFillColor(...DARK);doc.rect(14,Y,PW-28,12,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(9);doc.setTextColor(255,255,255);
      doc.text('TOTAL PLACEMENT SCORE',18,Y+8);
      doc.setTextColor(...scoreCol.map(v=>Math.min(255,v+80)));
      doc.text(`${score} / 100`,PW-18,Y+8,{align:'right'});
      Y+=22;

      doc.setDrawColor(220,210,190);doc.setLineWidth(0.3);doc.line(14,Y,PW-14,Y);
      Y+=10;

      // ── SECTION C: IMPROVEMENT ROADMAP ──────────
      doc.setFillColor(...GOLD);doc.rect(14,Y,2,8,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(...DARK);
      doc.text('C. Improvement Roadmap',20,Y+6);
      Y+=14;

      const tips=[];
      if(S.cgpa<7)tips.push({pri:'HIGH PRIORITY',col:[190,70,60],text:'Improve CGPA above 7.5. Many companies have strict cutoffs. Attend extra classes, clear doubts, and prioritize scoring subjects.'});
      if(S.code<6)tips.push({pri:'HIGH PRIORITY',col:[190,70,60],text:'Practice coding daily — 2 to 3 problems on LeetCode every day. Start with Easy level, move to Medium over 8 weeks.'});
      if(S.dsa<6)tips.push({pri:'HIGH PRIORITY',col:[190,70,60],text:'Master Data Structures & Algorithms. Focus on Arrays, Trees, Graphs, and Dynamic Programming. Use Striver\'s SDE Sheet as your roadmap.'});
      if(S.comm<6)tips.push({pri:'ACTION NEEDED',col:[160,120,40],text:'Improve communication through mock GDs and PI practice. Record yourself and review. Join public speaking clubs if possible.'});
      if(S.apt<5)tips.push({pri:'ACTION NEEDED',col:[160,120,40],text:'Dedicate 30 minutes daily to aptitude on IndiaBix or PrepInsta. Rotate between Quantitative, Logical, and Verbal sections.'});
      if(S.proj<2)tips.push({pri:'ACTION NEEDED',col:[160,120,40],text:'Build 2 to 3 strong projects (full-stack web apps, ML models, or mobile apps). Deploy them and maintain a clear GitHub README.'});
      if(S.intern===0)tips.push({pri:'ACTION NEEDED',col:[160,120,40],text:'Apply for at least one internship on Internshala, LinkedIn, or AngelList. Even a 4-week internship adds real credibility to your resume.'});
      if(S.backlogs>0)tips.push({pri:'URGENT',col:[190,70,60],text:'Clear ALL backlogs immediately. Most companies auto-reject profiles with active backlogs at the screening stage before any human review.'});
      if(tips.length===0)tips.push({pri:'EXCELLENT',col:[60,140,90],text:'Your profile is strong! Focus on FAANG-style interviews, competitive programming, LinkedIn networking, and polishing your resume.'});

      tips.forEach((t,i)=>{
        if(Y>PH-40){
          // Add page
          doc.addPage();doc.setFillColor(255,255,255);doc.rect(0,0,PW,PH,'F');
          doc.setFillColor(...GOLD);doc.rect(0,0,PW,14,'F');
          doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(255,255,255);
          doc.text('PlaceIQ — Improvement Roadmap (continued)',14,9);
          Y=24;
        }
        // Tip card
        const th=20;
        doc.setFillColor(252,250,245);doc.rect(14,Y,PW-28,th,'F');
        doc.setFillColor(...t.col);doc.rect(14,Y,3,th,'F');
        doc.setDrawColor(225,215,195);doc.setLineWidth(0.3);doc.rect(14,Y,PW-28,th,'S');

        // Priority tag
        doc.setFont('helvetica','bold');doc.setFontSize(6.5);doc.setTextColor(...t.col);
        doc.text(t.pri,20,Y+7);

        // Tip body
        doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(...MID);
        const lines=doc.splitTextToSize(t.text,PW-46);
        doc.text(lines,20,Y+14);

        Y+=th+5;
      });

      // Footer page 2
      doc.setFillColor(...DARK);doc.rect(0,PH-20,PW,20,'F');
      doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(180,170,150);
      doc.text('PlaceIQ — Career Intelligence System  |  Confidential — For Candidate Use Only',PW/2,PH-11,{align:'center'});
      doc.setTextColor(...GOLD);doc.text('Page 2 of 2',PW-20,PH-11,{align:'right'});

      // SAVE
      const fname=`PlaceIQ_Report_${S.name.replace(/\s+/g,'_')}_${score}pts.pdf`;
      doc.save(fname);
      btn.textContent='✓ Downloaded!';btn.disabled=false;
      setTimeout(()=>btn.textContent='⬇ Download PDF Report',3000);
    }catch(err){
      console.error(err);
      btn.textContent='⬇ Download PDF Report';btn.disabled=false;
      alert('Error generating PDF. Please try again.');
    }
  },120);
}
</script>
</body>
</html>