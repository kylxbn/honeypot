<?php

namespace App\Http\Middleware;

use App\Models\HoneypotRequest;
use Closure;
use GeoIp2\Database\Reader as GeoReader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogHoneypotRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $path     = $request->path();
        $fullPath = '/' . $path;

        // Skip dashboard and other internal paths
        $skipPaths = array_merge(
            config('honeypot.skip_paths', []),
            [config('honeypot.dashboard_path')]
        );
        foreach ($skipPaths as $skip) {
            if ($fullPath === $skip || str_starts_with($fullPath, $skip)) {
                return $next($request);
            }
        }

        try {
            $trapType = self::detectTrapType($fullPath);

            $headers = null;
            if (config('honeypot.log_headers', true)) {
                $headers = collect($request->headers->all())
                    ->map(fn($v) => count($v) === 1 ? $v[0] : $v)
                    ->except(['cookie', 'authorization'])
                    ->toArray();
            }

            $body = null;
            if (config('honeypot.log_request_body', true) && $request->isMethod('POST')) {
                $body = json_encode($request->except(['_token']));
            }

            [$countryCode, $countryName] = self::geolocate($request->ip());

            $honeypotRequest = HoneypotRequest::create([
                'ip_address'   => $request->ip(),
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'method'       => $request->method(),
                'url'          => $request->fullUrl(),
                'path'         => $fullPath,
                'query_string' => $request->getQueryString(),
                'user_agent'   => $request->userAgent(),
                'headers'      => $headers,
                'request_body' => $body,
                'referer'      => $request->header('Referer'),
                'trap_type'    => $trapType,
                'is_flagged'   => false,
            ]);

            $request->attributes->set('honeypot_request_id', $honeypotRequest->id);
        } catch (Throwable) {
            // Never let logging break the response
        }

        return $next($request);
    }

    /**
     * Returns [countryCode, countryName] or [null, null] if lookup fails
     * (missing DB file, private IP, etc.).
     */
    public static function geolocate(string $ip): array
    {
        try {
            $dbPath = config('honeypot.geoip_db_path');
            if (!$dbPath || !file_exists($dbPath)) {
                return [null, null];
            }
            $record = (new GeoReader($dbPath))->country($ip);
            return [
                $record->country->isoCode,
                $record->country->name,
            ];
        } catch (Throwable) {
            return [null, null];
        }
    }

    public static function detectTrapType(string $path): string
    {
        $lower = strtolower($path);

        if ($lower === '/') {
            return 'home';
        }

        $patterns = [
            'wp-login'       => 'wp-login',
            'wp-admin'       => 'wp-admin',
            'xmlrpc'         => 'wp-xmlrpc',
            'wp-config'      => 'wp-config',
            '.env'           => 'env-file',
            '.git'           => 'git-exposure',
            '.htpasswd'      => 'htpasswd',
            '.htaccess'      => 'htaccess',
            'web.config'     => 'iis-config',
            'phpmyadmin'     => 'phpmyadmin',
            '/pma'           => 'phpmyadmin',
            'phpinfo'        => 'phpinfo',
            'server-status'  => 'server-status',
            'server-info'    => 'server-status',
            '/admin'         => 'admin-panel',
            '/administrator' => 'admin-panel',
            '/login'         => 'admin-panel',
            'shell.php'      => 'webshell',
            'c99.php'        => 'webshell',
            'r57.php'        => 'webshell',
            'b374k'          => 'webshell',
            'wso.php'        => 'webshell',
            'cmd.php'        => 'webshell',
            '/uploads/'      => 'webshell',
            '/shell'         => 'webshell',
            'backup'         => 'backup-file',
            '.sql'           => 'backup-file',
            'dump'           => 'backup-file',
            '/api/'          => 'api-probe',
            'config.php'     => 'config-exposure',
            'configuration'  => 'config-exposure',
            'settings.php'   => 'config-exposure',
            'id_rsa'         => 'ssh-key',
            '.ssh'           => 'ssh-key',
            '/passwd'        => 'os-exposure',
            '/etc/'          => 'os-exposure',
            'elmah'          => 'dotnet-probe',
            'trace.axd'      => 'dotnet-probe',
            'robots.txt'     => 'robots',
            'sitemap'        => 'sitemap',
            'crossdomain'    => 'crossdomain',
            'clientaccess'   => 'crossdomain',
            '/canary/'       => 'canary',
        ];

        foreach ($patterns as $needle => $type) {
            if (str_contains($lower, $needle)) {
                return $type;
            }
        }

        return 'unknown';
    }
}
