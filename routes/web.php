<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HoneypotController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Dashboard (secret URL — keep private)
// ─────────────────────────────────────────────────────────────────────────────
$dash = config('honeypot.dashboard_path', '/dashboard-e3c34cc9c2be9abb5c01');

Route::get($dash,                    [DashboardController::class, 'index'])->name('dashboard.index');
Route::get($dash . '/requests',      [DashboardController::class, 'requests'])->name('dashboard.requests');
Route::get($dash . '/credentials',   [DashboardController::class, 'credentials'])->name('dashboard.credentials');
Route::get($dash . '/request/{id}',  [DashboardController::class, 'showRequest'])->name('dashboard.show-request');
Route::get($dash . '/canaries',      [DashboardController::class, 'canaries'])->name('dashboard.canaries');

// ─────────────────────────────────────────────────────────────────────────────
// Meta / discovery (robots, sitemap, crossdomain)
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/robots.txt',                   [HoneypotController::class, 'robotsTxt']);
Route::get('/sitemap.xml',                  [HoneypotController::class, 'sitemapXml']);
Route::get('/crossdomain.xml',              [HoneypotController::class, 'crossdomain']);
Route::get('/clientaccesspolicy.xml',       [HoneypotController::class, 'crossdomain']);

// ─────────────────────────────────────────────────────────────────────────────
// Fake company homepage & pages
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/',         [HoneypotController::class, 'home']);
Route::get('/about',    [HoneypotController::class, 'home']);
Route::get('/products', [HoneypotController::class, 'home']);
Route::get('/services', [HoneypotController::class, 'home']);
Route::get('/contact',  [HoneypotController::class, 'home']);

// ─────────────────────────────────────────────────────────────────────────────
// WordPress traps
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/wp-login.php',              [HoneypotController::class, 'wpLogin']);
Route::any('/wp-admin',                  [HoneypotController::class, 'wpAdmin']);
Route::any('/wp-admin/{any}',            [HoneypotController::class, 'wpAdmin'])->where('any', '.*');
Route::any('/xmlrpc.php',                [HoneypotController::class, 'xmlrpc']);
Route::get('/wp-config.php',             [HoneypotController::class, 'wpConfigPhp']);
Route::get('/wp-content/debug.log',      [HoneypotController::class, 'catchAll']);

// ─────────────────────────────────────────────────────────────────────────────
// Generic admin / login traps
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/login',            [HoneypotController::class, 'adminLogin']);
Route::any('/signin',           [HoneypotController::class, 'adminLogin']);
Route::any('/admin',            [HoneypotController::class, 'adminLogin']);
Route::any('/administrator',    [HoneypotController::class, 'adminLogin']);
Route::any('/admin/login',      [HoneypotController::class, 'adminLogin']);
Route::get('/admin/dashboard',  [HoneypotController::class, 'adminPanel']);
Route::get('/admin/{any}',      [HoneypotController::class, 'adminPanel'])->where('any', '.*');

// ─────────────────────────────────────────────────────────────────────────────
// Database / phpMyAdmin traps
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/phpmyadmin',          [HoneypotController::class, 'phpMyAdmin']);
Route::any('/phpmyadmin/{any}',    [HoneypotController::class, 'phpMyAdmin'])->where('any', '.*');
Route::any('/pma',                 [HoneypotController::class, 'phpMyAdmin']);
Route::any('/pma/{any}',           [HoneypotController::class, 'phpMyAdmin'])->where('any', '.*');
Route::any('/mysql',               [HoneypotController::class, 'phpMyAdmin']);
Route::any('/dbadmin',             [HoneypotController::class, 'phpMyAdmin']);

// ─────────────────────────────────────────────────────────────────────────────
// PHP diagnostics
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/phpinfo.php',   [HoneypotController::class, 'phpInfo']);
Route::get('/info.php',      [HoneypotController::class, 'phpInfo']);
Route::get('/server-status', [HoneypotController::class, 'serverStatus']);
Route::get('/server-info',   [HoneypotController::class, 'serverStatus']);

