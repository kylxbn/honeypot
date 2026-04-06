<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard — {{ config('honeypot.company.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:#0f172a;color:#e2e8f0;}
.sidebar{width:220px;background:#1e293b;min-height:100vh;position:fixed;top:0;left:0;padding-top:20px;}
.sidebar .brand{padding:0 20px 20px;border-bottom:1px solid #334155;font-weight:700;font-size:16px;color:#f8fafc;}
.sidebar a{display:block;padding:10px 20px;color:#94a3b8;text-decoration:none;font-size:14px;}
.sidebar a:hover,.sidebar a.active{background:#334155;color:#f8fafc;}
.main{margin-left:220px;padding:24px;}
.topbar{background:#1e293b;border-radius:10px;padding:12px 20px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;}
.stat-card{background:#1e293b;border-radius:10px;padding:20px;border-left:4px solid;}
.stat-card.blue{border-color:#3b82f6;}
.stat-card.green{border-color:#10b981;}
.stat-card.yellow{border-color:#f59e0b;}
.stat-card.red{border-color:#ef4444;}
.stat-card h6{color:#94a3b8;font-size:12px;text-transform:uppercase;letter-spacing:.05em;}
.stat-card h2{color:#f8fafc;font-size:28px;font-weight:700;margin:4px 0 0;}
.table-card{background:#1e293b;border-radius:10px;padding:20px;margin-top:20px;}
.table-dark{--bs-table-bg:#1e293b;--bs-table-striped-bg:#263042;}
</style>
</head>
<body>

<div class="sidebar">
  <div class="brand">⚡ {{ config('honeypot.company.name') }}</div>
  <a href="/admin/dashboard" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
  <a href="/admin/dashboard"><i class="bi bi-people me-2"></i>Users</a>
  <a href="/admin/dashboard"><i class="bi bi-cart me-2"></i>Orders</a>
  <a href="/admin/dashboard"><i class="bi bi-box me-2"></i>Products</a>
  <a href="/admin/dashboard"><i class="bi bi-file-earmark me-2"></i>Reports</a>
  <a href="/admin/dashboard"><i class="bi bi-gear me-2"></i>Settings</a>
  <a href="/admin/dashboard"><i class="bi bi-database me-2"></i>Database</a>
  <a href="/wp-admin"><i class="bi bi-wordpress me-2"></i>CMS</a>
  <a href="/phpmyadmin"><i class="bi bi-table me-2"></i>phpMyAdmin</a>
  <hr style="border-color:#334155;margin:10px 20px;">
  <a href="/login"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <div>
      <strong>Dashboard Overview</strong>
      <span class="text-muted ms-3 small">{{ now()->format('l, F j Y') }}</span>
    </div>
    <div class="small text-muted">Logged in as <strong class="text-light">admin</strong> &nbsp;·&nbsp; <a href="/login" class="text-danger text-decoration-none">Logout</a></div>
  </div>

  <div class="row g-3">
    <div class="col-md-3"><div class="stat-card blue"><h6>Total Users</h6><h2>1,842</h2><small class="text-success">↑ 12% this month</small></div></div>
    <div class="col-md-3"><div class="stat-card green"><h6>Revenue (MTD)</h6><h2>$142,800</h2><small class="text-success">↑ 8.3% vs last month</small></div></div>
    <div class="col-md-3"><div class="stat-card yellow"><h6>Open Orders</h6><h2>247</h2><small class="text-warning">18 need attention</small></div></div>
    <div class="col-md-3"><div class="stat-card red"><h6>Failed Logins</h6><h2>1,204</h2><small class="text-danger">Last 24h</small></div></div>
  </div>

  <div class="table-card mt-4">
    <h6 class="text-muted mb-3">Recent Users</h6>
    <table class="table table-dark table-striped table-sm mb-0">
      <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th></tr></thead>
      <tbody>
        @php
          $faker = \Faker\Factory::create(); $faker->seed(54321);
          for($i=1;$i<=8;$i++){
            $name=$faker->userName; $email=$faker->safeEmail;
            $role=$i===1?'admin':($i<=3?'moderator':'user');
            $login=$faker->dateTimeBetween('-7 days','now')->format('Y-m-d H:i');
            echo "<tr><td>$i</td><td>$name</td><td>$email</td><td><span class='badge ".($role==='admin'?'bg-danger':($role==='moderator'?'bg-warning text-dark':'bg-secondary'))."'>$role</span></td><td>$login</td><td><span class='badge bg-success'>Active</span></td></tr>";
          }
        @endphp
      </tbody>
    </table>
  </div>

  <div class="table-card">
    <h6 class="text-muted mb-3">System Info</h6>
    <table class="table table-dark table-sm mb-0">
      <tbody>
        <tr><td class="text-muted">Server</td><td>novatech-prod-01 (Ubuntu 22.04)</td></tr>
        <tr><td class="text-muted">PHP Version</td><td>8.4.1</td></tr>
        <tr><td class="text-muted">Database</td><td>MySQL 8.0.36 @ db-prod-01.internal</td></tr>
        <tr><td class="text-muted">DB Name</td><td>{{ config('honeypot.company.db_name') }}</td></tr>
        <tr><td class="text-muted">DB User</td><td>{{ config('honeypot.company.db_user') }}</td></tr>
        <tr><td class="text-muted">App URL</td><td>{{ config('app.url') }}</td></tr>
        <tr><td class="text-muted">Storage Used</td><td>47.2 GB / 100 GB</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
