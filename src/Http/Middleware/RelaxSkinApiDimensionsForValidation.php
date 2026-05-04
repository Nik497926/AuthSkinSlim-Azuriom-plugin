<?php

namespace Azuriom\Plugin\AuthSkinSlim\Http\Middleware;

use Azuriom\Support\SettingsRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * While Skin API validates uploads with Rule::dimensions from settings (exact size when scale=1),
 * temporarily widen min/max so HD / legacy / Alex layouts pass — only for Skin API upload routes.
 */
class RelaxSkinApiDimensionsForValidation
{
    private const SKIN_SETTING_KEYS = [
        'skin.width',
        'skin.height',
        'skin.scale',
        'skin.capes.width',
        'skin.capes.height',
        'skin.capes.scale',
    ];

    private const RELAXED = [
        'skin.width' => 1,
        'skin.height' => 1,
        'skin.scale' => 16384,
        'skin.capes.width' => 1,
        'skin.capes.height' => 1,
        'skin.capes.scale' => 16384,
    ];

    private const UPLOAD_ROUTE_NAMES = [
        'skin-api.update',
        'skin-api.api.update',
        'skin-api.api.capes.update',
    ];

    /** Defaults when a key is missing from settings (same as Skin API fallbacks). */
    private const DIMENSION_DEFAULTS = [
        'skin.width' => 64,
        'skin.height' => 64,
        'skin.scale' => 1,
        'skin.capes.width' => 64,
        'skin.capes.height' => 32,
        'skin.capes.scale' => 1,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! plugins()->isEnabled('skin-api')) {
            return $next($request);
        }

        if (! $this->relaxEnabled()) {
            return $next($request);
        }

        if (! $request->isMethod('POST') || ! $this->isSkinApiSkinOrCapeUpload($request)) {
            return $next($request);
        }

        $repo = app(SettingsRepository::class);
        $snapshot = $this->snapshotSkinDimensionSettings();

        foreach (self::RELAXED as $key => $value) {
            $repo->set($key, (string) $value);
        }

        try {
            return $next($request);
        } finally {
            $repo->set($snapshot);
        }
    }

    private function relaxEnabled(): bool
    {
        $v = setting('auth-skin-slim.relax_skin_api_dimensions', '0');

        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshotSkinDimensionSettings(): array
    {
        $out = [];
        foreach (self::SKIN_SETTING_KEYS as $key) {
            $out[$key] = setting($key, self::DIMENSION_DEFAULTS[$key] ?? 1);
        }

        return $out;
    }

    /**
     * Skin API registers a duplicate POST route without a name; match URI as well.
     */
    private function isSkinApiSkinOrCapeUpload(Request $request): bool
    {
        if ($request->routeIs(...self::UPLOAD_ROUTE_NAMES)) {
            return true;
        }

        return $request->is('api/skin-api/skins/update');
    }
}
