<?php

namespace App\Actions\Account;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SetDefaultAddress
{
    public function execute(User $user, Address $address): void
    {
        DB::transaction(function () use ($user, $address): void {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }
}
