<?php

namespace App\Actions\Account;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaveUserAddress
{
    public function execute(User $user, array $payload, ?Address $address = null): Address
    {
        return DB::transaction(function () use ($user, $payload, $address): Address {
            if ($address) {
                $address->update($payload);
            } else {
                $address = $user->addresses()->create($payload);
            }

            if ($payload['is_default'] ?? false) {
                app(SetDefaultAddress::class)->execute($user, $address);
            }

            return $address->fresh();
        });
    }
}