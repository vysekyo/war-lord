<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>none | Command Center</title>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
<style>
:root {
  --bg:          #020804;
  --green:       #00ff41;
  --green2:      #00cc33;
  --cyan:        #00d4ff;
  --red:         #ff4444;
  --purple:      #b344ff;
  --amber:       #ffcc00;
  --border:      rgba(0,255,65,.12);
  --border2:     rgba(0,255,65,.22);
  --surface:     rgba(4,14,4,.96);
  --surface2:    rgba(0,6,0,.98);
  --muted:       rgba(0,255,65,.45);
  --text:        rgba(255,255,255,.88);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }

body {
  background: #000;
  color: var(--green);
  font-family: 'Share Tech Mono', monospace;
  min-height: 100vh;
  overflow-x: hidden;
}

/* Grid BG */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(0,255,65,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,255,65,.03) 1px, transparent 1px);
  background-size: 44px 44px;
  pointer-events: none;
}

/* Scanlines */
body::after {
  content: '';
  position: fixed; inset: 0; z-index: 1;
  background: repeating-linear-gradient(
    to bottom, transparent 0, transparent 2px,
    rgba(0,0,0,.07) 2px, rgba(0,0,0,.07) 4px
  );
  pointer-events: none;
}

canvas.matrix-bg {
  position: fixed; inset: 0; z-index: 0;
  opacity: .07; pointer-events: none;
}

.content { position: relative; z-index: 2; }

/* ── NAV ── */
.nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 100;
  display: flex; justify-content: space-between; align-items: center;
  padding: 0 32px; height: 62px;
  background: rgba(0,0,0,.8);
  border-bottom: 1px solid var(--border);
  backdrop-filter: blur(14px);
}

.nav-logo {
  font-family: 'Orbitron', monospace;
  font-weight: 900; font-size: 20px;
  color: #fff; text-decoration: none;
  letter-spacing: 3px;
  text-shadow: 0 0 16px rgba(0,255,65,.35);
}
.nav-logo span { color: var(--green); }

.nav-links { display: flex; align-items: center; gap: 24px; }
.nav-links a {
  font-size: 11px; letter-spacing: 2px;
  color: var(--muted); text-decoration: none;
  transition: .2s; padding: 4px 0;
  border-bottom: 1px solid transparent;
}
.nav-links a:hover, .nav-links a.active {
  color: var(--green);
  border-bottom-color: var(--green);
  text-shadow: 0 0 8px rgba(0,255,65,.5);
}
.nav-links a.purple { color: rgba(179,68,255,.7); }
.nav-links a.purple:hover { color: var(--purple); border-bottom-color: var(--purple); text-shadow: 0 0 8px rgba(179,68,255,.5); }

.nav-badge {
  font-size: 9px; letter-spacing: 2px;
  padding: 4px 10px; border-radius: 999px;
  background: rgba(0,255,65,.07);
  border: 1px solid var(--border);
  color: var(--muted);
}

/* ── STATUSBAR ── */
.statusbar {
  position: fixed; top: 62px; left: 50%; transform: translateX(-50%);
  z-index: 99;
  display: flex; gap: 24px; align-items: center;
  padding: 7px 20px; border-radius: 999px;
  background: rgba(0,0,0,.55);
  border: 1px solid var(--border);
  backdrop-filter: blur(10px);
  font-size: 10px; color: rgba(0,255,65,.6); letter-spacing: 1.5px;
  white-space: nowrap;
}

.pulse {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 8px var(--green);
  animation: pulse 1.8s ease-in-out infinite;
  display: inline-block; margin-right: 6px;
}
@keyframes pulse { 0%,100%{opacity:1;box-shadow:0 0 8px var(--green);}50%{opacity:.3;box-shadow:none;} }

/* ── HERO ── */
.hero {
  min-height: 100vh;
  display: flex; flex-direction: column;
  justify-content: center; align-items: center;
  text-align: center; padding: 140px 32px 80px;
  position: relative;
}

