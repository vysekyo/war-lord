<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

function clean_host($host) {
    $host = trim($host);
    $host = preg_replace('#^https?://#', '', $host);
    $host = preg_replace('#/.*$#', '', $host);
    $host = str_replace(':2087', '', $host);
    return $host;
}

function whm_api($host, $token, $endpoint, $method = 'GET') {
    $url = "https://{$host}:2087/json-api/{$endpoint}";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ["Authorization: whm root:{$token}"],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($response, true);
}

/* ── Auth ── */
if (isset($_POST['login'])) {
    $_SESSION['host']  = clean_host($_POST['host']);
    $_SESSION['user']  = trim($_POST['user']);
    $_SESSION['token'] = trim($_POST['token']);
    header('Location: ?dashboard');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ?');
    exit;
}

/* ── Login Page ── */
if (!isset($_SESSION['host']) || !isset($_SESSION['token'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WHM // GODELIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #020804;
            --panel:   rgba(4,12,4,.95);
            --green:   #00ff41;
            --cyan:    #00d4ff;
            --red:     #ff4d4d;
            --border:  rgba(0,255,65,.15);
            --glow:    0 0 20px rgba(0,255,65,.3);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #000;
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Matrix canvas */
        #matrix {
            position: fixed; inset: 0;
            z-index: 0; opacity: .07;
            pointer-events: none;
        }

        /* Scanlines overlay */
        body::after {
            content: '';
            position: fixed; inset: 0; z-index: 1;
            background: repeating-linear-gradient(
                to bottom,
                transparent 0px,
                transparent 2px,
                rgba(0,255,65,.015) 2px,
                rgba(0,255,65,.015) 4px
            );
            pointer-events: none;
        }

        /* ── Navbar ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 10;
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 32px;
            background: rgba(0,0,0,.6);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(14px);
        }

        .nav-logo {
            font-family: 'Orbitron', monospace;
            font-weight: 900; font-size: 20px;
            color: #fff; text-decoration: none;
            letter-spacing: 3px;
            text-shadow: 0 0 14px rgba(0,255,65,.4);
        }

        .nav-logo span { color: var(--green); }

        .nav-links { display: flex; gap: 24px; }
        .nav-links a {
            color: #5dff85; text-decoration: none;
            font-size: 12px; letter-spacing: 2px;
            transition: .2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #fff;
            text-shadow: 0 0 10px rgba(0,255,65,.7);
        }

        /* ── Status bar ── */
        .statusbar {
            position: fixed; top: 64px; left: 50%; transform: translateX(-50%);
            z-index: 9;
            display: flex; gap: 24px; align-items: center;
            padding: 8px 20px; border-radius: 999px;
            background: rgba(0,0,0,.5);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            font-size: 11px; color: #7dff9e; letter-spacing: 1.5px;
            white-space: nowrap;
        }

        .pulse {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 10px var(--green);
            animation: pulse 1.5s ease-in-out infinite;
            display: inline-block; margin-right: 6px;
        }

        @keyframes pulse {
            0%,100% { opacity: 1; box-shadow: 0 0 10px var(--green); }
            50%      { opacity: .4; box-shadow: none; }
        }

        /* ── Login box ── */
        .login-wrap {
            position: relative; z-index: 5;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 120px 16px 40px;
        }

        .terminal-window {
            width: 100%; max-width: 520px;
            animation: slideUp .6s cubic-bezier(.22,1,.36,1) forwards;
            transform: translateY(40px); opacity: 0;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .terminal-bar {
            height: 44px;
            background: rgba(0,20,0,.9);
            border: 1px solid var(--border);
            border-bottom: none;
            border-radius: 16px 16px 0 0;
            display: flex; align-items: center; padding: 0 16px; gap: 8px;
            font-size: 12px; color: #5dff85; letter-spacing: 1px;
        }

        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot-r { background: #ff5f56; }
        .dot-y { background: #ffbd2e; }
        .dot-g { background: #27c93f; animation: pulse 1.5s ease-in-out infinite; }

        .terminal-body {
            background: rgba(4,12,4,.97);
            border: 1px solid var(--border);
            border-radius: 0 0 18px 18px;
            padding: 36px 32px 32px;
            box-shadow:
                0 0 40px rgba(0,255,65,.08),
                inset 0 0 30px rgba(0,255,65,.03);
            backdrop-filter: blur(12px);
        }

        .terminal-title {
            text-align: center;
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: 28px;
            color: #fff;
            letter-spacing: 4px;
            text-shadow: 0 0 20px rgba(0,255,65,.4);
            margin-bottom: 6px;
        }

        .terminal-sub {
            text-align: center;
            font-size: 10px;
            letter-spacing: 3px;
            color: rgba(0,255,65,.45);
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 10px; letter-spacing: 3px;
            color: rgba(0,255,65,.6);
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%; padding: 13px 16px;
            background: rgba(0,0,0,.6);
            border: 1px solid rgba(0,255,65,.18);
            border-radius: 10px;
            color: #fff;
            font-family: 'Share Tech Mono', monospace;
            font-size: 14px;
            transition: .2s;
            outline: none;
        }

        .form-group input:focus {
            border-color: rgba(0,255,65,.5);
            box-shadow: 0 0 0 3px rgba(0,255,65,.08), 0 0 16px rgba(0,255,65,.1);
        }

        .form-group input::placeholder { color: rgba(255,255,255,.2); }

        .btn-login {
            width: 100%; margin-top: 10px;
            padding: 15px;
            background: linear-gradient(135deg, var(--green), var(--cyan));
            border: none; border-radius: 12px;
            font-family: 'Orbitron', monospace;
            font-weight: 700; font-size: 13px;
            letter-spacing: 3px;
            color: #000; cursor: pointer;
            transition: .25s;
            box-shadow: 0 0 20px rgba(0,255,65,.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 30px rgba(0,255,65,.35);
        }

        .btn-login:active { transform: translateY(0); }
    </style>
</head>
<body>
<canvas id="matrix"></canvas>

<nav class="nav">
    <a href="#" class="nav-logo">/gode<span>lic</span></a>
    <div class="nav-links">
        <a href="index.php" class="active">./home</a>
        <a href="whm.php">./whm-panel</a>
        <a href="cpanelmanager.php">./cpanel-manager</a>
        <a href="perlalfar.php" class="purple">./perl-rce</a>
    </div>
</nav>

<div class="statusbar">
    <span><span class="pulse"></span>AWAITING AUTH</span>
    <span>PROTOCOL: WHM API</span>
    <span>PORT: 2087</span>
    <span>TLS: ACTIVE</span>
</div>

<div class="login-wrap">
    <div class="terminal-window">
        <div class="terminal-bar">
            <div class="dot dot-r"></div>
            <div class="dot dot-y"></div>
            <div class="dot dot-g"></div>
            <span style="margin-left:6px;">WHM — API Token Authentication</span>
        </div>
        <div class="terminal-body">
            <div class="terminal-title">WHM PANEL</div>
            <div class="terminal-sub">// ROOT API TOKEN AUTHENTICATION //</div>

            <form method="POST">
                <div class="form-group">
                    <label>Host</label>
                    <input type="text" name="host" placeholder="domain.com or IP" required>
                </div>
                <div class="form-group">
                    <label>User</label>
                    <input type="text" name="user" value="root" required>
                </div>
                <div class="form-group">
                    <label>WHM API Token</label>
                    <input type="password" name="token" placeholder="Paste your WHM API Token" required>
                </div>
                <button type="submit" name="login" class="btn-login">AUTHENTICATE &rarr;</button>
            </form>
        </div>
    </div>
</div>

<script>
const canvas = document.getElementById('matrix');
const ctx    = canvas.getContext('2d');
const chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()_+-=[]{}|;:,.<>?';
const fs     = 14;
let cols, drops;

function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    cols  = Math.floor(canvas.width / fs);
    drops = Array(cols).fill(1);
}
resize();
window.addEventListener('resize', resize);

function draw() {
    ctx.fillStyle = 'rgba(0,0,0,.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fs + 'px monospace';
    for (let i = 0; i < drops.length; i++) {
        ctx.fillText(chars[Math.floor(Math.random() * chars.length)], i * fs, drops[i] * fs);
        if (drops[i] * fs > canvas.height && Math.random() > .975) drops[i] = 0;
        drops[i]++;
    }
}
setInterval(draw, 50);
</script>
</body>
</html>
<?php
exit;
}

/* ── Dashboard Logic ── */
$host  = $_SESSION['host'];
$token = $_SESSION['token'];

/* Actions */
if (isset($_GET['suspend'])) {
    whm_api($host, $token, "suspendacct?user=" . urlencode($_GET['suspend']) . "&reason=Suspended+from+WHM+Dashboard");
    header('Location: ?dashboard'); exit;
}
if (isset($_GET['unsuspend'])) {
    whm_api($host, $token, "unsuspendacct?user=" . urlencode($_GET['unsuspend']));
    header('Location: ?dashboard'); exit;
}
if (isset($_GET['terminate'])) {
    whm_api($host, $token, "removeacct?user=" . urlencode($_GET['terminate']));
    header('Location: ?dashboard'); exit;
}

/* Fetch accounts */
$list     = whm_api($host, $token, 'listaccts?api.version=1');
$accounts = $list['data']['acct'] ?? [];

/* Stats — FIX: variable name mismatch was here */
$total     = count($accounts);
$active    = 0;
$suspended = 0;

foreach ($accounts as $acc) {
    if ($acc['suspended'] == 1) $suspended++;
    else $active++;
}

/* WHM session URL */
$session    = whm_api($host, $token, 'create_user_session?api.version=1&user=root&service=whostmgrd');
$whmLoginUrl = isset($session['data']['url']) ? $session['data']['url'] : '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WHM Dashboard // GODELIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #020804;
            --surface: rgba(4,14,4,.96);
            --surface2: rgba(0,8,0,.98);
            --green:   #00ff41;
            --green2:  #00cc33;
            --cyan:    #00d4ff;
            --red:     #ff4444;
            --amber:   #ffcc00;
            --border:  rgba(0,255,65,.12);
            --border2: rgba(0,255,65,.22);
            --text:    rgba(255,255,255,.88);
            --muted:   rgba(0,255,65,.45);
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

        /* Grid background */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(0,255,65,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,255,65,.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Scanlines */
        body::after {
            content: '';
            position: fixed; inset: 0; z-index: 1;
            background: repeating-linear-gradient(
                to bottom,
                transparent 0, transparent 2px,
                rgba(0,0,0,.08) 2px, rgba(0,0,0,.08) 4px
            );
            pointer-events: none;
        }

        #matrix-bg {
            position: fixed; inset: 0; z-index: 0;
            opacity: .045; pointer-events: none;
        }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 32px;
            height: 64px;
            background: rgba(0,0,0,.85);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(16px);
        }

        .topbar-left {
            display: flex; align-items: center; gap: 32px;
        }

        .logo {
            font-family: 'Orbitron', monospace;
            font-weight: 900; font-size: 18px;
            color: #fff; letter-spacing: 3px;
            text-shadow: 0 0 16px rgba(0,255,65,.35);
            text-decoration: none;
        }

        .logo span { color: var(--green); }

        .topbar-nav { display: flex; gap: 20px; }
        .topbar-nav a {
            font-size: 11px; letter-spacing: 2px;
            color: var(--muted); text-decoration: none;
            transition: .2s; padding: 4px 0;
            border-bottom: 1px solid transparent;
        }
        .topbar-nav a:hover, .topbar-nav a.active {
            color: var(--green);
            border-bottom-color: var(--green);
            text-shadow: 0 0 8px rgba(0,255,65,.5);
        }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .conn-badge {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(0,255,65,.07);
            border: 1px solid var(--border);
            font-size: 11px; color: var(--muted); letter-spacing: 1px;
        }

        .conn-badge .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
            animation: blink 1.8s ease-in-out infinite;
        }

        @keyframes blink {
            0%,100% { opacity: 1; }
            50%      { opacity: .3; }
        }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 18px; border-radius: 10px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px; letter-spacing: 1.5px;
            text-decoration: none; cursor: pointer;
            transition: .2s; border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--green), var(--cyan));
            color: #000; font-weight: bold;
            box-shadow: 0 0 16px rgba(0,255,65,.2);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 24px rgba(0,255,65,.35);
        }

        .btn-danger {
            background: rgba(255,68,68,.1);
            border: 1px solid rgba(255,68,68,.25);
            color: #ff6b6b;
        }
        .btn-danger:hover {
            background: rgba(255,68,68,.18);
            box-shadow: 0 0 14px rgba(255,68,68,.2);
        }

        /* ══════════════════════════════════════
           MAIN
        ══════════════════════════════════════ */
        .main {
            position: relative; z-index: 2;
            padding: 32px;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* ══════════════════════════════════════
           HERO BANNER
        ══════════════════════════════════════ */
        .hero {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            margin-bottom: 28px;
        }

        .hero-main {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-main::before {
            content: '';
            position: absolute; top: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(0,255,65,.12), transparent 70%);
            pointer-events: none;
        }

        .hero-main::after {
            content: '';
            position: absolute; bottom: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--green), var(--cyan), transparent);
        }

        .hero-tag {
            font-size: 10px; letter-spacing: 4px;
            color: var(--red);
            text-shadow: 0 0 8px var(--red);
            margin-bottom: 14px;
        }

        .hero-title {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: clamp(32px, 4vw, 56px);
            color: #fff;
            letter-spacing: 4px;
            line-height: 1.05;
            text-shadow: 0 0 30px rgba(0,255,65,.2);
            margin-bottom: 16px;
        }

        .hero-title span { color: var(--green); }

        .hero-desc {
            font-size: 13px; color: var(--muted);
            letter-spacing: 1px; line-height: 1.9;
        }

        .hero-desc strong { color: #fff; }

        /* Terminal panel */
        .hero-terminal {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            display: flex; flex-direction: column;
        }

        .term-bar {
            height: 40px;
            background: rgba(0,20,0,.5);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 16px; gap: 8px;
            font-size: 11px; color: var(--muted); letter-spacing: 1px;
            flex-shrink: 0;
        }

        .td { width: 11px; height: 11px; border-radius: 50%; }
        .td-r { background: #ff5f56; }
        .td-y { background: #ffbd2e; }
        .td-g { background: #27c93f; animation: blink 1.8s infinite; }

        .term-body {
            flex: 1; padding: 20px;
            font-size: 12px; line-height: 2.1;
            color: var(--green);
        }

        .term-line { display: flex; gap: 8px; }
        .term-prompt { color: var(--cyan); flex-shrink: 0; }
        .term-cmd { color: #fff; }
        .term-out { color: rgba(0,255,65,.7); padding-left: 16px; }
        .term-ok  { color: var(--green); padding-left: 16px; }

        .term-cursor {
            display: inline-block; width: 8px; height: 14px;
            background: var(--green);
            animation: cur 1s step-end infinite;
            vertical-align: middle; margin-left: 4px;
        }

        @keyframes cur { 0%,100% { opacity: 1; } 50% { opacity: 0; } }

        /* ══════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════ */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: .25s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--border2);
            box-shadow: 0 8px 32px rgba(0,255,65,.1);
        }

        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 100%; height: 2px;
        }

        .stat-card.c-green::before  { background: linear-gradient(90deg, var(--green), var(--cyan)); }
        .stat-card.c-cyan::before   { background: linear-gradient(90deg, var(--cyan), #0099dd); }
        .stat-card.c-red::before    { background: linear-gradient(90deg, var(--red), #ff9900); }
        .stat-card.c-amber::before  { background: linear-gradient(90deg, var(--amber), var(--green)); }

        .stat-icon {
            font-size: 22px; margin-bottom: 16px; display: block;
            filter: drop-shadow(0 0 6px rgba(0,255,65,.5));
        }

        .stat-tag {
            font-size: 9px; letter-spacing: 3px;
            color: var(--muted); margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-value {
            font-family: 'Orbitron', monospace;
            font-size: 36px; font-weight: 900;
            color: #fff;
            text-shadow: 0 0 20px rgba(0,255,65,.15);
        }

        .stat-value.small { font-size: 16px; margin-top: 4px; }

        .stat-hint {
            margin-top: 10px; font-size: 11px;
            color: rgba(0,255,65,.35); line-height: 1.6;
        }

        /* ══════════════════════════════════════
           TABLE
        ══════════════════════════════════════ */
        .table-section { }

        .section-head {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Orbitron', monospace;
            font-size: 14px; letter-spacing: 3px;
            color: #fff;
        }

        .section-badge {
            font-size: 11px; letter-spacing: 2px;
            color: var(--muted);
        }

        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }

        thead tr {
            background: rgba(0,255,65,.04);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 14px 18px;
            font-family: 'Orbitron', monospace;
            font-size: 10px; letter-spacing: 2.5px;
            color: var(--muted); text-align: left;
            font-weight: 400;
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(0,255,65,.05);
            font-size: 13px; color: var(--text);
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr {
            transition: .15s;
        }

        tbody tr:hover {
            background: rgba(0,255,65,.035);
        }

        .td-user {
            display: flex; align-items: center; gap: 10px;
        }

        .user-avatar {
            width: 34px; height: 34px; border-radius: 8px;
            background: rgba(0,255,65,.08);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: var(--green);
            font-family: 'Orbitron', monospace; font-weight: 700;
            flex-shrink: 0;
        }

        .td-domain { color: rgba(255,255,255,.55); font-size: 12px; }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 999px;
            font-size: 11px; letter-spacing: 1.5px; font-weight: bold;
        }

        .badge::before {
            content: ''; width: 6px; height: 6px;
            border-radius: 50%; flex-shrink: 0;
        }

        .badge-active {
            background: rgba(0,255,65,.1);
            border: 1px solid rgba(0,255,65,.2);
            color: var(--green);
        }
        .badge-active::before { background: var(--green); box-shadow: 0 0 6px var(--green); }

        .badge-suspended {
            background: rgba(255,68,68,.1);
            border: 1px solid rgba(255,68,68,.2);
            color: var(--red);
        }
        .badge-suspended::before { background: var(--red); }

        /* Action buttons */
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .act {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 13px; border-radius: 8px;
            font-size: 11px; letter-spacing: 1px;
            text-decoration: none; transition: .2s;
            font-family: 'Share Tech Mono', monospace;
            cursor: pointer;
        }

        .act-suspend {
            background: rgba(255,204,0,.09);
            border: 1px solid rgba(255,204,0,.2);
            color: var(--amber);
        }
        .act-suspend:hover { background: rgba(255,204,0,.18); transform: translateY(-1px); }

        .act-unsuspend {
            background: rgba(0,255,65,.09);
            border: 1px solid rgba(0,255,65,.2);
            color: var(--green);
        }
        .act-unsuspend:hover { background: rgba(0,255,65,.18); transform: translateY(-1px); }

        .act-terminate {
            background: rgba(255,68,68,.09);
            border: 1px solid rgba(255,68,68,.2);
            color: var(--red);
        }
        .act-terminate:hover { background: rgba(255,68,68,.18); transform: translateY(-1px); }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 1100px) {
            .hero  { grid-template-columns: 1fr; }
            .stats { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 640px) {
            .main  { padding: 16px; }
            .stats { grid-template-columns: 1fr 1fr; gap: 12px; }
            .topbar { padding: 0 16px; }
            .topbar-nav { display: none; }
            table { font-size: 12px; }
            th, td { padding: 10px 12px; }
        }

        /* Fade in animation for table rows */
        tbody tr {
            animation: fadeRow .4s ease both;
        }
        <?php foreach ($accounts as $i => $a): ?>
        tbody tr:nth-child(<?= $i+1 ?>) { animation-delay: <?= $i * 0.04 ?>s; }
        <?php endforeach; ?>

        @keyframes fadeRow {
            from { opacity: 0; transform: translateX(-8px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>

<canvas id="matrix-bg"></canvas>

<!-- TOPBAR -->
<header class="topbar">
    <div class="topbar-left">
        <a href="#" class="logo">/gode<span>lic</span></a>
        <nav class="topbar-nav">
            <a href="index.php" class="active">./home</a>
            <a href="whm.php">./whm-panel</a>
            <a href="cpanelmanager.php">./cpanel-manager</a>
            <a href="perlalfar.php" class="purple">./perl-rce</a>
        </nav>
    </div>
    <div class="topbar-right">
        <div class="conn-badge">
            <span class="dot"></span>
            <?php echo htmlspecialchars($host); ?>
        </div>
        <a class="btn btn-primary" href="<?php echo htmlspecialchars($whmLoginUrl); ?>" target="_blank">
            ⬡ OPEN WHM UI
        </a>
        <a class="btn btn-danger" href="?logout">⏻ LOGOUT</a>
    </div>
</header>

<main class="main">

    <!-- HERO -->
    <div class="hero">
        <div class="hero-main">
            <div class="hero-tag">&gt; WHM CONTROL PANEL // AUTHENTICATED</div>
            <h1 class="hero-title">WHM<br><span>DASHBOARD</span></h1>
            <p class="hero-desc">
                Connected to <strong><?php echo htmlspecialchars($host); ?></strong> via WHM API Token.<br>
                Session active &mdash; root privileges granted &mdash; port 2087 secured.
            </p>
        </div>

        <div class="hero-terminal">
            <div class="term-bar">
                <div class="td td-r"></div>
                <div class="td td-y"></div>
                <div class="td td-g"></div>
                <span style="margin-left:6px;">root@<?php echo htmlspecialchars($host); ?></span>
            </div>
            <div class="term-body">
                <div class="term-line">
                    <span class="term-prompt">root@whm:~#</span>
                    <span class="term-cmd">authenticate --token</span>
                </div>
                <div class="term-ok">[+] Authentication: SUCCESS</div>

                <div class="term-line" style="margin-top:6px;">
                    <span class="term-prompt">root@whm:~#</span>
                    <span class="term-cmd">listaccts --all</span>
                </div>
                <div class="term-out">[~] Total accounts: <?php echo $total; ?></div>
                <div class="term-ok">[+] Active: <?php echo $active; ?> / Suspended: <?php echo $suspended; ?></div>

                <div class="term-line" style="margin-top:6px;">
                    <span class="term-prompt">root@whm:~#</span>
                    <span class="term-cmd">status --session</span>
                </div>
                <div class="term-ok">[+] Control center ready<span class="term-cursor"></span></div>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card c-green">
            <span class="stat-icon">◈</span>
            <div class="stat-tag">TOTAL ACCOUNTS</div>
            <div class="stat-value"><?php echo $total; ?></div>
            <div class="stat-hint">All cPanel accounts on this server</div>
        </div>

        <div class="stat-card c-cyan">
            <span class="stat-icon">◉</span>
            <div class="stat-tag">ACTIVE</div>
            <div class="stat-value"><?php echo $active; ?></div>
            <div class="stat-hint">Operational accounts currently online</div>
        </div>

        <div class="stat-card c-red">
            <span class="stat-icon">⊗</span>
            <div class="stat-tag">SUSPENDED</div>
            <div class="stat-value"><?php echo $suspended; ?></div>
            <div class="stat-hint">Accounts blocked from WHM access</div>
        </div>

        <div class="stat-card c-amber">
            <span class="stat-icon">⬡</span>
            <div class="stat-tag">CONNECTED HOST</div>
            <div class="stat-value small"><?php echo htmlspecialchars($host); ?></div>
            <div class="stat-hint">Current WHM API endpoint</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-section">
        <div class="section-head">
            <div class="section-title">ACCOUNT LIST</div>
            <div class="section-badge"><?php echo $total; ?> ENTRIES // PORT 2087</div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>USERNAME</th>
                        <th>DOMAIN</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($accounts as $acc):
                    $u = htmlspecialchars($acc['user']);
                    $d = htmlspecialchars($acc['domain']);
                    $isSuspended = $acc['suspended'] == 1;
                ?>
                    <tr>
                        <td>
                            <div class="td-user">
                                <div class="user-avatar"><?php echo strtoupper(substr($acc['user'],0,2)); ?></div>
                                <?php echo $u; ?>
                            </div>
                        </td>
                        <td class="td-domain"><?php echo $d; ?></td>
                        <td>
                            <?php if ($isSuspended): ?>
                                <span class="badge badge-suspended">SUSPENDED</span>
                            <?php else: ?>
                                <span class="badge badge-active">ACTIVE</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions">
                                <?php if ($isSuspended): ?>
                                    <a class="act act-unsuspend"
                                       href="?unsuspend=<?php echo urlencode($acc['user']); ?>">
                                        ▶ UNSUSPEND
                                    </a>
                                <?php else: ?>
                                    <a class="act act-suspend"
                                       href="?suspend=<?php echo urlencode($acc['user']); ?>">
                                        ⏸ SUSPEND
                                    </a>
                                <?php endif; ?>
                                <a class="act act-terminate"
                                   onclick="return confirm('Terminate <?php echo $u; ?>? This cannot be undone.')"
                                   href="?terminate=<?php echo urlencode($acc['user']); ?>">
                                    ✕ TERMINATE
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;padding:40px;color:rgba(0,255,65,.3);">
                            NO ACCOUNTS FOUND
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
/* Matrix background */
const canvas = document.getElementById('matrix-bg');
const ctx    = canvas.getContext('2d');
const chars  = '01ABCDEFアイウエオカキクケコ@#$%^&*';
const fs     = 13;
let cols, drops;

function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    cols  = Math.floor(canvas.width / fs);
    drops = Array(cols).fill(1);
}
resize();
window.addEventListener('resize', resize);

function draw() {
    ctx.fillStyle = 'rgba(0,0,0,.06)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fs + 'px monospace';
    for (let i = 0; i < drops.length; i++) {
        ctx.fillText(chars[Math.floor(Math.random() * chars.length)], i * fs, drops[i] * fs);
        if (drops[i] * fs > canvas.height && Math.random() > .975) drops[i] = 0;
        drops[i]++;
    }
}
setInterval(draw, 55);
</script>

</body>
</html>