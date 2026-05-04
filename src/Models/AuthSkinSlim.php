<?php

namespace Azuriom\Plugin\AuthSkinSlim\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthSkinSlim extends Model
{
    protected $table = 'auth_skin_slim';

    protected $fillable = [
        'user_id',
        'is_slim',
        'skin_mtime',
    ];

    protected function casts(): array
    {
        return [
            'is_slim' => 'boolean',
            'skin_mtime' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
