<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ══════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════ */
function clean_host($host) {
    $host = trim($host);
    $host = preg_replace('#^https?://#', '', $host);
    $host = preg_replace('#/.*$#', '', $host);
    $host = str_replace([':2083',':2082'], '', $host);
    return $host;
}

function cpanel_uapi($host, $user, $token, $module, $function, $params = []) {
    $url = "https://{$host}:2083/execute/{$module}/{$function}";
    if ($params) $url .= '?' . http_build_query($params);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => ["Authorization: cpanel {$user}:{$token}"],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp = curl_exec($ch);
    if (curl_errno($ch)) return ['errors' => [curl_error($ch)]];
    curl_close($ch);
    return json_decode($resp, true) ?? ['errors' => ['Invalid JSON']];
}

function cpanel_uapi_post($host, $user, $token, $module, $function, $params = []) {
    $url = "https://{$host}:2083/execute/{$module}/{$function}";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($params),
        CURLOPT_HTTPHEADER     => ["Authorization: cpanel {$user}:{$token}"],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp = curl_exec($ch);
    if (curl_errno($ch)) return ['errors' => [curl_error($ch)]];
    curl_close($ch);
    return json_decode($resp, true) ?? ['errors' => ['Invalid JSON']];
}

/* ── Login / Logout ── */
if (isset($_POST['login'])) {
    $_SESSION['cp_host']  = clean_host($_POST['host']);
    $_SESSION['cp_user']  = trim($_POST['user']);
    $_SESSION['cp_token'] = trim($_POST['token']);
    header('Location: ?dashboard'); exit;
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: ?'); exit; }

/* ── Login Page ── */
if (!isset($_SESSION['cp_host']) || !isset($_SESSION['cp_token'])):
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>cPanel Manager — /godelic</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080c10;
  --surface: #0d1117;
  --surface2: #111820;
  --cyan: #38bdf8;
  --cyan-dim: rgba(56,189,248,0.12);
  --cyan-glow: rgba(56,189,248,0.25);
  --green: #4ade80;
  --border: rgba(56,189,248,0.1);
  --border2: rgba(56,189,248,0.22);
  --muted: rgba(56,189,248,0.45);
  --text: rgba(255,255,255,0.85);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background: var(--bg);
  color: var(--cyan);
  font-family: 'JetBrains Mono', monospace;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

/* Grid bg */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(56,189,248,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(56,189,248,0.03) 1px, transparent 1px);
  background-size: 40px 40px;
}

/* Glow orb */
body::after {
  content: '';
  position: fixed;
  width: 600px; height: 600px;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  background: radial-gradient(circle, rgba(56,189,248,0.06) 0%, transparent 70%);
  z-index: 0;
  pointer-events: none;
}

.login-card {
  position: relative; z-index: 2;
  width: 440px;
  background: var(--surface);
  border: 1px solid var(--border2);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 0 60px rgba(56,189,248,0.05), 0 40px 80px rgba(0,0,0,0.5);
  animation: fadeUp 0.5s ease both;
}
@keyframes fadeUp {
  from { transform: translateY(24px); opacity: 0; }
  to   { transform: translateY(0); opacity: 1; }
}

