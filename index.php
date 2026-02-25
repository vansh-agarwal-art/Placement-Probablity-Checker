<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PlaceIQ — Placement Analytics Platform</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@300;400;500&family=IBM+Plex+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
:root{
  --bg:#080c14;--surface:#0d1220;--card:#111827;--card2:#0f1a2e;
  --border:rgba(56,139,253,0.15);--border2:rgba(56,139,253,0.28);
  --blue:#388bfd;--blue-bright:#58a6ff;--blue-dim:rgba(56,139,253,0.08);
  --green:#3fb950;--green-dim:rgba(63,185,80,0.12);
  --red:#f85149;--red-dim:rgba(248,81,73,0.12);
  --amber:#d29922;--amber-dim:rgba(210,153,34,0.12);
  --text:#e6edf3;--text2:#8b949e;--text3:#484f58;
  --mono:'IBM Plex Mono',monospace;--sans:'IBM Plex Sans',sans-serif;--serif:'IBM Plex Serif',serif;
}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:var(--sans);background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;cursor:none;}

/* CURSOR */
#cur-x,#cur-y{position:fixed;pointer-events:none;z-index:9999;background:rgba(56,139,253,0.7);transition:opacity .2s;}
#cur-x{width:1px;height:24px;transform:translateX(-50%);}
#cur-y{height:1px;width:24px;transform:translateY(-50%);}
#cur-dot{position:fixed;pointer-events:none;z-index:10000;width:5px;height:5px;background:var(--blue-bright);border-radius:50%;transform:translate(-50%,-50%);box-shadow:0 0 8px var(--blue),0 0 20px rgba(56,139,253,.4);}
#cur-ring{position:fixed;pointer-events:none;z-index:9998;width:28px;height:28px;border:1px solid rgba(56,139,253,.45);border-radius:50%;transform:translate(-50%,-50%);transition:width .2s,height .2s,border-color .2s;}
.hov #cur-ring{width:20px;height:20px;border-color:var(--blue);}

/* TICKER */
.ticker-bar{position:fixed;bottom:0;left:0;right:0;z-index:300;height:32px;background:var(--blue);overflow:hidden;display:flex;align-items:center;}
.ticker-label{flex-shrink:0;padding:0 16px;font-family:var(--mono);font-size:.65rem;font-weight:500;color:#fff;letter-spacing:.12em;background:#1a3a6e;height:100%;display:flex;align-items:center;white-space:nowrap;border-right:1px solid rgba(255,255,255,.2);}
.ticker-scroll{display:flex;align-items:center;animation:ticker 55s linear infinite;white-space:nowrap;}
.ticker-item{font-family:var(--mono);font-size:.68rem;color:#fff;padding:0 32px;display:flex;align-items:center;gap:10px;}
.ticker-item::after{content:'◆';font-size:.4rem;color:rgba(255,255,255,.4);}
.t-name{font-weight:600;}.t-co{color:rgba(255,255,255,.75);}.t-pkg{color:#a8f0b8;font-weight:500;}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* FLOATING HEADLINES */
#headlines-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;}
.hl{position:absolute;white-space:nowrap;font-family:var(--mono);font-size:.62rem;color:rgba(56,139,253,.055);animation:hlDrift linear infinite;}
@keyframes hlDrift{0%{opacity:0;transform:translateY(0)}5%{opacity:1}95%{opacity:1}100%{opacity:0;transform:translateY(-100vh) translateX(var(--drift))}}

/* TOPBAR */
.topbar{position:fixed;top:0;left:0;right:0;z-index:200;height:48px;display:flex;align-items:center;background:rgba(8,12,20,.96);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 24px;gap:0;}
.tb-logo{font-family:var(--mono);font-size:.85rem;font-weight:500;color:var(--blue-bright);letter-spacing:.08em;padding-right:20px;margin-right:20px;border-right:1px solid var(--border);}
.tb-logo span{color:var(--text2);font-weight:300;}
.tb-tabs{display:flex;gap:0;height:48px;}
.tb-tab{padding:0 18px;font-size:.75rem;font-weight:500;color:var(--text2);letter-spacing:.04em;cursor:pointer;border-right:1px solid var(--border);display:flex;align-items:center;transition:all .2s;border-bottom:2px solid transparent;}
.tb-tab:hover{color:var(--text);background:var(--blue-dim);}
.tb-tab.active{color:var(--blue-bright);border-bottom-color:var(--blue);}
.tb-right{margin-left:auto;display:flex;align-items:center;gap:16px;}
.tb-stat{font-family:var(--mono);font-size:.65rem;color:var(--text2);display:flex;align-items:center;gap:6px;padding:4px 10px;border:1px solid var(--border);background:var(--blue-dim);}
.tb-stat .val{color:var(--green);font-weight:500;}
.live-indicator{display:flex;align-items:center;gap:5px;font-family:var(--mono);font-size:.6rem;color:var(--green);letter-spacing:.1em;}
.live-dot{width:5px;height:5px;border-radius:50%;background:var(--green);animation:livePulse 1.5s infinite;}
@keyframes livePulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(63,185,80,.5)}50%{opacity:.6;box-shadow:0 0 0 4px rgba(63,185,80,0)}}

/* PAGES */
.page{display:none;min-height:100vh;position:relative;z-index:10;}
.page.active{display:block;}

/* LANDING */
.landing-layout{display:grid;grid-template-columns:260px 1fr 280px;gap:0;min-height:calc(100vh - 80px);}
.sidebar-left{border-right:1px solid var(--border);padding:24px 0;background:var(--surface);}
.sidebar-section{padding:0 20px 20px;margin-bottom:4px;}
.sidebar-label{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.15em;text-transform:uppercase;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.stat-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(56,139,253,.06);}
.stat-row:last-child{border-bottom:none;}
.sr-label{font-size:.72rem;color:var(--text2);}
.sr-val{font-family:var(--mono);font-size:.72rem;font-weight:500;color:var(--green);}
.sr-val.red{color:var(--red);}.sr-val.blue{color:var(--blue-bright);}
.mini-chart{height:48px;display:flex;align-items:flex-end;gap:3px;margin-top:10px;}
.bar-col{flex:1;border-radius:1px;opacity:.7;animation:barGrow .6s ease backwards;}
@keyframes barGrow{from{height:0}}
.bar-col.g{background:var(--green);}.bar-col.b{background:var(--blue);}

/* CENTER */
.landing-center{padding:40px 48px;position:relative;}
.hero-tag{display:inline-flex;align-items:center;gap:8px;font-family:var(--mono);font-size:.65rem;color:var(--blue-bright);letter-spacing:.12em;margin-bottom:28px;padding:5px 12px;border:1px solid var(--border2);background:var(--blue-dim);}
.hero-tag::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--blue);animation:livePulse 1.5s infinite;}
.hero-h1{font-family:var(--serif);font-size:clamp(2.8rem,5vw,4.2rem);font-weight:700;line-height:1.1;color:var(--text);margin-bottom:8px;}
.hero-h1 em{font-style:italic;color:var(--blue-bright);}
.hero-h2{font-family:var(--serif);font-size:clamp(2.8rem,5vw,4.2rem);font-weight:300;font-style:italic;color:var(--text2);line-height:1.1;margin-bottom:28px;}
.hero-desc{font-size:.95rem;color:var(--text2);line-height:1.8;max-width:520px;margin-bottom:36px;font-weight:300;}
.btn-primary{display:inline-flex;align-items:center;gap:10px;padding:13px 32px;background:var(--blue);color:#fff;font-family:var(--mono);font-size:.78rem;font-weight:500;letter-spacing:.08em;border:none;cursor:pointer;transition:all .25s;text-transform:uppercase;}
.btn-primary:hover{background:var(--blue-bright);transform:translateY(-1px);box-shadow:0 8px 24px rgba(56,139,253,.35);}
.btn-ghost{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;background:none;color:var(--text2);font-family:var(--mono);font-size:.78rem;font-weight:400;letter-spacing:.06em;border:1px solid var(--border2);cursor:pointer;transition:all .25s;text-transform:uppercase;}
.btn-ghost:hover{color:var(--text);border-color:var(--blue);background:var(--blue-dim);}
.hero-btns{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:48px;}
.platform-metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border:1px solid var(--border);margin-top:12px;}
.pm-cell{padding:18px 20px;border-right:1px solid var(--border);transition:background .2s;}
.pm-cell:last-child{border-right:none;}
.pm-cell:hover{background:var(--blue-dim);}
.pm-num{font-family:var(--mono);font-size:1.6rem;font-weight:500;color:var(--blue-bright);line-height:1;}
.pm-label{font-size:.7rem;color:var(--text2);margin-top:4px;}
.news-cards{margin-top:32px;display:flex;flex-direction:column;gap:8px;}
.nc{padding:12px 16px;border:1px solid var(--border);background:var(--card);display:flex;gap:14px;align-items:center;transition:all .2s;cursor:default;animation:ncFade .5s ease backwards;}
.nc:hover{border-color:var(--border2);background:var(--card2);}
.nc-badge{flex-shrink:0;font-family:var(--mono);font-size:.55rem;padding:2px 6px;font-weight:500;letter-spacing:.08em;white-space:nowrap;}
.nc-badge.placed{background:var(--green-dim);color:var(--green);border:1px solid rgba(63,185,80,.3);}
.nc-badge.hot{background:var(--red-dim);color:var(--red);border:1px solid rgba(248,81,73,.3);}
.nc-badge.new{background:var(--blue-dim);color:var(--blue-bright);border:1px solid var(--border2);}
.nc-text{font-size:.8rem;color:var(--text2);line-height:1.4;flex:1;}
.nc-text strong{color:var(--text);font-weight:600;}
.nc-pkg{font-family:var(--mono);font-size:.7rem;color:var(--green);font-weight:500;flex-shrink:0;}
.nc-time{font-family:var(--mono);font-size:.6rem;color:var(--text3);flex-shrink:0;}
@keyframes ncFade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

/* RIGHT SIDEBAR */
.sidebar-right{border-left:1px solid var(--border);background:var(--surface);padding:20px 0;}
.sr-section{padding:0 16px 20px;border-bottom:1px solid var(--border);margin-bottom:16px;}
.sr-section:last-child{border-bottom:none;}
.sr-head{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.15em;text-transform:uppercase;margin-bottom:12px;}
.company-list{display:flex;flex-direction:column;gap:6px;}
.co-row{display:flex;justify-content:space-between;align-items:center;padding:6px 8px;background:var(--card);border:1px solid var(--border);font-size:.72rem;transition:background .2s;}
.co-row:hover{background:var(--card2);}
.co-name{font-weight:500;color:var(--text);}
.co-pkg{font-family:var(--mono);font-size:.65rem;color:var(--green);}
.notif-list{display:flex;flex-direction:column;gap:6px;}
.notif{padding:8px 10px;background:var(--card);border-left:2px solid var(--green);font-size:.72rem;color:var(--text2);line-height:1.5;}
.notif strong{color:var(--text);}
.notif.warn{border-left-color:var(--amber);}
.notif.info{border-left-color:var(--blue);}

/* FORM PAGE */
.form-layout{display:grid;grid-template-columns:220px 1fr;gap:0;min-height:calc(100vh - 48px);}
.form-sidebar{border-right:1px solid var(--border);background:var(--surface);padding:24px 0;}
.fs-step{padding:12px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border);transition:all .2s;cursor:default;opacity:.4;}
.fs-step.active{opacity:1;background:var(--blue-dim);border-right:2px solid var(--blue);}
.fs-step.done{opacity:.7;}
.fs-num{width:24px;height:24px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;font-family:var(--mono);font-size:.65rem;color:var(--text2);flex-shrink:0;}
.fs-step.active .fs-num{border-color:var(--blue);color:var(--blue-bright);background:var(--blue-dim);}
.fs-step.done .fs-num{background:var(--green-dim);border-color:var(--green);color:var(--green);}
.fs-title{font-size:.75rem;font-weight:600;color:var(--text);}
.fs-sub{font-size:.65rem;color:var(--text2);margin-top:2px;}
.form-sidebar-news{padding:20px 16px;}
.fsn-head{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.12em;margin-bottom:10px;}
.fsn-item{padding:8px 0;border-bottom:1px solid var(--border);font-size:.68rem;color:var(--text2);line-height:1.5;}
.fsn-item:last-child{border-bottom:none;}
.fsn-item strong{color:var(--green);}
.form-main{padding:32px 48px;}
.prog-wrap{margin-bottom:32px;}
.prog-meta{display:flex;justify-content:space-between;margin-bottom:8px;}
.prog-lbl{font-family:var(--mono);font-size:.6rem;color:var(--text2);letter-spacing:.1em;}
.prog-track{height:2px;background:var(--border);}
.prog-fill{height:100%;background:var(--blue);transition:width .5s ease;}
.qpanel{background:var(--card);border:1px solid var(--border);overflow:hidden;position:relative;}
.qpanel::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,var(--blue),transparent);}
.qp-header{padding:20px 24px 18px;border-bottom:1px solid var(--border);display:flex;gap:16px;align-items:flex-start;}
.qp-step-tag{font-family:var(--mono);font-size:.58rem;color:var(--blue-bright);letter-spacing:.15em;background:var(--blue-dim);padding:3px 8px;border:1px solid var(--border2);flex-shrink:0;margin-top:3px;}
.qp-category{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:5px;}
.qp-question{font-family:var(--serif);font-size:1.25rem;font-weight:700;color:var(--text);line-height:1.3;}
.qp-body{padding:28px 24px;}
.qp-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:rgba(0,0,0,.2);}
.step{display:none;}
.step.active{display:block;animation:stepIn .35s ease;}
@keyframes stepIn{from{opacity:0;transform:translateX(16px)}to{opacity:1;transform:none}}
.field-box{position:relative;}
.data-input{width:100%;padding:13px 16px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:.9rem;outline:none;transition:all .25s;}
.data-input:focus{border-color:var(--blue);background:var(--card2);box-shadow:0 0 0 2px rgba(56,139,253,.15);}
.data-input::placeholder{color:var(--text3);font-weight:300;}
.field-hint{font-family:var(--mono);font-size:.62rem;color:var(--text3);margin-top:8px;letter-spacing:.04em;}
.cgpa-display{text-align:center;padding:16px 0 24px;}
.cgpa-big{font-family:var(--mono);font-size:6.5rem;font-weight:300;color:var(--blue-bright);line-height:1;letter-spacing:-.04em;transition:color .3s;text-shadow:0 0 40px rgba(56,139,253,.3);}
.cgpa-unit{font-family:var(--mono);font-size:.75rem;color:var(--text3);letter-spacing:.1em;margin-top:6px;}
.cgpa-grade-bar{margin-top:16px;display:flex;gap:2px;}
.cgb-seg{flex:1;height:4px;background:var(--border);transition:background .3s;}
input[type=range]{-webkit-appearance:none;width:100%;height:2px;background:linear-gradient(90deg,var(--blue) var(--pct,50%),var(--border) var(--pct,50%));outline:none;cursor:pointer;}
input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:16px;height:16px;background:var(--blue-bright);cursor:grab;border:2px solid var(--bg);box-shadow:0 0 8px rgba(56,139,253,.5);}
input[type=range]::-webkit-slider-thumb:active{cursor:grabbing;}
.range-row{display:flex;justify-content:space-between;margin-top:6px;}
.rr-val{font-family:var(--mono);font-size:.6rem;color:var(--text3);}
.skill-matrix{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.sk-card{background:var(--surface);border:1px solid var(--border);padding:14px;transition:border-color .2s;}
.sk-card:hover{border-color:var(--border2);}
.sk-top{display:flex;justify-content:space-between;margin-bottom:8px;}
.sk-name{font-size:.72rem;color:var(--text2);}
.sk-val{font-family:var(--mono);font-size:1rem;font-weight:500;color:var(--blue-bright);}
.sk-bar{height:2px;background:var(--border);margin-bottom:8px;}
.sk-fill{height:100%;background:var(--blue);transition:width .2s;}
.sk-card input[type=range]{height:1px;}
.sk-card input[type=range]::-webkit-slider-thumb{width:12px;height:12px;}
.cnt-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.cnt-card{background:var(--surface);border:1px solid var(--border);padding:20px 16px;text-align:center;transition:border-color .2s;}
.cnt-card:hover{border-color:var(--border2);}
.cnt-emoji{font-size:1.5rem;margin-bottom:6px;}
.cnt-label{font-family:var(--mono);font-size:.62rem;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;margin-bottom:14px;}
.cnt-row{display:flex;align-items:center;justify-content:center;gap:16px;}
.cnt-btn{width:28px;height:28px;border:1px solid var(--border2);background:none;color:var(--blue-bright);font-size:1rem;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;}
.cnt-btn:hover{background:var(--blue);color:#fff;border-color:var(--blue);}
.cnt-val{font-family:var(--mono);font-size:1.8rem;font-weight:300;color:var(--text);min-width:32px;}
.bl-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;}
.bl-opt{padding:12px 6px;border:1px solid var(--border);background:var(--surface);color:var(--text2);cursor:pointer;text-align:center;transition:all .2s;font-family:var(--mono);font-size:.72rem;}
.bl-opt:hover{border-color:var(--border2);color:var(--text);}
.bl-opt.sel{border-color:var(--blue);color:var(--blue-bright);background:var(--blue-dim);}
.bl-opt.danger.sel{border-color:var(--red);color:var(--red);background:var(--red-dim);}
.btn-back-nav{font-family:var(--mono);font-size:.7rem;color:var(--text2);background:none;border:1px solid var(--border);padding:9px 20px;cursor:pointer;transition:all .2s;letter-spacing:.06em;}
.btn-back-nav:hover{color:var(--text);border-color:var(--border2);}
.btn-fwd-nav{font-family:var(--mono);font-size:.7rem;color:#fff;background:var(--blue);border:none;padding:10px 24px;cursor:pointer;transition:all .2s;letter-spacing:.06em;font-weight:500;}
.btn-fwd-nav:hover{background:var(--blue-bright);}
.hint-label{font-family:var(--mono);font-size:.6rem;color:var(--text3);}

/* RESULT PAGE */
.result-layout{display:grid;grid-template-columns:1fr 300px;gap:0;min-height:calc(100vh - 48px);}
.result-main{padding:32px 40px;}
.result-sidebar{border-left:1px solid var(--border);background:var(--surface);padding:24px 16px;}
.score-hero{background:var(--card);border:1px solid var(--border);padding:32px;margin-bottom:16px;position:relative;overflow:hidden;display:grid;grid-template-columns:auto 1fr;gap:32px;align-items:center;}
.score-hero::before{content:'';position:absolute;top:0;left:0;bottom:0;width:3px;background:var(--score-c,var(--blue));}
.score-ring-wrap{width:140px;height:140px;position:relative;flex-shrink:0;}
.score-ring-wrap svg{width:140px;height:140px;transform:rotate(-90deg);}
.sr-bg{fill:none;stroke:var(--border);stroke-width:8;}
.sr-fill{fill:none;stroke-width:8;stroke-linecap:round;stroke-dasharray:345;stroke-dashoffset:345;transition:stroke-dashoffset 1.5s cubic-bezier(.4,0,.2,1);}
.sr-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;}
.sr-num{font-family:var(--mono);font-size:2.4rem;font-weight:500;line-height:1;}
.sr-of{font-family:var(--mono);font-size:.6rem;color:var(--text3);margin-top:2px;}
.score-candidate{font-family:var(--mono);font-size:.6rem;color:var(--text3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px;}
.score-verdict{font-family:var(--serif);font-size:1.6rem;font-weight:700;margin-bottom:10px;}
.score-desc{font-size:.85rem;color:var(--text2);line-height:1.7;font-weight:300;}
.data-section{background:var(--card);border:1px solid var(--border);margin-bottom:14px;overflow:hidden;}
.ds-header{padding:10px 20px;border-bottom:1px solid var(--border);background:rgba(0,0,0,.2);display:flex;justify-content:space-between;}
.ds-title{font-family:var(--mono);font-size:.65rem;color:var(--text2);letter-spacing:.12em;text-transform:uppercase;}
.ds-subtitle{font-family:var(--mono);font-size:.6rem;color:var(--text3);}
.data-table{width:100%;}
.data-table tr:nth-child(even){background:rgba(0,0,0,.15);}
.data-table td{padding:9px 20px;font-size:.8rem;}
.dt-name{color:var(--text2);}
.dt-val{font-family:var(--mono);font-weight:500;color:var(--text);text-align:right;}
.dt-bar-cell{width:120px;}
.dt-bar{height:2px;background:var(--border);}
.dt-fill{height:100%;background:var(--blue);transition:width 1s ease;}
.dt-contrib{font-family:var(--mono);font-size:.72rem;text-align:right;}
.contrib-pos{color:var(--green);}.contrib-neg{color:var(--red);}
.tips-table{width:100%;}
.tips-table tr{border-bottom:1px solid var(--border);transition:background .2s;}
.tips-table tr:last-child{border-bottom:none;}
.tips-table tr:hover{background:rgba(56,139,253,.04);}
.tips-table td{padding:12px 20px;vertical-align:top;}
.tip-priority{font-family:var(--mono);font-size:.58rem;letter-spacing:.08em;padding:2px 8px;white-space:nowrap;border:1px solid;margin-top:2px;display:inline-block;}
.tp-high{color:var(--red);border-color:rgba(248,81,73,.4);background:var(--red-dim);}
.tp-med{color:var(--amber);border-color:rgba(210,153,34,.4);background:var(--amber-dim);}
.tp-ok{color:var(--green);border-color:rgba(63,185,80,.4);background:var(--green-dim);}
.tip-text-cell{font-size:.82rem;color:var(--text2);line-height:1.6;}
.tip-text-cell strong{color:var(--text);font-weight:600;}
.action-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;}
.act-btn{flex:1;min-width:140px;padding:12px 20px;font-family:var(--mono);font-size:.72rem;font-weight:500;letter-spacing:.07em;text-transform:uppercase;cursor:pointer;transition:all .25s;border:none;text-align:center;}
.act-primary{background:var(--blue);color:#fff;}
.act-primary:hover{background:var(--blue-bright);}
.act-secondary{background:none;color:var(--text2);border:1px solid var(--border2);}
.act-secondary:hover{color:var(--text);border-color:var(--blue);background:var(--blue-dim);}
.act-pdf{background:var(--green-dim);color:var(--green);border:1px solid rgba(63,185,80,.3);flex:1.5;}
.act-pdf:hover{background:rgba(63,185,80,.2);border-color:var(--green);}
.rs-card{background:var(--card);border:1px solid var(--border);margin-bottom:10px;padding:14px;}
.rs-head{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);}
.rs-item{display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid rgba(56,139,253,.06);font-size:.75rem;}
.rs-item:last-child{border-bottom:none;}
.rs-key{color:var(--text2);}.rs-val{font-family:var(--mono);font-weight:500;color:var(--text);}

/* LOADER */
#loader{display:none;position:fixed;inset:0;z-index:1000;background:var(--bg);align-items:center;justify-content:center;flex-direction:column;gap:20px;}
#loader.show{display:flex;}
.ld-bars{display:flex;gap:4px;align-items:flex-end;height:40px;}
.ld-bar{width:6px;background:var(--blue);border-radius:1px;animation:ldBar .8s ease-in-out infinite alternate;}
.ld-bar:nth-child(1){animation-delay:0s;height:20px}.ld-bar:nth-child(2){animation-delay:.1s;height:32px}.ld-bar:nth-child(3){animation-delay:.2s;height:40px}.ld-bar:nth-child(4){animation-delay:.3s;height:28px}.ld-bar:nth-child(5){animation-delay:.4s;height:16px}
@keyframes ldBar{to{opacity:.3;transform:scaleY(.4)}}
.ld-text{font-family:var(--mono);font-size:.75rem;color:var(--text2);letter-spacing:.12em;}
.ld-sub{font-family:var(--mono);font-size:.62rem;color:var(--text3);letter-spacing:.08em;text-align:center;}
.ld-track{width:260px;height:1px;background:var(--border);}
.ld-fill{height:100%;background:var(--blue);animation:ldFill 1.8s ease forwards;}
@keyframes ldFill{from{width:0}to{width:100%}}
.cfi{position:fixed;pointer-events:none;z-index:999;animation:cfiFall linear forwards;}
@keyframes cfiFall{0%{opacity:1;transform:translate(0,0) rotate(0deg)}100%{opacity:0;transform:translate(var(--tx),110vh) rotate(var(--r))}}

.field-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;}
.field-opt{padding:16px 12px;background:var(--surface);border:1px solid var(--border);text-align:center;cursor:pointer;transition:all .2s;}
.field-opt:hover{border-color:var(--border2);background:var(--card);}
.field-opt.sel{border-color:var(--blue);background:var(--blue-dim);}
.fo-icon{font-size:1.6rem;margin-bottom:6px;}
.fo-title{font-size:.78rem;font-weight:600;color:var(--text);margin-bottom:3px;}
.fo-sub{font-size:.62rem;color:var(--text2);}
@media(max-width:900px){.field-grid{grid-template-columns:repeat(2,1fr);}.roadmap-layout{grid-template-columns:1fr;}.roadmap-sidebar{display:none;}.phase-body{grid-template-columns:1fr;}}

