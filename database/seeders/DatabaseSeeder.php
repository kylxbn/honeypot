<?php

namespace Database\Seeders;

use App\Models\HoneypotCredential;
use App\Models\HoneypotRequest;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed fake honeypot activity so the dashboard looks populated from day 1.
     * All data is synthetic — no real credentials, no real IPs (using documentation ranges).
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(99999);

        // Documentation/example IP ranges (RFC 5737 / RFC 3849)
        $fakeIps = [
            '203.0.113.45', '203.0.113.87', '203.0.113.12',
            '198.51.100.22', '198.51.100.77', '198.51.100.200',
            '192.0.2.178',  '192.0.2.33',   '192.0.2.100',
            '45.33.32.156', '45.33.32.200',
            '104.21.0.8',   '104.21.44.77',
        ];

        $trapTypes = [
            'wp-login', 'wp-login', 'wp-login',   // common
            'admin-panel', 'admin-panel',
            'env-file', 'env-file',
            'phpmyadmin',
            'webshell', 'webshell',
            'git-exposure',
            'htpasswd',
            'backup-file',
            'api-probe',
            'phpinfo',
            'ssh-key',
            'unknown', 'unknown', 'unknown',
        ];

        $trapPaths = [
            'wp-login'    => '/wp-login.php',
            'admin-panel' => '/admin',
            'env-file'    => '/.env',
            'phpmyadmin'  => '/phpmyadmin',
            'webshell'    => '/shell.php',
            'git-exposure'=> '/.git/config',
            'htpasswd'    => '/.htpasswd',
            'backup-file' => '/backup.sql',
            'api-probe'   => '/api/v1/users',
            'phpinfo'     => '/phpinfo.php',
            'ssh-key'     => '/id_rsa',
            'unknown'     => '/' . $faker->slug(2),
        ];

        $userAgents = [
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0',
            'python-requests/2.31.0',
            'curl/7.88.1',
            'masscan/1.0',
            'Nikto/2.1.6',
            'sqlmap/1.7.8#stable',
            'WPScan v3.8.25',
            'zgrab/0.x',
            'Go-http-client/1.1',
            'libwww-perl/6.67',
        ];

        // Seed ~200 historical requests spread over the last 30 days
        for ($i = 0; $i < 200; $i++) {
            $trap   = $faker->randomElement($trapTypes);
            $path   = $trapPaths[$trap] ?? '/' . $faker->slug(2);
            $ip     = $faker->randomElement($fakeIps);
            $method = in_array($trap, ['wp-login', 'admin-panel']) && $faker->boolean(30) ? 'POST' : 'GET';
            $flagged = in_array($trap, ['wp-login', 'admin-panel', 'webshell']) && $method === 'POST';

            $req = HoneypotRequest::create([
                'ip_address'   => $ip,
                'method'       => $method,
                'url'          => config('app.url') . $path,
                'path'         => $path,
                'query_string' => $faker->boolean(20) ? 'page=' . $faker->numberBetween(1, 10) : null,
                'user_agent'   => $faker->randomElement($userAgents),
                'headers'      => ['host' => config('honeypot.company.domain'), 'accept' => '*/*'],
                'request_body' => $method === 'POST' ? json_encode(['username' => $faker->userName, 'password' => $faker->password]) : null,
                'referer'      => $faker->boolean(15) ? 'https://www.shodan.io/' : null,
                'trap_type'    => $trap,
                'is_flagged'   => $flagged,
                'created_at'   => now()->subDays($faker->numberBetween(0, 30))->subSeconds($faker->numberBetween(0, 86400)),
                'updated_at'   => now()->subDays($faker->numberBetween(0, 30)),
            ]);

            // Create matching credential for flagged POST attempts
            if ($flagged) {
                HoneypotCredential::create([
                    'honeypot_request_id' => $req->id,
                    'trap_url'            => config('app.url') . $path,
                    'username'            => $faker->randomElement(['admin', 'administrator', 'root', $faker->userName]),
                    'password'            => $faker->randomElement(['admin', 'password', '123456', 'admin123', $faker->password(6, 10)]),
                    'ip_address'          => $ip,
                    'created_at'          => $req->created_at,
                    'updated_at'          => $req->updated_at,
                ]);
            }
        }

        $this->command->info('Seeded 200 fake honeypot requests.');
    }
}
