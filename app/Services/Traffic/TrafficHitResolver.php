<?php

namespace App\Services\Traffic;

use App\Enums\TrafficEntityType;
use Illuminate\Http\Request;

class TrafficHitResolver
{
    /**
     * @return array{type: TrafficEntityType, key: string}|null
     */
    public function resolve(Request $request): ?array
    {
        if (! $request->isMethod('GET')) {
            return null;
        }

        $route = $request->route();
        if ($route === null) {
            return null;
        }

        $name = $route->getName();

        return match ($name) {
            'home' => [
                'type' => TrafficEntityType::Page,
                'key' => 'home',
            ],
            'blog.index' => [
                'type' => TrafficEntityType::Page,
                'key' => 'blog_index',
            ],
            'blog.show' => [
                'type' => TrafficEntityType::BlogPost,
                'key' => (string) $route->parameter('slug'),
            ],
            'products.show' => [
                'type' => TrafficEntityType::Product,
                'key' => (string) $route->parameter('slug'),
            ],
            'products.index' => $this->resolveProductIndex($request),
            default => null,
        };
    }

    /**
     * @return array{type: TrafficEntityType, key: string}|null
     */
    private function resolveProductIndex(Request $request): array
    {
        $categorySlug = $request->string('category')->trim()->toString();
        if ($categorySlug !== '') {
            return [
                'type' => TrafficEntityType::Category,
                'key' => $categorySlug,
            ];
        }

        return [
            'type' => TrafficEntityType::Page,
            'key' => 'products_index',
        ];
    }
}