.card-top {
  background: linear-gradient(135deg, rgba(56,189,248,0.08) 0%, transparent 60%);
  border-bottom: 1px solid var(--border);
  padding: 32px 36px 28px;
}
.card-top-bar {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 28px;
}
.dot { width: 11px; height: 11px; border-radius: 50%; }
.dot-r { background: #ff5f56; }
.dot-y { background: #ffbd2e; }
.dot-g { background: #27c93f; }

.logo-text {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 28px;
  color: #fff;
  letter-spacing: -0.5px;
}
.logo-text span { color: var(--cyan); }

.logo-sub {
  margin-top: 4px;
  font-size: 11px;
  letter-spacing: 3px;
  color: var(--muted);
  text-transform: uppercase;
}

.card-body { padding: 28px 36px 36px; }

.fg { margin-bottom: 18px; }
.fg label {
  display: block;
  font-size: 10px;
  letter-spacing: 2.5px;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 8px;
}
.fg input {
  width: 100%;
  padding: 12px 16px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: #fff;
  font-family: 'JetBrains Mono', monospace;
  font-size: 13px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.fg input:focus {
  border-color: rgba(56,189,248,0.5);
  box-shadow: 0 0 0 3px rgba(56,189,248,0.08);
}
.fg input::placeholder { color: rgba(255,255,255,0.2); }

.btn-auth {
  width: 100%;
  margin-top: 8px;
  padding: 14px;
  background: var(--cyan);
  border: none;
  border-radius: 11px;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 700;
  font-size: 13px;
  letter-spacing: 2px;
  color: #080c10;
  cursor: pointer;
  transition: all 0.2s;
  text-transform: uppercase;
}
.btn-auth:hover {
  background: #7dd3fc;
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(56,189,248,0.3);
}

.status-bar {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 24px;
  padding: 10px 14px;
  background: rgba(56,189,248,0.04);
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 10px;
  letter-spacing: 2px;
  color: var(--muted);
}
.pulse {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--cyan);
  box-shadow: 0 0 6px var(--cyan);
  animation: pulse 2s ease-in-out infinite;
  flex-shrink: 0;
}
@keyframes pulse { 0%,100%{opacity:1;}50%{opacity:0.3;} }
</style>
</head>
<body>
<div class="login-card">
  <div class="card-top">
    <div class="card-top-bar">
      <div class="dot dot-r"></div>
      <div class="dot dot-y"></div>
      <div class="dot dot-g"></div>
    </div>
    <div class="logo-text">/gode<span>lic</span></div>
    <div class="logo-sub">cPanel Manager // UAPI Token Auth</div>
  </div>
  <div class="card-body">
    <div class="status-bar">
      <span class="pulse"></span>
      AWAITING CREDENTIALS — PORT 2083 TLS
    </div>
    <form method="POST">
      <div class="fg">
        <label>Host</label>
        <input type="text" name="host" placeholder="domain.com atau IP" required autocomplete="off">
      </div>
      <div class="fg">
        <label>cPanel Username</label>
        <input type="text" name="user" placeholder="username" required autocomplete="off">
      </div>
      <div class="fg">
        <label>API Token</label>
        <input type="password" name="token" placeholder="Paste cPanel API Token" required>
      </div>
      <button type="submit" name="login" class="btn-auth">Authenticate →</button>
    </form>
  </div>
</div>
</body>
</html>
<?php exit; endif;

/* ══════════════════════════════════════════════
   DASHBOARD
══════════════════════════════════════════════ */
$host  = $_SESSION['cp_host'];
$user  = $_SESSION['cp_user'];
$token = $_SESSION['cp_token'];

/* ── AJAX handlers ── */
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $action = $_GET['ajax'];

    /* Create cPanel session for auto-login */
    if ($action === 'cpanel_session') {
        // Try UAPI session creation
        $res = cpanel_uapi_post($host, $user, $token, 'Session', 'create', []);
        if (!empty($res['data']['security_token'])) {
            $token_str = $res['data']['security_token'];
            echo json_encode(['url' => "https://{$host}:2083/{$token_str}/frontend/paper_lantern/index.html"]);
        } else {
            // Fallback: direct token-based URL
            // Some cPanel versions support ?login=1 with cpsess
            $ch = curl_init();
            $loginUrl = "https://{$host}:2083/login/?login_only=1";
            curl_setopt_array($ch, [
                CURLOPT_URL            => $loginUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query(['user' => $user, 'token' => $token, 'goto_uri' => '/']),
                CURLOPT_HTTPHEADER     => ["Authorization: cpanel {$user}:{$token}"],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_HEADER         => true,
            ]);
            $resp = curl_exec($ch);
            curl_close($ch);
            // Parse Location header
            if (preg_match('/Location:\s*(https?:\/\/[^\r\n]+)/i', $resp, $m)) {
                echo json_encode(['url' => trim($m[1])]);
            } else {
                // Last fallback: open with auth header via redirect trick
                echo json_encode(['url' => "https://{$host}:2083", 'fallback' => true]);
            }
        }
        exit;
    }

    if ($action === 'listdir') {
        $dir = $_GET['dir'] ?? '/home/' . $user;
        $res = cpanel_uapi($host, $user, $token, 'Fileman', 'list_files', ['dir' => $dir, 'include_mime' => 1]);
        echo json_encode($res); exit;
    }
    if ($action === 'readfile') {
        $file = $_GET['file'] ?? '';
        $res  = cpanel_uapi($host, $user, $token, 'Fileman', 'get_file_content', ['file' => $file]);
        echo json_encode($res); exit;
    }
    if ($action === 'savefile') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Fileman', 'save_file_content', ['file' => $data['file'], 'content' => $data['content']]);
        echo json_encode($res); exit;
    }
    if ($action === 'mkdir') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Fileman', 'mkdir', ['path' => $data['path'], 'name' => $data['name']]);
        echo json_encode($res); exit;
    }
    if ($action === 'delete') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Fileman', 'delete_files', ['files' => json_encode([['path' => $data['path'], 'type' => $data['type']]])]);
        echo json_encode($res); exit;
    }
    if ($action === 'upload') {
        $dir   = $_POST['dir'] ?? '/home/' . $user;
        $url   = "https://{$host}:2083/execute/Fileman/upload_files";
        $ch    = curl_init();
        $cfile = new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['dir' => $dir, 'file-1' => $cfile],
            CURLOPT_HTTPHEADER     => ["Authorization: cpanel {$user}:{$token}"],
            CURLOPT_TIMEOUT        => 60,
        ]);
        $resp = curl_exec($ch); curl_close($ch);
        echo $resp; exit;
    }
    if ($action === 'ftplist') {
        $res = cpanel_uapi($host, $user, $token, 'Ftp', 'list_ftp', ['include_acct_types' => 'sub']);
        echo json_encode($res); exit;
    }
    if ($action === 'ftpcreate') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Ftp', 'add_ftp', [
            'user' => $data['user'], 'pass' => $data['pass'],
            'quota' => $data['quota'] ?? 250, 'homedir' => $data['homedir'] ?? '',
            'quota_type' => ($data['quota'] == 0) ? 'unlimited' : 'megabytes',
        ]);
        echo json_encode($res); exit;
    }
    if ($action === 'ftpdelete') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Ftp', 'delete_ftp', ['user' => $data['user'], 'destroy' => 1]);
        echo json_encode($res); exit;
    }
    if ($action === 'dblist') {
        $dbs   = cpanel_uapi($host, $user, $token, 'Mysql', 'list_databases');
        $users = cpanel_uapi($host, $user, $token, 'Mysql', 'list_users');
        echo json_encode(['databases' => $dbs, 'users' => $users]); exit;
    }
    if ($action === 'dbcreate') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Mysql', 'create_database', ['name' => $data['name']]);
        echo json_encode($res); exit;
    }
    if ($action === 'dbusercreate') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Mysql', 'create_user', ['name' => $data['name'], 'password' => $data['password']]);
        echo json_encode($res); exit;
    }
    if ($action === 'dbassign') {
        $data = json_decode(file_get_contents('php://input'), true);
        $res  = cpanel_uapi_post($host, $user, $token, 'Mysql', 'set_privileges_on_database', ['user' => $data['user'], 'database' => $data['database'], 'privileges' => 'ALL PRIVILEGES']);
        echo json_encode($res); exit;
    }
    if ($action === 'stats') {
        $res = cpanel_uapi($host, $user, $token, 'StatsBar', 'get_stats', ['display' => 'bandwidthusage|diskusage|ftpaccounts|mysqlDatabases|emailaccounts']);
        echo json_encode($res); exit;
    }

    echo json_encode(['error' => 'Unknown action']); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>/godelic // cPanel Manager</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
:root {
  --bg:        #07090d;
  --surface:   #0d1117;
  --surface2:  #10161f;
  --surface3:  #141c27;
  --cyan:      #38bdf8;
  --cyan2:     #0ea5e9;
  --cyan-dim:  rgba(56,189,248,0.08);
  --cyan-glow: rgba(56,189,248,0.2);
  --green:     #4ade80;
  --red:       #f87171;
  --amber:     #fbbf24;
  --purple:    #a78bfa;
  --border:    rgba(56,189,248,0.09);
  --border2:   rgba(56,189,248,0.2);
  --muted:     rgba(56,189,248,0.4);
  --text:      rgba(255,255,255,0.82);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }

