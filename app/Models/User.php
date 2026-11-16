<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'username',
    'phone',
    'email',
    'password',
    'is_admin',
    'admin_role_id',
    'phone_verified_at',
    'email_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        if ($this->is_admin) {
            return true;
        }

        return $this->admin_role_id !== null && $this->canAdmin('panel.access');
    }

    public function hasAdminPanelAccess(): bool
    {
        return $this->is_admin || $this->admin_role_id !== null;
    }

    public function canAdmin(string $permission): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $permissions = $this->adminRole?->permissions ?? [];

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        if (str_contains($permission, '.')) {
            [$prefix] = explode('.', $permission, 2);

            return in_array($prefix.'.*', $permissions, true);
        }

        return false;
    }

    public function adminRole(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }

    public function hasPassword(): bool
    {
        return filled($this->password);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function defaultAddress(): ?Address
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->latest()->first();
    }
}
