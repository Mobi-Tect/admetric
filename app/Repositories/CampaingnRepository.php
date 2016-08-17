<?php

namespace App\Repositories;

use App\User;
use App\Campaingn;

class CampaingnRepository
{
    /**
     * Get all of the Campaingn for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function forUser(User $user)
    {
        return Campaingn::where('user_id', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }
}