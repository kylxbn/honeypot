<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>404 Not Found</title>
<style>
body{font-family:sans-serif;background:#f4f4f4;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.box{text-align:center;background:#fff;padding:40px 60px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.08);}
h1{font-size:80px;color:#ddd;margin:0;line-height:1;}
h2{color:#555;margin:10px 0 6px;}
p{color:#888;margin:0;}
a{color:#0d6efd;text-decoration:none;}
</style>
</head>
<body>
<div class="box">
  <h1>404</h1>
  <h2>Page Not Found</h2>
  <p>The requested URL was not found on this server.</p>
  <p style="margin-top:14px;font-size:12px;color:#aaa;">Apache/2.4.58 (Ubuntu) Server at {{ config('honeypot.company.domain') }} Port 80</p>
  <p style="margin-top:14px;"><a href="/">← Go Home</a></p>
</div>
</body>
</html>
