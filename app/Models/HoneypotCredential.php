<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoneypotCredential extends Model
{
    protected $fillable = [
        'honeypot_request_id',
        'trap_url',
        'username',
        'password',
        'additional_fields',
        'ip_address',
    ];

    protected $casts = [
        'additional_fields' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(HoneypotRequest::class, 'honeypot_request_id');
    }
}
