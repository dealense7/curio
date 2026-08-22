<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Models\Model;
use App\Models\User\User;
use App\Support\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthLoginAttempt extends Model
{
    use HasPublicId;

    protected $fillable = [
        'user_id',
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
