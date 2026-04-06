<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — {{ config('honeypot.company.name') }}</title>
<style>
*{box-sizing:border-box;}
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#1a1a2e;display:flex;align-items:center;justify-content:center;min-height:100vh;}
.card{background:#16213e;border:1px solid #0f3460;border-radius:12px;padding:40px;width:380px;box-shadow:0 20px 60px rgba(0,0,0,.5);}
.logo{text-align:center;margin-bottom:28px;}
.logo h1{color:#e94560;font-size:24px;font-weight:700;margin:0;}
.logo p{color:#a0aec0;font-size:13px;margin:4px 0 0;}
label{display:block;color:#a0aec0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;}
input{width:100%;background:#0f3460;border:1px solid #1a4a80;border-radius:8px;color:#e2e8f0;font-size:14px;padding:10px 14px;margin-bottom:18px;outline:none;transition:border-color .2s;}
input:focus{border-color:#e94560;}
button{width:100%;background:linear-gradient(135deg,#e94560,#c0392b);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;padding:12px;cursor:pointer;transition:opacity .2s;}
button:hover{opacity:.9;}
.footer{text-align:center;margin-top:20px;color:#4a5568;font-size:12px;}
.footer a{color:#a0aec0;text-decoration:none;}
.error{background:rgba(233,69,96,.15);border:1px solid #e94560;border-radius:8px;color:#e94560;padding:10px 14px;margin-bottom:16px;font-size:13px;}
</style>
</head>
<body>

<div class="card">
  <div class="logo">
    <h1>🛡 Admin Portal</h1>
    <p>{{ config('honeypot.company.name') }} — Internal Access Only</p>
  </div>

  @if(session('error'))
  <div class="error">{{ session('error') }}</div>
  @endif

  <form method="POST" action="/admin">
    @csrf
    <label for="username">Username</label>
    <input type="text" id="username" name="username" autocomplete="username" required placeholder="admin">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required placeholder="••••••••">

    <button type="submit">Sign In →</button>
  </form>

  <div class="footer">
    <a href="/">← Back to website</a> &nbsp;·&nbsp; <a href="/wp-admin">CMS Login</a>
  </div>
</div>

</body>
</html>