body {
  background: var(--bg);
  color: var(--text);
  font-family: 'JetBrains Mono', monospace;
  min-height: 100vh;
  overflow-x: hidden;
}

/* Subtle grid */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(56,189,248,0.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(56,189,248,0.025) 1px, transparent 1px);
  background-size: 40px 40px;
  pointer-events: none;
}

/* ── TOPBAR ── */
.topbar {
  position: sticky; top: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px;
  height: 58px;
  background: rgba(7,9,13,0.92);
  border-bottom: 1px solid var(--border);
  backdrop-filter: blur(20px);
}

.logo {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 18px;
  color: #fff;
  text-decoration: none;
  letter-spacing: -0.3px;
}
.logo span { color: var(--cyan); }

.topbar-nav { display: flex; gap: 4px; }
.topbar-nav a {
  font-size: 11px;
  letter-spacing: 1px;
  color: var(--muted);
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 7px;
  transition: all 0.2s;
}
.topbar-nav a:hover { background: var(--cyan-dim); color: var(--cyan); }
.topbar-nav a.active { background: var(--cyan-dim); color: var(--cyan); }

.topbar-right { display: flex; align-items: center; gap: 8px; }

.conn-badge {
  display: flex; align-items: center; gap: 7px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--cyan-dim);
  border: 1px solid var(--border);
  font-size: 11px; color: var(--muted); letter-spacing: 0.5px;
}
.conn-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 6px var(--green);
  animation: blink 2s ease-in-out infinite;
  flex-shrink: 0;
}
@keyframes blink { 0%,100%{opacity:1;}50%{opacity:0.35;} }

.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px;
  border-radius: 9px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px; letter-spacing: 1px;
  text-decoration: none; cursor: pointer;
  transition: all 0.2s; border: 1px solid transparent;
  white-space: nowrap;
}
.btn-open {
  background: var(--cyan);
  color: #07090d;
  font-weight: 700;
  border-color: var(--cyan);
}
.btn-open:hover { background: #7dd3fc; box-shadow: 0 0 20px rgba(56,189,248,0.25); transform: translateY(-1px); }
.btn-open.loading-btn { opacity: 0.7; cursor: wait; }

.btn-logout {
  background: transparent;
  border-color: rgba(248,113,113,0.25);
  color: var(--red);
}
.btn-logout:hover { background: rgba(248,113,113,0.1); }

/* ── LAYOUT ── */
.layout {
  display: grid;
  grid-template-columns: 210px 1fr;
  min-height: calc(100vh - 58px);
  position: relative; z-index: 2;
}

/* ── SIDEBAR ── */
.sidebar {
  background: var(--surface);
  border-right: 1px solid var(--border);
  padding: 16px 12px;
  position: sticky; top: 58px;
  height: calc(100vh - 58px);
  overflow-y: auto;
}

.sidebar-host {
  padding: 14px;
  border: 1px solid var(--border);
  border-radius: 12px;
  background: var(--cyan-dim);
  margin-bottom: 20px;
  font-size: 10px;
  line-height: 1.9;
  color: var(--muted);
}
.sidebar-host strong {
  display: block;
  font-size: 12px;
  color: var(--cyan);
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-label {
  display: block;
  font-size: 9px;
  letter-spacing: 3px;
  color: rgba(56,189,248,0.25);
  padding: 0 6px 8px;
  text-transform: uppercase;
}

.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 12px; letter-spacing: 0.5px;
  color: var(--muted);
  border: 1px solid transparent;
  margin-bottom: 2px;
  transition: all 0.18s;
  user-select: none;
}
.nav-item:hover { background: var(--cyan-dim); color: var(--cyan); }
.nav-item.active {
  background: rgba(56,189,248,0.12);
  color: var(--cyan);
  border-color: rgba(56,189,248,0.18);
}
.nav-item-icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; }

/* ── MAIN ── */
.main { padding: 28px; overflow-y: auto; }

/* ── PANELS ── */
.panel { display: none; }
.panel.active { display: block; animation: panelIn 0.22s ease; }
@keyframes panelIn { from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);} }

.panel-head { margin-bottom: 24px; }
.panel-head h2 {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 20px;
  color: #fff;
  letter-spacing: -0.3px;
  margin-bottom: 4px;
}
.panel-head p { font-size: 11px; color: var(--muted); letter-spacing: 0.5px; }

/* ── STAT CARDS ── */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 14px;
  margin-bottom: 24px;
}

.stat-card {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 20px;
  position: relative; overflow: hidden;
  transition: border-color 0.2s, transform 0.2s;
}
.stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
.stat-card::after {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, var(--cyan), transparent);
}

.stat-label { font-size: 9px; letter-spacing: 3px; color: var(--muted); text-transform: uppercase; margin-bottom: 12px; }
.stat-value {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 30px;
  color: #fff;
  line-height: 1;
}
.stat-sub { margin-top: 8px; font-size: 10px; color: rgba(56,189,248,0.3); }

