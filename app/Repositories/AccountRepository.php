<?php

namespace App\Repositories;

use App\User;
use App\Account;

class AccountRepository
{
    /**
     * Get all of the Account for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Account::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }
}