<?php

namespace App\Http\Middleware;

use App\Support\ShopAppearance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckShopMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $maintenance = ShopAppearance::maintenanceActive();
        } catch (\Throwable) {
            return $next($request);
        }

        if (! $maintenance) {
            return $next($request);
        }

        if ($request->is('admin', 'admin/*', 'livewire/*', 'up', 'payment/callback/*')) {
            return $next($request);
        }

        if ($request->user()?->is_admin ?? false) {
            return $next($request);
        }

        if ($request->routeIs('login', 'logout', 'password.*')) {
            return $next($request);
        }

        return response()->view('shop.maintenance', [
            'message' => ShopAppearance::maintenanceMessage(),
            'siteName' => ShopAppearance::siteName(),
        ], 503);
    }
}