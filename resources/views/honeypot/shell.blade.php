<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>WSO 2.8.5</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#1a1a1a;color:#00ff00;font-family:'Courier New',monospace;font-size:13px;}
.header{background:#222;border-bottom:1px solid #333;padding:8px 14px;display:flex;gap:20px;align-items:center;}
.header .title{color:#ff4444;font-weight:bold;font-size:15px;}
.header .info{color:#888;font-size:11px;}
.header .info span{color:#aaa;}
.sidebar{float:left;width:180px;background:#111;min-height:calc(100vh - 38px);padding:10px 0;}
.sidebar a{display:block;padding:6px 14px;color:#666;text-decoration:none;font-size:12px;}
.sidebar a:hover{color:#00ff00;background:#222;}
.sidebar .section{color:#444;font-size:10px;text-transform:uppercase;padding:8px 14px 4px;}
.content{margin-left:180px;padding:14px;}
.terminal{background:#000;border:1px solid #333;border-radius:4px;padding:14px;margin-bottom:14px;min-height:200px;}
.terminal .prompt{color:#00aaff;}
.terminal .output{color:#aaffaa;white-space:pre-wrap;margin-top:8px;line-height:1.5;}
.terminal .error{color:#ff6666;}
form.cmd-form{display:flex;gap:8px;margin-top:10px;}
form.cmd-form input{flex:1;background:#000;border:1px solid #00ff00;color:#00ff00;font-family:'Courier New',monospace;font-size:13px;padding:6px 10px;outline:none;}
form.cmd-form button{background:#333;color:#00ff00;border:1px solid #00ff00;padding:6px 14px;cursor:pointer;font-family:'Courier New',monospace;}
form.cmd-form button:hover{background:#00ff00;color:#000;}
.info-table{width:100%;border-collapse:collapse;font-size:12px;}
.info-table td{padding:4px 8px;border-bottom:1px solid #222;color:#888;}
.info-table td:first-child{color:#555;width:140px;}
</style>
</head>
<body>

<div class="header">
  <span class="title">WSO 2.8.5</span>
  <span class="info">uname: <span>Linux novatech-prod-01 5.15.0-91-generic</span></span>
  <span class="info">user: <span>www-data</span></span>
  <span class="info">cwd: <span>/var/www/html</span></span>
  <span class="info">php: <span>8.4.1</span></span>
  <span class="info" style="margin-left:auto;color:#ff4444;">⚠ UNAME: Linux | SAFE_MODE: OFF</span>
</div>

<div class="sidebar">
  <div class="section">Navigation</div>
  <a href="/shell.php">Cmd</a>
  <a href="/shell.php">File Manager</a>
  <a href="/shell.php">SQL Client</a>
  <a href="/shell.php">PHP Eval</a>
  <div class="section">Config</div>
  <a href="/.env">.env file</a>
  <a href="/phpinfo.php">phpinfo</a>
  <a href="/phpmyadmin">phpMyAdmin</a>
  <div class="section">System</div>
  <a href="/shell.php">Processes</a>
  <a href="/shell.php">Network</a>
  <a href="/passwd">passwd</a>
</div>

<div class="content">

  <table class="info-table" style="margin-bottom:14px;width:auto;">
    <tr><td>Software</td><td>nginx/1.24.0 + PHP/8.4.1</td></tr>
    <tr><td>OS</td><td>Linux novatech-prod-01 5.15.0-91-generic x86_64</td></tr>
    <tr><td>User</td><td>www-data (uid=33)</td></tr>
    <tr><td>Disabled funcs</td><td><span style="color:#ff4444;">none</span></td></tr>
    <tr><td>CWD</td><td>/var/www/html</td></tr>
  </table>

  <div class="terminal">
    @if($cmd !== null)
    <div class="prompt">www-data@novatech-prod-01:/var/www/html$ {{ htmlspecialchars($cmd) }}</div>
    <div class="{{ str_contains($output ?? '', 'not found') || str_contains($output ?? '', 'denied') ? 'error' : 'output' }}">{{ $output }}</div>
    @else
    <div class="output" style="color:#555;">— Execute a command below —</div>
    @endif
  </div>

  <form class="cmd-form" method="POST" action="/shell.php">
    @csrf
    <span style="color:#00aaff;">www-data@novatech-prod-01:/var/www/html$</span>
    <input type="text" name="cmd" placeholder="command..." autofocus value="{{ old('cmd') }}">
    <button type="submit">Run</button>
  </form>

</div>

</body>
</html>