/* ── TERMINAL BLOCK ── */
.terminal-block {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  margin-top: 20px;
}
.terminal-block-bar {
  display: flex; align-items: center; gap: 7px;
  padding: 0 16px;
  height: 38px;
  background: rgba(0,0,0,0.3);
  border-bottom: 1px solid var(--border);
  font-size: 11px; color: var(--muted);
}
.dot-sm { width: 10px; height: 10px; border-radius: 50%; }
.terminal-block-body {
  padding: 20px 24px;
  font-size: 12px;
  line-height: 2.2;
  color: var(--cyan);
}
.tb-prompt { color: var(--muted); }
.tb-cmd { color: #fff; }
.tb-out { color: rgba(56,189,248,0.5); }
.tb-ok { color: var(--green); }
.cursor {
  display: inline-block;
  width: 7px; height: 13px;
  background: var(--cyan);
  vertical-align: middle;
  margin-left: 3px;
  animation: blink 1s step-end infinite;
}

/* ── FILE MANAGER ── */
.fm-bar {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 12px 12px 0 0;
  border-bottom: none;
  flex-wrap: wrap;
  gap: 8px;
}
.fm-path {
  flex: 1;
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: var(--muted);
  overflow: hidden; min-width: 0;
}
.fm-path-item { color: var(--cyan); cursor: pointer; white-space: nowrap; }
.fm-path-item:hover { text-decoration: underline; }
.fm-path-sep { color: rgba(56,189,248,0.25); }

.fm-acts { display: flex; gap: 6px; flex-shrink: 0; }
.fm-btn {
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 11px; letter-spacing: 0.5px;
  border: 1px solid var(--border);
  background: var(--cyan-dim);
  color: var(--cyan);
  cursor: pointer; transition: all 0.18s;
  font-family: inherit;
  white-space: nowrap;
}
.fm-btn:hover { background: rgba(56,189,248,0.15); border-color: var(--border2); }
.fm-btn.danger { border-color: rgba(248,113,113,0.2); background: rgba(248,113,113,0.07); color: var(--red); }
.fm-btn.danger:hover { background: rgba(248,113,113,0.15); }
.fm-btn.success { border-color: rgba(74,222,128,0.2); background: rgba(74,222,128,0.07); color: var(--green); }

.fm-wrap {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 0 0 14px 14px;
  overflow: hidden;
}

/* ── TABLE ── */
.tbl { width: 100%; border-collapse: collapse; }
.tbl th, .tbl td {
  padding: 11px 16px;
  text-align: left;
  border-bottom: 1px solid rgba(56,189,248,0.05);
}
.tbl th {
  font-size: 9px; letter-spacing: 2.5px; text-transform: uppercase;
  color: var(--muted); background: rgba(56,189,248,0.03);
  font-weight: 400;
}
.tbl td { font-size: 12px; color: var(--text); }
.tbl tbody tr { transition: background 0.15s; }
.tbl tbody tr:hover { background: rgba(56,189,248,0.04); }
.tbl tbody tr:last-child td { border-bottom: none; }

.file-icon { font-size: 14px; margin-right: 8px; }
.file-name { color: var(--cyan); cursor: pointer; }
.file-name:hover { text-decoration: underline; }
.file-meta { color: rgba(56,189,248,0.35); font-size: 11px; }

/* ── BADGE ── */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 9px; border-radius: 999px;
  font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase;
}
.badge-ok { background: rgba(74,222,128,0.09); border: 1px solid rgba(74,222,128,0.2); color: var(--green); }
.badge-info { background: var(--cyan-dim); border: 1px solid var(--border); color: var(--cyan); }

/* ── MODAL ── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 200;
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(8px);
  display: none; align-items: center; justify-content: center;
  padding: 20px;
}
.modal-overlay.open { display: flex; }

.modal {
  background: var(--surface);
  border: 1px solid var(--border2);
  border-radius: 18px;
  padding: 28px;
  width: 100%; max-width: 520px;
  max-height: 88vh; overflow-y: auto;
  animation: modalIn 0.22s ease;
  box-shadow: 0 24px 60px rgba(0,0,0,0.5);
}
@keyframes modalIn { from{transform:scale(.95);opacity:0;}to{transform:scale(1);opacity:1;} }

.modal-hd {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 24px;
}
.modal-title {
  font-family: 'Syne', sans-serif;
  font-size: 14px; font-weight: 800;
  color: #fff; letter-spacing: -0.2px;
}
.modal-close {
  background: none; border: none;
  color: var(--muted); font-size: 18px;
  cursor: pointer; padding: 4px; line-height: 1;
  transition: color 0.2s;
}
.modal-close:hover { color: var(--red); }

.fg2 { margin-bottom: 16px; }
.fg2 label {
  display: block;
  font-size: 9px; letter-spacing: 2.5px; text-transform: uppercase;
  color: var(--muted); margin-bottom: 7px;
}
.fg2 input, .fg2 select, .fg2 textarea {
  width: 100%;
  padding: 10px 14px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 9px;
  color: #fff;
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px;
  outline: none; transition: all 0.2s;
}
.fg2 input:focus, .fg2 select:focus, .fg2 textarea:focus {
  border-color: rgba(56,189,248,0.45);
  box-shadow: 0 0 0 3px rgba(56,189,248,0.07);
}
.fg2 textarea { resize: vertical; min-height: 180px; line-height: 1.6; }
.fg2 select option { background: #0d1117; }

.modal-acts { display: flex; gap: 8px; margin-top: 20px; }
.mbtn {
  flex: 1; padding: 11px;
  border-radius: 10px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px; letter-spacing: 1px;
  cursor: pointer; transition: all 0.2s;
}
.mbtn-primary {
  background: var(--cyan);
  border: none; color: #07090d; font-weight: 700;
}
.mbtn-primary:hover { background: #7dd3fc; }
.mbtn-secondary {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--muted);
}
.mbtn-secondary:hover { border-color: var(--border2); color: var(--text); }

/* ── SECTION TOOLBAR ── */
.sec-toolbar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 14px;
}
.sec-title {
  font-family: 'Syne', sans-serif;
  font-size: 13px; font-weight: 800;
  color: #fff; letter-spacing: -0.2px;
}

/* ── ADD BTN ── */
.add-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 16px;
  border-radius: 9px;
  background: var(--cyan-dim);
  border: 1px solid var(--border);
  color: var(--cyan);
  cursor: pointer; transition: all 0.18s;
  font-family: inherit; font-size: 11px; letter-spacing: 0.5px;
}
.add-btn:hover { background: rgba(56,189,248,0.15); border-color: var(--border2); }

.btn-group { display: flex; gap: 6px; }

/* ── TABLE WRAP ── */
.tbl-wrap {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
}
.tbl-wrap + .sec-toolbar { margin-top: 20px; }