// ─────────────────────────────────────────────────────────────────────────────
// Webshell traps
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/shell.php',         [HoneypotController::class, 'shell']);
Route::any('/shell',             [HoneypotController::class, 'shell']);
Route::any('/c99.php',           [HoneypotController::class, 'shell']);
Route::any('/r57.php',           [HoneypotController::class, 'shell']);
Route::any('/wso.php',           [HoneypotController::class, 'shell']);
Route::any('/b374k.php',         [HoneypotController::class, 'shell']);
Route::any('/cmd.php',           [HoneypotController::class, 'shell']);
Route::any('/uploads/shell.php', [HoneypotController::class, 'shell']);
Route::any('/uploads/cmd.php',   [HoneypotController::class, 'shell']);
Route::any('/tmp/shell.php',     [HoneypotController::class, 'shell']);

// ─────────────────────────────────────────────────────────────────────────────
// Sensitive file exposure traps
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/.env',             [HoneypotController::class, 'envFile']);
Route::get('/.env.backup',      [HoneypotController::class, 'envFile']);
Route::get('/.env.prod',        [HoneypotController::class, 'envFile']);
Route::get('/.git/config',      [HoneypotController::class, 'gitConfig']);
Route::get('/.git/HEAD',        [HoneypotController::class, 'gitHead']);
Route::get('/.htpasswd',        [HoneypotController::class, 'htpasswd']);
Route::get('/.htaccess',        [HoneypotController::class, 'htaccess']);
Route::get('/web.config',       [HoneypotController::class, 'webConfig']);
Route::get('/config.php',       [HoneypotController::class, 'configPhp']);
Route::get('/configuration.php',[HoneypotController::class, 'configPhp']);
Route::get('/settings.php',     [HoneypotController::class, 'configPhp']);
Route::get('/id_rsa',           [HoneypotController::class, 'sshKey']);
Route::get('/id_rsa.pub',       [HoneypotController::class, 'sshPub']);
Route::get('/.ssh/id_rsa',      [HoneypotController::class, 'sshKey']);
Route::get('/.aws/credentials', [HoneypotController::class, 'awsCredentials']);
Route::get('/aws-credentials',  [HoneypotController::class, 'awsCredentials']);
Route::get('/.DS_Store',        [HoneypotController::class, 'dsStore']);
Route::get('/passwd',           [HoneypotController::class, 'passwd']);
Route::get('/etc/passwd',       [HoneypotController::class, 'passwd']);

// ─────────────────────────────────────────────────────────────────────────────
// SQL dump traps
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/backup.sql',    [HoneypotController::class, 'backupSql']);
Route::get('/database.sql',  [HoneypotController::class, 'backupSql']);
Route::get('/dump.sql',      [HoneypotController::class, 'backupSql']);
Route::get('/db.sql',        [HoneypotController::class, 'backupSql']);
Route::get('/db_backup.sql', [HoneypotController::class, 'backupSql']);

// ─────────────────────────────────────────────────────────────────────────────
// Fake REST API
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/api/v1/users',   [HoneypotController::class, 'apiUsers']);
Route::post('/api/v1/login',  [HoneypotController::class, 'apiLogin']);
Route::post('/api/login',     [HoneypotController::class, 'apiLogin']);
Route::get('/api/v1/admin',   [HoneypotController::class, 'apiAdmin']);
Route::get('/api/v1/config',  [HoneypotController::class, 'apiConfig']);
Route::any('/api/{any}',      [HoneypotController::class, 'catchAll'])->where('any', '.*');

// ─────────────────────────────────────────────────────────────────────────────
// .NET / IIS probes
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/elmah.axd',         [HoneypotController::class, 'dotnetProbe']);
Route::get('/elmah.axd/{any}',   [HoneypotController::class, 'dotnetProbe'])->where('any', '.*');
Route::get('/trace.axd',         [HoneypotController::class, 'dotnetProbe']);

// ─────────────────────────────────────────────────────────────────────────────
// Canary token trigger endpoint
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/canary/{token}', [HoneypotController::class, 'canary']);

// ─────────────────────────────────────────────────────────────────────────────
// Catch-all — MUST remain last
// ─────────────────────────────────────────────────────────────────────────────
Route::any('/{any}', [HoneypotController::class, 'catchAll'])->where('any', '.*');
