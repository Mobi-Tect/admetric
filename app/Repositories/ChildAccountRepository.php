<?php

namespace App\Repositories;

use App\User;
use App\ChildsAccount;

class ChildAccountRepository
{
    /**
     * Get all of the Account for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return ChildsAccount::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }
}