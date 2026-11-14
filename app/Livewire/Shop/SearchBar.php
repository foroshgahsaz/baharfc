<?php

namespace App\Livewire\Shop;

use Livewire\Component;

class SearchBar extends Component
{
    public string $q = '';

    public function mount(): void
    {
        $this->q = request()->string('q')->toString();
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
