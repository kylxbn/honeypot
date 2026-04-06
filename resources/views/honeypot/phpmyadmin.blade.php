<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>phpMyAdmin 5.2.1</title>
<style>
body{margin:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;background:#f4f4f4;}
#pma_header{background:#f4f4f4;border-bottom:2px solid #d3d3d3;padding:4px 8px;display:flex;align-items:center;gap:10px;}
#pma_header img{height:22px;}
#pma_header strong{font-size:14px;color:#333;}
#pma_navigation{background:#fff;border-right:1px solid #d3d3d3;width:200px;position:fixed;top:38px;left:0;bottom:0;overflow-y:auto;padding:8px 0;}
#pma_navigation .nav-header{background:#eef;padding:4px 10px;font-weight:bold;font-size:11px;color:#336;border-bottom:1px solid #d3d3d3;}
#pma_navigation a{display:block;padding:4px 14px;color:#336;text-decoration:none;font-size:11px;}
#pma_navigation a:hover{background:#dde;}
#pma_navigation .db-item{padding:4px 10px;font-weight:bold;color:#555;cursor:pointer;}
#pma_navigation .table-item a{padding-left:24px;}
#pma_main{margin-left:200px;padding:10px;margin-top:38px;}
.pma-card{background:#fff;border:1px solid #d3d3d3;border-radius:2px;margin-bottom:10px;}
.pma-card-header{background:#eef;padding:6px 10px;border-bottom:1px solid #d3d3d3;font-weight:bold;font-size:12px;color:#336;}
.pma-card-body{padding:10px;}
table.pma-table{border-collapse:collapse;width:100%;font-size:11px;}
table.pma-table th{background:#eef;padding:4px 8px;border:1px solid #d3d3d3;text-align:left;color:#336;}
table.pma-table td{padding:4px 8px;border:1px solid #e8e8e8;}
table.pma-table tr:hover td{background:#f8f8ff;}
.pma-btn{background:#5d8fbd;color:#fff;border:1px solid #4a7aa5;padding:3px 10px;border-radius:2px;font-size:11px;cursor:pointer;text-decoration:none;display:inline-block;}
.pma-btn:hover{background:#4a7aa5;}
.success-msg{background:#dff0d8;border:1px solid #d6e9c6;color:#3c763d;padding:6px 10px;border-radius:2px;margin-bottom:8px;font-size:11px;}
</style>
</head>
<body>

<div id="pma_header">
  <strong>phpMyAdmin</strong>
  <span style="color:#999;font-size:11px;">5.2.1</span>
  <span style="margin-left:auto;font-size:11px;color:#555;">
    Server: db-prod-01.internal &nbsp;|&nbsp; User: <strong>{{ config('honeypot.company.db_user') }}</strong> &nbsp;|&nbsp; Database: <strong>{{ config('honeypot.company.db_name') }}</strong>
  </span>
</div>

<div id="pma_navigation">
  <div class="nav-header">Databases</div>
  @foreach(['novatech_prod','novatech_wp','information_schema','mysql','performance_schema'] as $db)
  <div class="db-item">🗄 {{ $db }}</div>
    @if($db === 'novatech_prod')
    @foreach(['users','customers','orders','products','payments','sessions','audit_log'] as $tbl)
    <div class="table-item"><a href="/phpmyadmin?db=novatech_prod&table={{ $tbl }}">📋 {{ $tbl }}</a></div>
    @endforeach
    @endif
  @endforeach
</div>

<div id="pma_main">
  <div class="pma-card">
    <div class="pma-card-header">📋 Table: <strong>users</strong> — Database: <strong>{{ config('honeypot.company.db_name') }}</strong></div>
    <div class="pma-card-body">
      <div class="success-msg">✔ Showing rows 0 - 19 (20 total, Query took 0.0012 seconds.)</div>
      <p style="margin-bottom:8px;">
        <a class="pma-btn" href="/phpmyadmin">Browse</a>
        <a class="pma-btn" href="/phpmyadmin">Structure</a>
        <a class="pma-btn" href="/phpmyadmin">SQL</a>
        <a class="pma-btn" href="/phpmyadmin">Export</a>
      </p>
      <table class="pma-table">
        <thead>
          <tr>
            <th><input type="checkbox"></th>
            <th>id</th><th>username</th><th>email</th><th>password</th><th>role</th><th>is_active</th><th>created_at</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
          <tr>
            <td><input type="checkbox"></td>
            <td>{{ $u['id'] }}</td>
            <td>{{ $u['username'] }}</td>
            <td>{{ $u['email'] }}</td>
            <td style="font-family:monospace;color:#800;">{{ $u['password_md5'] }}</td>
            <td><span style="color:{{ $u['role']==='admin'?'#a00':($u['role']==='moderator'?'#660':'#060') }}">{{ $u['role'] }}</span></td>
            <td>{{ $u['is_active'] ? '1' : '0' }}</td>
            <td>{{ $u['created_at'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <p style="margin-top:8px;color:#999;font-size:10px;">💡 Note: Passwords are stored as MD5 hashes (unsalted). Consider upgrading to bcrypt.</p>
    </div>
  </div>
</div>

</body>
</html>
