<?php

namespace App\Http\Controllers;

use App\Models\CanaryToken;
use App\Models\HoneypotCredential;
use App\Models\HoneypotRequest;
use Faker\Factory as Faker;
use Illuminate\Http\Request;

class HoneypotController extends Controller
{
    // -------------------------------------------------------------------------
    // Credential capture helper
    // -------------------------------------------------------------------------

    private function captureCredentials(
        Request $request,
        string $usernameField = 'username',
        string $passwordField = 'password',
    ): void {
        try {
            $username = $request->input($usernameField)
                ?? $request->input('user')
                ?? $request->input('email')
                ?? $request->input('log')
                ?? $request->input('usr');

            $password = $request->input($passwordField)
                ?? $request->input('pass')
                ?? $request->input('pwd')
                ?? $request->input('passwd');

            $excluded = [$usernameField, $passwordField, '_token', 'log', 'pwd',
                         'wp-submit', 'redirect_to', 'testcookie', 'rememberme',
                         'username', 'password', 'user', 'email', 'pass', 'passwd'];

            $additional = array_filter(
                $request->except($excluded),
                fn($v) => $v !== null && $v !== ''
            );

            HoneypotCredential::create([
                'honeypot_request_id' => $request->attributes->get('honeypot_request_id'),
                'trap_url'            => $request->fullUrl(),
                'username'            => $username,
                'password'            => $password,
                'additional_fields'   => !empty($additional) ? $additional : null,
                'ip_address'          => $request->ip(),
            ]);

            // Mark the parent request as flagged
            $id = $request->attributes->get('honeypot_request_id');
            if ($id) {
                HoneypotRequest::where('id', $id)->update(['is_flagged' => true]);
            }
        } catch (\Throwable) {
        }
    }

    // -------------------------------------------------------------------------
    // Canary token helper
    // -------------------------------------------------------------------------

    private function canaryUrl(string $trapSource, string $fallback = 'https://novatech-solutions.com'): string
    {
        try {
            $token = CanaryToken::where('trap_source', $trapSource)->first();
            return $token ? $token->url() : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    // -------------------------------------------------------------------------
    // Fake user data (deterministic via seeded Faker)
    // -------------------------------------------------------------------------

    private function fakeUsers(): array
    {
        $faker = Faker::create();
        $faker->seed(54321);
        $users = [];
        $apiCanaryUrl = $this->canaryUrl('api-avatar');
        $sqlCanaryUrl = $this->canaryUrl('sql-dump-avatar');

        for ($i = 1; $i <= 20; $i++) {
            $plainPass = $faker->password(8, 12);
            $users[] = [
                'id'           => $i,
                'username'     => $faker->userName,
                'email'        => $faker->safeEmail,
                'password'     => $plainPass,
                'password_md5' => md5($plainPass),
                'first_name'   => $faker->firstName,
                'last_name'    => $faker->lastName,
                'phone'        => $faker->phoneNumber,
                'role'         => $i === 1 ? 'admin' : ($i <= 3 ? 'moderator' : 'user'),
                'is_active'    => $faker->boolean(90),
                'last_login'   => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
                'created_at'   => $faker->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d H:i:s'),
                // Canary: if someone fetches this avatar URL, we know they processed the data
                'avatar_url'   => ($i === 1 ? $apiCanaryUrl : "https://cdn.novatech-solutions.com/avatars/{$i}.jpg"),
                'avatar_url_sql' => ($i === 1 ? $sqlCanaryUrl : "https://cdn.novatech-solutions.com/avatars/{$i}.jpg"),
            ];
        }
        return $users;
    }

    // -------------------------------------------------------------------------
    // Pages
    // -------------------------------------------------------------------------

    public function home()
    {
        return view('honeypot.home');
    }

    // -------------------------------------------------------------------------
    // WordPress traps
    // -------------------------------------------------------------------------

    public function wpLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->captureCredentials($request, 'log', 'pwd');
            return redirect('/wp-admin');
        }
        return view('honeypot.wp-login');
    }