.hero-pre {
  font-size: 11px; color: var(--red); letter-spacing: 4px;
  text-transform: uppercase; margin-bottom: 20px;
  text-shadow: 0 0 8px var(--red);
  animation: fadeIn .6s ease both;
}

.hero h1 {
  font-family: 'Orbitron', monospace;
  font-size: clamp(40px, 8vw, 90px);
  font-weight: 900; color: #fff;
  letter-spacing: 6px; text-transform: uppercase;
  line-height: 1; margin-bottom: 16px;
  animation: fadeIn .8s .1s ease both;
}

.hero h1 .g {
  color: var(--green);
  text-shadow: 0 0 30px rgba(0,255,65,.5);
}

.hero-sub {
  font-size: 12px; color: var(--muted); letter-spacing: 3px;
  margin-bottom: 12px;
  animation: fadeIn .8s .2s ease both;
}

.typing-line {
  font-size: 13px; color: rgba(0,255,65,.6);
  height: 20px; margin-bottom: 56px;
  letter-spacing: 1px;
  animation: fadeIn .8s .3s ease both;
}

@keyframes fadeIn { from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);} }

/* ── TOOL CARDS ── */
.tool-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px; max-width: 860px; width: 100%;
  margin: 0 auto 80px;
  animation: fadeIn .9s .4s ease both;
}
@media(max-width:600px){ .tool-cards{grid-template-columns:1fr;} }

.tool-card {
  border: 1px solid var(--border);
  background: var(--surface2);
  padding: 30px 26px;
  text-align: left; text-decoration: none;
  display: block; position: relative; overflow: hidden;
  border-radius: 16px;
  transition: .25s;
}

