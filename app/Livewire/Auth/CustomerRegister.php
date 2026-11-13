<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CustomerRegister extends Component
{
    public string $name = '';

    public string $username = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique(User::class, 'username')],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique(User::class, 'phone')],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'نام را وارد کنید.',
            'username.required' => 'نام کاربری را وارد کنید.',
            'password.required' => 'رمز عبور را وارد کنید.',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => filled($validated['phone']) ? $validated['phone'] : null,
            'password' => $validated['password'],
            'is_admin' => false,
        ]);

        Auth::login($user);

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.customer-register');
    }
}
