<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheGuestProductPages
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || $request->user() !== null || ! $request->routeIs('products.show')) {
            return $next($request);
        }

        $key = 'guest_product_page:'.sha1($request->fullUrl());

        if (Cache::has($key)) {
            return response(Cache::get($key))
                ->header('X-Page-Cache', 'HIT');
        }

        $response = $next($request);

        if ($response->isSuccessful() && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            Cache::put($key, $response->getContent(), now()->addMinutes((int) config('shop.cache.product_page_minutes', 15)));
            $response->headers->set('X-Page-Cache', 'MISS');
        }

        return $response;
    }
}
