<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_on_homepage(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_route_has_rate_limit_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('login');

        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle:auth-pages', implode(',', $route->gatherMiddleware()));
    }

    public function test_checkout_route_has_rate_limit_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('checkout.index');

        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle:checkout', implode(',', $route->gatherMiddleware()));
    }
}
