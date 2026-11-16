<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Component;

class SearchBar extends Component
{
    public string $q = '';

    /** @var array<int, array{id: int, name: string, slug: string, formatted_price: string}> */
    public array $results = [];

    public bool $open = false;

    public function mount(): void
    {
        $this->q = request()->string('q')->toString();
    }

    public function updatedQ(): void
    {
        $term = trim($this->q);

        if (mb_strlen($term) < 2) {
            $this->results = [];
            $this->open = false;

            return;
        }

        $this->results = Product::query()
            ->where('is_active', true)
            ->search($term)
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'formatted_price' => $product->formattedPrice(),
            ])
            ->all();

        $this->open = count($this->results) > 0;
    }

    public function closeResults(): void
    {
        $this->open = false;
    }

    public function search(): void
    {
        $query = trim($this->q);

        if ($query === '') {
            $this->redirect(route('products.index'), navigate: true);

            return;
        }

        $this->redirect(route('products.index', ['q' => $query]), navigate: true);
    }

    public function render()
    {
        return view('livewire.shop.search-bar');
    }
}