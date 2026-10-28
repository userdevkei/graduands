<link rel="icon" type="image/png" href="{{ asset('images/teau-logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{
  /* TEAU palette, taken from the university crest */
  --ink:#0a4a2c; --ink-2:#0e6139; --ink-3:#05994b;      /* deep green (surfaces), hover green, crest green */
  --gold:#f1ea14; --gold-soft:#fbf8c4;                   /* crest yellow */
  --navy:#2a3c6f;                                        /* crest blue */
  --paper:#f3f7f4; --surface:#fff; --line:#dfe8e2; --line-2:#ebf1ed;
  --text:#16241c; --muted:#5d6f64;
  --ok:#0a7a3d; --ok-soft:#dbf2e4;
  --info:#2a3c6f; --info-soft:#e3e8f5;
  --warn:#8c5a04; --warn-soft:#fcf0c8;
  --danger:#b0352b; --danger-soft:#fbe3e0;
  --font-ui:'Public Sans',system-ui,-apple-system,'Segoe UI',sans-serif;
  --font-display:'Source Serif 4',Georgia,'Times New Roman',serif;
  --bs-body-font-family:var(--font-ui); --bs-body-color:var(--text); --bs-body-bg:var(--paper);
  --bs-border-color:var(--line); --bs-link-color:#0b6b3a; --bs-link-hover-color:#074d29;
}
html{-webkit-text-size-adjust:100%}
body{font-size:.94rem;-webkit-font-smoothing:antialiased;color:var(--text);background:var(--paper)}
h1,h2,h3,h4{font-family:var(--font-display)}
a{text-decoration:none}
.muted{color:var(--muted)} .small-muted{color:var(--muted);font-size:.84rem}
:focus-visible{outline:3px solid rgba(5,153,75,.45);outline-offset:2px}
@media (prefers-reduced-motion:reduce){*{transition:none!important;animation:none!important}}