/* ── LOADING ── */
.loading {
  display: flex; align-items: center; justify-content: center;
  gap: 10px; padding: 48px;
  color: var(--muted); font-size: 12px; letter-spacing: 1px;
}
.spinner {
  width: 18px; height: 18px;
  border: 2px solid var(--border);
  border-top-color: var(--cyan);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty {
  text-align: center; padding: 48px;
  color: var(--muted); font-size: 12px; letter-spacing: 1px;
}

/* ── TOAST ── */
.toast-wrap {
  position: fixed; top: 70px; right: 16px;
  z-index: 300; display: flex; flex-direction: column; gap: 6px;
  pointer-events: none;
}
.toast {
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 11px; letter-spacing: 0.5px;
  background: var(--surface2);
  border: 1px solid var(--border);
  animation: toastIn 0.3s ease;
  min-width: 220px;
  backdrop-filter: blur(10px);
  pointer-events: auto;
}
@keyframes toastIn { from{transform:translateX(16px);opacity:0;}to{transform:translateX(0);opacity:1;} }
.toast.ok   { border-color: rgba(74,222,128,0.3); color: var(--green); }
.toast.err  { border-color: rgba(248,113,113,0.3); color: var(--red); }
.toast.info { border-color: var(--border2); color: var(--cyan); }

/* ── RESPONSIVE ── */
@media(max-width:800px) {
  .layout { grid-template-columns: 1fr; }
  .sidebar { display: none; }
  .stat-grid { grid-template-columns: 1fr 1fr; }
}
@media(max-width:480px) {
  .main { padding: 14px; }
  .stat-grid { grid-template-columns: 1fr; }
  .topbar-nav { display: none; }
}
</style>
</head>
<body>

<div class="toast-wrap" id="toastWrap"></div>

<!-- TOPBAR -->
<header class="topbar">
  <div style="display:flex;align-items:center;gap:24px;">
    <a href="index.php" class="logo">/gode<span>lic</span></a>
    <nav class="topbar-nav">
      <a href="index.php">./home</a>
      <a href="whm.php">./whm</a>
      <a href="cpanelmanager.php" class="active">./cpanel</a>
    </nav>
  </div>
  <div class="topbar-right">
    <div class="conn-badge">
      <span class="conn-dot"></span>
      <?= htmlspecialchars($user) ?>@<?= htmlspecialchars($host) ?>
    </div>
    <button class="btn btn-open" id="openCpanelBtn" onclick="openCpanel()">⊞ Open cPanel</button>
    <a class="btn btn-logout" href="?logout">⏻ Logout</a>
  </div>
</header>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-host">
      <strong><?= htmlspecialchars($host) ?></strong>
      <?= htmlspecialchars($user) ?><br>
      port 2083 · TLS
    </div>
    <span class="sidebar-label">Navigation</span>
    <div class="nav-item active" onclick="switchPanel('overview', this)">
      <span class="nav-item-icon">⊡</span> Overview
    </div>
    <div class="nav-item" onclick="switchPanel('filemanager', this)">
      <span class="nav-item-icon">◫</span> File Manager
    </div>
    <div class="nav-item" onclick="switchPanel('ftp', this)">
      <span class="nav-item-icon">⇄</span> FTP Accounts
    </div>
    <div class="nav-item" onclick="switchPanel('database', this)">
      <span class="nav-item-icon">⊗</span> Databases
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- OVERVIEW -->
    <div class="panel active" id="panel-overview">
      <div class="panel-head">
        <h2>Overview</h2>
        <p>Ringkasan akun cPanel · <?= htmlspecialchars($host) ?></p>
      </div>
      <div class="stat-grid" id="statCards">
        <div class="loading"><div class="spinner"></div> Memuat...</div>
      </div>
      <div class="terminal-block">
        <div class="terminal-block-bar">
          <div class="dot-sm" style="background:#ff5f56;"></div>
          <div class="dot-sm" style="background:#ffbd2e;"></div>
          <div class="dot-sm" style="background:#27c93f;"></div>
          <span style="margin-left:8px;font-size:11px;">bash — <?= htmlspecialchars($user) ?>@<?= htmlspecialchars($host) ?></span>
        </div>
        <div class="terminal-block-body">
          <div><span class="tb-prompt"><?= htmlspecialchars($user) ?>@cpanel:~$ </span><span class="tb-cmd">uapi Fileman list_files --output=json</span></div>
          <div class="tb-out">&gt; Connecting to <?= htmlspecialchars($host) ?>:2083...</div>
          <div class="tb-ok">&gt; Auth OK — API token validated ✓</div>
          <div class="tb-out">&gt; Session established via UAPI</div>
          <div style="margin-top:4px;"><span class="tb-prompt"><?= htmlspecialchars($user) ?>@cpanel:~$ </span><span class="tb-cmd">status --services=all</span></div>
          <div class="tb-ok">&gt; All services operational<span class="cursor"></span></div>
        </div>
      </div>
    </div>

    <!-- FILE MANAGER -->
    <div class="panel" id="panel-filemanager">
      <div class="panel-head">
        <h2>File Manager</h2>
        <p>Browse dan kelola file server via cPanel UAPI</p>
      </div>
      <div class="fm-bar">
        <div class="fm-path" id="fmPath"></div>
        <div class="fm-acts">
          <button class="fm-btn" onclick="fmMkdir()">+ Folder</button>
          <button class="fm-btn success" onclick="fmNewFile()">+ File</button>
          <button class="fm-btn" onclick="fmUploadPrompt()">↑ Upload</button>
          <input type="file" id="fmUploadInput" style="display:none" onchange="fmUpload()">
        </div>
      </div>
      <div class="fm-wrap" id="fmTableWrap">
        <div class="loading"><div class="spinner"></div> Memuat...</div>
      </div>
    </div>

    <!-- FTP -->
    <div class="panel" id="panel-ftp">
      <div class="panel-head">
        <h2>FTP Accounts</h2>
        <p>Kelola akun FTP via cPanel UAPI</p>
      </div>
      <div class="sec-toolbar">
        <div class="sec-title">Daftar Akun FTP</div>
        <button class="add-btn" onclick="openModal('modalFtpCreate')">+ Buat Akun FTP</button>
      </div>
      <div id="ftpTableWrap" class="tbl-wrap">
        <div class="loading"><div class="spinner"></div> Memuat...</div>
      </div>
    </div>

    <!-- DATABASE -->
    <div class="panel" id="panel-database">
      <div class="panel-head">
        <h2>MySQL Databases</h2>
        <p>Kelola database dan user MySQL via cPanel UAPI</p>
      </div>
      <div class="sec-toolbar">
        <div class="sec-title">Databases</div>
        <div class="btn-group">
          <button class="add-btn" onclick="openModal('modalDbCreate')">+ Database</button>
          <button class="add-btn" onclick="openModal('modalDbUser')">+ User</button>
          <button class="add-btn" onclick="openModal('modalDbAssign')">⟳ Assign</button>
        </div>
      </div>
      <div id="dbTableWrap" class="tbl-wrap" style="margin-bottom:20px;">
        <div class="loading"><div class="spinner"></div></div>
      </div>
      <div class="sec-toolbar">
        <div class="sec-title">DB Users</div>
      </div>
      <div id="dbUserTableWrap" class="tbl-wrap">
        <div class="loading"><div class="spinner"></div></div>
      </div>
    </div>

  </main>
</div>

<!-- MODAL: FILE EDITOR -->
<div class="modal-overlay" id="modalFileEditor">
  <div class="modal" style="max-width:680px;">
    <div class="modal-hd">
      <div class="modal-title" id="fileEditorTitle">File Editor</div>
      <button class="modal-close" onclick="closeModal('modalFileEditor')">✕</button>
    </div>
    <div class="fg2">
      <textarea id="fileEditorContent" rows="20" placeholder="Memuat..."></textarea>
    </div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="fileSave()">💾 Simpan</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalFileEditor')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: MKDIR -->
<div class="modal-overlay" id="modalMkdir">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Buat Folder</div>
      <button class="modal-close" onclick="closeModal('modalMkdir')">✕</button>
    </div>
    <div class="fg2"><label>Nama Folder</label><input type="text" id="mkdirName" placeholder="nama-folder"></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="mkdirConfirm()">Buat</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalMkdir')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: NEW FILE -->
<div class="modal-overlay" id="modalNewFile">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Buat File Baru</div>
      <button class="modal-close" onclick="closeModal('modalNewFile')">✕</button>
    </div>
    <div class="fg2"><label>Nama File</label><input type="text" id="newFileName" placeholder="index.php"></div>
    <div class="fg2"><label>Konten (opsional)</label><textarea id="newFileContent" rows="6" placeholder="// konten file..."></textarea></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="newFileConfirm()">Buat</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalNewFile')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: FTP CREATE -->
<div class="modal-overlay" id="modalFtpCreate">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Buat Akun FTP</div>
      <button class="modal-close" onclick="closeModal('modalFtpCreate')">✕</button>
    </div>
    <div class="fg2"><label>Username</label><input type="text" id="ftpUser" placeholder="ftpuser"></div>
    <div class="fg2"><label>Password</label><input type="password" id="ftpPass" placeholder="••••••••"></div>
    <div class="fg2"><label>Direktori</label><input type="text" id="ftpDir" placeholder="/home/<?= htmlspecialchars($user) ?>/public_html"></div>
    <div class="fg2"><label>Quota MB (0 = unlimited)</label><input type="number" id="ftpQuota" value="0" min="0"></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="ftpCreate()">Buat</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalFtpCreate')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: DB CREATE -->
<div class="modal-overlay" id="modalDbCreate">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Buat Database</div>
      <button class="modal-close" onclick="closeModal('modalDbCreate')">✕</button>
    </div>
    <div class="fg2"><label>Nama Database</label><input type="text" id="dbName" placeholder="nama_database"></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="dbCreate()">Buat</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalDbCreate')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: DB USER -->
<div class="modal-overlay" id="modalDbUser">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Buat DB User</div>
      <button class="modal-close" onclick="closeModal('modalDbUser')">✕</button>
    </div>
    <div class="fg2"><label>Username</label><input type="text" id="dbUName" placeholder="db_user"></div>
    <div class="fg2"><label>Password</label><input type="password" id="dbUPass" placeholder="••••••••"></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="dbUserCreate()">Buat</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalDbUser')">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL: DB ASSIGN -->
<div class="modal-overlay" id="modalDbAssign">
  <div class="modal">
    <div class="modal-hd">
      <div class="modal-title">Assign User ke Database</div>
      <button class="modal-close" onclick="closeModal('modalDbAssign')">✕</button>
    </div>
    <div class="fg2"><label>Database</label><select id="assignDb"></select></div>
    <div class="fg2"><label>User</label><select id="assignUser"></select></div>
    <div class="modal-acts">
      <button class="mbtn mbtn-primary" onclick="dbAssign()">Assign ALL PRIVILEGES</button>
      <button class="mbtn mbtn-secondary" onclick="closeModal('modalDbAssign')">Batal</button>
    </div>
  </div>
</div>

<script>
const CPHOST = <?= json_encode($host) ?>;
const CPUSER = <?= json_encode($user) ?>;
let fmCurrentDir = '/home/<?= htmlspecialchars($user) ?>';
let fmCurrentFile = '';

/* ══ OPEN CPANEL (auto-login) ══ */
async function openCpanel() {
  const btn = document.getElementById('openCpanelBtn');
  btn.classList.add('loading-btn');
  btn.textContent = '⟳ Connecting...';
  try {
    const r = await fetch('?ajax=cpanel_session');
    const d = await r.json();
    if (d.url) {
      window.open(d.url, '_blank');
      if (d.fallback) {
        toast('Membuka cPanel — login mungkin diperlukan', 'info');
      } else {
        toast('cPanel dibuka dengan sesi aktif ✓', 'ok');
      }
    } else {
      toast('Gagal membuat sesi cPanel', 'err');
    }
  } catch(e) {
    // Final fallback: open port 2083 directly
    window.open('https://' + CPHOST + ':2083', '_blank');
    toast('Membuka cPanel langsung...', 'info');
  }
  btn.classList.remove('loading-btn');
  btn.textContent = '⊞ Open cPanel';
}

/* ══ PANEL SWITCH ══ */
function switchPanel(id, el) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('panel-' + id).classList.add('active');
  if (el) el.classList.add('active');
  if (id === 'overview')     loadStats();
  if (id === 'filemanager')  fmLoad(fmCurrentDir);
  if (id === 'ftp')          ftpLoad();
  if (id === 'database')     dbLoad();
}

