<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanaryTrigger extends Model
{
    protected $fillable = [
        'canary_token_id',
        'ip_address',
        'country_code',
        'country_name',
        'user_agent',
        'referer',
    ];

    public function token(): BelongsTo
    {
        return $this->belongsTo(CanaryToken::class, 'canary_token_id');
    }
}