.tool-card::before {
  content: ''; position: absolute; top: 0; left: 0;
  width: 0; height: 3px; transition: width .35s;
  border-radius: 3px 3px 0 0;
}
.tool-card.whm::before    { background: linear-gradient(90deg, var(--red), #ff8800); }
.tool-card.cpanel::before { background: linear-gradient(90deg, var(--cyan), #0099dd); }
.tool-card.perl::before   { background: linear-gradient(90deg, var(--purple), #6622cc); }

.tool-card:hover { transform: translateY(-4px); }
.tool-card.whm:hover    { border-color: rgba(255,68,68,.3); box-shadow: 0 8px 40px rgba(255,68,68,.1); }
.tool-card.cpanel:hover { border-color: rgba(0,212,255,.3); box-shadow: 0 8px 40px rgba(0,212,255,.1); }
.tool-card.perl:hover   { border-color: rgba(179,68,255,.3); box-shadow: 0 8px 40px rgba(179,68,255,.1); }
.tool-card:hover::before { width: 100%; }

.tool-card-tag {
  font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
  padding: 4px 10px; display: inline-block; margin-bottom: 18px;
  border-radius: 4px;
}
.tool-card.whm .tool-card-tag    { border: 1px solid rgba(255,68,68,.4); color: var(--red); }
.tool-card.cpanel .tool-card-tag { border: 1px solid rgba(0,212,255,.4); color: var(--cyan); }
.tool-card.perl .tool-card-tag   { border: 1px solid rgba(179,68,255,.4); color: var(--purple); }

.tool-card h3 {
  font-family: 'Orbitron', monospace;
  font-size: 15px; font-weight: 700;
  color: #fff; letter-spacing: 2px; margin-bottom: 10px;
}

.tool-card p {
  font-size: 12px; color: rgba(0,255,65,.5); line-height: 1.8; margin-bottom: 22px;
}

.enter-link { font-size: 11px; letter-spacing: 2px; }
.tool-card.whm .enter-link    { color: var(--red); }
.tool-card.cpanel .enter-link { color: var(--cyan); }
.tool-card.perl .enter-link   { color: var(--purple); }

.hk-cursor {
  display: inline-block; width: 8px; height: 13px;
  vertical-align: middle; margin-left: 3px;
  animation: cur 1s step-end infinite;
}
.tool-card.whm .hk-cursor    { background: var(--red); }
.tool-card.cpanel .hk-cursor { background: var(--cyan); }
.tool-card.perl .hk-cursor   { background: var(--purple); }
@keyframes cur { 0%,100%{opacity:1;}50%{opacity:0;} }

.tool-card.perl { grid-column: 1 / -1; }

/* ── STATS STRIP ── */
.stats-strip {
  display: flex; gap: 0; justify-content: center;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  background: rgba(0,255,65,.02);
  margin-bottom: 80px;
  flex-wrap: wrap;
}

.stat-item {
  text-align: center; padding: 32px 48px;
  border-right: 1px solid var(--border);
  flex: 1; min-width: 140px;
}
.stat-item:last-child { border-right: none; }

.stat-num {
  font-family: 'Orbitron', monospace; font-size: 24px; font-weight: 700;
  color: var(--green); text-shadow: 0 0 12px rgba(0,255,65,.4);
  margin-bottom: 8px;
}
.stat-label { font-size: 9px; letter-spacing: 3px; color: var(--muted); }

/* ── SECTIONS ── */
.section {
  max-width: 1060px; margin: 0 auto;
  padding: 0 32px 80px;
}

.section-head {
  display: flex; align-items: center; gap: 16px;
  margin-bottom: 28px;
}
.section-head h2 {
  font-family: 'Orbitron', monospace;
  font-size: 13px; font-weight: 700;
  color: #fff; letter-spacing: 4px;
}
.section-head::after {
  content: ''; flex: 1; height: 1px;
  background: linear-gradient(to right, var(--border), transparent);
}

/* ── FEATURE GRID ── */
.feat-grid {
  display: grid; grid-template-columns: repeat(3,1fr); gap: 14px;
}
@media(max-width:720px){ .feat-grid{grid-template-columns:1fr;} }

.feat-item {
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 22px;
  background: var(--surface2);
  position: relative; overflow: hidden;
  transition: .2s;
}
.feat-item:hover {
  border-color: var(--border2);
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0,255,65,.08);
}
.feat-item::before {
  content: ''; position: absolute; top: 0; left: 0;
  width: 100%; height: 2px;
  background: linear-gradient(to right, rgba(0,255,65,.2), transparent);
}

.feat-num {
  font-family: 'Orbitron', monospace; font-size: 9px;
  color: var(--red); letter-spacing: 2px; margin-bottom: 12px;
}
.feat-item h4 {
  font-size: 12px; color: var(--green); letter-spacing: 1px;
  margin-bottom: 8px; font-family: 'Orbitron', monospace;
}
.feat-item p { font-size: 11px; color: rgba(0,255,65,.45); line-height: 1.8; }

/* ── TERMINAL PREVIEW ── */
.terminal-wrap {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 18px; overflow: hidden;
}
.term-bar {
  height: 42px; background: rgba(0,20,0,.5);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; padding: 0 16px; gap: 8px;
  font-size: 11px; color: var(--muted); letter-spacing: 1px;
}
.td { width: 11px; height: 11px; border-radius: 50%; }
.td-r { background: #ff5f56; }
.td-y { background: #ffbd2e; }
.td-g { background: #27c93f; animation: pulse 1.8s infinite; }
.term-body {
  padding: 24px; font-size: 13px; line-height: 2.1; color: var(--green);
}
.term-prompt { color: var(--cyan); }
.term-out    { color: rgba(0,255,65,.6); }
.term-ok     { color: var(--green); }
.term-cursor {
  display: inline-block; width: 8px; height: 14px;
  background: var(--green); animation: cur 1s step-end infinite;
  vertical-align: middle; margin-left: 3px;
}

/* ── FOOTER ── */
footer {
  border-top: 1px solid var(--border);
  padding: 24px 32px;
  display: flex; justify-content: space-between; align-items: center;
  font-size: 11px; color: var(--muted); letter-spacing: 2px;
  position: relative; z-index: 2;
}

@media(max-width:600px){
  .nav-links { gap: 14px; }
  .nav-links a { font-size: 10px; }
  footer { flex-direction: column; gap: 8px; text-align: center; }
  .statusbar { font-size: 9px; gap: 14px; }
}
</style>
</head>
<body>
<canvas class="matrix-bg" id="matrix"></canvas>

<!-- NAV -->
<nav class="nav">
  <a href="index.php" class="nav-logo">no<span>ne</span></a>
  <div class="nav-links">
    <a href="index.php" class="active">./home</a>
    <a href="whm.php">./whm-panel</a>
    <a href="cpanelmanager.php">./cpanel-manager</a>
    <a href="#" class="purple">#</a>
    <span class="nav-badge">SECURE</span>
  </div>
</nav>

<!-- STATUSBAR -->
<div class="statusbar">
  <span><span class="pulse"></span>SYSTEM ONLINE</span>
  <span>ENCRYPTED: AES-256</span>
  <span>PORT: 2087 / 2083</span>
  <span>STATUS: ALL CLEAR</span>
</div>

<div class="content">

<!-- HERO -->
<section class="hero">
  <div class="hero-pre">&gt; INITIALIZING COMMAND CENTER...</div>
  <h1>/gode<span class="g">lic</span></h1>
  <div class="hero-sub">// SERVER MANAGEMENT TERMINAL //</div>
  <div class="typing-line" id="typing"></div>

  <div class="tool-cards">
    <a href="whm.php" class="tool-card whm">
      <div class="tool-card-tag">WHM API // PORT 2087</div>
      <h3>WHM PANEL</h3>
      <p>Kelola server WHM via API token. Suspend, unsuspend, terminate, dan pantau akun cPanel dari satu dashboard terpadu.</p>
      <div class="enter-link">&gt; ./akses_whm.sh <span class="hk-cursor"></span></div>
    </a>

    <a href="cpanelmanager.php" class="tool-card cpanel">
      <div class="tool-card-tag">cPANEL API // PORT 2083</div>
      <h3>cPANEL MANAGER</h3>
      <p>File manager, FTP account, database, dan direktori server langsung via cPanel API token authentication.</p>
      <div class="enter-link">&gt; ./akses_cpanel.sh <span class="hk-cursor"></span></div>
    </a>

    <a href="#" class="tool-card perl">
      <div class="tool-card-tag">#</div>
      <h3 style="color:#cc77ff;">#</h3>
      <p>#.</p>
      <div class="enter-link" style="color:var(--purple);">&gt; ./run_command.sh <span class="hk-cursor" style="background:var(--purple);"></span></div>
    </a>
  </div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div class="stat-item"><div class="stat-num">3</div><div class="stat-label">TOOLS</div></div>
  <div class="stat-item"><div class="stat-num">2087</div><div class="stat-label">WHM PORT</div></div>
  <div class="stat-item"><div class="stat-num">2083</div><div class="stat-label">cPANEL PORT</div></div>
  <div class="stat-item"><div class="stat-num">SSL</div><div class="stat-label">ENCRYPTED</div></div>
  <div class="stat-item"><div class="stat-num">API</div><div class="stat-label">AUTH METHOD</div></div>
</div>

<!-- FEATURES -->
<div class="section">
  <div class="section-head"><h2>FITUR TERSEDIA</h2></div>
  <div class="feat-grid">
    <div class="feat-item">
      <div class="feat-num">[WHM::01]</div>
      <h4>MANAGE ACCOUNTS</h4>
      <p>List, suspend, unsuspend, dan terminate akun cPanel dari panel WHM terpusat.</p>
    </div>
    <div class="feat-item">
      <div class="feat-num">[WHM::02]</div>
      <h4>CREATE ACCOUNT</h4>
      <p>Buat akun cPanel baru dengan username, domain, dan password custom langsung dari dashboard.</p>
    </div>
    <div class="feat-item">
      <div class="feat-num">[WHM::03]</div>
      <h4>AUTOLOGIN URL</h4>
      <p>Generate autologin URL untuk akses langsung ke WHM UI tanpa perlu input ulang credentials.</p>
    </div>
    <div class="feat-item">
      <div class="feat-num">[cP::01]</div>
      <h4>FILE MANAGER</h4>
      <p>Browse direktori server, navigasi folder, upload & buat file baru langsung dari browser.</p>
    </div>
    <div class="feat-item">
      <div class="feat-num">[cP::02]</div>
      <h4>FTP MANAGER</h4>
      <p>Buat & hapus akun FTP dengan username, password, quota, dan direktori target yang custom.</p>
    </div>
    <div class="feat-item">
      <div class="feat-num">[cP::03]</div>
      <h4>DATABASE MANAGER</h4>
      <p>Buat database MySQL, user, dan assign privilege langsung via cPanel API.</p>
    </div>
  </div>
</div>

<!-- TERMINAL PREVIEW -->
<div class="section" style="padding-top:0;">
  <div class="section-head"><h2>TERMINAL</h2></div>
  <div class="terminal-wrap">
    <div class="term-bar">
      <div class="td td-r"></div><div class="td td-y"></div><div class="td td-g"></div>
      <span style="margin-left:8px;">bash — root@godelic:~</span>
    </div>
    <div class="term-body">
      <div><span class="term-prompt">root@godelic:~# </span><span>./connect_whm.sh --host yourdomain.com --port 2087</span></div>
      <div class="term-out">&gt; Connecting via TLS...</div>
      <div class="term-ok">&gt; Authentication: SUCCESS ✓</div>
      <div class="term-out">&gt; Listing accounts... 12 found</div>
      <div style="margin-top:8px;"><span class="term-prompt">root@godelic:~# </span><span>./connect_cpanel.sh --host yourdomain.com --port 2083</span></div>
      <div class="term-out">&gt; Connecting via TLS...</div>
      <div class="term-ok">&gt; Authentication: SUCCESS ✓</div>
      <div class="term-out">&gt; File system ready. FTP manager ready.</div>
      <div style="margin-top:8px;"><span class="term-prompt">root@godelic:~# </span><span class="term-cursor"></span></div>
    </div>
  </div>
</div>

<footer>
  <div>none // COMMAND CENTER</div>
  <div>© 2026 — ENCRYPTED // ALL RIGHTS RESERVED</div>
</footer>

</div>

<script>
// Matrix
const canvas=document.getElementById('matrix');
const ctx=canvas.getContext('2d');
const chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*<>/\\|{}[]01アイウエオ';
const fs=13; let drops=[];
function resize(){
  canvas.width=window.innerWidth; canvas.height=window.innerHeight;
  drops=Array(Math.floor(canvas.width/fs)).fill(0).map(()=>Math.random()*canvas.height/fs|0);
}
resize(); window.addEventListener('resize',resize);
function draw(){
  ctx.fillStyle='rgba(0,0,0,0.04)'; ctx.fillRect(0,0,canvas.width,canvas.height);
  drops.forEach((y,i)=>{
    ctx.fillStyle='#aaffaa'; ctx.font='bold '+fs+'px monospace';
    ctx.fillText(chars[Math.random()*chars.length|0],i*fs,y*fs);
    ctx.fillStyle='#00cc33'; ctx.font=fs+'px monospace';
    ctx.fillText(chars[Math.random()*chars.length|0],i*fs,(y-1)*fs);
    if(y*fs>canvas.height&&Math.random()>.97)drops[i]=0;
    drops[i]++;
  });
}
setInterval(draw,42);

// Typing
const msgs=["Welcome to none...","Your server. Your rules.","WHM + cPanel in one place.","Select a tool to begin."];
let mi=0,ci=0,el=document.getElementById('typing');
function type(){
  if(ci<msgs[mi].length){el.textContent+=msgs[mi][ci++];setTimeout(type,46);}
  else setTimeout(erase,1800);
}
function erase(){
  if(el.textContent.length>0){el.textContent=el.textContent.slice(0,-1);setTimeout(erase,22);}
  else{mi=(mi+1)%msgs.length;ci=0;setTimeout(type,400);}
}
type();
</script>
</body>
</html>