/* ══ TOAST ══ */
function toast(msg, type = 'info') {
  const w = document.getElementById('toastWrap');
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.textContent = msg;
  w.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

/* ══ MODAL ══ */
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

/* ══ STATS ══ */
async function loadStats() {
  const el = document.getElementById('statCards');
  el.innerHTML = '<div class="loading"><div class="spinner"></div> Memuat statistik...</div>';
  try {
    const r = await fetch('?ajax=stats');
    const d = await r.json();
    const stats = d.data ?? [];
    const map = {};
    if (Array.isArray(stats)) stats.forEach(s => map[s.name] = s);
    el.innerHTML = `
      <div class="stat-card">
        <div class="stat-label">Disk Usage</div>
        <div class="stat-value">${map.diskusage?.value ?? '—'}</div>
        <div class="stat-sub">/ ${map.diskusage?.maximum ?? '∞'} MB</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Bandwidth</div>
        <div class="stat-value">${map.bandwidthusage?.value ?? '—'}</div>
        <div class="stat-sub">MB used this month</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">FTP Accounts</div>
        <div class="stat-value">${map.ftpaccounts?.value ?? '—'}</div>
        <div class="stat-sub">active accounts</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Databases</div>
        <div class="stat-value">${map.mysqlDatabases?.value ?? '—'}</div>
        <div class="stat-sub">MySQL databases</div>
      </div>`;
  } catch(e) {
    el.innerHTML = `<div class="loading" style="color:var(--red);">Gagal memuat statistik</div>`;
  }
}

/* ══ FILE MANAGER ══ */
async function fmLoad(dir) {
  fmCurrentDir = dir;
  fmRenderPath(dir);
  const wrap = document.getElementById('fmTableWrap');
  wrap.innerHTML = '<div class="loading"><div class="spinner"></div> Memuat...</div>';
  try {
    const r = await fetch('?ajax=listdir&dir=' + encodeURIComponent(dir));
    const d = await r.json();
    const files = d.data?.files ?? d.data ?? [];
    if (!files.length) { wrap.innerHTML = '<div class="empty">Direktori kosong</div>'; return; }
    let html = `<table class="tbl"><thead><tr>
      <th>Nama</th><th>Ukuran</th><th>Permissions</th><th>Modified</th><th>Aksi</th>
    </tr></thead><tbody>`;
    files.forEach(f => {
      const isDir = f.type === 'dir';
      const icon  = isDir ? '📁' : '📄';
      const name  = f.file ?? f.name ?? '';
      const full  = dir.replace(/\/$/, '') + '/' + name;
      const size  = isDir ? '—' : fmFmt(f.size ?? 0);
      const perms = f.nicemode ?? f.permissions ?? '';
      const mtime = f.humanmodify ?? '';
      const nav   = isDir
        ? `fmNav('${full.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}')` 
        : `fmOpenFile('${full.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}','${name.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}',${f.size??0})`;
      html += `<tr>
        <td><span class="file-icon">${icon}</span><span class="file-name" onclick="${nav}">${escH(name)}</span></td>
        <td class="file-meta">${size}</td>
        <td class="file-meta">${perms}</td>
        <td class="file-meta">${mtime}</td>
        <td><button class="fm-btn danger" onclick="fmDelete('${full.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}','${isDir?'dir':'file'}')">✕</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    wrap.innerHTML = html;
  } catch(e) {
    wrap.innerHTML = `<div class="empty" style="color:var(--red);">Error: ${e.message}</div>`;
  }
}

function fmRenderPath(dir) {
  const parts = dir.split('/').filter(Boolean);
  const el = document.getElementById('fmPath');
  let built = '', html = '';
  parts.forEach(p => {
    built += '/' + p;
    const path = built;
    html += `<span class="fm-path-sep">/</span><span class="fm-path-item" onclick="fmNav('${path.replace(/\\/g,'\\\\').replace(/'/g,"\\'")}'">${escH(p)}</span>`;
  });
  el.innerHTML = html || '<span class="fm-path-item">/</span>';
}

function fmNav(dir) { fmLoad(dir); }
function fmFmt(b) {
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(1) + ' MB';
}

async function fmOpenFile(path, name, size) {
  if (size > 512000) { toast('File terlalu besar (>512KB)', 'err'); return; }
  fmCurrentFile = path;
  document.getElementById('fileEditorTitle').textContent = name;
  document.getElementById('fileEditorContent').value = 'Memuat...';
  openModal('modalFileEditor');
  try {
    const r = await fetch('?ajax=readfile&file=' + encodeURIComponent(path));
    const d = await r.json();
    document.getElementById('fileEditorContent').value = d.data?.content ?? d.data ?? '';
  } catch(e) {
    document.getElementById('fileEditorContent').value = 'Error: ' + e.message;
  }
}

async function fileSave() {
  const content = document.getElementById('fileEditorContent').value;
  try {
    const r = await fetch('?ajax=savefile', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({file:fmCurrentFile,content})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0], 'err'); return; }
    toast('File tersimpan ✓', 'ok'); closeModal('modalFileEditor');
  } catch(e) { toast('Error: '+e.message,'err'); }
}

