<!DOCTYPE html>
<html><head><title>Error Log - ELMAH</title>
<style>body{font-family:Verdana,Arial,sans-serif;font-size:12px;background:#fff;}
h1{font-size:18px;background:#003366;color:#fff;padding:8px 14px;margin:0;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th{background:#e5e5e5;padding:5px 8px;border:1px solid #ccc;text-align:left;}
td{padding:4px 8px;border:1px solid #e0e0e0;font-size:11px;}
tr:nth-child(even){background:#f9f9f9;}
.err{color:#c00;font-weight:bold;}
</style></head>
<body>
<h1>Error Log — ELMAH 1.2.2</h1>
<p style="padding:8px 14px;background:#ffffcc;border-bottom:1px solid #ccc;font-size:11px;">
Application: <strong>NovaTechWebApp</strong> | Server: <strong>NOVATECH-PROD-01</strong> | Total errors: <strong>1,284</strong>
</p>
<table>
<tr><th>#</th><th>Type</th><th>Message</th><th>User</th><th>Time</th></tr>
@php
$errors=[
['SqlException','Login failed for user \'novatech_app\'. Password: Pr0d_DB_P@ssw0rd_2024!','SYSTEM',now()->subMinutes(5)],
['HttpException','403 Forbidden: /admin/users/delete','admin',now()->subMinutes(22)],
['NullReferenceException','Object reference not set: PaymentController.ProcessCard()','j.smith',now()->subHour()],
['UnauthorizedAccessException','Access denied to /etc/shadow','www-data',now()->subHours(2)],
['SqlException','Deadlock detected on table orders','SYSTEM',now()->subHours(3)],
];
@endphp
@foreach($errors as $i=>[$type,$msg,$user,$time])
<tr>
  <td>{{ $i+1 }}</td>
  <td class="err">{{ $type }}</td>
  <td>{{ $msg }}</td>
  <td>{{ $user }}</td>
  <td>{{ $time->format('Y-m-d H:i:s') }}</td>
</tr>
@endforeach
</table>
</body></html>
