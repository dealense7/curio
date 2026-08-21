<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthLoginAttempt extends Model
{
    use HasPublicId;

    protected $fillable = [
        'user_id',
        'company_id',
        'login',
        'succeeded',
        'ip_address',
        'user_agent',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'succeeded'    => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