function fmMkdir() { document.getElementById('mkdirName').value=''; openModal('modalMkdir'); }

async function mkdirConfirm() {
  const name = document.getElementById('mkdirName').value.trim();
  if (!name) return;
  try {
    const r = await fetch('?ajax=mkdir',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({path:fmCurrentDir,name})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Folder dibuat ✓','ok'); closeModal('modalMkdir'); fmLoad(fmCurrentDir);
  } catch(e) { toast('Error: '+e.message,'err'); }
}

function fmNewFile() { document.getElementById('newFileName').value=''; document.getElementById('newFileContent').value=''; openModal('modalNewFile'); }

async function newFileConfirm() {
  const name = document.getElementById('newFileName').value.trim();
  const content = document.getElementById('newFileContent').value;
  if (!name) return;
  const path = fmCurrentDir.replace(/\/$/, '') + '/' + name;
  try {
    const r = await fetch('?ajax=savefile',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({file:path,content})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('File dibuat ✓','ok'); closeModal('modalNewFile'); fmLoad(fmCurrentDir);
  } catch(e) { toast('Error: '+e.message,'err'); }
}

async function fmDelete(path, type) {
  if (!confirm('Hapus: ' + path + '?')) return;
  try {
    const r = await fetch('?ajax=delete',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({path,type})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Dihapus ✓','ok'); fmLoad(fmCurrentDir);
  } catch(e) { toast('Error: '+e.message,'err'); }
}

function fmUploadPrompt() { document.getElementById('fmUploadInput').click(); }

async function fmUpload() {
  const file = document.getElementById('fmUploadInput').files[0];
  if (!file) return;
  toast('Mengupload ' + file.name + '...', 'info');
  const fd = new FormData();
  fd.append('file', file); fd.append('dir', fmCurrentDir);
  try {
    const r = await fetch('?ajax=upload',{method:'POST',body:fd});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Upload selesai ✓','ok'); fmLoad(fmCurrentDir);
  } catch(e) { toast('Error: '+e.message,'err'); }
  document.getElementById('fmUploadInput').value='';
}

/* ══ FTP ══ */
async function ftpLoad() {
  const wrap = document.getElementById('ftpTableWrap');
  wrap.innerHTML = '<div class="loading"><div class="spinner"></div> Memuat...</div>';
  try {
    const r = await fetch('?ajax=ftplist');
    const d = await r.json();
    const accs = d.data ?? [];
    if (!accs.length) { wrap.innerHTML = '<div class="empty">Belum ada akun FTP</div>'; return; }
    let html = `<table class="tbl"><thead><tr><th>Username</th><th>Direktori</th><th>Quota</th><th>Status</th><th>Aksi</th></tr></thead><tbody>`;
    accs.forEach(a => {
      const u = a.user ?? '';
      const quota = a.quota_mb == 0 ? 'Unlimited' : a.quota_mb + ' MB';
      html += `<tr>
        <td>${escH(u)}</td>
        <td class="file-meta">${escH(a.homedir??'—')}</td>
        <td class="file-meta">${quota}</td>
        <td><span class="badge badge-ok">Active</span></td>
        <td><button class="fm-btn danger" onclick="ftpDelete('${escH(u)}')">✕ Hapus</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    wrap.innerHTML = html;
  } catch(e) { wrap.innerHTML = `<div class="empty" style="color:var(--red);">Error: ${e.message}</div>`; }
}

async function ftpCreate() {
  const data = {user:document.getElementById('ftpUser').value.trim(),pass:document.getElementById('ftpPass').value,homedir:document.getElementById('ftpDir').value.trim(),quota:parseInt(document.getElementById('ftpQuota').value)||0};
  if (!data.user||!data.pass) { toast('Username dan password wajib','err'); return; }
  try {
    const r = await fetch('?ajax=ftpcreate',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Akun FTP dibuat ✓','ok'); closeModal('modalFtpCreate'); ftpLoad();
  } catch(e) { toast('Error: '+e.message,'err'); }
}

async function ftpDelete(u) {
  if (!confirm('Hapus akun FTP '+u+'?')) return;
  try {
    const r = await fetch('?ajax=ftpdelete',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({user:u})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Akun FTP dihapus ✓','ok'); ftpLoad();
  } catch(e) { toast('Error: '+e.message,'err'); }
}

/* ══ DATABASE ══ */
async function dbLoad() {
  const dbW = document.getElementById('dbTableWrap');
  const uW  = document.getElementById('dbUserTableWrap');
  dbW.innerHTML = uW.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
  try {
    const r = await fetch('?ajax=dblist');
    const d = await r.json();
    const dbs   = d.databases?.data ?? [];
    const users = d.users?.data ?? [];

    if (!dbs.length) {
      dbW.innerHTML = '<div class="empty">Belum ada database</div>';
    } else {
      let html = `<table class="tbl"><thead><tr><th>Nama Database</th><th>Status</th></tr></thead><tbody>`;
      dbs.forEach(db => { html += `<tr><td>${escH(db.database??db.name??db)}</td><td><span class="badge badge-ok">Active</span></td></tr>`; });
      dbW.innerHTML = html + '</tbody></table>';
    }

    if (!users.length) {
      uW.innerHTML = '<div class="empty">Belum ada DB user</div>';
    } else {
      let html = `<table class="tbl"><thead><tr><th>Username</th><th>Status</th></tr></thead><tbody>`;
      users.forEach(u => { html += `<tr><td>${escH(u.user??u)}</td><td><span class="badge badge-ok">Active</span></td></tr>`; });
      uW.innerHTML = html + '</tbody></table>';
    }

    const selDb   = document.getElementById('assignDb');
    const selUser = document.getElementById('assignUser');
    selDb.innerHTML   = dbs.map(d => `<option value="${escH(d.database??d.name??d)}">${escH(d.database??d.name??d)}</option>`).join('');
    selUser.innerHTML = users.map(u => `<option value="${escH(u.user??u)}">${escH(u.user??u)}</option>`).join('');
  } catch(e) {
    dbW.innerHTML = `<div class="empty" style="color:var(--red);">Error: ${e.message}</div>`;
  }
}

async function dbCreate() {
  const name = document.getElementById('dbName').value.trim();
  if (!name) return;
  try {
    const r = await fetch('?ajax=dbcreate',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Database dibuat ✓','ok'); closeModal('modalDbCreate'); dbLoad();
  } catch(e) { toast('Error: '+e.message,'err'); }
}

async function dbUserCreate() {
  const name = document.getElementById('dbUName').value.trim();
  const pass = document.getElementById('dbUPass').value;
  if (!name||!pass) { toast('Nama dan password wajib','err'); return; }
  try {
    const r = await fetch('?ajax=dbusercreate',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name,password:pass})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('DB User dibuat ✓','ok'); closeModal('modalDbUser'); dbLoad();
  } catch(e) { toast('Error: '+e.message,'err'); }
}

async function dbAssign() {
  const database = document.getElementById('assignDb').value;
  const user     = document.getElementById('assignUser').value;
  if (!database||!user) { toast('Pilih database dan user','err'); return; }
  try {
    const r = await fetch('?ajax=dbassign',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({database,user})});
    const d = await r.json();
    if (d.errors?.length) { toast('Error: '+d.errors[0],'err'); return; }
    toast('Privileges assigned ✓','ok'); closeModal('modalDbAssign');
  } catch(e) { toast('Error: '+e.message,'err'); }
}

/* ══ UTILS ══ */
function escH(s) {
  if (typeof s !== 'string') s = String(s ?? '');
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ══ INIT ══ */
loadStats();
fmLoad(fmCurrentDir);
</script>
</body>
</html>
