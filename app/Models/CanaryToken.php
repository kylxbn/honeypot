<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CanaryToken extends Model
{
    protected $fillable = [
        'token',
        'label',
        'trap_source',
        'description',
        'trigger_count',
        'first_triggered_at',
        'last_triggered_at',
    ];

    protected $casts = [
        'first_triggered_at' => 'datetime',
        'last_triggered_at'  => 'datetime',
    ];

    public function triggers(): HasMany
    {
        return $this->hasMany(CanaryTrigger::class);
    }

    public function url(): string
    {
        return url('/canary/' . $this->token);
    }

    public function recordTrigger(string $ip, ?string $countryCode, ?string $countryName, ?string $userAgent, ?string $referer): void
    {
        $this->triggers()->create([
            'ip_address'   => $ip,
            'country_code' => $countryCode,
            'country_name' => $countryName,
            'user_agent'   => $userAgent,
            'referer'      => $referer,
        ]);

        $this->increment('trigger_count');
        $this->last_triggered_at = now();
        if (!$this->first_triggered_at) {
            $this->first_triggered_at = now();
        }
        $this->save();
    }
}
