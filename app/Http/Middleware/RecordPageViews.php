<?php

namespace App\Http\Middleware;

use App\Services\Traffic\TrafficHitResolver;
use App\Services\Traffic\TrafficStatsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordPageViews
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('shop.traffic.enabled', true)) {
            return $response;
        }

        if (! $response->isSuccessful()) {
            return $response;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        if ($this->shouldSkip($request)) {
            return $response;
        }

        if ($request->routeIs('products.show') && $response->headers->get('X-Page-Cache') === 'HIT') {
            return $response;
        }

        $hit = app(TrafficHitResolver::class)->resolve($request);
        if ($hit === null) {
            return $response;
        }

        app(TrafficStatsService::class)->record($hit['type'], $hit['key']);

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        if ($request->is('admin', 'admin/*', 'livewire/*', 'up')) {
            return true;
        }

        $agent = strtolower($request->userAgent() ?? '');
        foreach (['bot', 'spider', 'crawler', 'slurp', 'facebookexternalhit'] as $needle) {
            if (str_contains($agent, $needle)) {
                return true;
            }
        }

        return false;
    }
}