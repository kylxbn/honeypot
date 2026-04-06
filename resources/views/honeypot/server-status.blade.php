<!DOCTYPE html>
<html><head><title>Apache Status</title>
<style>body{font-family:sans-serif;font-size:12px;}h1{font-size:20px;}table{border-collapse:collapse;}td,th{border:1px solid #ddd;padding:3px 8px;font-size:11px;}th{background:#ebebeb;}</style>
</head><body>
<h1>Apache Server Status for novatech-prod-01</h1>
<p>Server Version: Apache/2.4.58 (Ubuntu) OpenSSL/3.0.10<br>
Server MPM: prefork<br>
Server Built: 2023-11-19T00:00:00</p>
<hr>
<p>Current Time: {{ now()->format('D, d M Y H:i:s T') }}<br>
Restart Time: {{ now()->subDays(14)->format('D, d M Y H:i:s T') }}<br>
Parent Server Config. Generation: 1<br>
Parent Server MPM Generation: 0<br>
Server uptime: 14 days 3 hours 27 minutes 18 seconds<br>
Server load: 0.42 0.38 0.31<br>
Total accesses: 1,842,091 - Total Traffic: 14.2 GB<br>
CPU Usage: u4.18 s1.04 cu0 cs0 - .0341% CPU load</p>
<p>2 requests currently being processed, 8 idle workers</p>
<pre style="font-family:monospace;font-size:11px;background:#f5f5f5;padding:8px;">__W_____W__...........</pre>
<hr>
<table>
<tr><th>Srv</th><th>PID</th><th>Acc</th><th>M</th><th>CPU</th><th>SS</th><th>Req</th><th>Conn</th><th>Client</th><th>VHost</th><th>Request</th></tr>
@php $ips=['203.0.113.45','198.51.100.22','192.0.2.178','45.33.32.156','104.21.0.8']; @endphp
@for($i=0;$i<5;$i++)
<tr><td>0-0</td><td>{{ 1000+$i }}</td><td>0/{{ rand(10,500) }}/{{ rand(1000,50000) }}</td><td>W</td><td>0.00</td><td>{{ rand(0,60) }}</td><td>{{ rand(100,9999) }}</td><td>0.0</td><td>{{ $ips[$i] }}</td><td>novatech-solutions.com</td><td>GET /{{ ['wp-admin','api/v1/users','.env','phpmyadmin','backup.sql'][$i] }} HTTP/1.1</td></tr>
@endfor
</table>
</body></html>