/* ---------- buttons & forms ---------- */
.btn{border-radius:10px;font-weight:600;padding:.55rem 1.05rem;font-size:.92rem}
.btn-sm{padding:.4rem .75rem;font-size:.84rem;border-radius:9px}
.btn-primary{--bs-btn-bg:var(--ink);--bs-btn-border-color:var(--ink);--bs-btn-hover-bg:var(--ink-2);--bs-btn-hover-border-color:var(--ink-2);--bs-btn-active-bg:var(--ink-2);--bs-btn-active-border-color:var(--ink-2);--bs-btn-disabled-bg:var(--ink);--bs-btn-disabled-border-color:var(--ink)}
.btn-gold{--bs-btn-color:var(--ink);--bs-btn-bg:var(--gold);--bs-btn-border-color:var(--gold);--bs-btn-hover-color:var(--ink);--bs-btn-hover-bg:#e3dc0c;--bs-btn-hover-border-color:#e3dc0c;--bs-btn-active-bg:#e3dc0c;--bs-btn-active-color:var(--ink)}
.btn-ghost{--bs-btn-color:var(--text);--bs-btn-bg:#fff;--bs-btn-border-color:#cfdcd4;--bs-btn-hover-bg:#eff5f1;--bs-btn-hover-border-color:#bccbc1;--bs-btn-hover-color:var(--text);--bs-btn-active-bg:#e4ede7;--bs-btn-active-color:var(--text)}
.btn-ghost-dark{--bs-btn-color:#fff;--bs-btn-bg:rgba(255,255,255,.08);--bs-btn-border-color:rgba(255,255,255,.22);--bs-btn-hover-bg:rgba(255,255,255,.16);--bs-btn-hover-border-color:rgba(255,255,255,.35);--bs-btn-hover-color:#fff}
.btn-danger-soft{--bs-btn-color:var(--danger);--bs-btn-bg:var(--danger-soft);--bs-btn-border-color:transparent;--bs-btn-hover-bg:#f6cfca;--bs-btn-hover-color:var(--danger);--bs-btn-hover-border-color:transparent}
.form-control,.form-select{border-radius:10px;border-color:#cfdcd4;padding:.6rem .85rem;font-size:.94rem}
.form-select-sm,.form-control-sm{padding:.4rem .7rem;font-size:.86rem;border-radius:9px}
.form-control:focus,.form-select:focus{border-color:var(--ink-3);box-shadow:0 0 0 .22rem rgba(5,153,75,.16)}
.form-control:disabled,.form-select:disabled{background:#eff5f1;color:#6a7b70}
.form-label{font-weight:600;font-size:.84rem;margin-bottom:.35rem}
.form-check-input:checked{background-color:var(--ink);border-color:var(--ink)}
.field-group{border:0;padding:0;margin:0 0 1.9rem}
.field-group legend{font-family:var(--font-display);font-weight:600;font-size:1.05rem;margin-bottom:.15rem;float:none;width:auto;padding:0}
.field-group .hint{color:var(--muted);font-size:.85rem;margin:0 0 .9rem}

/* ---------- notes (alerts) ---------- */
.note{display:flex;gap:.8rem;align-items:flex-start;padding:.95rem 1.15rem;border-radius:12px;font-size:.92rem;line-height:1.45}
.note>i{font-size:1.1rem;margin-top:.05rem}
.note-ok{background:var(--ok-soft);color:#0f5a40}.note-ok>i{color:var(--ok)}
.note-warn{background:var(--warn-soft);color:#6f3c03}.note-warn>i{color:var(--warn)}
.note-info{background:var(--info-soft);color:#1d2c57}.note-info>i{color:var(--info)}
.note-danger{background:var(--danger-soft);color:#7d241c}.note-danger>i{color:var(--danger)}
.note-muted{background:var(--line-2);color:#3f5648}.note-muted>i{color:var(--muted)}

/* ---------- chips ---------- */
.chip{display:inline-flex;align-items:center;gap:.4rem;padding:.2rem .65rem;border-radius:999px;font-size:.78rem;font-weight:600;background:var(--line-2);color:var(--muted);white-space:nowrap}
.chip::before{content:"";width:.42rem;height:.42rem;border-radius:50%;background:currentColor}
.chip-pending,.chip-warn{background:var(--warn-soft);color:var(--warn)}
.chip-confirmed,.chip-ok,.chip-valid{background:var(--ok-soft);color:var(--ok)}
.chip-updated,.chip-info{background:var(--info-soft);color:var(--info)}
.chip-danger,.chip-invalid{background:var(--danger-soft);color:var(--danger)}
.chip-duplicate{background:var(--warn-soft);color:var(--warn)}
.chip-muted,.chip-staged{background:var(--line-2);color:var(--muted)}
.chip-committed{background:var(--ok-soft);color:var(--ok)}
.chip-cancelled{background:var(--line-2);color:var(--muted)}

/* ---------- panels ---------- */
.panel{background:var(--surface);border:1px solid var(--line);border-radius:16px;box-shadow:0 1px 2px rgba(10,74,44,.05)}
.panel-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem 1rem;padding:1.05rem 1.35rem;border-bottom:1px solid var(--line-2)}
.panel-title{font-family:var(--font-display);font-weight:600;font-size:1.1rem;margin:0}
.panel-body{padding:1.35rem}
.page-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.6rem}
.page-title{font-weight:600;font-size:1.9rem;letter-spacing:-.012em;margin:0;line-height:1.15}
.page-sub{color:var(--muted);margin:.3rem 0 0}
.empty{padding:2.2rem 1.35rem;text-align:center;color:var(--muted)}
.empty i{font-size:1.6rem;display:block;margin-bottom:.4rem;color:#9db0a4}

/* ---------- admin shell ---------- */
.app{display:flex;min-height:100vh}
.sidebar{width:264px;flex:0 0 264px;background:var(--ink);color:#cfe3d6;display:flex;flex-direction:column;padding:1.3rem .9rem}
.brand{display:flex;align-items:center;gap:.75rem;padding:.2rem .55rem 1.2rem;color:#fff}
.brand:hover{color:#fff}
.brand-mark{width:46px;height:46px;border-radius:13px;background:#fff;display:grid;place-items:center;flex:0 0 46px;padding:3px;overflow:hidden}
.brand-mark img{width:100%;height:100%;object-fit:contain;display:block}
.split-side .brand-mark{width:68px;height:68px;flex-basis:68px;border-radius:18px;padding:5px}
.brand-name{font-family:var(--font-display);font-weight:600;font-size:1.05rem;line-height:1.15}
.brand-sub{font-size:.76rem;color:#9cc2ab}
.nav-group{margin:1.15rem 0 .3rem;padding:0 .75rem;font-size:.78rem;font-weight:600;color:#8fb59f}
.side-link{display:flex;align-items:center;gap:.75rem;padding:.62rem .75rem;border-radius:10px;color:#cfe3d6;font-weight:500;position:relative}
.side-link i{font-size:1.08rem;opacity:.92}
.side-link:hover{background:rgba(255,255,255,.06);color:#fff}
.side-link.active{background:rgba(255,255,255,.11);color:#fff}
.side-link.active::before{content:"";position:absolute;left:-.9rem;top:.6rem;bottom:.6rem;width:4px;border-radius:0 4px 4px 0;background:var(--gold)}
.side-user{margin-top:auto;display:flex;align-items:center;gap:.75rem;padding:.9rem .5rem 0;border-top:1px solid rgba(255,255,255,.09)}
.avatar{width:38px;height:38px;border-radius:11px;background:var(--ink-2);color:#fff;display:grid;place-items:center;font-weight:600;font-size:.85rem;flex:0 0 38px}
.side-user .nm{color:#fff;font-weight:600;line-height:1.2;font-size:.9rem}
.side-user .rl{font-size:.76rem;color:#9cc2ab}
.side-user button{margin-left:auto;background:none;border:0;color:#a9cdb8;font-size:1.15rem;padding:.35rem;border-radius:8px}
.side-user button:hover{color:#fff;background:rgba(255,255,255,.1)}
.main{flex:1;min-width:0}
.content{padding:2rem 2.25rem 3.5rem}
.topbar{display:none;align-items:center;gap:.8rem;padding:.8rem 1rem;background:var(--ink);color:#fff}
.topbar button{background:none;border:0;color:#fff;font-size:1.5rem;line-height:1}
.scrim{display:none}
@media (min-width:992px){.sidebar{position:sticky;top:0;height:100vh;overflow-y:auto}}
@media (max-width:991.98px){
  .topbar{display:flex}
  .content{padding:1.4rem 1rem 3rem}
  .sidebar{position:fixed;inset:0 auto 0 0;z-index:1040;transform:translateX(-100%);transition:transform .22s ease}
  body.nav-open .sidebar{transform:none}
  body.nav-open .scrim{display:block;position:fixed;inset:0;background:rgba(6,28,17,.55);z-index:1035}
}

/* ---------- dashboard hero ---------- */
.hero{background:var(--ink);color:#fff;border-radius:20px;padding:2rem 2.2rem}
.hero-label{color:#a9cdb8;font-size:.9rem;font-weight:500;margin-bottom:.2rem}
.hero-figure{font-family:var(--font-display);font-size:5rem;line-height:1;font-weight:600;letter-spacing:-.02em}
.hero-figure small{font-size:1.9rem;color:#a9cdb8;font-weight:500;margin-left:.1rem}
.hero-sub{color:#cfe6d9;margin:.6rem 0 0;max-width:30ch}
.seg{display:flex;gap:4px;height:16px;border-radius:999px;overflow:hidden;background:rgba(255,255,255,.1)}
.seg>span{display:block;height:100%;width:0;border-radius:999px;transition:width 1.1s cubic-bezier(.2,.7,.2,1)}
.s-confirmed{background:#5ed38f}.s-updated{background:#9fb4ee}.s-pending{background:#f1ea14}
.legend{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1.5rem}
.legend b{display:block;font-family:var(--font-display);font-size:1.85rem;font-weight:600;line-height:1.1}
.legend .lbl{display:flex;align-items:center;gap:.45rem;color:#cfe6d9;font-size:.86rem;margin-bottom:.25rem}
.legend .dot{width:.6rem;height:.6rem;border-radius:50%}
.legend .pc{color:#a9cdb8;font-size:.84rem}
@media (max-width:575.98px){.hero{padding:1.5rem 1.25rem}.hero-figure{font-size:3.8rem}.legend{grid-template-columns:1fr}}

/* ---------- lists, bars, timeline ---------- */
.bar{height:6px;border-radius:99px;background:var(--line-2);overflow:hidden}
.bar>span{display:block;height:100%;border-radius:99px;background:var(--ok)}
.row-item{display:flex;align-items:center;gap:.85rem;padding:.85rem 1.35rem;border-top:1px solid var(--line-2)}
.row-item:first-child{border-top:0}
.row-item .grow{flex:1;min-width:0}
.row-item .ttl{font-weight:600;line-height:1.25}
.timeline{list-style:none;margin:0;padding:.35rem 0}
.timeline li{display:flex;gap:.85rem;padding:.7rem 1.35rem}
.tl-dot{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;flex:0 0 34px;font-size:1rem}
.tone-ok{background:var(--ok-soft);color:var(--ok)}.tone-info{background:var(--info-soft);color:var(--info)}
.tone-warn{background:var(--warn-soft);color:var(--warn)}.tone-danger{background:var(--danger-soft);color:var(--danger)}
.tone-muted{background:var(--line-2);color:var(--muted)}
.who{display:flex;align-items:center;gap:.75rem}
.ini{width:36px;height:36px;border-radius:10px;background:#e1f0e7;color:var(--ink);display:grid;place-items:center;font-weight:700;font-size:.8rem;flex:0 0 36px}
.ini-lg{width:64px;height:64px;border-radius:18px;font-size:1.3rem;flex:0 0 64px;background:var(--ink);color:#fff;font-family:var(--font-display)}
.sub{color:var(--muted);font-size:.82rem}

/* ---------- tiles & filters ---------- */
.tile{display:flex;align-items:center;gap:.9rem;padding:1rem 1.15rem;background:#fff;border:1.5px solid var(--line);border-radius:14px;width:100%;text-align:left;color:inherit}
.tile:hover{border-color:#bccbc1}
.tile.active{border-color:var(--ink);box-shadow:0 0 0 3px rgba(10,74,44,.1)}
.tile .ic{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;font-size:1.2rem}
.tile .num{font-family:var(--font-display);font-size:1.6rem;font-weight:600;line-height:1}
.tile .lb{color:var(--muted);font-size:.84rem;margin-top:.2rem}
.seg-tabs{display:inline-flex;background:var(--line-2);border-radius:11px;padding:3px}
.seg-tabs button{border:0;background:none;padding:.4rem .9rem;border-radius:9px;font-weight:600;font-size:.84rem;color:var(--muted)}
.seg-tabs button.active{background:#fff;color:var(--ink);box-shadow:0 1px 3px rgba(10,74,44,.14)}
.kv{display:grid;grid-template-columns:200px 1fr;gap:0}
.kv>div{padding:.8rem 1.35rem;border-top:1px solid var(--line-2)}
.kv>div:nth-child(-n+2){border-top:0}
.kv .k{color:var(--muted);font-weight:500;font-size:.88rem}
.kv .changed{background:var(--gold-soft)}
.diff-old{color:var(--muted);text-decoration:line-through;margin-right:.4rem}
@media (max-width:575.98px){.kv{grid-template-columns:1fr}.kv .k{padding-bottom:0;border-top:1px solid var(--line-2)}.kv>div:nth-child(2){border-top:0}.kv>div:nth-child(even){border-top:0}}

/* ---------- DataTables ---------- */
.dt-wrap{padding:1.1rem 1.35rem 1.25rem;overflow-x:auto}
.table-pro{margin:0!important;font-variant-numeric:tabular-nums;--bs-table-bg:transparent}
.table-pro thead th{font-size:.8rem;font-weight:600;color:var(--muted);background:#f6f9f7;border-bottom:1px solid var(--line)!important;padding:.8rem 1rem;white-space:nowrap}
.table-pro tbody td{padding:.8rem 1rem;border-color:var(--line-2);vertical-align:middle}
.table-pro tbody tr:hover>td{background:#f9fbfa}
.table-pro.clickable tbody tr{cursor:pointer}
div.dataTables_wrapper div.dataTables_filter{text-align:right}
div.dataTables_wrapper div.dataTables_filter input{margin-left:0;width:min(280px,100%);border-radius:10px;border:1px solid #cfdcd4;padding:.5rem .8rem}
div.dataTables_wrapper div.dataTables_length select{border-radius:10px;border:1px solid #cfdcd4;padding:.4rem 2rem .4rem .7rem;width:auto;display:inline-block}
div.dataTables_wrapper div.dataTables_filter input:focus,div.dataTables_wrapper div.dataTables_length select:focus{border-color:var(--ink-3);box-shadow:0 0 0 .22rem rgba(5,153,75,.16);outline:0}
div.dataTables_wrapper div.dataTables_info{color:var(--muted);font-size:.85rem;padding-top:.9rem}
div.dataTables_wrapper div.dataTables_paginate{padding-top:.5rem}
.page-link{border:0;border-radius:9px!important;color:var(--text);margin:0 2px;font-weight:500}
.page-item.active .page-link{background:var(--ink);color:#fff}
.page-item.disabled .page-link{background:none;color:#a4b5a9}
div.dataTables_wrapper div.dataTables_processing{background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 8px 24px rgba(10,74,44,.14);color:var(--muted);padding:.8rem 1.2rem;z-index:5;height:auto}
table.dataTable.table-pro>thead>tr>th:not(.sorting_disabled){padding-right:1.6rem}

/* ---------- import ---------- */
.dropzone{display:block;border:2px dashed #bccbc1;border-radius:14px;padding:1.8rem 1rem;text-align:center;cursor:pointer;color:var(--muted);background:#f9fbfa}
.dropzone:hover,.dropzone.has-file{border-color:var(--ink-3);background:#f1f8f4}
.dropzone i{font-size:2rem;color:var(--ink-3);display:block;margin-bottom:.3rem}
.dropzone input{position:absolute;opacity:0;width:1px;height:1px}
.col-chip{display:inline-block;background:var(--line-2);border-radius:8px;padding:.15rem .55rem;margin:.15rem .1rem;font-size:.82rem;font-family:var(--font-ui);color:#2f4a3a}
.col-chip.req{background:var(--gold-soft);color:#6a4b0c;font-weight:600}

/* ---------- steps ---------- */
.steps{display:flex;align-items:center;gap:.7rem;flex-wrap:wrap}
.step{display:flex;align-items:center;gap:.55rem;color:#9cc2ab;font-size:.86rem;font-weight:500}
.step .n{width:26px;height:26px;border-radius:50%;border:1.5px solid currentColor;display:grid;place-items:center;font-weight:700;font-size:.76rem}
.step.active{color:#fff}.step.active .n{background:var(--gold);border-color:var(--gold);color:var(--ink)}
.step.done{color:#d6f3e6}.step.done .n{background:#5ed38f;border-color:#5ed38f;color:var(--ink)}
.step-line{flex:1;height:1px;background:rgba(255,255,255,.22);min-width:20px;max-width:60px}
.steps.light .step{color:#9db0a4}.steps.light .step.active{color:var(--ink)}.steps.light .step.done{color:var(--ok)}
.steps.light .step.active .n{background:var(--ink);border-color:var(--ink);color:#fff}
.steps.light .step.done .n{background:var(--ok);border-color:var(--ok);color:#fff}
.steps.light .step-line{background:var(--line)}

/* ---------- student & login ---------- */
.split{min-height:100vh;display:grid;grid-template-columns:minmax(340px,5fr) 7fr}
.split-side{background:var(--ink);color:#fff;padding:3rem 3.4rem;display:flex;flex-direction:column;justify-content:space-between;gap:2.5rem}
.split-side h1{font-weight:600;font-size:2.3rem;line-height:1.18;letter-spacing:-.012em;margin:0 0 1rem;max-width:16ch}
.split-side p{color:#c4dccc;max-width:38ch}
.split-main{display:grid;place-items:center;padding:2.5rem 1.5rem}
.auth-card{width:100%;max-width:440px}
.process{list-style:none;padding:0;margin:0;display:grid;gap:1rem}
.process li{display:flex;gap:.85rem;color:#d9ece0}
.process .n{width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);display:grid;place-items:center;font-weight:700;font-size:.8rem;flex:0 0 28px}
@media (max-width:860px){.split{grid-template-columns:1fr}.split-side{padding:2rem 1.5rem;gap:1.5rem}.split-side h1{font-size:1.8rem}}
.student-head{background:var(--ink);color:#fff;padding:1.4rem 0 5.2rem}
.wrap{width:100%;max-width:820px;margin:0 auto;padding:0 1.25rem}
.student-name{font-weight:600;font-size:2.2rem;letter-spacing:-.012em;margin:1.8rem 0 .5rem;line-height:1.15}
.meta-chip{display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.1);color:#e0f0e6;border-radius:999px;padding:.3rem .8rem;font-size:.83rem}
.student-body{margin-top:-3.2rem;padding-bottom:3rem}
.lock-icon{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;font-size:1.6rem;background:var(--ok-soft);color:var(--ok)}
@media (max-width:575.98px){.student-name{font-size:1.7rem}}
</style>
