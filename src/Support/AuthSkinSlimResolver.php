<?php

namespace Azuriom\Plugin\AuthSkinSlim\Support;

use Azuriom\Plugin\AuthSkinSlim\Models\AuthSkinSlim;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AuthSkinSlimResolver
{
    /**
     * true = slim (Alex) model, false = default (Steve) or no skin.
     * Cached in auth_skin_slim; invalidated when skins/{id}.png mtime changes.
     */
    public static function isSlimForUser(int $userId): bool
    {
        $relativePath = "skins/{$userId}.png";
        $disk = Storage::disk('public');

        if (! $disk->exists($relativePath)) {
            if (Schema::hasTable('auth_skin_slim')) {
                AuthSkinSlim::query()->where('user_id', $userId)->delete();
            }

            return false;
        }

        if (! Schema::hasTable('auth_skin_slim')) {
            return SkinSlimDetector::isSlimFromPath($disk->path($relativePath));
        }

        $mtime = (int) $disk->lastModified($relativePath);

        $row = AuthSkinSlim::query()->where('user_id', $userId)->first();
        if ($row !== null && (int) $row->skin_mtime === $mtime) {
            return $row->is_slim;
        }

        $isSlim = SkinSlimDetector::isSlimFromPath($disk->path($relativePath));

        AuthSkinSlim::query()->updateOrCreate(
            ['user_id' => $userId],
            [
                'is_slim' => $isSlim,
                'skin_mtime' => $mtime,
            ]
        );

        return $isSlim;
    }
}
