<?php

namespace Azuriom\Plugin\AuthSkinSlim\Http\Middleware;

use Azuriom\Plugin\AuthSkinSlim\Support\AuthSkinSlimResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeSkinSlimIntoAuthApiResponse
{
    private const ROUTES = ['auth.authenticate'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $route = $request->route();
        if ($route === null || ! in_array($route->getName(), self::ROUTES, true)) {
            return $response;
        }

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || $content === '') {
            return $response;
        }

        $data = json_decode($content, true);
        if (! is_array($data) || ! isset($data['id'], $data['access_token'])) {
            return $response;
        }

        $data['skin'] = [
            'slim' => AuthSkinSlimResolver::isSlimForUser((int) $data['id']),
        ];

        $response->setContent(json_encode($data));

        return $response;
    }
}