.roadmap-layout{display:grid;grid-template-columns:260px 1fr;gap:0;min-height:calc(100vh - 80px);}
.roadmap-sidebar{border-right:1px solid var(--border);background:var(--surface);padding:24px 0;position:sticky;top:48px;height:calc(100vh - 48px);overflow-y:auto;}
.rms-section{padding:0 16px 20px;border-bottom:1px solid var(--border);margin-bottom:16px;}
.rms-head{font-family:var(--mono);font-size:.58rem;color:var(--text3);letter-spacing:.15em;text-transform:uppercase;margin-bottom:12px;}
.field-badge{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;background:var(--blue-dim);border:1px solid var(--border2);width:100%;margin-bottom:12px;}
.field-icon{font-size:1.4rem;}
.field-title{font-size:.85rem;font-weight:600;color:var(--text);}
.field-sub{font-size:.65rem;color:var(--text2);margin-top:2px;}
.rms-gap-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid rgba(56,139,253,.06);}
.rms-gap-label{font-size:.7rem;color:var(--text2);}
.rms-gap-val{font-family:var(--mono);font-size:.7rem;font-weight:500;}
.rms-gap-val.ok{color:var(--green);}.rms-gap-val.warn{color:var(--amber);}.rms-gap-val.bad{color:var(--red);}
.phase-nav-item{padding:10px 16px;display:flex;align-items:center;gap:10px;cursor:pointer;transition:all .2s;border-left:2px solid transparent;font-size:.78rem;color:var(--text2);}
.phase-nav-item:hover{background:var(--blue-dim);color:var(--text);}
.phase-nav-item.active{border-left-color:var(--blue);background:var(--blue-dim);color:var(--blue-bright);}
.pni-dot{width:8px;height:8px;border-radius:50%;background:var(--border);flex-shrink:0;}
.phase-nav-item.active .pni-dot{background:var(--blue-bright);}
.phase-nav-item.done .pni-dot{background:var(--green);}
.roadmap-main{padding:32px 40px 60px;overflow-y:auto;}
.rm-hero{background:var(--card);border:1px solid var(--border);padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;}
.rm-hero::before{content:'';position:absolute;top:0;left:0;bottom:0;width:3px;background:var(--blue);}
.rm-hero-tag{font-family:var(--mono);font-size:.6rem;color:var(--blue-bright);letter-spacing:.12em;margin-bottom:10px;}
.rm-hero-title{font-family:var(--serif);font-size:1.8rem;font-weight:700;color:var(--text);margin-bottom:6px;}
.rm-hero-sub{font-size:.9rem;color:var(--text2);line-height:1.7;font-weight:300;max-width:600px;}
.rm-meta-row{display:flex;gap:20px;margin-top:16px;flex-wrap:wrap;}
.rm-meta-chip{font-family:var(--mono);font-size:.65rem;color:var(--text2);padding:4px 10px;border:1px solid var(--border);background:var(--blue-dim);}
.phases-container{display:flex;flex-direction:column;gap:0;}
.phase-block{background:var(--card);border:1px solid var(--border);margin-bottom:16px;overflow:hidden;position:relative;}
.phase-block::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;}
.phase-1::before{background:var(--blue);}
.phase-2::before{background:var(--amber);}
.phase-3::before{background:#a855f7;}
.phase-4::before{background:var(--green);}
.phase-header{padding:18px 24px;display:flex;align-items:center;gap:16px;border-bottom:1px solid var(--border);cursor:pointer;user-select:none;}
.phase-num{width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-family:var(--mono);font-size:.75rem;font-weight:600;border:1px solid;flex-shrink:0;}
.phase-1 .phase-num{border-color:var(--blue);color:var(--blue-bright);background:var(--blue-dim);}
.phase-2 .phase-num{border-color:var(--amber);color:var(--amber);background:var(--amber-dim);}
.phase-3 .phase-num{border-color:#a855f7;color:#c084fc;background:rgba(168,85,247,.1);}
.phase-4 .phase-num{border-color:var(--green);color:var(--green);background:var(--green-dim);}
.phase-meta{flex:1;}
.phase-title-text{font-family:var(--serif);font-size:1.1rem;font-weight:700;color:var(--text);}
.phase-subtitle{font-size:.72rem;color:var(--text2);margin-top:2px;}
.phase-duration{font-family:var(--mono);font-size:.65rem;color:var(--text3);padding:3px 10px;border:1px solid var(--border);background:rgba(0,0,0,.2);}
.phase-toggle{font-family:var(--mono);font-size:.8rem;color:var(--text3);transition:transform .3s;}
.phase-body{padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.pb-section{background:var(--surface);border:1px solid var(--border);padding:16px;}
.pbs-title{font-family:var(--mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.phase-1 .pbs-title{color:var(--blue);}
.phase-2 .pbs-title{color:var(--amber);}
.phase-3 .pbs-title{color:#a855f7;}
.phase-4 .pbs-title{color:var(--green);}
.skill-chip{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border:1px solid var(--border);font-size:.72rem;color:var(--text2);margin:3px 2px;background:var(--card);transition:all .2s;}
.skill-chip.gap{border-color:rgba(248,81,73,.4);color:var(--red);background:var(--red-dim);}
.skill-chip.gap::before{content:'⚠';font-size:.65rem;}
.skill-chip.strong{border-color:rgba(63,185,80,.3);color:var(--green);background:var(--green-dim);}
.skill-chip.strong::before{content:'✓ ';}
.resource-item{padding:8px 0;border-bottom:1px solid rgba(56,139,253,.06);display:flex;gap:10px;align-items:flex-start;}
.resource-item:last-child{border-bottom:none;}
.res-icon{font-size:.9rem;flex-shrink:0;margin-top:1px;}
.res-text{font-size:.75rem;color:var(--text2);line-height:1.5;}
.res-text strong{color:var(--text);}
.res-type{font-family:var(--mono);font-size:.55rem;color:var(--text3);letter-spacing:.08em;display:block;margin-top:2px;}
.milestone-list{list-style:none;}
.milestone-list li{padding:7px 0;border-bottom:1px solid rgba(56,139,253,.06);font-size:.78rem;color:var(--text2);display:flex;align-items:flex-start;gap:8px;line-height:1.5;}
.milestone-list li:last-child{border-bottom:none;}
.milestone-list li::before{content:'→';font-family:var(--mono);font-size:.7rem;color:var(--text3);flex-shrink:0;margin-top:2px;}
.gap-alert{background:var(--red-dim);border:1px solid rgba(248,81,73,.3);border-left:3px solid var(--red);padding:10px 14px;margin-bottom:16px;font-size:.78rem;color:#f87171;line-height:1.6;}
.gap-alert strong{color:var(--red);}
.full-width{grid-column:1/-1;}
.rm-action-row{display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;}
#landing{padding:80px 0 60px;}

@media(max-width:900px){
  .landing-layout,.form-layout,.result-layout{grid-template-columns:1fr;}
  .sidebar-left,.sidebar-right,.form-sidebar,.result-sidebar{display:none;}
  .form-main,.result-main{padding:24px 20px;}
  .landing-center{padding:32px 20px;}
  .skill-matrix,.cnt-grid,.bl-grid{grid-template-columns:1fr 1fr;}
  .bl-grid{grid-template-columns:repeat(3,1fr);}
  .score-hero{grid-template-columns:1fr;text-align:center;}
  .score-ring-wrap{margin:0 auto;}
  .platform-metrics{grid-template-columns:1fr 1fr 1fr;}
  .topbar{padding:0 16px;}
}
</style>
</head>
<body>
<div id="cur-x"></div><div id="cur-y"></div><div id="cur-dot"></div><div id="cur-ring"></div>
<div id="headlines-bg"></div>
<div id="loader">
  <div class="ld-bars"><div class="ld-bar"></div><div class="ld-bar"></div><div class="ld-bar"></div><div class="ld-bar"></div><div class="ld-bar"></div></div>
  <div class="ld-text">ANALYZING PROFILE</div>
  <div class="ld-sub" id="ld-sub">Initializing assessment engine...</div>
  <div class="ld-track"><div class="ld-fill" id="ld-fill"></div></div>
</div>
<div id="cfwrap"></div>

<div class="ticker-bar">
  <div class="ticker-label">LIVE PLACEMENTS</div>
  <div class="ticker-scroll" id="ticker"></div>
</div>

<div class="topbar">
  <div class="tb-logo">PlaceIQ <span>Analytics</span></div>
  <div class="tb-tabs">
    <div class="tb-tab active" onclick="showPage('landing')">Dashboard</div>
    <div class="tb-tab" onclick="startAssessment()">Assessment</div>
    <div class="tb-tab" id="tab-result" style="display:none" onclick="showPage('result-page')">My Report</div>
    <div class="tb-tab" id="tab-roadmap" style="display:none" onclick="showRoadmapPage()">🗺 Roadmap</div>
  </div>
  <div class="tb-right">
    <div class="tb-stat">Placements Today <span class="val" id="tb-count">142</span></div>
    <div class="live-indicator"><span class="live-dot"></span>LIVE</div>
    <div class="tb-stat"><span class="val" id="tb-time"></span></div>
  </div>
</div>

<!-- LANDING -->
<div class="page active" id="landing">
  <div class="landing-layout">
    <div class="sidebar-left">
      <div class="sidebar-section">
        <div class="sidebar-label">Market Overview</div>
        <div class="stat-row"><span class="sr-label">Avg Package (Tech)</span><span class="sr-val">₹12.4 LPA</span></div>
        <div class="stat-row"><span class="sr-label">Top Package</span><span class="sr-val">₹45 LPA</span></div>
        <div class="stat-row"><span class="sr-label">Companies Hiring</span><span class="sr-val blue">284</span></div>
        <div class="stat-row"><span class="sr-label">Open Positions</span><span class="sr-val blue">4,820</span></div>
        <div class="stat-row"><span class="sr-label">Unplaced Students</span><span class="sr-val red">↑ 23%</span></div>
        <div class="stat-row"><span class="sr-label">CGPA Cutoff (avg)</span><span class="sr-val">7.0+</span></div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-label">Placement Rate by CGPA</div>
        <div class="mini-chart">
          <div class="bar-col b" style="height:10%;animation-delay:.1s"></div>
          <div class="bar-col b" style="height:22%;animation-delay:.15s"></div>
          <div class="bar-col b" style="height:38%;animation-delay:.2s"></div>
          <div class="bar-col g" style="height:56%;animation-delay:.25s"></div>
          <div class="bar-col g" style="height:70%;animation-delay:.3s"></div>
          <div class="bar-col g" style="height:82%;animation-delay:.35s"></div>
          <div class="bar-col g" style="height:91%;animation-delay:.4s"></div>
          <div class="bar-col g" style="height:96%;animation-delay:.45s"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:4px">
          <span style="font-family:var(--mono);font-size:.55rem;color:var(--text3)">5.0</span>
          <span style="font-family:var(--mono);font-size:.55rem;color:var(--text3)">10.0</span>
        </div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-label">Top Skills Demanded</div>
        <div class="stat-row"><span class="sr-label">Data Structures</span><span class="sr-val">94%</span></div>
        <div class="stat-row"><span class="sr-label">System Design</span><span class="sr-val">78%</span></div>
        <div class="stat-row"><span class="sr-label">SQL / Databases</span><span class="sr-val">71%</span></div>
        <div class="stat-row"><span class="sr-label">Communication</span><span class="sr-val">68%</span></div>
        <div class="stat-row"><span class="sr-label">Problem Solving</span><span class="sr-val">99%</span></div>
      </div>
    </div>
    <div class="landing-center">
      <div class="hero-tag">Placement Intelligence Platform — Batch 2025–26</div>
      <h1 class="hero-h1">Know Your <em>Placement Probability</em></h1>
      <p class="hero-h2">Before Companies Decide.</p>
      <p class="hero-desc">PlaceIQ analyzes your academic profile across 8 industry-standard parameters and gives you a precise probability score — the same factors top recruiters use to shortlist candidates.</p>
      <div class="hero-btns">
        <button class="btn-primary" onclick="startAssessment()">▶ Start Assessment</button>
        <button class="btn-ghost">View Sample Report</button>
      </div>
      <div class="platform-metrics">
        <div class="pm-cell"><div class="pm-num">8</div><div class="pm-label">Parameters Analyzed</div></div>
        <div class="pm-cell"><div class="pm-num">100</div><div class="pm-label">Point Score Scale</div></div>
        <div class="pm-cell"><div class="pm-num">PDF</div><div class="pm-label">Report Generated</div></div>
      </div>
      <div class="news-cards" id="news-cards"></div>
    </div>
    <div class="sidebar-right">
      <div class="sr-section">
        <div class="sr-head">Top Hiring Companies</div>
        <div class="company-list">
          <div class="co-row"><span class="co-name">Google</span><span class="co-pkg">₹40–50 LPA</span></div>
          <div class="co-row"><span class="co-name">Microsoft</span><span class="co-pkg">₹35–45 LPA</span></div>
          <div class="co-row"><span class="co-name">Amazon</span><span class="co-pkg">₹28–38 LPA</span></div>
          <div class="co-row"><span class="co-name">Infosys</span><span class="co-pkg">₹6–8 LPA</span></div>
          <div class="co-row"><span class="co-name">TCS</span><span class="co-pkg">₹7–10 LPA</span></div>
          <div class="co-row"><span class="co-name">Wipro</span><span class="co-pkg">₹6.5–8 LPA</span></div>
          <div class="co-row"><span class="co-name">Flipkart</span><span class="co-pkg">₹22–30 LPA</span></div>
          <div class="co-row"><span class="co-name">PhonePe</span><span class="co-pkg">₹18–25 LPA</span></div>
        </div>
      </div>
      <div class="sr-section">
        <div class="sr-head">Recent Alerts</div>
        <div class="notif-list">
          <div class="notif"><strong>Google</strong> campus drive registration closes in 3 days</div>
          <div class="notif warn"><strong>Warning:</strong> 3 backlogs = auto-reject at 80% companies</div>
          <div class="notif info"><strong>New:</strong> Infosys added 200 more seats for 2026 batch</div>
          <div class="notif"><strong>Tip:</strong> CGPA 8+ candidates get 3× more shortlists</div>
          <div class="notif warn"><strong>Deadline:</strong> Resume submissions close Friday for TCS NQT</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FORM PAGE -->
<div class="page" id="form-page">
  <div class="form-layout">
    <div class="form-sidebar">
      <div class="fs-step active" id="fss1"><div class="fs-num">01</div><div class="fs-info"><div class="fs-title">Identity</div><div class="fs-sub">Your name</div></div></div>
      <div class="fs-step" id="fss2"><div class="fs-num">02</div><div class="fs-info"><div class="fs-title">Academics</div><div class="fs-sub">CGPA score</div></div></div>
      <div class="fs-step" id="fss3"><div class="fs-num">03</div><div class="fs-info"><div class="fs-title">Skills</div><div class="fs-sub">Technical & soft</div></div></div>
      <div class="fs-step" id="fss4"><div class="fs-num">04</div><div class="fs-info"><div class="fs-title">Experience</div><div class="fs-sub">Projects & internships</div></div></div>
      <div class="fs-step" id="fss5"><div class="fs-num">05</div><div class="fs-info"><div class="fs-title">Backlogs</div><div class="fs-sub">Academic record</div></div></div>
      <div class="fs-step" id="fss6"><div class="fs-num">06</div><div class="fs-info"><div class="fs-title">Career Goal</div><div class="fs-sub">Target field</div></div></div>
      <div class="form-sidebar-news">
        <div class="fsn-head">While You Fill...</div>
        <div class="fsn-item"><strong>Priya Sharma</strong> placed at Microsoft — ₹42 LPA → CGPA 9.1</div>
        <div class="fsn-item"><strong>Rahul Gupta</strong> cracked Google with 0 backlogs and 8+ DSA rating</div>
        <div class="fsn-item"><strong>Ankit Verma</strong> missed TCS shortlist due to active backlog</div>
        <div class="fsn-item"><strong>Sneha Patel</strong> — 3 projects on GitHub → Amazon offer ₹32 LPA</div>
      </div>
    </div>
    <div class="form-main">
      <div class="prog-wrap">
        <div class="prog-meta"><span class="prog-lbl">ASSESSMENT PROGRESS</span><span class="prog-lbl" id="prog-txt">Step 1 of 6</span></div>
        <div class="prog-track"><div class="prog-fill" id="prog-fill" style="width:0%"></div></div>
      </div>
      <div class="step active" id="s1">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 01</span><div><div class="qp-category">Identity Verification</div><div class="qp-question">What is your full name?</div></div></div>
          <div class="qp-body"><div class="field-box"><input type="text" class="data-input" id="inp-name" placeholder="Enter your full name" maxlength="60" autocomplete="off"></div><div class="field-hint">// This identifies your assessment record and report file</div></div>
          <div class="qp-footer"><span class="hint-label">Press Enter to continue</span><button class="btn-fwd-nav" onclick="next(1)">CONTINUE →</button></div>
        </div>
      </div>
      <div class="step" id="s2">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 02</span><div><div class="qp-category">Academic Record</div><div class="qp-question">What is your current CGPA?</div></div></div>
          <div class="qp-body">
            <div class="cgpa-display"><div class="cgpa-big" id="cgpa-val">7.5</div><div class="cgpa-unit">OUT OF 10.0</div><div class="cgpa-grade-bar" id="cgpa-bar"></div></div>
            <input type="range" id="inp-cgpa" min="0" max="10" step="0.1" value="7.5" oninput="onCgpa(this.value)" style="--pct:75%">
            <div class="range-row"><span class="rr-val">0.0</span><span class="rr-val">5.0</span><span class="rr-val">10.0</span></div>
          </div>
          <div class="qp-footer"><button class="btn-back-nav" onclick="prev(2)">← BACK</button><button class="btn-fwd-nav" onclick="next(2)">CONTINUE →</button></div>
        </div>
      </div>
      <div class="step" id="s3">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 03</span><div><div class="qp-category">Skill Matrix</div><div class="qp-question">Rate your technical & soft skills</div></div></div>
          <div class="qp-body">
            <div class="skill-matrix">
              <div class="sk-card"><div class="sk-top"><span class="sk-name">💻 Coding</span><span class="sk-val" id="sv-code">5</span></div><div class="sk-bar"><div class="sk-fill" id="sf-code" style="width:50%"></div></div><input type="range" min="0" max="10" value="5" id="inp-code" oninput="skillUp('code',this.value)" style="--pct:50%"></div>
              <div class="sk-card"><div class="sk-top"><span class="sk-name">🗣️ Communication</span><span class="sk-val" id="sv-comm">5</span></div><div class="sk-bar"><div class="sk-fill" id="sf-comm" style="width:50%"></div></div><input type="range" min="0" max="10" value="5" id="inp-comm" oninput="skillUp('comm',this.value)" style="--pct:50%"></div>
              <div class="sk-card"><div class="sk-top"><span class="sk-name">🧮 Aptitude</span><span class="sk-val" id="sv-apt">5</span></div><div class="sk-bar"><div class="sk-fill" id="sf-apt" style="width:50%"></div></div><input type="range" min="0" max="10" value="5" id="inp-apt" oninput="skillUp('apt',this.value)" style="--pct:50%"></div>
              <div class="sk-card"><div class="sk-top"><span class="sk-name">🧩 DSA</span><span class="sk-val" id="sv-dsa">5</span></div><div class="sk-bar"><div class="sk-fill" id="sf-dsa" style="width:50%"></div></div><input type="range" min="0" max="10" value="5" id="inp-dsa" oninput="skillUp('dsa',this.value)" style="--pct:50%"></div>
            </div>
          </div>
          <div class="qp-footer"><button class="btn-back-nav" onclick="prev(3)">← BACK</button><button class="btn-fwd-nav" onclick="next(3)">CONTINUE →</button></div>
        </div>
      </div>
      <div class="step" id="s4">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 04</span><div><div class="qp-category">Field Experience</div><div class="qp-question">Projects built & internships completed</div></div></div>
          <div class="qp-body">
            <div class="cnt-grid">
              <div class="cnt-card"><div class="cnt-emoji">🛠️</div><div class="cnt-label">Projects Built</div><div class="cnt-row"><button class="cnt-btn" onclick="cnt('proj',-1)">−</button><div class="cnt-val" id="cv-proj">0</div><button class="cnt-btn" onclick="cnt('proj',1)">+</button></div></div>
              <div class="cnt-card"><div class="cnt-emoji">🏢</div><div class="cnt-label">Internships Done</div><div class="cnt-row"><button class="cnt-btn" onclick="cnt('intern',-1)">−</button><div class="cnt-val" id="cv-intern">0</div><button class="cnt-btn" onclick="cnt('intern',1)">+</button></div></div>
            </div>
          </div>
          <div class="qp-footer"><button class="btn-back-nav" onclick="prev(4)">← BACK</button><button class="btn-fwd-nav" onclick="next(4)">CONTINUE →</button></div>
        </div>
      </div>
      <div class="step" id="s5">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 05</span><div><div class="qp-category">Academic Risk Assessment</div><div class="qp-question">How many active backlogs do you have?</div></div></div>
          <div class="qp-body">
            <div class="bl-grid">
              <button class="bl-opt sel" onclick="blSel(this,0)">None</button>
              <button class="bl-opt" onclick="blSel(this,1)">1</button>
              <button class="bl-opt" onclick="blSel(this,2)">2</button>
              <button class="bl-opt danger" onclick="blSel(this,3)">3</button>
              <button class="bl-opt danger" onclick="blSel(this,5)">4+</button>
            </div>
            <div style="margin-top:16px;padding:12px 14px;border-left:2px solid var(--red);background:var(--red-dim)"><p class="field-hint" style="color:rgba(248,81,73,.8)">// WARNING: Active backlogs trigger auto-rejection at 80%+ of Fortune 500 campus drives before any human review.</p></div>
          </div>
          <div class="qp-footer"><button class="btn-back-nav" onclick="prev(5)">← BACK</button><button class="btn-fwd-nav" onclick="next(5)">CONTINUE →</button></div>
        </div>
      </div>
      <div class="step" id="s6">
        <div class="qpanel">
          <div class="qp-header"><span class="qp-step-tag">MODULE 06</span><div><div class="qp-category">Career Targeting</div><div class="qp-question">What field do you want to enter?</div></div></div>
          <div class="qp-body">
            <p style="font-size:.8rem;color:var(--text2);margin-bottom:18px;line-height:1.6;">Select your target career field. We'll generate a personalized roadmap with phase-by-phase guidance based on your current skill gaps.</p>
            <div class="field-grid" id="field-grid">
              <div class="field-opt" data-field="sde" onclick="selField(this)">
                <div class="fo-icon">💻</div>
                <div class="fo-title">Software Engineer</div>
                <div class="fo-sub">DSA · System Design · Backend</div>
              </div>
              <div class="field-opt" data-field="fullstack" onclick="selField(this)">
                <div class="fo-icon">🌐</div>
                <div class="fo-title">Full Stack Developer</div>
                <div class="fo-sub">React · Node · Databases</div>
              </div>
              <div class="field-opt" data-field="ml" onclick="selField(this)">
                <div class="fo-icon">🤖</div>
                <div class="fo-title">ML / Data Science</div>
                <div class="fo-sub">Python · ML · Analytics</div>
              </div>
              <div class="field-opt" data-field="devops" onclick="selField(this)">
                <div class="fo-icon">☁️</div>
                <div class="fo-title">DevOps / Cloud</div>
                <div class="fo-sub">AWS · Docker · CI/CD</div>
              </div>
              <div class="field-opt" data-field="security" onclick="selField(this)">
                <div class="fo-icon">🔒</div>
                <div class="fo-title">Cybersecurity</div>
                <div class="fo-sub">Networking · Pen Testing</div>
              </div>
              <div class="field-opt" data-field="mobile" onclick="selField(this)">
                <div class="fo-icon">📱</div>
                <div class="fo-title">Mobile Developer</div>
                <div class="fo-sub">Android / iOS · Flutter</div>
              </div>
              <div class="field-opt" data-field="uiux" onclick="selField(this)">
                <div class="fo-icon">🎨</div>
                <div class="fo-title">UI/UX Designer</div>
                <div class="fo-sub">Figma · Research · Prototyping</div>
              </div>
              <div class="field-opt" data-field="pm" onclick="selField(this)">
                <div class="fo-icon">📊</div>
                <div class="fo-title">Product / Business</div>
                <div class="fo-sub">Strategy · Analytics · Management</div>
              </div>
            </div>
          </div>
          <div class="qp-footer"><button class="btn-back-nav" onclick="prev(6)">← BACK</button><button class="btn-fwd-nav" onclick="calculate()">RUN ANALYSIS ▶</button></div>
        </div>
      </div>
    </div><!-- end form-main -->
  </div><!-- end form-layout -->
</div><!-- end page#form-page -->

<div class="page" id="result-page">
  <div class="result-layout">
    <div class="result-main">
      <div class="score-hero" id="score-hero">
        <div class="score-ring-wrap">
          <svg viewBox="0 0 140 140"><circle class="sr-bg" cx="70" cy="70" r="55"/><circle class="sr-fill" id="sr-fill" cx="70" cy="70" r="55"/></svg>
          <div class="sr-center"><div class="sr-num" id="res-num" style="color:var(--blue-bright)">0</div><div class="sr-of">/ 100</div></div>
        </div>
        <div>
          <div class="score-candidate">ASSESSMENT REPORT — <span id="res-name">CANDIDATE</span></div>
          <div class="score-verdict" id="res-verdict" style="color:var(--blue-bright)">—</div>
          <div class="score-desc" id="res-desc"></div>
        </div>
      </div>
      <div class="data-section">
        <div class="ds-header"><span class="ds-title">PARAMETER ANALYSIS</span><span class="ds-subtitle">Score contribution by factor</span></div>
        <table class="data-table" id="bk-table"></table>
      </div>
      <div class="data-section">
        <div class="ds-header"><span class="ds-title">IMPROVEMENT ROADMAP</span><span class="ds-subtitle">Prioritized action items</span></div>
        <table class="tips-table" id="tips-table"></table>
      </div>
      <div class="action-row">
        <button class="act-btn act-primary" onclick="restart()">Retake Assessment</button>
        <button class="act-btn act-secondary" onclick="copyResult()">Copy Result</button>
        <button class="act-btn" style="background:rgba(168,85,247,.15);color:#c084fc;border:1px solid rgba(168,85,247,.3);flex:1.5;" onclick="showRoadmapPage()">🗺 View Career Roadmap</button>
        <button class="act-btn act-pdf" onclick="downloadPDF()" id="pdf-btn">⬇ Download PDF Report</button>
      </div>
    </div>
    <div class="result-sidebar">
      <div class="rs-card"><div class="rs-head">Your Input Summary</div><div id="rs-inputs"></div></div>
      <div class="rs-card">
        <div class="rs-head">Score Benchmarks</div>
        <div class="rs-item"><span class="rs-key">Elite (80–100)</span><span class="rs-val" style="color:var(--green)">Top Tier</span></div>
        <div class="rs-item"><span class="rs-key">Strong (65–79)</span><span class="rs-val" style="color:var(--blue-bright)">Good Fit</span></div>
        <div class="rs-item"><span class="rs-key">Average (50–64)</span><span class="rs-val" style="color:var(--amber)">Developing</span></div>
        <div class="rs-item"><span class="rs-key">Weak (35–49)</span><span class="rs-val" style="color:var(--red)">Needs Work</span></div>
        <div class="rs-item"><span class="rs-key">Critical (&lt;35)</span><span class="rs-val" style="color:var(--red)">Urgent</span></div>
      </div>
      <div class="rs-card"><div class="rs-head">Live Placements</div><div id="rs-news"></div></div>
    </div>
  </div>
</div>

<!-- ROADMAP PAGE -->
<div class="page" id="roadmap-page">
  <div class="roadmap-layout">
    <div class="roadmap-sidebar">
      <div class="rms-section">
        <div class="rms-head">Your Target Field</div>
        <div class="field-badge" id="rm-field-badge">
          <span class="field-icon" id="rm-field-icon">💻</span>
          <div><div class="field-title" id="rm-field-title">Software Engineer</div><div class="field-sub" id="rm-field-sub">Backend · DSA · System Design</div></div>
        </div>
      </div>
      <div class="rms-section">
        <div class="rms-head">Your Skill Gaps</div>
        <div id="rm-gaps"></div>
      </div>
      <div class="rms-section">
        <div class="rms-head">Phase Navigation</div>
        <div id="rm-phase-nav"></div>
      </div>
    </div>
    <div class="roadmap-main" id="roadmap-main">
      <div class="rm-hero" id="rm-hero"></div>
      <div class="phases-container" id="phases-container"></div>
      <div class="rm-action-row">
        <button class="act-btn act-primary" onclick="showPage('result-page');document.querySelectorAll('.tb-tab')[2].classList.add('active');">← Back to Report</button>
        <button class="act-btn act-secondary" onclick="restart()">New Assessment</button>
        <button class="act-btn act-pdf" id="rm-pdf-btn" onclick="downloadRoadmapPDF()" style="flex:1.5;">⬇ Download Roadmap PDF</button>
      </div>
    </div>
  </div>
</div>

<script>
// ── ALL EXISTING JS ──────────────────────────────────────────────
const PLACEMENTS=[
  {name:'Arjun Mehta',co:'Google',pkg:'₹45 LPA',time:'2m ago'},
  {name:'Priya Sharma',co:'Microsoft',pkg:'₹38 LPA',time:'5m ago'},
  {name:'Rahul Gupta',co:'Amazon',pkg:'₹32 LPA',time:'9m ago'},
  {name:'Sneha Patel',co:'Flipkart',pkg:'₹28 LPA',time:'11m ago'},
  {name:'Vikram Rao',co:'Infosys',pkg:'₹8 LPA',time:'14m ago'},
  {name:'Ananya Singh',co:'TCS',pkg:'₹7 LPA',time:'18m ago'},
  {name:'Karan Joshi',co:'Wipro',pkg:'₹6.5 LPA',time:'22m ago'},
  {name:'Divya Nair',co:'PhonePe',pkg:'₹24 LPA',time:'27m ago'},
  {name:'Suresh Kumar',co:'Zomato',pkg:'₹18 LPA',time:'31m ago'},
  {name:'Meera Iyer',co:'Swiggy',pkg:'₹20 LPA',time:'34m ago'},
  {name:'Amit Sharma',co:'Paytm',pkg:'₹16 LPA',time:'38m ago'},
  {name:'Pooja Reddy',co:'Ola',pkg:'₹14 LPA',time:'42m ago'},
  {name:'Nikhil Verma',co:"BYJU's",pkg:'₹12 LPA',time:'46m ago'},
  {name:'Ritika Gupta',co:'Razorpay',pkg:'₹22 LPA',time:'51m ago'},
  {name:'Aditya Bose',co:'Freshworks',pkg:'₹19 LPA',time:'55m ago'},
];
const BG_HEADLINES=[
  'BREAKING: Arjun Mehta placed at Google with ₹45 LPA package — CGPA 9.2, 6 contest wins',
  'TCS announces 10,000 new campus hires for batch 2026 — applications open now',
  'Students with DSA rating 8+ get 3× more interview shortlists — PlaceIQ 2025 data report',
  'Microsoft India hiring surge: 200 SDE roles open exclusively for CS graduates',
  'CGPA below 6.5 — 73% of students missed TCS NQT cutoff this placement season',
  'Priya Sharma cracks Amazon SDE-1 with perfect DSA score after 90 days of LeetCode',
  'Infosys InfyTQ score now mandatory for all campus shortlisting — minimum 60% required',
  'Students with 2+ internships see 40% higher placement rates across all companies',
  'Google, Microsoft, Amazon on campus — confirmed drive dates announced for 2026 batch',
  'Rahul Gupta lands ₹32 LPA at Amazon — 5 GitHub projects, 0 backlogs, CGPA 8.6',
  'CRITICAL ALERT: 3 active backlogs = auto-reject at Cognizant, Wipro, TCS, Accenture',
  'Communication skills now ranked #2 factor in campus selections — soft skills matter',
  'Placement season 2026: 284 companies registered for campus drives — highest ever',
  'Sneha Patel placed at Flipkart ₹28 LPA — 3 ML projects on GitHub, 1 internship at Razorpay',
  'New: Aptitude cutoff raised to 70th percentile for all Infosys drives effective immediately',
  'Top performers report: CGPA 9+ students placed at 96% rate this season vs 42% for sub-6',
];

(function buildTicker(){
  const t=document.getElementById('ticker');
  const items=[...PLACEMENTS,...PLACEMENTS].map(p=>`<div class="ticker-item"><span class="t-name">${p.name}</span><span class="t-co">→ ${p.co}</span><span class="t-pkg">${p.pkg}</span></div>`).join('');
  t.innerHTML=items+items;
})();

(function buildHeadlines(){
  const bg=document.getElementById('headlines-bg');
  BG_HEADLINES.forEach(hl=>{
    const el=document.createElement('div');el.className='hl';el.textContent=hl;
    const dur=18+Math.random()*20;
    el.style.cssText=`left:${Math.random()*85}%;top:${100+Math.random()*200}%;--drift:${(Math.random()-.5)*60}px;animation-duration:${dur}s;animation-delay:-${Math.random()*dur}s`;
    bg.appendChild(el);
  });
})();

(function buildNewsCards(){
  const news=[
    {badge:'placed',badgeText:'PLACED',name:'Arjun Mehta',text:'secured SDE role at <strong>Google</strong> with CGPA 9.2 and 6 LeetCode contest wins',pkg:'₹45 LPA',time:'2m ago'},
    {badge:'hot',badgeText:'HOT ROLE',name:'Microsoft India',text:'hiring <strong>200 software engineers</strong> from campus — drive opens Monday',pkg:'₹35–42 LPA',time:'8m ago'},
    {badge:'placed',badgeText:'PLACED',name:'Sneha Patel',text:'cracked <strong>Amazon SDE-1</strong> — 3 GitHub projects, 1 internship, 0 backlogs',pkg:'₹32 LPA',time:'15m ago'},
    {badge:'new',badgeText:'NEW DRIVE',name:'Infosys',text:'opens <strong>InfyTQ campus drive</strong> — CGPA 6.5+ required, no active backlogs',pkg:'₹7–9 LPA',time:'32m ago'},
    {badge:'placed',badgeText:'PLACED',name:'Rahul Gupta',text:'<strong>Flipkart</strong> offer after scoring 9/10 in DSA and clearing all 3 rounds',pkg:'₹28 LPA',time:'45m ago'},
  ];
  document.getElementById('news-cards').innerHTML=news.map((n,i)=>`
    <div class="nc" style="animation-delay:${i*.1}s">
      <span class="nc-badge ${n.badge}">${n.badgeText}</span>
      <div class="nc-text"><strong>${n.name}</strong> ${n.text}</div>
      <span class="nc-pkg">${n.pkg}</span>
      <span class="nc-time">${n.time}</span>
    </div>`).join('');
})();

let count=142;
setInterval(()=>{if(Math.random()>.7){count++;const el=document.getElementById('tb-count');if(el)el.textContent=count;}},4000);
function updateTime(){const el=document.getElementById('tb-time');if(el)el.textContent=new Date().toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit'});}
setInterval(updateTime,1000);updateTime();

// CURSOR
const cx2=document.getElementById('cur-x'),cy3=document.getElementById('cur-y'),cd=document.getElementById('cur-dot'),cr=document.getElementById('cur-ring');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cx2.style.left=mx+'px';cx2.style.top=(my-12)+'px';cy3.style.top=my+'px';cy3.style.left=(mx-12)+'px';cd.style.left=mx+'px';cd.style.top=my+'px';});
(function raf(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;cr.style.left=rx+'px';cr.style.top=ry+'px';requestAnimationFrame(raf)})();
document.querySelectorAll('button,input,.bl-opt,.sk-card,.cnt-card,.nc,.co-row,.stat-row').forEach(el=>{el.addEventListener('mouseenter',()=>document.body.classList.add('hov'));el.addEventListener('mouseleave',()=>document.body.classList.remove('hov'));});

function buildGradeBar(v){
  const bar=document.getElementById('cgpa-bar');bar.innerHTML='';
  for(let i=0;i<10;i++){const d=document.createElement('div');d.className='cgb-seg';const col=v>=8?'var(--green)':v>=6.5?'var(--blue)':v>=5?'var(--amber)':'var(--red)';d.style.background=i<Math.floor(v)?col:'var(--border)';bar.appendChild(d);}
}
buildGradeBar(7.5);

const S={name:'',cgpa:7.5,code:5,comm:5,apt:5,dsa:5,proj:0,intern:0,backlogs:0,field:'sde'};
const counts={proj:0,intern:0};let blVal=0;
let selectedField='sde';

function showPage(id){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById(id).classList.add('active');window.scrollTo(0,0);
  document.querySelectorAll('.tb-tab').forEach(t=>t.classList.remove('active'));
  if(id==='landing')document.querySelector('.tb-tab').classList.add('active');
  else if(id==='form-page')document.querySelectorAll('.tb-tab')[1].classList.add('active');
  else if(id==='result-page')document.querySelectorAll('.tb-tab')[2].classList.add('active');
  else if(id==='roadmap-page')document.querySelectorAll('.tb-tab')[3].classList.add('active');
}
function startAssessment(){showPage('form-page');showStep(1);}

function showStep(n){
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('s'+n).classList.add('active');
  document.getElementById('prog-fill').style.width=((n-1)/6*100)+'%';
  document.getElementById('prog-txt').textContent=`Step ${n} of 6`;
  for(let i=1;i<=6;i++){
    const el=document.getElementById('fss'+i);
    el.classList.remove('active','done');
    if(i<n)el.classList.add('done');else if(i===n)el.classList.add('active');
    el.querySelector('.fs-num').textContent=i<n?'✓':String(i).padStart(2,'0');
  }
}
function next(from){
  if(from===1){const v=document.getElementById('inp-name').value.trim();if(!v){shake('inp-name');return}S.name=v;}
  if(from===2)S.cgpa=parseFloat(document.getElementById('inp-cgpa').value);
  if(from===3){S.code=+document.getElementById('inp-code').value;S.comm=+document.getElementById('inp-comm').value;S.apt=+document.getElementById('inp-apt').value;S.dsa=+document.getElementById('inp-dsa').value;}
  if(from===4){S.proj=counts.proj;S.intern=counts.intern;}
  if(from===5){S.backlogs=blVal;}
  showStep(from+1);
}
function prev(from){showStep(from-1);}
function shake(id){const el=document.getElementById(id);el.style.borderColor='var(--red)';el.animate([{transform:'translateX(-5px)'},{transform:'translateX(5px)'},{transform:'none'}],{duration:300});setTimeout(()=>el.style.borderColor='',900);}
function onCgpa(v){v=parseFloat(v);document.getElementById('cgpa-val').textContent=v.toFixed(1);document.getElementById('inp-cgpa').style.setProperty('--pct',(v/10*100)+'%');const col=v>=8?'var(--green)':v>=6.5?'var(--blue-bright)':v>=5?'var(--amber)':'var(--red)';document.getElementById('cgpa-val').style.color=col;document.getElementById('cgpa-val').style.textShadow=`0 0 40px ${col}`;buildGradeBar(v);}
function skillUp(k,v){document.getElementById('sv-'+k).textContent=v;document.getElementById('sf-'+k).style.width=(v*10)+'%';document.getElementById('inp-'+k).style.setProperty('--pct',(v*10)+'%');}
function cnt(k,d){counts[k]=Math.max(0,Math.min(k==='proj'?20:5,counts[k]+d));document.getElementById('cv-'+k).textContent=counts[k];}
function blSel(el,val){document.querySelectorAll('.bl-opt').forEach(b=>b.classList.remove('sel'));el.classList.add('sel');blVal=val;}
function selField(el){document.querySelectorAll('.field-opt').forEach(f=>f.classList.remove('sel'));el.classList.add('sel');selectedField=el.dataset.field;}

function calculate(){
  S.backlogs=blVal;S.proj=counts.proj;S.intern=counts.intern;S.field=selectedField||'sde';
  const ld=document.getElementById('loader');ld.classList.add('show');
  const lf=document.getElementById('ld-fill');lf.style.animation='none';void lf.offsetWidth;lf.style.animation='ldFill 1.8s ease forwards';
  const msgs=['Scanning academic record...','Calibrating skill vectors...','Comparing against 2025 placement data...','Computing probability score...','Building your career roadmap...'];
  let i=0;const iv=setInterval(()=>{const el=document.getElementById('ld-sub');if(el&&i<msgs.length)el.textContent=msgs[i++];else clearInterval(iv);},350);
  setTimeout(()=>{ld.classList.remove('show');showResult(computeScore());},1900);
}
function computeScore(){
  let s=0;const g=S.cgpa;
  if(g>=9)s+=28;else if(g>=8.5)s+=25;else if(g>=8)s+=22;else if(g>=7.5)s+=18;else if(g>=7)s+=14;else if(g>=6.5)s+=10;else if(g>=6)s+=6;else s+=2;
  s+=S.code*1.8;s+=S.comm*1.2;s+=S.apt*1.0;s+=S.dsa*1.4;s+=Math.min(S.proj,5)*2;s+=Math.min(S.intern,2)*4;s-=S.backlogs*7;
  return Math.max(0,Math.min(100,Math.round(s)));
}

function showResult(score){
  document.getElementById('tab-result').style.display='flex';
  document.getElementById('tab-roadmap').style.display='flex';
  showPage('result-page');
  document.getElementById('res-name').textContent=S.name.toUpperCase();
  const rsIn=document.getElementById('rs-inputs');
  if(rsIn)rsIn.innerHTML=[['CGPA',S.cgpa.toFixed(1)],['Coding',`${S.code}/10`],['Communication',`${S.comm}/10`],['Aptitude',`${S.apt}/10`],['DSA',`${S.dsa}/10`],['Projects',S.proj],['Internships',S.intern],['Backlogs',S.backlogs===0?'None':S.backlogs],['Target Field',FIELD_DATA[S.field]?.title||'—']].map(([k,v])=>`<div class="rs-item"><span class="rs-key">${k}</span><span class="rs-val">${v}</span></div>`).join('');
  const rsNews=document.getElementById('rs-news');
  if(rsNews)rsNews.innerHTML=PLACEMENTS.slice(0,5).map(p=>`<div class="rs-item"><span class="rs-key">${p.name}</span><span class="rs-val" style="color:var(--green)">${p.pkg}</span></div>`).join('');
  let color,verdict,desc;
  if(score>=80){color='var(--green)';verdict='Elite Candidate';desc='Exceptional profile. You will be shortlisted by top companies. Focus on interview preparation — your resume will speak for itself.';fireConfetti();}
  else if(score>=65){color='var(--blue-bright)';verdict='Strong Profile';desc='Solid standing across all parameters. You are competitive at mid-to-large companies. Close the gaps highlighted below to break into the top tier.'}
  else if(score>=50){color='var(--amber)';verdict='Developing Profile';desc='Fair placement chances, but several areas need targeted improvement. Follow the roadmap consistently for 3–6 months.'}
  else if(score>=35){color='#e08a4a';verdict='Below Threshold';desc='Your profile will be filtered at most companies. Focused, disciplined effort on the roadmap below can significantly change this outcome.'}
  else{color='var(--red)';verdict='Immediate Action Required';desc="Don't be discouraged. Placement is possible with a focused 6-month plan. Start with clearing backlogs and daily coding practice today."}
  document.getElementById('res-num').style.color=color;
  document.getElementById('res-verdict').style.color=color;
  document.getElementById('res-verdict').textContent=verdict;
  document.getElementById('res-desc').textContent=desc;
  document.getElementById('score-hero').style.setProperty('--score-c',color);
  const ring=document.getElementById('sr-fill');
  const computedColor=getComputedStyle(document.documentElement).getPropertyValue(color.includes('var(')&&color.includes('--')?color.replace('var(--','--').replace(')',''):'--blue').trim()||'#388bfd';
  ring.style.stroke=computedColor||'#388bfd';
  const circ=2*Math.PI*55;ring.style.strokeDasharray=circ;ring.style.strokeDashoffset=circ;
  setTimeout(()=>ring.style.strokeDashoffset=circ-(score/100)*circ,150);
  animNum('res-num',0,score,1500);

  const bkData=[
    {name:'CGPA',raw:`${S.cgpa.toFixed(1)}/10`,pct:Math.min(100,S.cgpa*10),contrib:S.cgpa>=9?28:S.cgpa>=8.5?25:S.cgpa>=8?22:S.cgpa>=7.5?18:S.cgpa>=7?14:S.cgpa>=6.5?10:S.cgpa>=6?6:2,max:28},
    {name:'Coding Skills',raw:`${S.code}/10`,pct:S.code*10,contrib:Math.round(S.code*1.8),max:18},
    {name:'Communication',raw:`${S.comm}/10`,pct:S.comm*10,contrib:Math.round(S.comm*1.2),max:12},
    {name:'Aptitude',raw:`${S.apt}/10`,pct:S.apt*10,contrib:Math.round(S.apt),max:10},
    {name:'DSA / Problem Solving',raw:`${S.dsa}/10`,pct:S.dsa*10,contrib:Math.round(S.dsa*1.4),max:14},
    {name:'Projects',raw:`${S.proj} built`,pct:Math.min(100,S.proj*20),contrib:Math.min(S.proj,5)*2,max:10},
    {name:'Internships',raw:`${S.intern} done`,pct:Math.min(100,S.intern*50),contrib:Math.min(S.intern,2)*4,max:8},
    {name:'Backlog Penalty',raw:S.backlogs===0?'None':String(S.backlogs),pct:0,contrib:-S.backlogs*7,max:0,penalty:true},
  ];
  const bt=document.getElementById('bk-table');
  bt.innerHTML=bkData.map((b,i)=>`<tr><td class="dt-name">${b.name}</td><td class="dt-val">${b.raw}</td><td class="dt-bar-cell">${!b.penalty?`<div class="dt-bar"><div class="dt-fill" id="bf${i}" style="width:0%"></div></div>`:''}</td><td class="dt-contrib ${b.contrib<0?'contrib-neg':'contrib-pos'}">${b.contrib>0?'+':''}${b.contrib} pts</td></tr>`).join('');
  setTimeout(()=>bkData.forEach((b,i)=>{const el=document.getElementById('bf'+i);if(el)el.style.width=b.pct+'%';}),300);

  const tips=[];
  if(S.cgpa<7)tips.push({pri:'HIGH',cls:'tp-high',text:'<strong>Improve CGPA above 7.5.</strong> Many top companies have strict 7+ cutoffs. Attend extra sessions, clear doubts early, prioritise scoring subjects.'});
  if(S.code<6)tips.push({pri:'HIGH',cls:'tp-high',text:'<strong>Daily coding practice is non-negotiable.</strong> Solve 2–3 LeetCode problems every day. Start Easy → Medium over 8 weeks. Track 100+ solved.'});
  if(S.dsa<6)tips.push({pri:'HIGH',cls:'tp-high',text:"<strong>Master DSA fundamentals.</strong> Arrays, Trees, Graphs, Dynamic Programming appear in every product company interview. Use Striver's SDE Sheet."});
  if(S.comm<6)tips.push({pri:'MEDIUM',cls:'tp-med',text:'<strong>Build communication confidence.</strong> Practice mock GDs, record HR answers, review them. Join a public speaking club or debate society.'});
  if(S.apt<5)tips.push({pri:'MEDIUM',cls:'tp-med',text:'<strong>Strengthen aptitude daily.</strong> 30 min on IndiaBix or PrepInsta — rotate between Quantitative, Logical Reasoning, and Verbal sections.'});
  if(S.proj<2)tips.push({pri:'MEDIUM',cls:'tp-med',text:'<strong>Build 2–3 showcase projects.</strong> A deployed full-stack app, ML model, or mobile app with a clear GitHub README will stand out to recruiters.'});
  if(S.intern===0)tips.push({pri:'MEDIUM',cls:'tp-med',text:'<strong>Secure at least one internship.</strong> Apply via Internshala, LinkedIn, AngelList. Even 4 weeks adds real credibility and context to your resume.'});
  if(S.backlogs>0)tips.push({pri:'URGENT',cls:'tp-high',text:'<strong>Clear ALL backlogs — Priority #1.</strong> 80% of companies auto-reject profiles with active backlogs before any human sees your resume.'});
  if(tips.length===0)tips.push({pri:'GREAT',cls:'tp-ok',text:'<strong>Strong foundation!</strong> Focus on competitive programming, FAANG-style mock interviews, LinkedIn networking, and polishing your resume.'});
  document.getElementById('tips-table').innerHTML=tips.map(t=>`<tr><td style="width:90px;padding-top:14px"><span class="tip-priority ${t.cls}">${t.pri}</span></td><td class="tip-text-cell">${t.text}</td></tr>`).join('');
}

function animNum(id,from,to,dur){const el=document.getElementById(id);const st=performance.now();(function f(now){const p=Math.min((now-st)/dur,1),e=1-Math.pow(1-p,4);el.textContent=Math.round(from+(to-from)*e);if(p<1)requestAnimationFrame(f);})(performance.now());}

function fireConfetti(){
  const cols=['#388bfd','#3fb950','#58a6ff','#d29922','#e6edf3','#f85149'];
  const wrap=document.getElementById('cfwrap');wrap.innerHTML='';
  for(let i=0;i<80;i++){const el=document.createElement('div');el.className='cfi';const sz=4+Math.random()*7;el.style.cssText=`left:${Math.random()*100}vw;top:-20px;width:${sz}px;height:${sz}px;background:${cols[Math.floor(Math.random()*cols.length)]};border-radius:${Math.random()>.5?'50%':'1px'};--tx:${(Math.random()-.5)*160}px;--r:${Math.random()*720}deg;animation-duration:${2+Math.random()*2}s;animation-delay:${Math.random()*.6}s`;wrap.appendChild(el);setTimeout(()=>el.remove(),5000);}
}

function copyResult(){const score=document.getElementById('res-num').textContent;const verdict=document.getElementById('res-verdict').textContent;navigator.clipboard.writeText(`PlaceIQ Report\nCandidate: ${S.name}\nScore: ${score}/100\nVerdict: ${verdict}`).then(()=>{const b=document.querySelector('.act-secondary');b.textContent='✓ Copied!';setTimeout(()=>b.textContent='Copy Result',2000);});}

function restart(){Object.assign(counts,{proj:0,intern:0});blVal=0;selectedField='sde';document.getElementById('inp-name').value='';document.getElementById('inp-cgpa').value=7.5;onCgpa(7.5);['code','comm','apt','dsa'].forEach(k=>{document.getElementById('inp-'+k).value=5;skillUp(k,5);});document.getElementById('cv-proj').textContent='0';document.getElementById('cv-intern').textContent='0';document.querySelectorAll('.bl-opt').forEach(b=>b.classList.remove('sel'));document.querySelector('.bl-opt').classList.add('sel');document.querySelectorAll('.field-opt').forEach(f=>f.classList.remove('sel'));document.getElementById('sr-fill').style.strokeDashoffset=2*Math.PI*55;document.getElementById('tab-result').style.display='none';document.getElementById('tab-roadmap').style.display='none';showPage('form-page');showStep(1);}

document.addEventListener('keydown',e=>{if(e.key==='Enter'){const btn=document.querySelector('.step.active .btn-fwd-nav');if(btn)btn.click();}});

// ── CAREER ROADMAP DATA ──────────────────────────────────────────
const FIELD_DATA = {
  sde: {
    title: 'Software Engineer',
    icon: '💻',
    sub: 'Backend · DSA · System Design',
    desc: 'Software Engineers at product companies build scalable systems, solve complex algorithmic problems, and design architecture for millions of users. This path demands deep DSA skills and systems thinking.',
    timeline: '4–6 months',
    avgPkg: '₹12–45 LPA',
    topCompanies: 'Google, Amazon, Microsoft, Flipkart',
    phases: [
      {
        title: 'Foundation',
        subtitle: 'Core programming & problem solving basics',
        duration: '4–6 weeks',
        skills: ['C++ / Java / Python basics','OOP Concepts','Time & Space Complexity','Basic Data Structures','Git & GitHub'],
        gapSkills: {code: 'Coding', dsa: 'DSA'},
        tools: ['VS Code', 'GitHub', 'LeetCode', 'GeeksForGeeks'],
        resources: [
          {icon:'📺', title:'CS50 by Harvard', sub:'Free · OpenCourseWare', type:'Course'},
          {icon:'📗', title:'Introduction to Algorithms (CLRS)', sub:'Book', type:'Book'},
          {icon:'💻', title:'LeetCode Easy Problems (50+)', sub:'Platform', type:'Practice'},
          {icon:'🎥', title:'Love Babbar C++ / Java playlist', sub:'YouTube · Free', type:'Video'},
        ],
        milestones: ['Write programs in your primary language fluently','Understand Big-O notation for all code you write','Solve 50+ LeetCode Easy problems','Build a simple CLI project and push to GitHub'],
      },
      {
        title: 'Core DSA',
        subtitle: 'Master the interview staples',
        duration: '6–8 weeks',
        skills: ['Arrays & Strings','Linked Lists','Stacks & Queues','Trees & BST','Graphs (BFS/DFS)','Dynamic Programming','Recursion & Backtracking'],
        gapSkills: {dsa: 'DSA', code: 'Coding'},
        tools: ['LeetCode', 'HackerRank', 'Codeforces', 'Striver SDE Sheet'],
        resources: [
          {icon:'📋', title:"Striver's SDE Sheet (180 problems)", sub:'Free · takeUforward.org', type:'Problem Set'},
          {icon:'📺', title:'Abdul Bari Algorithms — YouTube', sub:'Free · Complete course', type:'Video'},
          {icon:'💻', title:'LeetCode Medium Daily (1–2/day)', sub:'Platform', type:'Practice'},
          {icon:'📗', title:'Cracking the Coding Interview', sub:'Book · G. L. McDowell', type:'Book'},
        ],
        milestones: ['Complete Striver SDE Sheet Phase 1','Solve 150+ problems (Easy+Medium mix)','Understand all graph traversal patterns','Implement DP solutions for classic problems'],
      },
      {
        title: 'Advanced & System Design',
        subtitle: 'Think at scale like a senior engineer',
        duration: '6–8 weeks',
        skills: ['System Design basics','Databases & SQL','REST APIs','Operating Systems','Computer Networks','Concurrency & Threading','Microservices'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['Postman', 'MySQL / PostgreSQL', 'Docker (basics)', 'AWS S3 (basics)'],
        resources: [
          {icon:'📗', title:'Designing Data-Intensive Applications', sub:'Book · Martin Kleppmann', type:'Book'},
          {icon:'🌐', title:'System Design Primer — GitHub', sub:'Free · donnemartin/system-design-primer', type:'Guide'},
          {icon:'📺', title:'Gaurav Sen System Design — YouTube', sub:'Free', type:'Video'},
          {icon:'💻', title:'Build a full CRUD REST API project', sub:'Personal project', type:'Project'},
        ],
        milestones: ['Design a URL shortener, chat app, or ride-sharing system','Build and deploy a REST API to the cloud','Complete 200+ LeetCode (including 20+ Hard)','Write a blog post explaining a system you designed'],
      },
      {
        title: 'Interview Ready',
        subtitle: 'Mock interviews, polish & apply',
        duration: '3–4 weeks',
        skills: ['Mock Technical Interviews','Behavioral / HR prep','Resume building','Competitive Programming','LinkedIn optimization','Negotiation basics'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['Pramp', 'Interviewing.io', 'LinkedIn', 'Glassdoor'],
        resources: [
          {icon:'🎯', title:'Pramp — Free mock interviews', sub:'Free · peer-to-peer', type:'Platform'},
          {icon:'📝', title:'Resume Worded — ATS checker', sub:'Free tier available', type:'Tool'},
          {icon:'📺', title:'TechLead & Clement Mihailescu on YouTube', sub:'Interview tips', type:'Video'},
          {icon:'💡', title:'Blind & TeamBlind — real interview experiences', sub:'Community', type:'Community'},
        ],
        milestones: ['Complete 10+ mock interviews on Pramp','Finalize a 1-page ATS-optimized resume','Apply to 20+ companies systematically','Prepare STAR stories for behavioral rounds'],
      },
    ],
  },
  fullstack: {
    title: 'Full Stack Developer',
    icon: '🌐',
    sub: 'React · Node.js · Databases',
    desc: 'Full Stack Developers build end-to-end web applications — from beautiful frontends to powerful backends and databases. This is one of the most in-demand roles in startups and product companies.',
    timeline: '4–5 months',
    avgPkg: '₹8–25 LPA',
    topCompanies: 'Razorpay, Zomato, Swiggy, PhonePe, startups',
    phases: [
      {
        title: 'Frontend Fundamentals',
        subtitle: 'HTML, CSS, JavaScript — the holy trinity',
        duration: '3–4 weeks',
        skills: ['HTML5 & Semantic markup','CSS3 & Flexbox / Grid','JavaScript ES6+','DOM manipulation','Browser DevTools','Responsive Design','Git & GitHub'],
        gapSkills: {code: 'Coding'},
        tools: ['VS Code', 'Chrome DevTools', 'GitHub', 'Figma (basics)'],
        resources: [
          {icon:'🌐', title:'The Odin Project — Full Stack curriculum', sub:'Free · theodinproject.com', type:'Course'},
          {icon:'📺', title:'Traversy Media JavaScript Crash Course', sub:'YouTube · Free', type:'Video'},
          {icon:'💻', title:'Frontend Mentor challenges', sub:'Free · frontendmentor.io', type:'Practice'},
          {icon:'📗', title:'Eloquent JavaScript — Marijn Haverbeke', sub:'Free online', type:'Book'},
        ],
        milestones: ['Build 3 static HTML/CSS pages (portfolio, blog, landing page)','Complete 10 JavaScript challenges','Deploy a site using GitHub Pages','Make a fully responsive design'],
      },
      {
        title: 'React & Backend Basics',
        subtitle: 'Build dynamic apps front-to-back',
        duration: '5–6 weeks',
        skills: ['React (components, hooks, state)','Node.js & Express','REST API design','MongoDB / PostgreSQL','Authentication (JWT)','npm & package management'],
        gapSkills: {code: 'Coding', proj: 'Projects'},
        tools: ['React DevTools', 'Postman', 'MongoDB Atlas', 'Heroku / Railway'],
        resources: [
          {icon:'📺', title:'Scrimba React Course', sub:'Free + paid · scrimba.com', type:'Course'},
          {icon:'📺', title:'Net Ninja Node.js & Express Series', sub:'YouTube · Free', type:'Video'},
          {icon:'💻', title:'Build a Todo App + Blog API', sub:'Personal project', type:'Project'},
          {icon:'📗', title:'Node.js Design Patterns — Mario Casciaro', sub:'Book', type:'Book'},
        ],
        milestones: ['Build and deploy a React SPA (single page app)','Create a REST API with full CRUD operations','Connect frontend to backend with authentication','Push full project to GitHub with clear README'],
      },
      {
        title: 'Full Stack Projects',
        subtitle: 'Build real, deployable applications',
        duration: '6–8 weeks',
        skills: ['Next.js / full-stack frameworks','State management (Redux/Zustand)','Database optimization','File uploads & cloud storage','WebSockets (real-time)','Testing basics (Jest)','CI/CD pipeline basics'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['Next.js', 'AWS S3', 'Vercel / Netlify', 'GitHub Actions'],
        resources: [
          {icon:'🌐', title:'Next.js official docs tutorial', sub:'Free · nextjs.org', type:'Docs'},
          {icon:'📺', title:'Fireship.io — modern web dev', sub:'YouTube + courses', type:'Video'},
          {icon:'💻', title:'Build: E-commerce app or Chat app', sub:'Capstone project', type:'Project'},
          {icon:'🎯', title:'Contribute to open source on GitHub', sub:'Community', type:'Practice'},
        ],
        milestones: ['Deploy 2 full-stack apps with live URLs','Implement real-time feature using WebSockets','Add CI/CD so every push auto-deploys','Get one app to 100+ GitHub stars or real users'],
      },
      {
        title: 'Job Ready',
        subtitle: 'Portfolio, interviews, offers',
        duration: '3–4 weeks',
        skills: ['System design for web apps','Resume & LinkedIn polish','Technical interviews','Portfolio site','Freelance or contract work','Salary negotiation'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['LinkedIn', 'AngelList', 'Wellfound', 'Portfolio hosting'],
        resources: [
          {icon:'🌐', title:'Josh W Comeau — CSS & React blog', sub:'Free · joshwcomeau.com', type:'Blog'},
          {icon:'📝', title:'Flowcv.io — developer resume builder', sub:'Free', type:'Tool'},
          {icon:'💼', title:'Internshala + LinkedIn for applications', sub:'Job boards', type:'Platform'},
          {icon:'🎯', title:'LeetCode top 75 (Blind 75) for interviews', sub:'Problem set', type:'Practice'},
        ],
        milestones: ['Build personal portfolio site with 3 featured projects','Apply to 30+ companies / internships','Complete 5 technical mock interviews','Get at least one job offer or internship'],
      },
    ],
  },
  ml: {
    title: 'ML / Data Science',
    icon: '🤖',
    sub: 'Python · Machine Learning · Analytics',
    desc: 'Data Scientists and ML Engineers extract insights from data and build intelligent systems. This field combines statistics, programming, and domain knowledge — and is one of the fastest growing in tech.',
    timeline: '5–7 months',
    avgPkg: '₹10–35 LPA',
    topCompanies: 'Flipkart, Amazon, Google, CRED, Meesho',
    phases: [
      {
        title: 'Python & Math Foundation',
        subtitle: 'The language of data and the math behind it',
        duration: '4–5 weeks',
        skills: ['Python (NumPy, Pandas, Matplotlib)','Statistics & Probability','Linear Algebra basics','Calculus (derivatives, gradients)','SQL fundamentals','Jupyter Notebooks'],
        gapSkills: {code: 'Coding', apt: 'Aptitude'},
        tools: ['Jupyter', 'Google Colab', 'VS Code', 'MySQL'],
        resources: [
          {icon:'📺', title:"Krish Naik's Python for ML — YouTube", sub:'Free · complete playlist', type:'Video'},
          {icon:'📗', title:'Think Stats — Allen B. Downey', sub:'Free online', type:'Book'},
          {icon:'💻', title:'Kaggle — Python & Pandas courses', sub:'Free · kaggle.com', type:'Course'},
          {icon:'🌐', title:'3Blue1Brown — Essence of Linear Algebra', sub:'YouTube · Free', type:'Video'},
        ],
        milestones: ['Manipulate a real dataset with Pandas','Run SQL queries on a 100k+ row database','Visualize insights using Matplotlib/Seaborn','Complete Kaggle\'s Pandas micro-course'],
      },
      {
        title: 'Core Machine Learning',
        subtitle: 'Algorithms, models, and the ML pipeline',
        duration: '6–8 weeks',
        skills: ['Supervised Learning (regression, classification)','Unsupervised Learning (clustering, PCA)','Feature Engineering','Model Evaluation & Validation','Scikit-Learn','Gradient Descent & optimization'],
        gapSkills: {code: 'Coding', dsa: 'Problem Solving'},
        tools: ['Scikit-learn', 'Pandas', 'Matplotlib', 'Seaborn', 'Google Colab'],
        resources: [
          {icon:'🎓', title:"Andrew Ng's Machine Learning — Coursera", sub:'Paid (audit free) · coursera.org', type:'Course'},
          {icon:'📗', title:"Hands-On ML with Scikit-Learn — Aurélien Géron", sub:'Book', type:'Book'},
          {icon:'💻', title:'Kaggle — ML competitions (beginner tier)', sub:'Free', type:'Practice'},
          {icon:'📺', title:'StatQuest with Josh Starmer — YouTube', sub:'Free · intuitive explanations', type:'Video'},
        ],
        milestones: ['Build end-to-end ML pipeline from CSV to predictions','Achieve top 25% on a Kaggle beginner competition','Implement 5 algorithms from scratch (without libraries)','Write a report comparing model performance metrics'],
      },
      {
        title: 'Deep Learning & Specialization',
        subtitle: 'Neural networks and your focus area',
        duration: '6–8 weeks',
        skills: ['Neural Networks & backpropagation','TensorFlow / PyTorch','Computer Vision OR NLP OR Tabular ML','Transfer Learning','Model deployment (Flask / FastAPI)','MLflow basics'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['PyTorch', 'TensorFlow', 'Hugging Face', 'FastAPI', 'MLflow'],
        resources: [
          {icon:'🎓', title:"Andrew Ng's Deep Learning Specialization", sub:'Coursera · audit free', type:'Course'},
          {icon:'📗', title:'Deep Learning — Goodfellow, Bengio, Courville', sub:'Free online · deeplearningbook.org', type:'Book'},
          {icon:'💻', title:'Fast.ai — Practical Deep Learning for coders', sub:'Free · fast.ai', type:'Course'},
          {icon:'🌐', title:'Papers With Code — SOTA models', sub:'Free · paperswithcode.com', type:'Research'},
        ],
        milestones: ['Train a CNN on image classification task (>85% accuracy)','Build a text classifier or recommender system','Deploy an ML model as a web API','Start a Kaggle notebook with detailed EDA & modeling'],
      },
      {
        title: 'Interview & Portfolio',
        subtitle: 'Showcase your work and land the role',
        duration: '3–4 weeks',
        skills: ['ML interview prep (statistics, probability)','Case study frameworks','A/B testing concepts','Data storytelling','SQL for analytics','Communication of results'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['Tableau / Power BI', 'Notion (portfolio)', 'LinkedIn', 'GitHub'],
        resources: [
          {icon:'📗', title:'Data Science Interview Prep — Ace the Data Science Interview', sub:'Book', type:'Book'},
          {icon:'💻', title:'StrataScratch — SQL & ML interview questions', sub:'Free tier · stratascratch.com', type:'Practice'},
          {icon:'🌐', title:'Towards Data Science on Medium', sub:'Community blog', type:'Blog'},
          {icon:'🎯', title:'Portfolio: 3 projects with GitHub + write-ups', sub:'Personal', type:'Project'},
        ],
        milestones: ['Complete 3 end-to-end ML projects on GitHub','Write 2 blog posts explaining your ML work','Answer 50+ DS interview questions on StrataScratch','Apply to 20+ data roles with tailored resume'],
      },
    ],
  },
  devops: {
    title: 'DevOps / Cloud Engineer',
    icon: '☁️',
    sub: 'AWS · Docker · CI/CD',
    desc: 'DevOps engineers bridge development and operations — automating deployment pipelines, managing cloud infrastructure, and ensuring systems stay fast and reliable at scale.',
    timeline: '4–6 months',
    avgPkg: '₹10–30 LPA',
    topCompanies: 'AWS, Infosys, TCS, Wipro, startups',
    phases: [
      {
        title: 'Linux & Networking',
        subtitle: 'The foundation every DevOps engineer needs',
        duration: '3–4 weeks',
        skills: ['Linux CLI & shell scripting','Bash scripting','Networking basics (TCP/IP, DNS, HTTP)','SSH & security basics','File system & permissions','Process management'],
        gapSkills: {code: 'Coding', apt: 'Aptitude'},
        tools: ['Ubuntu/Linux', 'Terminal', 'VS Code', 'Git'],
        resources: [
          {icon:'📺', title:'The Linux Command Line — William Shotts', sub:'Free online · linuxcommand.org', type:'Book'},
          {icon:'🎓', title:'Linux Foundation free courses', sub:'Free · training.linuxfoundation.org', type:'Course'},
          {icon:'💻', title:'OverTheWire: Bandit — Linux wargame', sub:'Free · overthewire.org', type:'Practice'},
          {icon:'📺', title:'NetworkChuck — Linux & Networking', sub:'YouTube · Free', type:'Video'},
        ],
        milestones: ['Write 5 useful Bash automation scripts','Set up and configure a Linux server from scratch','Understand how HTTP requests travel end-to-end','SSH into a remote machine and manage processes'],
      },
      {
        title: 'Docker & Containers',
        subtitle: 'Package and ship applications reliably',
        duration: '4–5 weeks',
        skills: ['Docker basics & Dockerfile','Docker Compose','Kubernetes fundamentals','Container networking','Image optimization','Helm charts basics'],
        gapSkills: {proj: 'Projects'},
        tools: ['Docker Desktop', 'Docker Hub', 'Kubernetes (minikube)', 'Helm'],
        resources: [
          {icon:'📗', title:'Docker Deep Dive — Nigel Poulton', sub:'Book', type:'Book'},
          {icon:'🎓', title:'KodeKloud — Docker & Kubernetes labs', sub:'Paid · hands-on labs', type:'Course'},
          {icon:'📺', title:'TechWorld with Nana — Docker & K8s', sub:'YouTube · Free', type:'Video'},
          {icon:'💻', title:'Dockerize a personal project end-to-end', sub:'Hands-on project', type:'Project'},
        ],
        milestones: ['Containerize a 3-tier web application','Set up Docker Compose with app + database','Deploy a basic Kubernetes cluster locally','Publish a Docker image to Docker Hub'],
      },
      {
        title: 'Cloud & CI/CD',
        subtitle: 'AWS + automated pipelines',
        duration: '6–8 weeks',
        skills: ['AWS (EC2, S3, RDS, Lambda, IAM)','Terraform / Infrastructure as Code','GitHub Actions / Jenkins CI/CD','Monitoring (CloudWatch, Grafana)','Ansible basics','Cost optimization'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['AWS Console', 'Terraform', 'GitHub Actions', 'Grafana', 'Ansible'],
        resources: [
          {icon:'🎓', title:'AWS Solutions Architect Associate — exam prep', sub:'A Cloud Guru / Udemy', type:'Course'},
          {icon:'📗', title:'Terraform: Up & Running — Yevgeniy Brikman', sub:'Book', type:'Book'},
          {icon:'💻', title:'Build a full CI/CD pipeline for a web app', sub:'Capstone project', type:'Project'},
          {icon:'🌐', title:'roadmap.sh/devops — structured roadmap', sub:'Free guide', type:'Guide'},
        ],
        milestones: ['Deploy a containerized app to AWS ECS/EKS','Write Terraform to spin up infrastructure in one command','Create CI/CD pipeline: push code → auto deploy to AWS','Get AWS Cloud Practitioner certified'],
      },
      {
        title: 'Interview & Certification',
        subtitle: 'Credentials and job hunt strategy',
        duration: '3–4 weeks',
        skills: ['AWS Solutions Architect prep','System design for DevOps (HA, DR)','Incident management & SRE basics','Mock technical interviews','Resume for DevOps roles'],
        gapSkills: {comm: 'Communication'},
        tools: ['Whizlabs', 'TutorialsDojo', 'LinkedIn', 'GitHub portfolio'],
        resources: [
          {icon:'🎓', title:'AWS SAA-C03 practice exams — TutorialsDojo', sub:'Paid · tutorialsdojo.com', type:'Practice'},
          {icon:'📗', title:'Site Reliability Engineering — Google SRE Book', sub:'Free online · sre.google', type:'Book'},
          {icon:'💼', title:'DevOps jobs on LinkedIn & Naukri', sub:'Job boards', type:'Platform'},
          {icon:'💻', title:'GitHub portfolio: 3 infrastructure projects', sub:'Personal', type:'Project'},
        ],
        milestones: ['Pass AWS Cloud Practitioner or SAA certification','Complete 3 documented infrastructure projects on GitHub','Apply to 25+ DevOps / Cloud roles','Do 5 mock system design interviews'],
      },
    ],
  },
  security: {
    title: 'Cybersecurity Analyst',
    icon: '🔒',
    sub: 'Networking · Pen Testing · Security',
    desc: 'Cybersecurity professionals protect systems, networks, and data from threats. This field demands curiosity, deep technical knowledge, and a hacker mindset — one of the fastest growing with massive talent shortage.',
    timeline: '5–7 months',
    avgPkg: '₹8–25 LPA',
    topCompanies: 'TCS, Wipro, Deloitte, IBM, government',
    phases: [
      {
        title: 'Networking & Linux',
        subtitle: 'The bedrock of cybersecurity knowledge',
        duration: '4–5 weeks',
        skills: ['Networking (OSI model, TCP/IP, protocols)','Linux CLI & permissions','Firewalls & proxies','Wireshark & packet analysis','DNS, DHCP, HTTP/S','VPNs & encryption basics'],
        gapSkills: {code: 'Coding', apt: 'Aptitude'},
        tools: ['Kali Linux', 'Wireshark', 'Nmap', 'VirtualBox'],
        resources: [
          {icon:'📗', title:'CompTIA Network+ Study Guide', sub:'Book', type:'Book'},
          {icon:'📺', title:'Professor Messer — Network+ free course', sub:'YouTube · Free', type:'Video'},
          {icon:'💻', title:'TryHackMe — Pre-Security learning path', sub:'Free starter · tryhackme.com', type:'Platform'},
          {icon:'📺', title:'NetworkChuck — networking fundamentals', sub:'YouTube · Free', type:'Video'},
        ],
        milestones: ['Capture and analyze HTTP traffic with Wireshark','Set up a home lab with VirtualBox + Kali Linux','Complete TryHackMe Pre-Security path (100%)','Understand how common attacks (MITM, DoS) work'],
      },
      {
        title: 'Security Fundamentals',
        subtitle: 'Core security concepts and tools',
        duration: '5–6 weeks',
        skills: ['Cryptography fundamentals','Web application security (OWASP Top 10)','Authentication & authorization','Vulnerability scanning','Log analysis & SIEM','Incident response basics'],
        gapSkills: {apt: 'Aptitude', dsa: 'Problem Solving'},
        tools: ['Burp Suite (Community)', 'Nessus / OpenVAS', 'Splunk (free)', 'Metasploit'],
        resources: [
          {icon:'📗', title:'The Web Application Hacker\'s Handbook', sub:'Book', type:'Book'},
          {icon:'🎓', title:'Google Cybersecurity Certificate — Coursera', sub:'Paid (audit free)', type:'Course'},
          {icon:'💻', title:'TryHackMe — SOC Level 1 path', sub:'Free/paid · tryhackme.com', type:'Platform'},
          {icon:'🌐', title:'PortSwigger Web Security Academy', sub:'Free · portswigger.net', type:'Course'},
        ],
        milestones: ['Exploit OWASP Top 10 vulnerabilities in a lab environment','Complete TryHackMe SOC Level 1 (50%)','Write a vulnerability assessment report for a test system','Understand how SQL injection and XSS attacks work'],
      },
      {
        title: 'Ethical Hacking & Pen Testing',
        subtitle: 'Think like an attacker to defend better',
        duration: '6–8 weeks',
        skills: ['Reconnaissance & OSINT','Exploitation techniques (ethical)','Privilege escalation','Post-exploitation & persistence','Report writing','CTF (Capture The Flag) competitions'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['Metasploit', 'Burp Suite Pro', 'Hydra', 'John the Ripper', 'Maltego'],
        resources: [
          {icon:'💻', title:'HackTheBox — Practice machines', sub:'Free tier · hackthebox.com', type:'Platform'},
          {icon:'🎓', title:'eJPT Certification — eLearnSecurity', sub:'Paid · entry-level cert', type:'Certification'},
          {icon:'📗', title:'Hacking: The Art of Exploitation — Jon Erickson', sub:'Book', type:'Book'},
          {icon:'🌐', title:'CTFtime.org — upcoming competitions', sub:'Free', type:'Platform'},
        ],
        milestones: ['Own 5 easy machines on HackTheBox','Participate in 3 CTF competitions','Write a full penetration test report on a practice app','Pass eJPT or CompTIA Security+ exam'],
      },
      {
        title: 'Certification & Job Hunt',
        subtitle: 'Get certified and get hired',
        duration: '3–4 weeks',
        skills: ['Security+ or CEH exam prep','Security analyst interview prep','SOC analyst workflows','Resume for cybersecurity','Bug bounty basics'],
        gapSkills: {comm: 'Communication'},
        tools: ['Bugcrowd / HackerOne', 'LinkedIn', 'Resume tools', 'GitHub'],
        resources: [
          {icon:'🎓', title:'CompTIA Security+ (SY0-701) — Professor Messer', sub:'Free videos · professormesser.com', type:'Course'},
          {icon:'💰', title:'HackerOne bug bounty — start with easy programs', sub:'Free · hackerone.com', type:'Platform'},
          {icon:'💼', title:'Cybersecurity jobs on Naukri, LinkedIn', sub:'Job boards', type:'Platform'},
          {icon:'📝', title:'Build a security portfolio on GitHub', sub:'Writeups & tools', type:'Project'},
        ],
        milestones: ['Pass CompTIA Security+ or equivalent','Submit 3 bug bounty reports (any payout)','Complete 3 HackTheBox machines at medium difficulty','Apply to 20+ SOC analyst / security roles'],
      },
    ],
  },
  mobile: {
    title: 'Mobile Developer',
    icon: '📱',
    sub: 'Android / iOS · Flutter · React Native',
    desc: 'Mobile developers build apps that live in billions of pockets. Whether Android, iOS, or cross-platform with Flutter, this is a creative and highly in-demand career path.',
    timeline: '4–5 months',
    avgPkg: '₹8–22 LPA',
    topCompanies: 'Zomato, Swiggy, PhonePe, OYO, startups',
    phases: [
      {
        title: 'Mobile Fundamentals',
        subtitle: 'Choose your platform and learn the basics',
        duration: '3–4 weeks',
        skills: ['Dart / Kotlin / Swift basics','Flutter OR Android Studio setup','UI components & layouts','State management basics','Version control with Git','App lifecycle & navigation'],
        gapSkills: {code: 'Coding'},
        tools: ['Android Studio / Xcode', 'Flutter SDK', 'Git', 'Figma'],
        resources: [
          {icon:'🎓', title:'Flutter official docs + codelabs', sub:'Free · flutter.dev', type:'Docs'},
          {icon:'📺', title:'Vandad Nahavandipoor — Flutter YouTube', sub:'Free', type:'Video'},
          {icon:'📗', title:'Flutter Complete Reference — Alberto Miola', sub:'Book', type:'Book'},
          {icon:'💻', title:'Build 3 basic Flutter apps (calculator, todo, weather)', sub:'Starter projects', type:'Project'},
        ],
        milestones: ['Set up development environment & run first app','Build a multi-screen UI with navigation','Implement state management for a simple app','Deploy debug APK to a real Android device'],
      },
      {
        title: 'APIs & Data',
        subtitle: 'Connect your app to the real world',
        duration: '4–5 weeks',
        skills: ['REST API integration','JSON parsing','Local storage (SQLite, Hive)','Authentication (Firebase Auth)','Push notifications','Error handling & loading states'],
        gapSkills: {code: 'Coding', proj: 'Projects'},
        tools: ['Firebase', 'Dio / Retrofit', 'Hive / SQLite', 'Postman'],
        resources: [
          {icon:'📺', title:'Firebase + Flutter — official tutorials', sub:'Free · firebase.google.com', type:'Docs'},
          {icon:'🎓', title:'Udemy — Angela Yu Flutter Bootcamp', sub:'Paid · highly rated', type:'Course'},
          {icon:'💻', title:'Build a weather or news app using free APIs', sub:'Project', type:'Project'},
          {icon:'📺', title:'Rivaan Ranawat — Flutter projects', sub:'YouTube · Free', type:'Video'},
        ],
        milestones: ['Build an app that fetches data from a public API','Implement offline caching with local storage','Add Firebase authentication to an app','Handle loading, error, and empty states gracefully'],
      },
      {
        title: 'Production App Development',
        subtitle: 'Build, test and publish a real app',
        duration: '6–8 weeks',
        skills: ['Advanced state management (Riverpod/Bloc)','Unit & widget testing','Performance optimization','Animations & custom UI','In-app purchases basics','Google Play / App Store publishing'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['Flutter Riverpod', 'GitHub Actions', 'Google Play Console', 'Firebase Analytics'],
        resources: [
          {icon:'📗', title:'Flutter Cookbook — official docs', sub:'Free · docs.flutter.dev', type:'Docs'},
          {icon:'📺', title:'Code With Andrea — Flutter architecture', sub:'YouTube + blog', type:'Video'},
          {icon:'💻', title:'Publish your app to Google Play Store', sub:'Capstone project', type:'Project'},
          {icon:'🌐', title:'Pub.dev — discover Flutter packages', sub:'Free · pub.dev', type:'Resource'},
        ],
        milestones: ['Publish a live app on Google Play (even free)','Implement custom animations and gestures','Write tests with >60% code coverage','Get 10+ downloads and collect user feedback'],
      },
      {
        title: 'Interview & Portfolio',
        subtitle: 'Showcase apps and get hired',
        duration: '3–4 weeks',
        skills: ['Mobile system design basics','Technical interviews for mobile','Portfolio apps on Play Store','LinkedIn for mobile devs','DSA basics for interviews'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['LinkedIn', 'Google Play Console', 'GitHub', 'LeetCode'],
        resources: [
          {icon:'💻', title:'LeetCode Blind 75 — for technical rounds', sub:'Free · leetcode.com', type:'Practice'},
          {icon:'🌐', title:'roadmap.sh/android — structured path', sub:'Free guide', type:'Guide'},
          {icon:'💼', title:'Internshala, LinkedIn for mobile dev jobs', sub:'Job boards', type:'Platform'},
          {icon:'📝', title:'Build portfolio: 3 apps with Play Store links', sub:'Personal', type:'Project'},
        ],
        milestones: ['Have 3 published apps (or TestFlight builds) in portfolio','Complete 75 LeetCode problems','Apply to 20+ mobile developer roles','Do 5 technical mock interviews'],
      },
    ],
  },
  uiux: {
    title: 'UI/UX Designer',
    icon: '🎨',
    sub: 'Figma · Research · Prototyping',
    desc: 'UI/UX Designers craft digital experiences people love — from research and wireframes to polished interfaces. This career blends psychology, aesthetics, and technical understanding.',
    timeline: '3–5 months',
    avgPkg: '₹6–18 LPA',
    topCompanies: 'Flipkart, Razorpay, Meesho, Swiggy, design agencies',
    phases: [
      {
        title: 'Design Fundamentals',
        subtitle: 'Visual principles and thinking like a designer',
        duration: '3–4 weeks',
        skills: ['Visual design principles (typography, color, space)','Gestalt principles','UX thinking & design process','Figma basics','UI patterns & conventions','Accessibility basics'],
        gapSkills: {code: 'Creativity', comm: 'Communication'},
        tools: ['Figma', 'Adobe Color', 'Google Fonts', 'Coolors'],
        resources: [
          {icon:'🎓', title:'Google UX Design Certificate — Coursera', sub:'Paid (audit free) · 7 courses', type:'Course'},
          {icon:'📗', title:"Don't Make Me Think — Steve Krug", sub:'Book', type:'Book'},
          {icon:'📺', title:'DesignCourse — Gary Simon on YouTube', sub:'Free', type:'Video'},
          {icon:'🌐', title:'Laws of UX — lawsofux.com', sub:'Free reference', type:'Guide'},
        ],
        milestones: ['Redesign 3 existing app screens and explain your decisions','Create a color palette and typography system','Complete Figma basics tutorial (100%)','Write a short critique of a well-known app\'s UX'],
      },
      {
        title: 'User Research & Wireframing',
        subtitle: 'Understand users before you design for them',
        duration: '4–5 weeks',
        skills: ['User interviews & surveys','Personas & empathy maps','User journey mapping','Information architecture','Wireframing & lo-fi prototyping','Usability testing basics'],
        gapSkills: {comm: 'Communication', apt: 'Research'},
        tools: ['Figma', 'Notion (research docs)', 'Maze.co (testing)', 'Optimal Workshop'],
        resources: [
          {icon:'📗', title:'The Design of Everyday Things — Don Norman', sub:'Book · classic', type:'Book'},
          {icon:'💻', title:'NNGroup articles & free UX guides', sub:'Free · nngroup.com', type:'Blog'},
          {icon:'📺', title:'AJ&Smart — design sprint & UX process', sub:'YouTube · Free', type:'Video'},
          {icon:'🎓', title:'Interaction Design Foundation — UX Research', sub:'Paid · affordable', type:'Course'},
        ],
        milestones: ['Conduct 3 user interviews for a design problem','Create user personas based on real research','Map a complete user journey for an existing app','Test a prototype with 5 users and document findings'],
      },
      {
        title: 'Hi-Fi Design & Prototyping',
        subtitle: 'Create beautiful, interactive designs',
        duration: '5–6 weeks',
        skills: ['Figma components & design systems','Auto layout & responsive frames','Micro-interactions & motion','Prototyping for user testing','Handoff to developers','Dark mode & theming'],
        gapSkills: {proj: 'Projects'},
        tools: ['Figma', 'LottieFiles', 'Zeplin / Figma Dev Mode', 'Principle / ProtoPie'],
        resources: [
          {icon:'📺', title:'Mizko — Figma master course on YouTube', sub:'Free', type:'Video'},
          {icon:'🌐', title:'Figma Community — free UI kits & plugins', sub:'Free · figma.com/community', type:'Resource'},
          {icon:'💻', title:'Daily UI challenge — dailyui.co', sub:'Free email prompts', type:'Practice'},
          {icon:'📗', title:'Refactoring UI — Adam Wathan & Steve Schoger', sub:'Book', type:'Book'},
        ],
        milestones: ['Design a complete app (5+ screens) with design system','Build an interactive prototype and share link','Complete 30 Daily UI challenges','Get feedback from senior designers on Dribbble or Behance'],
      },
      {
        title: 'Portfolio & Job Hunt',
        subtitle: 'Case studies and landing your first role',
        duration: '3–4 weeks',
        skills: ['Portfolio case study writing','UX interview preparation','Presenting design decisions','Freelance vs in-house tradeoffs','Salary negotiation','LinkedIn for designers'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['Behance / Dribbble', 'Notion portfolio', 'LinkedIn', 'Webflow (optional)'],
        resources: [
          {icon:'🌐', title:'UX Portfolio Formula — customized career advice', sub:'uxportfolioformula.com', type:'Guide'},
          {icon:'📺', title:'Sarah Doody — UX career & portfolio', sub:'YouTube · Free', type:'Video'},
          {icon:'💼', title:'LinkedIn, Behance, Dribbble for job discovery', sub:'Job platforms', type:'Platform'},
          {icon:'📝', title:'Write 3 in-depth case studies (problem → solution)', sub:'Portfolio', type:'Project'},
        ],
        milestones: ['Publish a portfolio with 3 detailed case studies','Get 2 pieces of feedback from working UX designers','Apply to 20+ UX/product design roles','Complete 3 design challenge take-homes'],
      },
    ],
  },
  pm: {
    title: 'Product / Business Analyst',
    icon: '📊',
    sub: 'Strategy · Analytics · Management',
    desc: 'Product Managers and Business Analysts sit at the intersection of tech, business, and users. They define what to build and why — roles that are increasingly open to non-CS graduates with strong analytical skills.',
    timeline: '3–4 months',
    avgPkg: '₹8–20 LPA',
    topCompanies: 'Amazon, Flipkart, CRED, Razorpay, consulting firms',
    phases: [
      {
        title: 'Product & Business Basics',
        subtitle: 'How products and companies work',
        duration: '3–4 weeks',
        skills: ['PM role & responsibilities','Product lifecycle','Business models & metrics','SWOT & competitor analysis','Stakeholder mapping','North Star metrics & KPIs'],
        gapSkills: {comm: 'Communication', apt: 'Aptitude'},
        tools: ['Notion', 'Google Docs', 'Miro (whiteboards)', 'Canva'],
        resources: [
          {icon:'📗', title:'Inspired — Marty Cagan', sub:'Book · the PM bible', type:'Book'},
          {icon:'🎓', title:'Product School — free PM fundamentals', sub:'Free · productschool.com', type:'Course'},
          {icon:'📺', title:'Lenny\'s Podcast — product interviews', sub:'Podcast · free', type:'Podcast'},
          {icon:'🌐', title:'ProductHunt — study new products daily', sub:'Free · producthunt.com', type:'Resource'},
        ],
        milestones: ['Write a product teardown of 3 apps you use daily','Define North Star metric for a product case study','Map a complete product roadmap for a hypothetical app','Present a 5-minute product pitch in a mock setting'],
      },
      {
        title: 'Data & Analytics',
        subtitle: 'Let data drive every decision',
        duration: '4–5 weeks',
        skills: ['SQL for analytics','Excel / Google Sheets (advanced)','A/B testing fundamentals','Funnel analysis & retention','Cohort analysis','Dashboards & reporting'],
        gapSkills: {apt: 'Aptitude', code: 'Analytics'},
        tools: ['SQL', 'Google Sheets', 'Tableau / Power BI', 'Mixpanel / Amplitude'],
        resources: [
          {icon:'💻', title:'Mode SQL Tutorial — free course', sub:'Free · mode.com/sql-tutorial', type:'Course'},
          {icon:'🎓', title:'Google Data Analytics Certificate', sub:'Coursera · audit free', type:'Course'},
          {icon:'📗', title:'Measure What Matters — John Doerr (OKRs)', sub:'Book', type:'Book'},
          {icon:'💻', title:'StrataScratch — SQL product analyst questions', sub:'Free tier', type:'Practice'},
        ],
        milestones: ['Write 20+ SQL queries on a real dataset','Build a dashboard tracking 5 product KPIs','Design a complete A/B test (hypothesis + metrics + result)','Analyze a funnel and identify the biggest drop-off point'],
      },
      {
        title: 'Product Execution',
        subtitle: 'PRDs, roadmaps, and working with engineers',
        duration: '5–6 weeks',
        skills: ['Writing PRDs (Product Requirement Docs)','User story writing','Agile & Scrum methodology','Working with design & engineering','Prioritization (RICE, ICE)','OKR setting & tracking'],
        gapSkills: {proj: 'Projects', intern: 'Experience'},
        tools: ['JIRA / Linear', 'Confluence', 'Figma (reading wireframes)', 'Notion roadmap'],
        resources: [
          {icon:'📗', title:'The Lean Startup — Eric Ries', sub:'Book', type:'Book'},
          {icon:'📺', title:'Shreyas Doshi — PM frameworks on Twitter/X', sub:'Free · @shreyas', type:'Blog'},
          {icon:'💻', title:'Reforge growth & retention courses', sub:'Paid · reforge.com', type:'Course'},
          {icon:'💡', title:'Run a mock product sprint for any problem', sub:'Personal exercise', type:'Project'},
        ],
        milestones: ['Write a complete PRD for a feature you want to see built','Create a 3-month product roadmap with priorities & rationale','Participate in a hackathon as PM/BA','Shadow or interview a working PM for insights'],
      },
      {
        title: 'Interview & Portfolio',
        subtitle: 'Case studies, guesstimates and getting hired',
        duration: '3–4 weeks',
        skills: ['Product case interviews','Estimation / guesstimate questions','Behavioral interview (STAR)','PM portfolio & writing samples','Networking with PMs','Offer negotiation'],
        gapSkills: {comm: 'Communication'},
        tools: ['LinkedIn', 'Notion portfolio', 'Medium (blogging)', 'Glassdoor'],
        resources: [
          {icon:'📗', title:'Decode and Conquer — Lewis Lin', sub:'Book · PM interview prep', type:'Book'},
          {icon:'💻', title:'PM Exercises — pmexercises.com', sub:'Free case library', type:'Practice'},
          {icon:'📺', title:'Exponent — PM interview prep YouTube', sub:'Free + paid', type:'Video'},
          {icon:'🌐', title:'Product Alliance — community & cases', sub:'productalliance.com', type:'Community'},
        ],
        milestones: ['Complete 20+ product case practice problems','Write 3 product analysis blog posts on Medium','Apply to 25+ APM / BA / Product roles','Do 5 mock case interviews with peers'],
      },
    ],
  },
};

function showRoadmapPage(){
  const fd=FIELD_DATA[S.field]||FIELD_DATA.sde;
  // Sidebar - field badge
  document.getElementById('rm-field-icon').textContent=fd.icon;
  document.getElementById('rm-field-title').textContent=fd.title;
  document.getElementById('rm-field-sub').textContent=fd.sub;

  // Sidebar - skill gaps
  const gapRows=[
    {label:'CGPA',val:S.cgpa.toFixed(1),ok:S.cgpa>=7.5,warn:S.cgpa>=6&&S.cgpa<7.5},
    {label:'Coding',val:`${S.code}/10`,ok:S.code>=7,warn:S.code>=5&&S.code<7},
    {label:'DSA',val:`${S.dsa}/10`,ok:S.dsa>=7,warn:S.dsa>=5&&S.dsa<7},
    {label:'Communication',val:`${S.comm}/10`,ok:S.comm>=7,warn:S.comm>=5&&S.comm<7},
    {label:'Projects',val:String(S.proj),ok:S.proj>=3,warn:S.proj>=1&&S.proj<3},
    {label:'Internships',val:String(S.intern),ok:S.intern>=1,warn:false},
  ];
  document.getElementById('rm-gaps').innerHTML=gapRows.map(r=>`<div class="rms-gap-row"><span class="rms-gap-label">${r.label}</span><span class="rms-gap-val ${r.ok?'ok':r.warn?'warn':'bad'}">${r.val} ${r.ok?'✓':r.warn?'~':'⚠'}</span></div>`).join('');

  // Sidebar - phase nav
  document.getElementById('rm-phase-nav').innerHTML=fd.phases.map((ph,i)=>`<div class="phase-nav-item" onclick="scrollToPhase(${i})"><span class="pni-dot"></span>Phase ${i+1}: ${ph.title}</div>`).join('');

  // Hero
  document.getElementById('rm-hero').innerHTML=`
    <div class="rm-hero-tag">CAREER ROADMAP — ${fd.title.toUpperCase()}</div>
    <div class="rm-hero-title">${fd.icon} Your Path to ${fd.title}</div>
    <div class="rm-hero-sub">${fd.desc}</div>
    <div class="rm-meta-row">
      <span class="rm-meta-chip">⏱ ${fd.timeline} total</span>
      <span class="rm-meta-chip">💰 Avg: ${fd.avgPkg}</span>
      <span class="rm-meta-chip">🏢 ${fd.topCompanies}</span>
    </div>`;

  // Build phases
  const urgentGaps=[];
  if(S.cgpa<7)urgentGaps.push('CGPA below 7');
  if(S.backlogs>0)urgentGaps.push(`${S.backlogs} active backlog(s)`);
  if(S.code<5)urgentGaps.push('Coding skills need work');
  if(S.dsa<5)urgentGaps.push('DSA fundamentals weak');

  let phasesHTML='';
  if(urgentGaps.length>0){
    phasesHTML+=`<div class="gap-alert"><strong>⚠ Fix These First Before Starting Any Phase:</strong> ${urgentGaps.join(' · ')}. These will block your progress at any company.</div>`;
  }

  fd.phases.forEach((ph,i)=>{
    const phaseClass=`phase-${i+1}`;
    const gapSkillNames=Object.values(ph.gapSkills||{});
    const hasGaps=gapSkillNames.some(sk=>{
      if(sk==='Coding')return S.code<6;
      if(sk==='DSA'||sk==='Problem Solving')return S.dsa<6;
      if(sk==='Communication')return S.comm<6;
      if(sk==='Aptitude'||sk==='Research')return S.apt<6;
      if(sk==='Projects')return S.proj<2;
      if(sk==='Experience')return S.intern<1;
      return false;
    });

    const skillsHTML=ph.skills.map(sk=>{
      let cls='';
      if(sk.toLowerCase().includes('dsa')||sk.toLowerCase().includes('algorithm'))cls=S.dsa<6?'gap':'strong';
      else if(sk.toLowerCase().includes('coding')||sk.toLowerCase().includes('python')||sk.toLowerCase().includes('javascript'))cls=S.code<6?'gap':'strong';
      return `<span class="skill-chip ${cls}">${sk}</span>`;
    }).join('');

    const resourcesHTML=ph.resources.map(r=>`
      <div class="resource-item">
        <span class="res-icon">${r.icon}</span>
        <div class="res-text"><strong>${r.title}</strong><br>${r.sub}<span class="res-type">${r.type}</span></div>
      </div>`).join('');

    const milestonesHTML=ph.milestones.map(m=>`<li>${m}</li>`).join('');

    phasesHTML+=`
    <div class="phase-block ${phaseClass}" id="phase-block-${i}">
      <div class="phase-header" onclick="togglePhase(${i})">
        <div class="phase-num">0${i+1}</div>
        <div class="phase-meta">
          <div class="phase-title-text">${ph.title}</div>
          <div class="phase-subtitle">${ph.subtitle}</div>
        </div>
        <span class="phase-duration">${ph.duration}</span>
        ${hasGaps?'<span style="font-family:var(--mono);font-size:.6rem;color:var(--red);padding:3px 8px;border:1px solid rgba(248,81,73,.3);background:var(--red-dim);">GAPS HERE</span>':''}
        <span class="phase-toggle" id="ph-toggle-${i}">▼</span>
      </div>
      <div class="phase-body" id="ph-body-${i}">
        <div class="pb-section">
          <div class="pbs-title">Skills to Learn</div>
          <div>${skillsHTML}</div>
        </div>
        <div class="pb-section">
          <div class="pbs-title">Tools & Platforms</div>
          <div>${ph.tools.map(t=>`<span class="skill-chip">${t}</span>`).join('')}</div>
        </div>
        <div class="pb-section">
          <div class="pbs-title">Resources</div>
          ${resourcesHTML}
        </div>
        <div class="pb-section">
          <div class="pbs-title">Phase Milestones</div>
          <ul class="milestone-list">${milestonesHTML}</ul>
        </div>
      </div>
    </div>`;
  });

  document.getElementById('phases-container').innerHTML=phasesHTML;
  document.getElementById('tab-roadmap').classList.add('active');
  showPage('roadmap-page');
}

function togglePhase(i){
  const body=document.getElementById('ph-body-'+i);
  const toggle=document.getElementById('ph-toggle-'+i);
  if(body.style.display==='none'){body.style.display='';toggle.textContent='▼';}
  else{body.style.display='none';toggle.textContent='▶';}
}
function scrollToPhase(i){
  const el=document.getElementById('phase-block-'+i);
  if(el){el.scrollIntoView({behavior:'smooth',block:'start'});}
  document.querySelectorAll('.phase-nav-item').forEach((n,j)=>{n.classList.toggle('active',j===i);});
}

document.addEventListener('keydown',e=>{if(e.key==='Enter'){const btn=document.querySelector('.step.active .btn-fwd-nav');if(btn)btn.click();}});

function downloadRoadmapPDF(){
  const btn=document.getElementById('rm-pdf-btn');
  btn.textContent='⏳ Generating...';btn.disabled=true;
  setTimeout(()=>{
    try{
      const {jsPDF}=window.jspdf;
      const doc=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
      const PW=210,PH=297;
      const fd=FIELD_DATA[S.field]||FIELD_DATA.sde;
      const NAVY=[10,30,60],DARK=[20,30,50],MID=[70,85,110],WHITE=[255,255,255],MUTED=[130,145,165];
      const PHASE_COLS=[[40,110,210],[180,140,30],[140,60,210],[40,155,80]];
      const now=new Date();

      // ── Helper functions ─────────────────────────────────────
      function footer(pageNum,total){
        doc.setFillColor(...NAVY);doc.rect(0,PH-14,PW,14,'F');
        doc.setFont('helvetica','normal');doc.setFontSize(6.5);doc.setTextColor(140,170,210);
        doc.text('PlaceIQ Career Roadmap  |  Confidential — For Candidate Use Only',PW/2,PH-5,{align:'center'});
        doc.setTextColor(80,120,180);doc.text(`Page ${pageNum} of ${total}`,PW-14,PH-5,{align:'right'});
      }
      function pageHeader(subtitle){
        doc.setFillColor(...NAVY);doc.rect(0,0,PW,12,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(7.5);doc.setTextColor(...WHITE);
        doc.text('PlaceIQ — Career Roadmap',14,8);
        doc.setFont('helvetica','normal');doc.setTextColor(140,170,210);
        doc.text(subtitle,PW-14,8,{align:'right'});
      }
      function sectionHead(y,txt,col){
        doc.setFillColor(...col);doc.rect(14,y,PW-28,8,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(...WHITE);
        doc.text(txt,18,y+5.8);
        return y+13;
      }
      function wrapText(txt,x,y,maxW,lineH){
        const lines=doc.splitTextToSize(txt,maxW);
        doc.text(lines,x,y);
        return y+lines.length*lineH;
      }

      // ── PAGE 1 — Cover ───────────────────────────────────────
      doc.setFillColor(...NAVY);doc.rect(0,0,PW,PH,'F');
      // Decorative grid
      doc.setDrawColor(255,255,255,0.03);doc.setLineWidth(0.2);
      for(let i=0;i<PW;i+=12){doc.line(i,0,i,PH);}
      for(let i=0;i<PH;i+=12){doc.line(0,i,PW,i);}
      // Title block
      doc.setFillColor(20,45,90);doc.rect(0,60,PW,120,'F');
      doc.setFillColor(...PHASE_COLS[0]);doc.rect(0,60,4,120,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(10);doc.setTextColor(160,190,230);
      doc.text('PLACEIQ  —  CAREER ROADMAP',PW/2,75,{align:'center'});
      doc.setFont('helvetica','bold');doc.setFontSize(28);doc.setTextColor(...WHITE);
      const titleLines=doc.splitTextToSize(fd.title,PW-40);
      doc.text(titleLines,PW/2,92,{align:'center'});
      doc.setFont('helvetica','normal');doc.setFontSize(11);doc.setTextColor(140,170,210);
      doc.text(fd.sub,PW/2,92+titleLines.length*14,{align:'center'});
      // Candidate info box
      doc.setFillColor(8,15,35);doc.rect(20,196,PW-40,50,'F');
      doc.setDrawColor(...PHASE_COLS[0]);doc.setLineWidth(0.5);doc.rect(20,196,PW-40,50,'S');
      doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(140,170,210);
      doc.text('CANDIDATE',30,206);doc.text('TARGET FIELD',30,222);doc.text('TIMELINE',30,238);doc.text('AVG PACKAGE',30,254);
      doc.setFont('helvetica','bold');doc.setFontSize(9);doc.setTextColor(...WHITE);
      doc.text(S.name||'—',80,206);
      doc.text(fd.title,80,222);
      doc.text(fd.timeline,80,238);
      doc.setTextColor(80,200,120);doc.text(fd.avgPkg,80,254);
      // Phase overview strip
      doc.setFillColor(5,10,25);doc.rect(0,262,PW,22,'F');
      fd.phases.forEach((ph,i)=>{
        const x=14+i*(PW-28)/4+4;
        doc.setFillColor(...PHASE_COLS[i]);doc.rect(x-2,264,38,18,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(6);doc.setTextColor(...WHITE);
        doc.text(`PHASE ${i+1}`,x,269);
        doc.setFont('helvetica','normal');doc.setFontSize(5.5);doc.setTextColor(220,230,255);
        const lines=doc.splitTextToSize(ph.title,34);
        doc.text(lines,x,274);
      });
      // Date
      doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(80,100,140);
      doc.text(`Generated: ${now.toLocaleDateString('en-IN',{year:'numeric',month:'long',day:'numeric'})}`,PW/2,292,{align:'center'});

      // Count total pages: cover + 1 overview + 1 per phase
      const totalPages=2+fd.phases.length;

      // ── PAGE 2 — Overview & Skill Gaps ───────────────────────
      doc.addPage();
      pageHeader(`${S.name} — Overview`);
      let Y=20;
      // Field description
      doc.setFillColor(248,250,254);doc.rect(14,Y,PW-28,24,'F');
      doc.setFillColor(...PHASE_COLS[0]);doc.rect(14,Y,3,24,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(...DARK);doc.text('About This Career Path',20,Y+7);
      doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(...MID);
      const descLines=doc.splitTextToSize(fd.desc,PW-36);
      doc.text(descLines,20,Y+13);
      Y+=30;
      // Two-column: skill gaps + meta
      Y=sectionHead(Y,'YOUR SKILL GAPS vs. FIELD REQUIREMENTS',NAVY);
      const gaps=[
        {n:'CGPA',v:S.cgpa.toFixed(1),req:'7.5+',ok:S.cgpa>=7.5,warn:S.cgpa>=6&&S.cgpa<7.5},
        {n:'Coding',v:`${S.code}/10`,req:'7+',ok:S.code>=7,warn:S.code>=5&&S.code<7},
        {n:'DSA',v:`${S.dsa}/10`,req:'6+',ok:S.dsa>=6,warn:S.dsa>=4&&S.dsa<6},
        {n:'Communication',v:`${S.comm}/10`,req:'6+',ok:S.comm>=6,warn:S.comm>=4&&S.comm<6},
        {n:'Aptitude',v:`${S.apt}/10`,req:'6+',ok:S.apt>=6,warn:S.apt>=4&&S.apt<6},
        {n:'Projects',v:String(S.proj),req:'3+',ok:S.proj>=3,warn:S.proj>=1&&S.proj<3},
        {n:'Internships',v:String(S.intern),req:'1+',ok:S.intern>=1,warn:false},
        {n:'Backlogs',v:S.backlogs===0?'None':String(S.backlogs),req:'0',ok:S.backlogs===0,warn:false},
      ];
      gaps.forEach((g,i)=>{
        const ry=Y+i*9.5;
        if(i%2===0){doc.setFillColor(248,250,254);doc.rect(14,ry-2,PW-28,9.5,'F');}
        const col=g.ok?[40,155,80]:g.warn?[180,140,30]:[200,60,55];
        doc.setFillColor(...col);doc.rect(14,ry-2,3,9.5,'F');
        doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(...MID);doc.text(g.n,20,ry+4);
        doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(...DARK);doc.text(g.v,90,ry+4);
        doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(...MUTED);doc.text(`Required: ${g.req}`,120,ry+4);
        const status=g.ok?'✓ MET':g.warn?'~ CLOSE':'✗ GAP';
        doc.setFont('helvetica','bold');doc.setFontSize(7);doc.setTextColor(...col);doc.text(status,PW-18,ry+4,{align:'right'});
      });
      Y+=gaps.length*9.5+10;
      // Phase overview table
      Y=sectionHead(Y,'PHASE OVERVIEW',NAVY);
      fd.phases.forEach((ph,i)=>{
        if(Y>PH-40){return;}
        const ry=Y+i*18;
        doc.setFillColor(...PHASE_COLS[i].map(v=>Math.round(v+(255-v)*.9)));doc.rect(14,ry-2,PW-28,16,'F');
        doc.setFillColor(...PHASE_COLS[i]);doc.rect(14,ry-2,3,16,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(7);doc.setTextColor(...PHASE_COLS[i]);
        doc.text(`PHASE ${i+1}  ·  ${ph.duration}`,20,ry+4);
        doc.setFont('helvetica','bold');doc.setFontSize(8.5);doc.setTextColor(...DARK);doc.text(ph.title,20,ry+10);
        doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(...MID);doc.text(ph.subtitle,PW-18,ry+10,{align:'right'});
      });
      footer(2,totalPages);

      // ── PAGES 3+ — One page per phase ────────────────────────
      fd.phases.forEach((ph,pi)=>{
        doc.addPage();
        const PC=PHASE_COLS[pi];
        pageHeader(`Phase ${pi+1}: ${ph.title} — ${S.name}`);
        Y=18;
        // Phase hero bar
        doc.setFillColor(...PC);doc.rect(0,Y,PW,16,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(7);doc.setTextColor(220,235,255);
        doc.text(`PHASE ${pi+1}  ·  ${ph.duration.toUpperCase()}`,16,Y+6);
        doc.setFont('helvetica','bold');doc.setFontSize(12);doc.setTextColor(...WHITE);
        doc.text(ph.title,16,Y+13);
        doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(200,220,255);
        doc.text(ph.subtitle,PW-16,Y+13,{align:'right'});
        Y+=22;

        // Skills to learn
        Y=sectionHead(Y,'SKILLS TO LEARN',PC);
        const colW=(PW-32)/3;
        ph.skills.forEach((sk,si)=>{
          const col=si%3,row=Math.floor(si/3);
          const sx=16+col*colW,sy=Y+row*8;
          if(sy>PH-50){return;}
          doc.setFillColor(...PC.map(v=>Math.round(v+(255-v)*.88)));
          doc.roundedRect(sx,sy-2,colW-4,7,1,1,'F');
          doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(...DARK);
          doc.text(sk,sx+3,sy+3,{maxWidth:colW-8});
        });
        Y+=Math.ceil(ph.skills.length/3)*8+8;

        // Resources & Milestones in two columns
        const halfW=(PW-32)/2;
        const leftX=14,rightX=14+halfW+4;
        let leftY=Y,rightY=Y;

        // Resources (left)
        doc.setFillColor(...NAVY);doc.rect(leftX,leftY,halfW,7,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(7.5);doc.setTextColor(...WHITE);
        doc.text('RESOURCES',leftX+4,leftY+5);
        leftY+=11;
        ph.resources.forEach(r=>{
          if(leftY>PH-30)return;
          doc.setFillColor(248,250,254);doc.rect(leftX,leftY-2,halfW,16,'F');
          doc.setFillColor(...PC);doc.rect(leftX,leftY-2,2,16,'F');
          doc.setFont('helvetica','bold');doc.setFontSize(7.5);doc.setTextColor(...DARK);
          const titleLines=doc.splitTextToSize(r.title,halfW-14);
          doc.text(titleLines,leftX+6,leftY+4);
          doc.setFont('helvetica','normal');doc.setFontSize(6.5);doc.setTextColor(...MUTED);
          doc.text(r.sub,leftX+6,leftY+4+titleLines.length*4);
          doc.setFillColor(...PC.map(v=>Math.round(v+(255-v)*.85)));
          doc.roundedRect(leftX+halfW-18,leftY,14,5,1,1,'F');
          doc.setFont('helvetica','bold');doc.setFontSize(5.5);doc.setTextColor(...PC);
          doc.text(r.type.substring(0,8).toUpperCase(),leftX+halfW-11,leftY+3.5,{align:'center'});
          leftY+=19;
        });

        // Milestones (right)
        doc.setFillColor(...NAVY);doc.rect(rightX,rightY,halfW,7,'F');
        doc.setFont('helvetica','bold');doc.setFontSize(7.5);doc.setTextColor(...WHITE);
        doc.text('PHASE MILESTONES',rightX+4,rightY+5);
        rightY+=11;
        ph.milestones.forEach((m,mi)=>{
          if(rightY>PH-30)return;
          const mLines=doc.splitTextToSize(m,halfW-14);
          const mH=Math.max(12,mLines.length*5+6);
          doc.setFillColor(248,250,254);doc.rect(rightX,rightY-2,halfW,mH,'F');
          doc.setFillColor(...PC);doc.circle(rightX+5,rightY+mH/2-2,2.5,'F');
          doc.setFont('helvetica','bold');doc.setFontSize(6.5);doc.setTextColor(...WHITE);
          doc.text(String(mi+1),rightX+5,rightY+mH/2,{align:'center'});
          doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(...DARK);
          doc.text(mLines,rightX+11,rightY+5);
          rightY+=mH+3;
        });

        // Tools strip at bottom
        const btY=Math.max(leftY,rightY)+4;
        if(btY<PH-30){
          doc.setFillColor(240,244,252);doc.rect(14,btY,PW-28,10,'F');
          doc.setFont('helvetica','bold');doc.setFontSize(6.5);doc.setTextColor(...MID);
          doc.text('TOOLS:',18,btY+7);
          let tx=38;
          ph.tools.forEach(t=>{
            if(tx>PW-20)return;
            const tw=doc.getStringUnitWidth(t)*6.5*0.35+8;
            doc.setFillColor(...PC.map(v=>Math.round(v+(255-v)*.8)));
            doc.roundedRect(tx,btY+2,tw,6,1,1,'F');
            doc.setFont('helvetica','normal');doc.setFontSize(6);doc.setTextColor(...PC);
            doc.text(t,tx+tw/2,btY+6.5,{align:'center'});
            tx+=tw+4;
          });
        }
        footer(pi+3,totalPages);
      });

      doc.save(`PlaceIQ_Roadmap_${fd.title.replace(/\s+/g,'_')}_${S.name.replace(/\s+/g,'_')}.pdf`);
      btn.textContent='✓ Downloaded!';btn.disabled=false;
      setTimeout(()=>{btn.textContent='⬇ Download Roadmap PDF';},3000);
    }catch(err){
      console.error(err);
      btn.textContent='⬇ Download Roadmap PDF';btn.disabled=false;
    }
  },100);
}
function downloadPDF(){
  const btn=document.getElementById('pdf-btn');btn.textContent='⏳ Generating...';btn.disabled=true;
  setTimeout(()=>{
    try{
      const {jsPDF}=window.jspdf;
      const doc=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
      const PW=210,PH=297;
      const score=parseInt(document.getElementById('res-num').textContent)||0;
      const NAVY=[10,30,60],DARK=[20,30,50],MID=[70,85,110],WHITE=[255,255,255],MUTED=[130,145,165],BLUE=[40,110,210];
      const scoreCol=score>=80?[40,155,80]:score>=65?[40,110,210]:score>=50?[180,140,30]:score>=35?[200,110,40]:[200,60,55];
      function hr(y){doc.setDrawColor(230,235,245);doc.setLineWidth(.3);doc.line(14,y,PW-14,y);}
      function secHead(y,txt){doc.setFillColor(...NAVY);doc.rect(14,y,PW-28,9,'F');doc.setFont('helvetica','bold');doc.setFontSize(8.5);doc.setTextColor(...WHITE);doc.text(txt,18,y+6.2);return y+14;}
      doc.setFillColor(...WHITE);doc.rect(0,0,PW,PH,'F');
      doc.setFillColor(...NAVY);doc.rect(0,0,PW,46,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(24);doc.setTextColor(...WHITE);doc.text('PlaceIQ',18,22);
      doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(160,190,230);doc.text('Placement Analytics Platform',18,30);
      doc.setFont('helvetica','bold');doc.setFontSize(9);doc.setTextColor(160,190,230);doc.text('CAMPUS PLACEMENT ASSESSMENT REPORT',PW-18,22,{align:'right'});
      const now=new Date();
      doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(120,155,200);
      doc.text(now.toLocaleDateString('en-IN',{year:'numeric',month:'long',day:'numeric'}),PW-18,30,{align:'right'});
      doc.text(`Report ID: PIQ-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}-${Math.random().toString(36).substr(2,6).toUpperCase()}`,PW-18,36,{align:'right'});
      doc.setFillColor(248,250,254);doc.rect(0,48,PW,38,'F');
      doc.setFillColor(...scoreCol);doc.rect(0,48,4,38,'F');
      doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(...MUTED);doc.text('CANDIDATE NAME',18,58);
      doc.setFont('helvetica','bold');doc.setFontSize(20);doc.setTextColor(...DARK);doc.text(S.name,18,70);
      doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(...MUTED);
      doc.text(`Assessment Date: ${now.toLocaleDateString('en-IN',{year:'numeric',month:'long',day:'numeric'})}  |  ${now.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'})}`,18,80);
      const scCx=PW/2,scCy=145,scR=38;
      doc.setFillColor(248,250,254);doc.circle(scCx,scCy,scR+6,'F');
      doc.setDrawColor(225,230,240);doc.setLineWidth(7);doc.circle(scCx,scCy,scR,'S');
      doc.setDrawColor(...scoreCol);doc.setLineWidth(7);
      const aS=-Math.PI/2,aE=aS+(score/100)*2*Math.PI;
      for(let i=0;i<60;i++){const a1=aS+(aE-aS)*(i/60),a2=aS+(aE-aS)*((i+1)/60);doc.line(scCx+scR*Math.cos(a1),scCy+scR*Math.sin(a1),scCx+scR*Math.cos(a2),scCy+scR*Math.sin(a2));}
      doc.setFillColor(...WHITE);doc.circle(scCx,scCy,scR-5,'F');
      doc.setFont('helvetica','bold');doc.setFontSize(34);doc.setTextColor(...scoreCol);doc.text(String(score),scCx,scCy+7,{align:'center'});
      doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(...MUTED);doc.text('out of 100',scCx,scCy+16,{align:'center'});
      const verd=score>=80?'Elite Candidate':score>=65?'Strong Profile':score>=50?'Developing Profile':score>=35?'Needs Improvement':'Immediate Action Required';
      doc.setFont('helvetica','bold');doc.setFontSize(15);doc.setTextColor(...scoreCol);doc.text(verd,scCx,scCy+scR+16,{align:'center'});
      const bX=30,bY=scCy+scR+26,bW=PW-60,bH=6;
      doc.setFillColor(225,230,240);doc.roundedRect(bX,bY,bW,bH,2,2,'F');
      doc.setFillColor(...scoreCol);doc.roundedRect(bX,bY,(score/100)*bW,bH,2,2,'F');
      doc.setFillColor(...NAVY);doc.rect(0,PH-16,PW,16,'F');doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(140,170,210);
      doc.text('PlaceIQ — Placement Analytics Platform  |  Confidential — For Candidate Use Only',PW/2,PH-7,{align:'center'});
      // Page 2
      doc.addPage();doc.setFillColor(...WHITE);doc.rect(0,0,PW,PH,'F');
      doc.setFillColor(...NAVY);doc.rect(0,0,PW,14,'F');doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(...WHITE);doc.text('PlaceIQ — Detailed Assessment Report',14,9.5);
      let Y=22;
      Y=secHead(Y,'PARAMETER ANALYSIS');
      const inputs=[['CGPA',`${S.cgpa.toFixed(1)} / 10.0`,S.cgpa/10,S.cgpa>=7.5?'GOOD':S.cgpa>=6.5?'AVERAGE':'LOW'],['Coding Skills',`${S.code} / 10`,S.code/10,S.code>=7?'GOOD':S.code>=5?'AVERAGE':'LOW'],['Communication',`${S.comm} / 10`,S.comm/10,S.comm>=7?'GOOD':S.comm>=5?'AVERAGE':'LOW'],['Aptitude',`${S.apt} / 10`,S.apt/10,S.apt>=7?'GOOD':S.apt>=5?'AVERAGE':'LOW'],['DSA',`${S.dsa} / 10`,S.dsa/10,S.dsa>=7?'GOOD':S.dsa>=5?'AVERAGE':'LOW'],['Projects',String(S.proj),Math.min(1,S.proj/5),S.proj>=3?'GOOD':S.proj>=1?'AVERAGE':'LOW'],['Internships',String(S.intern),Math.min(1,S.intern/2),S.intern>=1?'GOOD':'LOW'],['Backlogs',S.backlogs===0?'None':String(S.backlogs),0,S.backlogs===0?'CLEAR':'RISK'],['Target Field',(FIELD_DATA[S.field]||FIELD_DATA.sde).title,1,'INFO']];
      inputs.forEach((row,i)=>{const ry=Y+i*10;if(i%2===0){doc.setFillColor(248,250,254);doc.rect(14,ry-2.5,PW-28,10,'F');}doc.setFont('helvetica','normal');doc.setFontSize(8);doc.setTextColor(...MID);doc.text(row[0],18,ry+4);const tagCol=row[3]==='GOOD'||row[3]==='CLEAR'||row[3]==='INFO'?[40,155,80]:row[3]==='RISK'||row[3]==='LOW'?[200,60,55]:[180,140,30];doc.setFont('helvetica','bold');doc.setFontSize(6.5);doc.setTextColor(...tagCol);doc.text(row[3],PW-18,ry+4,{align:'right'});doc.setFont('helvetica','bold');doc.setFontSize(8);doc.setTextColor(...DARK);doc.text(row[1],PW-46,ry+4,{align:'right'});});
      Y+=inputs.length*10+8;hr(Y);Y+=8;
      Y=secHead(Y,'IMPROVEMENT TIPS');
      const tips2=[];
      if(S.cgpa<7)tips2.push({p:'HIGH',c:[200,60,55],t:'Improve CGPA above 7.5. Many top companies have strict 7+ cutoffs.'});
      if(S.code<6)tips2.push({p:'HIGH',c:[200,60,55],t:'Practice coding daily — 2-3 LeetCode problems every day.'});
      if(S.dsa<6)tips2.push({p:'HIGH',c:[200,60,55],t:"Master DSA: Arrays, Trees, Graphs, DP. Use Striver's SDE Sheet."});
      if(S.comm<6)tips2.push({p:'MEDIUM',c:[180,140,30],t:'Build communication confidence. Practice mock GDs and HR answers.'});
      if(S.proj<2)tips2.push({p:'MEDIUM',c:[180,140,30],t:'Build 2-3 showcase projects with clear GitHub READMEs.'});
      if(S.intern===0)tips2.push({p:'MEDIUM',c:[180,140,30],t:'Secure at least one internship via Internshala or LinkedIn.'});
      if(S.backlogs>0)tips2.push({p:'URGENT',c:[200,60,55],t:'Clear ALL backlogs immediately. Most companies auto-reject profiles with backlogs.'});
      if(tips2.length===0)tips2.push({p:'GREAT',c:[40,155,80],t:'Strong profile! Focus on FAANG interview prep and polish your resume.'});
      tips2.forEach(t=>{if(Y>PH-40)return;const bH2=18;doc.setFillColor(250,252,255);doc.rect(14,Y,PW-28,bH2,'F');doc.setFillColor(...t.c);doc.rect(14,Y,3,bH2,'F');doc.setFont('helvetica','bold');doc.setFontSize(6.5);doc.setTextColor(...t.c);doc.text(t.p,22,Y+7);doc.setFont('helvetica','normal');doc.setFontSize(7.5);doc.setTextColor(...MID);const lines=doc.splitTextToSize(t.t,PW-50);doc.text(lines,40,Y+7);Y+=bH2+4;});
      doc.setFillColor(...NAVY);doc.rect(0,PH-16,PW,16,'F');doc.setFont('helvetica','normal');doc.setFontSize(7);doc.setTextColor(140,170,210);doc.text('PlaceIQ — Placement Analytics Platform  |  Confidential',PW/2,PH-7,{align:'center'});
      doc.save(`PlaceIQ_Report_${S.name.replace(/\s+/g,'_')}_${score}pts.pdf`);
      btn.textContent='✓ Downloaded!';btn.disabled=false;setTimeout(()=>btn.textContent='⬇ Download PDF Report',3000);
    }catch(err){console.error(err);btn.textContent='⬇ Download PDF Report';btn.disabled=false;}
  },100);
}
</script>
</body>
</html>
