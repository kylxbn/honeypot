<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HoneypotRequest extends Model
{
    protected $fillable = [
        'ip_address',
        'method',
        'url',
        'path',
        'query_string',
        'user_agent',
        'headers',
        'request_body',
        'referer',
        'trap_type',
        'is_flagged',
    ];

    protected $casts = [
        'headers'    => 'array',
        'is_flagged' => 'boolean',
    ];

    public function credential(): HasOne
    {
        return $this->hasOne(HoneypotCredential::class);
    }

    public function trapLabel(): string
    {
        return match ($this->trap_type) {
            'wp-login'       => 'WordPress Login',
            'wp-admin'       => 'WordPress Admin',
            'wp-xmlrpc'      => 'WordPress XML-RPC',
            'wp-config'      => 'wp-config.php',
            'env-file'       => '.env File',
            'git-exposure'   => '.git Exposure',
            'htpasswd'       => '.htpasswd',
            'htaccess'       => '.htaccess',
            'iis-config'     => 'web.config',
            'phpmyadmin'     => 'phpMyAdmin',
            'phpinfo'        => 'phpinfo',
            'server-status'  => 'Server Status',
            'admin-panel'    => 'Admin Panel',
            'webshell'       => 'Webshell',
            'backup-file'    => 'Backup/SQL File',
            'api-probe'      => 'API Probe',
            'config-exposure'=> 'Config Exposure',
            'ssh-key'        => 'SSH Key',
            'os-exposure'    => 'OS File',
            'dotnet-probe'   => '.NET Diagnostics',
            'robots'         => 'robots.txt',
            'sitemap'        => 'Sitemap',
            'crossdomain'    => 'crossdomain.xml',
            'dashboard'      => 'Dashboard Access',
            'home'           => 'Homepage',
            default          => ucfirst(str_replace('-', ' ', $this->trap_type)),
        };
    }

    public function trapColor(): string
    {
        return match ($this->trap_type) {
            'webshell', 'ssh-key', 'os-exposure'   => 'red',
            'wp-login', 'admin-panel', 'phpmyadmin' => 'orange',
            'env-file', 'wp-config', 'iis-config'   => 'yellow',
            'git-exposure', 'htpasswd', 'htaccess'  => 'yellow',
            'backup-file', 'config-exposure'        => 'yellow',
            'api-probe', 'dotnet-probe'             => 'blue',
            'dashboard'                             => 'purple',
            default                                 => 'gray',
        };
    }
}