    public function wpAdmin(Request $request)
    {
        return view('honeypot.wp-admin');
    }

    public function xmlrpc(Request $request)
    {
        // Log the full body (often contains method calls)
        return response(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<methodResponse><fault><value><struct>' .
            '<member><name>faultCode</name><value><int>403</int></value></member>' .
            '<member><name>faultString</name><value><string>Forbidden.</string></value></member>' .
            '</struct></value></fault></methodResponse>',
            403,
            ['Content-Type' => 'text/xml']
        );
    }

    // -------------------------------------------------------------------------
    // Generic admin traps
    // -------------------------------------------------------------------------

    public function adminLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->captureCredentials($request);
            return redirect('/admin/dashboard');
        }
        return view('honeypot.admin-login');
    }

    public function adminPanel()
    {
        return view('honeypot.admin-panel');
    }

    // -------------------------------------------------------------------------
    // phpMyAdmin trap
    // -------------------------------------------------------------------------

    public function phpMyAdmin(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->captureCredentials($request, 'pma_username', 'pma_password');
        }
        return view('honeypot.phpmyadmin', ['users' => $this->fakeUsers()]);
    }

    // -------------------------------------------------------------------------
    // Fake webshell
    // -------------------------------------------------------------------------

    public function shell(Request $request)
    {
        $output = null;
        $cmd    = null;

        if ($request->isMethod('post')) {
            $cmd    = $request->input('cmd', '');
            $output = $this->fakeShellOutput(trim($cmd));

            // Store the command as a credential capture
            try {
                HoneypotCredential::create([
                    'honeypot_request_id' => $request->attributes->get('honeypot_request_id'),
                    'trap_url'            => $request->fullUrl(),
                    'username'            => 'shell_cmd',
                    'password'            => $cmd,
                    'ip_address'          => $request->ip(),
                ]);
                $id = $request->attributes->get('honeypot_request_id');
                if ($id) {
                    HoneypotRequest::where('id', $id)->update(['is_flagged' => true]);
                }
            } catch (\Throwable) {
            }
        }

        return view('honeypot.shell', compact('output', 'cmd'));
    }

    private function fakeShellOutput(string $cmd): string
    {
        $baseCmd = strtolower(explode(' ', $cmd)[0]);

        return match (true) {
            str_starts_with($cmd, 'ls')      => $this->fakeLs($cmd),
            $baseCmd === 'pwd'               => '/var/www/html',
            $baseCmd === 'whoami'            => 'www-data',
            $baseCmd === 'id'                => 'uid=33(www-data) gid=33(www-data) groups=33(www-data)',
            str_contains($cmd, '/etc/passwd')=> $this->fakePasswd(),
            str_contains($cmd, '/etc/shadow')=> 'cat: /etc/shadow: Permission denied',
            $baseCmd === 'uname'             => 'Linux novatech-prod-01 5.15.0-91-generic #101-Ubuntu SMP Tue Nov 14 13:30:08 UTC 2023 x86_64 x86_64 x86_64 GNU/Linux',
            $baseCmd === 'hostname'          => 'novatech-prod-01',
            str_starts_with($cmd, 'cat')     => 'cat: ' . (explode(' ', $cmd)[1] ?? 'file') . ': No such file or directory',
            $baseCmd === 'ifconfig'          => "eth0: flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 1500\n        inet 10.0.1.42  netmask 255.255.255.0  broadcast 10.0.1.255",
            $baseCmd === 'ps'               => "  PID TTY          TIME CMD\n    1 ?        00:00:01 php-fpm\n   42 ?        00:00:00 nginx\n  107 ?        00:00:00 sh",
            $baseCmd === 'env'               => $this->fakeEnvContent(),
            $baseCmd === 'netstat'           => "tcp   0   0 0.0.0.0:80   0.0.0.0:*   LISTEN\ntcp   0   0 0.0.0.0:443  0.0.0.0:*   LISTEN",
            $baseCmd === 'php'               => 'PHP 8.4.1 (cli) (built: Nov 20 2023)',
            $baseCmd === 'mysql'             => 'ERROR 1045 (28000): Access denied for user \'www-data\'@\'localhost\'',
            default                          => "sh: 1: {$baseCmd}: not found",
        };
    }

    private function fakeLs(string $cmd): string
    {
        if (str_contains($cmd, '/etc')) {
            return "passwd  shadow  hosts  hostname  nginx  php  mysql  ssl  cron.d  cron.daily";
        }
        if (str_contains($cmd, '-la') || str_contains($cmd, '-l')) {
            return implode("\n", [
                'total 64',
                'drwxr-xr-x 12 www-data www-data 4096 Jan 15 09:23 .',
                'drwxr-xr-x  5 root     root     4096 Jan  1 00:00 ..',
                '-rw-r--r--  1 www-data www-data  512 Jan 15 09:00 .env',
                'drwxr-xr-x  7 www-data www-data 4096 Jan 15 09:23 app',
                'drwxr-xr-x  3 www-data www-data 4096 Jan  1 00:00 bootstrap',
                'drwxr-xr-x  4 www-data www-data 4096 Jan  1 00:00 config',
                'drwxr-xr-x  5 www-data www-data 4096 Jan 15 08:00 database',
                '-rw-r--r--  1 www-data www-data 1892 Jan  1 00:00 composer.json',
                'drwxr-xr-x  2 www-data www-data 4096 Jan  1 00:00 public',
                'drwxr-xr-x  4 www-data www-data 4096 Jan 15 09:00 storage',
            ]);
        }
        return "app  bootstrap  config  database  .env  public  resources  routes  storage  vendor";
    }

    // -------------------------------------------------------------------------
    // File / config exposures
    // -------------------------------------------------------------------------

    public function envFile()
    {
        return response($this->fakeEnvContent(), 200)
            ->header('Content-Type', 'text/plain');
    }

    private function fakeEnvContent(): string
    {
        $appUrlCanary     = $this->canaryUrl('env-app-url',  'https://novatech-solutions.com');
        $webhookCanary    = $this->canaryUrl('env-webhook',  'https://hooks.novatech-solutions.com/notify');
        $awsCanary        = $this->canaryUrl('aws-key-test', 'https://s3.novatech-solutions.com/health');

        return <<<ENV
APP_NAME="NovaTech Solutions"
APP_ENV=production
APP_KEY=base64:kN4YkW+RZpLo8QmJxT2vHiUcWdEsF9nM3aBbCeGhIjY=
APP_DEBUG=true
APP_URL={$appUrlCanary}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db-prod-01.internal
DB_PORT=3306
DB_DATABASE=novatech_prod
DB_USERNAME=novatech_app
DB_PASSWORD=Pr0d_DB_P@ssw0rd_2024!

REDIS_HOST=redis-01.internal
REDIS_PASSWORD=R3d1s_S3cr3t_K3y!
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=system@novatech-solutions.com
MAIL_PASSWORD=Gm@1lS3rv1c3P@ss!
MAIL_ENCRYPTION=tls

AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=novatech-prod-storage-2024
AWS_ENDPOINT={$awsCanary}

STRIPE_KEY=pk_live_51NkOmKAbCdEfGhIjKlMnOpQrStUvWxYz12345678
STRIPE_SECRET=sk_live_51NkOmKAbCdEfGhIjKlMnOpQrStUvWxYz87654321

OPENAI_API_KEY=sk-proj-FakeKeyForHoneypot1234567890abcdefghij

JWT_SECRET=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.honeypot.fakekey

WEBHOOK_SECRET_URL={$webhookCanary}

ADMIN_EMAIL=admin@novatech-internal.com
ADMIN_PASSWORD=N0v@T3ch4dm1n!2024
BACKUP_ENCRYPTION_KEY=Bckp_3ncrypt_K3y_2024!
ENV;
    }

    public function gitConfig()
    {
        return response(
            "[core]\n\trepositoryformatversion = 0\n\tfilemode = true\n\tbare = false\n\tlogallrefupdates = true\n[remote \"origin\"]\n\turl = git@github.com:novatech-internal/novatech-app.git\n\tfetch = +refs/heads/*:refs/remotes/origin/*\n[branch \"main\"]\n\tremote = origin\n\tmerge = refs/heads/main\n[user]\n\temail = deploy@novatech-solutions.com\n\tname = NovaTech Deploy Bot",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function gitHead()
    {
        return response("ref: refs/heads/main\n", 200, ['Content-Type' => 'text/plain']);
    }

    public function htpasswd()
    {
        return response(
            "admin:\$apr1\$rA1Vq0Vz\$xH7n.VbJ3NqKmLp8wS2eT1\nbackup:\$apr1\$xK8mQ1Nt\$yJ4o.UaI5OpBcDe6fGhHi0\nmonitor:\$apr1\$zL9nR2Ou\$aK5p.VbJ7MqNoCd1eFeGh2\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function htaccess()
    {
        return response(
            "AuthType Basic\nAuthName \"Restricted Area\"\nAuthUserFile /etc/apache2/.htpasswd\nRequire valid-user\n\nOptions +Indexes\n\nphp_flag display_errors On\nphp_value error_reporting E_ALL\nphp_value upload_max_filesize 256M\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function webConfig()
    {
        return response(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<configuration><connectionStrings>' .
            '<add name="DefaultConnection" connectionString="Server=db-prod-01.internal;Database=novatech_prod;User Id=novatech_sa;Password=Pr0d_DB_P@ss!2024;" providerName="System.Data.SqlClient"/>' .
            '</connectionStrings><appSettings>' .
            '<add key="ApiSecret" value="nT9xK2mP4qR7sW1vY3zA6bC8dE0fG5hI"/>' .
            '<add key="AdminPassword" value="N0v@T3ch4dm1n!"/>' .
            '<add key="Environment" value="Production"/>' .
            '</appSettings></configuration>',
            200, ['Content-Type' => 'application/xml']
        );
    }

    public function wpConfigPhp()
    {
        return response(
            "<?php\n/** NovaTech WordPress Config */\ndefine( 'DB_NAME', 'novatech_wp' );\ndefine( 'DB_USER', 'wp_admin' );\ndefine( 'DB_PASSWORD', 'WpD@tab@se2024!' );\ndefine( 'DB_HOST', 'db-prod-01.internal' );\ndefine( 'AUTH_KEY',         'f3h#K9mP\$qR2sT5uV7wX1yZ' );\ndefine( 'SECURE_AUTH_KEY',  'a4B6cD8eF0gH2iJ4kL6mN8oP' );\ndefine( 'LOGGED_IN_KEY',    'Q0rS2tU4vW6xY8zA0bC2dE4f' );\ndefine( 'DEBUG', false );\n\$table_prefix = 'wp_';\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function configPhp()
    {
        return response(
            "<?php\n// NovaTech Application Configuration\ndefine('DB_HOST', 'db-prod-01.internal');\ndefine('DB_NAME', 'novatech_prod');\ndefine('DB_USER', 'novatech_app');\ndefine('DB_PASS', 'Pr0d_DB_P@ssw0rd_2024!');\ndefine('SECRET_KEY', 'nT9xK2mP4qR7sW1vY3zA6bC8dE0fG5hI');\ndefine('ADMIN_USER', 'admin');\ndefine('ADMIN_PASS', 'N0v@T3ch4dm1n!2024');\ndefine('ENV', 'production');\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function sshKey()
    {
        return response(
            "-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA2a2rwplBQLF29amygykEMmYz0+Kcj3bKBp29P2rFj7cMSQ==\nbHGGMjwLMBpCkSBMmFvEdFJREWGm0U2yNvFHCKMknPzJrBe98ycF81GbLEKsNY==\nVjEVgsGk2Gp1nFAFMgKBgQC5sHdNNRDXGmLThcXBJRkl8E9b2jXoIhQq3XPLHB==\nfQkN5rW8mP0Kx1T9yV2uI4nM7oJzA3bC6dE8fG1hI2kL3mN4oP5qR6sT7uV8wX==\n-----END RSA PRIVATE KEY-----\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function sshPub()
    {
        return response(
            "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC2a2rwplBQLzj3gFakeKeyData+Kcj3bKBp29P2rFj7cMSQbHGGMjwLMBpCk= deploy@novatech-solutions.com\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    public function awsCredentials()
    {
        return response(
            "[default]\naws_access_key_id = AKIAIOSFODNN7EXAMPLE\naws_secret_access_key = wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY\nregion = us-east-1\n\n[production]\naws_access_key_id = AKIAI44QH8DHBEXAMPLE\naws_secret_access_key = je7MtGbClwBF/2Zp9Utk/h3yCo8nvbEXAMPLEKEY\nregion = us-east-1\n",
            200, ['Content-Type' => 'text/plain']
        );
    }

    // -------------------------------------------------------------------------
    // Server diagnostics
    // -------------------------------------------------------------------------

    public function phpInfo()
    {
        return view('honeypot.phpinfo');
    }

    public function serverStatus()
    {
        return view('honeypot.server-status');
    }

    public function dotnetProbe()
    {
        return view('honeypot.elmah');
    }

    // -------------------------------------------------------------------------
    // Backup files
    // -------------------------------------------------------------------------

    public function backupSql()
    {
        $users  = $this->fakeUsers();
        $faker  = Faker::create();
        $faker->seed(11111);

        $sql  = "-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)\n";
        $sql .= "-- Server version\t8.0.36\n-- Date: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: novatech_prod\n\n";
        $sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n\n";
        $logoCanary = $this->canaryUrl('backup-sql-logo', 'https://cdn.novatech-solutions.com/logo.png');

        $sql .= "-- Table structure for table `settings`\n\n";
        $sql .= "CREATE TABLE `settings` (`key` varchar(100) PRIMARY KEY, `value` text) ENGINE=InnoDB;\n";
        $sql .= "INSERT INTO `settings` VALUES ('company_logo_url','{$logoCanary}'),('app_version','3.4.1'),('maintenance_mode','0');\n\n";

        $sql .= "-- Table structure for table `users`\n\n";
        $sql .= "DROP TABLE IF EXISTS `users`;\n";
        $sql .= "CREATE TABLE `users` (\n  `id` int NOT NULL AUTO_INCREMENT,\n  `username` varchar(255) NOT NULL,\n  `email` varchar(255) NOT NULL,\n  `password` varchar(255) NOT NULL COMMENT 'MD5 hashed',\n  `avatar_url` varchar(500) DEFAULT NULL,\n  `role` enum('admin','moderator','user') DEFAULT 'user',\n  `is_active` tinyint(1) DEFAULT 1,\n  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,\n  PRIMARY KEY (`id`)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";
        $sql .= "-- Dumping data for table `users`\n\nINSERT INTO `users` VALUES\n";

        $rows = [];
        foreach ($users as $u) {
            $rows[] = sprintf(
                "(%d,'%s','%s','%s','%s','%s',%d,'%s')",
                $u['id'], addslashes($u['username']), addslashes($u['email']),
                md5($u['password']), addslashes($u['avatar_url_sql']),
                $u['role'], $u['is_active'] ? 1 : 0, $u['created_at']
            );
        }
        $sql .= implode(",\n", $rows) . ";\n\n";

        // Add a fake credit cards table
        $sql .= "-- Table structure for table `payment_methods`\n\n";
        $sql .= "CREATE TABLE `payment_methods` (\n  `id` int NOT NULL AUTO_INCREMENT,\n  `user_id` int NOT NULL,\n  `card_number` varchar(20) NOT NULL,\n  `card_holder` varchar(255) NOT NULL,\n  `expiry` varchar(10) NOT NULL,\n  `cvv` varchar(4) NOT NULL,\n  PRIMARY KEY (`id`)\n) ENGINE=InnoDB;\n\n";
        $sql .= "INSERT INTO `payment_methods` VALUES\n";
        $cardRows = [];
        for ($i = 1; $i <= 10; $i++) {
            $cardRows[] = sprintf(
                "(%d,%d,'4%s','%s %s','%02d/%d','%03d')",
                $i, $i, $faker->numerify('###############'),
                $faker->firstName, $faker->lastName,
                $faker->numberBetween(1, 12), $faker->numberBetween(2025, 2029),
                $faker->numberBetween(100, 999)
            );
        }
        $sql .= implode(",\n", $cardRows) . ";\n";

        return response($sql, 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="backup_' . now()->format('Ymd') . '.sql"');
    }

    // -------------------------------------------------------------------------
    // API endpoints
    // -------------------------------------------------------------------------

    public function apiUsers(Request $request)
    {
        $users = collect($this->fakeUsers())->map(fn($u) => array_except_keys($u, ['password', 'password_md5', 'avatar_url_sql']));

        return response()->json([
            'status'   => 'success',
            'data'     => $users,
            'total'    => count($users),
            'page'     => 1,
            'per_page' => 25,
        ]);
    }

    public function apiLogin(Request $request)
    {
        $this->captureCredentials($request);

        // Always return a fake JWT so attackers think they succeeded
        $fakeJwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.'
            . base64_encode(json_encode(['sub' => 1, 'role' => 'admin', 'exp' => now()->addDay()->timestamp]))
            . '.FakeSignatureDoNotTrust';

        return response()->json([
            'status' => 'success',
            'token'  => $fakeJwt,
            'user'   => [
                'id'       => 1,
                'username' => $request->input('username', 'admin'),
                'role'     => 'admin',
            ],
        ]);
    }

    public function apiAdmin(Request $request)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Key');
        if (!$token) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Bearer token required'], 401);
        }

        return response()->json([
            'status'  => 'success',
            'server'  => ['php' => '8.4.1', 'db' => 'MySQL 8.0.36', 'os' => 'Ubuntu 22.04'],
            'stats'   => ['users' => 20, 'revenue_mtd' => 142800.50, 'active_sessions' => 7],
            'secrets' => ['db_pass' => 'Pr0d_DB_P@ssw0rd_2024!', 'api_key' => 'nT9xK2mP4qR7sW1vY'],
        ]);
    }

    public function apiConfig()
    {
        return response()->json([
            'database' => ['host' => 'db-prod-01.internal', 'name' => 'novatech_prod',
                           'user' => 'novatech_app', 'password' => 'Pr0d_DB_P@ssw0rd_2024!'],
            'redis'    => ['host' => 'redis-01.internal', 'password' => 'R3d1s_S3cr3t_K3y!'],
            'aws'      => ['key' => 'AKIAIOSFODNN7EXAMPLE', 'secret' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'],
            'stripe'   => ['secret' => 'sk_live_51NkOmKAbCdEfGhIjKlMnOpQrStUvWxYz87654321'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Meta / discovery files
    // -------------------------------------------------------------------------

    public function robotsTxt()
    {
        $content = "User-agent: *\n"
            . "# Staff & CMS\n"
            . "Disallow: /wp-admin\n"
            . "Disallow: /wp-login.php\n"
            . "Disallow: /wp-config.php\n"
            . "Disallow: /admin\n"
            . "Disallow: /administrator\n"
            . "# Sensitive files - do NOT crawl\n"
            . "Disallow: /.env\n"
            . "Disallow: /.git\n"
            . "Disallow: /backup.sql\n"
            . "Disallow: /database.sql\n"
            . "Disallow: /dump.sql\n"
            . "Disallow: /phpmyadmin\n"
            . "Disallow: /phpinfo.php\n"
            . "Disallow: /config.php\n"
            . "# Internal tools\n"
            . "Disallow: /api/v1/admin\n"
            . "Disallow: /api/v1/config\n"
            . "Disallow: /server-status\n"
            . "Disallow: /uploads\n"
            . "# Old / deprecated\n"
            . "Disallow: /old-admin\n"
            . "Disallow: /dev\n"
            . "Disallow: /staging\n"
            . "Disallow: /test\n"
            . "Disallow: /private\n"
            . "Disallow: /internal\n"
            . "Disallow: /id_rsa\n"
            . "Disallow: /.htpasswd\n"
            . "\nSitemap: " . config('app.url') . "/sitemap.xml\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    public function sitemapXml()
    {
        $base = config('app.url');
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (['/', '/about', '/products', '/services', '/contact', '/login'] as $path) {
            $xml .= "<url><loc>{$base}{$path}</loc></url>";
        }
        $xml .= '</urlset>';
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function crossdomain()
    {
        return response(
            '<?xml version="1.0"?><!DOCTYPE cross-domain-policy SYSTEM "http://www.adobe.com/xml/dtds/cross-domain-policy.dtd"><cross-domain-policy><allow-access-from domain="*"/><allow-http-request-headers-from domain="*" headers="*"/></cross-domain-policy>',
            200, ['Content-Type' => 'text/xml']
        );
    }

    public function dsStore()
    {
        // Return the magic bytes of a real .DS_Store file header
        return response("\x00\x00\x00\x01Bud1\x00\x00\x10\x00\x00\x00\x04", 200)
            ->header('Content-Type', 'application/octet-stream');
    }

    public function passwd()
    {
        return response($this->fakePasswd(), 200)->header('Content-Type', 'text/plain');
    }

    private function fakePasswd(): string
    {
        return implode("\n", [
            'root:x:0:0:root:/root:/bin/bash',
            'daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin',
            'bin:x:2:2:bin:/bin:/usr/sbin/nologin',
            'www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin',
            'mysql:x:999:999:MySQL Server:/var/lib/mysql:/bin/false',
            'redis:x:998:998::/var/lib/redis:/bin/false',
            'novatech:x:1000:1000:NovaTech App,,,:/home/novatech:/bin/bash',
            'deploy:x:1001:1001:Deploy Bot,,,:/home/deploy:/bin/bash',
        ]);
    }

    // -------------------------------------------------------------------------
    // Canary token trigger
    // -------------------------------------------------------------------------

    public function canary(Request $request, string $token)
    {
        try {
            $canary = CanaryToken::where('token', $token)->first();
            if ($canary) {
                [$countryCode, $countryName] = \App\Http\Middleware\LogHoneypotRequest::geolocate($request->ip());
                $canary->recordTrigger(
                    $request->ip(),
                    $countryCode,
                    $countryName,
                    $request->userAgent(),
                    $request->header('Referer'),
                );
                // Also flag the honeypot_request that was already logged by the middleware
                $id = $request->attributes->get('honeypot_request_id');
                if ($id) {
                    \App\Models\HoneypotRequest::where('id', $id)->update(['is_flagged' => true]);
                }
            }
        } catch (\Throwable) {
        }

        // Return a convincing 1×1 transparent pixel so the caller gets a valid image response
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200)->header('Content-Type', 'image/gif');
    }

    // -------------------------------------------------------------------------
    // Catch-all
    // -------------------------------------------------------------------------

    public function catchAll(Request $request)
    {
        return response()->view('honeypot.not-found', [], 404);
    }
}

// Helper – array_except_keys not built-in, define inline
function array_except_keys(array $arr, array $keys): array
{
    return array_diff_key($arr, array_flip($keys));
}
